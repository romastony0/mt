<?php
// define("DB_CONNECTIONS","SLAVE_INTDEXTER");
// ini_set('display_errors', 1); 
include_once("../framework/initialise/framework.init.php");
include_once("functions.php");
global $library,$request,$db,$curl;
//echo '<pre>';print_r($db);
$library->loadLibrary ( "curl" );
console(LOG_LEVEL_TRACE, "Incoming Request ::".var_export($request, true));
$currentdatetime = date("Y-m-d H:i:s");
// echo '<pre>';print_r($db);

$datasring = trim(file_get_contents('php://input'));
console(LOG_LEVEL_TRACE, "Incoming Request XML DATA 8::".$datasring);
/* console(LOG_LEVEL_TRACE, "Incoming Request XML DATA 8.1::".var_export($datasring,true)); */
$dataPOST = simplexml_load_string($datasring);
//$xmlData = simplexml_load_string($dataPOST);
// console(LOG_LEVEL_TRACE, "Incoming Request XML DATA 4::".$dataPOST);
// console(LOG_LEVEL_TRACE, "Incoming Request XML DATA 4.1::".var_export($dataPOST,true));
$processed = json_encode($dataPOST);
// console(LOG_LEVEL_TRACE, "Incoming Request XML DATA 5::".$processed);
// console(LOG_LEVEL_TRACE, "Incoming Request 5.1::".var_export($processed	,true));


// $xml=simplexml_load_string($dataPOST);
// $jsonencode  = json_encode($xml);
$jsondecode = json_decode($processed, true);
// console(LOG_LEVEL_TRACE, "Incoming Request 6::".$jsondecode);
// console(LOG_LEVEL_TRACE, "Incoming Request 6.1::".var_export($jsondecode,true));
$externalidget = $jsondecode['@attributes'];


console(LOG_LEVEL_TRACE, "XML Parameter :".var_export($externalidget,true));

/* Pleas find below link 
Notification of Subscription Status Changes : 
http://13.235.101.157/telk-sub/gateway/subscription_callback.php
Notification of Successful Billing Events :
	http://13.235.101.157/telk-sub/gateway/billing_callback.php */
echo "Success";
?>
