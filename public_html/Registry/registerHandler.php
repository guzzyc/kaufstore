<?php
require_once '../../vendor/autoload.php';
use Jajo\JSONDB;
try{
	$json_db = new JSONDB(__DIR__);
	$json_db->delete()
	->from( 'register.json' )
		->trigger();
}catch(Exception $e){
	// Ignore not existibg JSON Db
}	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="theme-color"><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Register Application:: Kaufland ::</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet"  href="../Util/KauflandStyle.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>

var loyaltyApplicationURL="http://localhost:8000";
var registerApplicationURL="http://localhost:8000";
var customerId=0;

function log(customerId,text){

	$("#history").val($("#history").val()+"\r\n"+"Customer Id:"+customerId+" "+text);
	$("#history").height($("#history").prop('scrollHeight'));
}	
function checkEligibility(customerId,transactionId,grandTotal){

	var checkEligibilityReqCode=1;

	var result = $.ajax({
		url:loyaltyApplicationURL+"/Loyalty/voucherHandler.php",
		type: 'GET',data: { requestCode:checkEligibilityReqCode,grandTotal:grandTotal,customerId:customerId,transactionId:transactionId},
		async: false,
        error: function() { 
			throw("Can not generate voucher.");
		}
	});
	console.log(result);
	console.log(result.responseText);
	return result.responseText;
}

function rollBackVoucher(voucherId){
	$.ajax({
		url:loyaltyApplicationURL+"/Loyalty/rollbackVoucher.php",
		type: 'POST',data: { voucherId:voucherId},
        success: function(returndata) { 
			if (!returndata){
				alert("Can not roll back voucher.");
			}
		}
	});
}

// This functiom generates random string at length 20
function makeid() {
    var result           = '';
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < 10; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result;
}

// This functiom generates random transansaction id (ideally it should be sequential)
function generateUniqTransactionId(){
	return makeid();
}

// This function generates random shopping amount between 0 - 200, so that with 0.5 probability earns voucher
var maxTotal=200;
function calculateTotal(shoppingCart){
	return Math.round(Math.random() * maxTotal);
}

// This function generates random result, so that with 0.8 probability successfully provision credit card
// If successful returns random provision id
function getBankProvision(customerId,shoppingCart){
	if  (Math.random() > 0.8)
		throw ("Credit card not provisioned");
	return makeid();
}

function persistRegisterDatabase(customerId,transactionId,bankProvisionId,voucherId){
	$.ajax({
		url:registerApplicationURL+"/Registry/registerPersist.php",
		type: 'POST',data: { customerId:customerId,transactionId:transactionId,bankProvisionId:bankProvisionId,voucherId:voucherId},
        success: function(returndata) { 
			console.log(returndata);
			if (!returndata){
				alert("Error n persisting transactions");
			}
		}
	});
}
function buy(customerId,shoppingCart){
	var transactionId;
	var voucherId;
	var bankProvisionId;
	var grandTotal;
	
	transactionId = generateUniqTransactionId();
	grandTotal = calculateTotal(shoppingCart);
	
	log(customerId,"Transaction "+transactionId+" is starts with grand total :"+grandTotal);

	try{
		voucherId = checkEligibility(customerId,transactionId,grandTotal);
	}catch (err){
		log(customerId,"Loyalty system might not be running. Transaction "+transactionId+" is aborted.");
		return;
	}

	if (!voucherId || voucherId =='0')
		log(customerId,"No eligibility for voucher by business rule");
	else
		log(customerId,"Loyalty system generated voucher: "+voucherId);
	
	try{
		bankProvisionId = getBankProvision(customerId,grandTotal);
	}catch (err){
		log(customerId,"Could not get provision.");
		if (voucherId && voucherId != '0'){
			log(customerId,"Voucher "+voucherId+" is rolled back.");		
			rollBackVoucher(voucherId);
		}
		return;
	}
	log(customerId,"Transaction provisioned credit card by id:"+bankProvisionId);	
	
	// Everything is persist transaction as successful
	persistRegisterDatabase(customerId,transactionId,bankProvisionId,voucherId);
}
var buysCnt=0;
var maxBuys=10;

function startPurchase(){
	var shoppingCart;
	
	buy(customerId++,shoppingCart);
	buysCnt++;
	setTimeout(function (){
		if (buysCnt < maxBuys)
			startPurchase();
	},5000);
}

function pullDatabase(){
	$.ajax({
		url:registerApplicationURL+"/Registry/register.json",
		type: 'GET',data: {},
        success: function(returndata) { 
			$("#dbase").val("");
			for (var i = 0; i < returndata.length; i++){
				$("#dbase").val($("#dbase").val()+"\r\nTransaction: ");
				var obj = returndata[i];
				for (var key in obj){
					var value = obj[key];
					$("#dbase").val($("#dbase").val()+"\r\n - " + key + ": " + value);				
				}
			}
			$("#dbase").height($("#dbase").prop('scrollHeight'));	
		}
	});
}
</script>	
</head>
<body onload="setInterval(pullDatabase,5000)">
	<h4>Registery Panel</h4>
	<div class="cleardiv"></div>	
	<div id="randomPurchase" class="kaufCard" onclick="startPurchase()">
		<div class="kaufPanel">
			<a name="purchaseBtn class="waves-effect waves-light sideNavLine"><i class="material-icons left sideNavLine">shopping_cart</i>Start Shopping</a>
		</div>
	</div>
	<div class="cleardiv"></div>
	<h5 style="width:500px">Transaction Log</h5>
	<h5>Register Database</h5>	
	<div class="cleardiv"></div>
	</div>	
	<div class="kaufLogPanel" >
		<textarea style="width:500px" id="history">
		</textarea>
	</div>
	<div class="kaufLogPanel" >
		<textarea style="width:500px" id="dbase">
		</textarea>
	</div>	
</body>