<?php
// This function persist all transaction important keys customerid, transactionid, voucherid and provision id
require_once '../../vendor/autoload.php';
use Jajo\JSONDB;
$json_db = new JSONDB(__DIR__);



// 1 Check and generate voucher
// 2 Rollback code for customer id
$voucherId=$_REQUEST['voucherId'];
$customerId=$_REQUEST['customerId'];
$transactionId=$_REQUEST['transactionId'];
$bankProvisionId=$_REQUEST['bankProvisionId'];

$json_db->insert( 'register.json', 
	[ 
		'customer' => $customerId, 
		'transaction' => $transactionId, 
		'voucher' => $voucherId,
		'bankProvisionId' => $bankProvisionId
	]
);	

echo "Hello";		// Return success
?>