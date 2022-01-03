<?php

require_once '../../vendor/autoload.php';
use Jajo\JSONDB;
$json_db = new JSONDB(__DIR__);

$voucherId=$_REQUEST['voucherId'];

$json_db->delete()
	->from( 'voucher.json' )
	->where( [ 'voucher' => $voucherId ] )
	->trigger();

echo "1";
?>