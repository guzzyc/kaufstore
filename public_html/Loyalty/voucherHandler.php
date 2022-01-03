<?php

require_once '../../vendor/autoload.php';
use Jajo\JSONDB;
$json_db = new JSONDB(__DIR__);


// 1 Check and generate voucher
// 2 Rollback code for customer id
$requestCode=$_REQUEST['requestCode'];
$customerId=$_REQUEST['customerId'];
$transactionId=$_REQUEST['transactionId'];
$grandTotal=$_REQUEST['grandTotal'];


if ($grandTotal > 100){
	$tokenId = generateRandomTokenString();
	$json_db->insert( 'voucher.json', 
		[ 
			'voucher' => $tokenId,		
			'customer' => $customerId, 
			'transaction' => $transactionId, 
		]
	);	
	echo  $tokenId;
}
else{
	echo 0;		// Return voucherid
}

function generateRandomTokenString($length = 10) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}
?>