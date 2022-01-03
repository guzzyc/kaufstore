<?php
require_once '../../vendor/autoload.php';
use Jajo\JSONDB;
try{
	$json_db = new JSONDB(__DIR__);
	$json_db->delete()
		->from( 'voucher.json' )
		->trigger();
}catch(Exception $e){
	// Ignore not existibg JSON Db
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="theme-color"><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Loyalty Application:: Kaufland ::</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link rel="stylesheet"  href="../Util/KauflandStyle.css">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>

var loyaltyApplicationURL="http://localhost:8000";
var registerApplicationURL="http://localhost:8000";

function pullDatabase(){
	$.ajax({
		url:registerApplicationURL+"/Loyalty/voucher.json",
		type: 'GET',data: {},
        success: function(returndata) { 
			$("#dbase").val("");
			for (var i = 0; i < returndata.length; i++){
				$("#dbase").val($("#dbase").val()+"\r\nVoucher:");
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
	<h4>Loyalty Panel</h4>
	<div class="cleardiv"></div>	
	<h5>Voucher Database</h5>	
	<div class="cleardiv"></div>
	</div>	
	<div class="kaufLogPanel" >
		<textarea style="width:500px" id="dbase">
		</textarea>
	</div>	
</body>