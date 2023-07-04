<?php
//define("DB_CONNECTIONS","MASTER");
ini_set('display_errors', 1); 
include_once("../framework/initialise/framework.init.php");
include_once("functions.php");
global $library,$request,$db,$curl;
//echo '<pre>';print_r($db);
$library->loadLibrary ( "curl" );
console(LOG_LEVEL_TRACE, "Incoming Request ::".var_export($request, true));
$currentdatetime = date("Y-m-d H:i:s");

/* array ( 'bc_id' => '188', 'mno_id' => '4', 'svc_id' => '59', 'ext_ref' => '5282202003051034', 'user_id' => '48', 'svc_name' => 'Innovate Entertainment ', 'status_id' => '2', 'created_at' => '2020-03-05T10:35:05+02:00', 'expires_at' => '2020-03-05T10:35:05+02:00', 'updated_at' => '2020-03-05T10:35:07+02:00', 'campaign_id' => '0', 'status_name' => 'ACTIVE', 'user_msisdn' => '27677458401', 'affiliate_id' => '0', 'billing_rate' => '500', 'channel_name' => 'WAP_DOI', 'renewal_type' => 'AUTO', 'billing_cycle' => 'DAILY', 'last_billed_at' => '2020-03-05T10:35:05+02:00', 'next_billing_at' => '2020-03-05T10:35:05+02:00', 'subscription_id' => '625', 'subscription_started_at' => '2020-03-05T10:35:05+02:00', )


{"bc_id":188,"mno_id":4,"svc_id":59,"ext_ref":"5282202003051034","user_id":48,"svc_name":"Innovate Entertainment ","status_id":2,"created_at":"2020-03-05T10:35:05+02:00","expires_at":"2020-03-05T10:35:05+02:00","updated_at":"2020-03-05T10:35:07+02:00","campaign_id":0,"status_name":"ACTIVE","user_msisdn":"27677458401","affiliate_id":0,"billing_rate":500,"channel_name":"WAP_DOI","renewal_type":"AUTO","billing_cycle":"DAILY","last_billed_at":"2020-03-05T10:35:05+02:00","next_billing_at":"2020-03-05T10:35:05+02:00","subscription_id":625,"subscription_started_at":"2020-03-05T10:35:05+02:00"} */

//Incoming Request ::array ( 'bc_id' => '188', 'mno_id' => '4', 'svc_id' => '83', 'ext_ref' => '2339202003251227', 'user_id' => '669454', 'svc_name' => 'Innovate Entertainment ', 'status_id' => '2', 'created_at' => '2020-03-25T12:28:04+02:00', 'expires_at' => '2020-03-25T12:28:04+02:00', 'updated_at' => '2020-03-25T12:28:08+02:00', 'campaign_id' => '0', 'status_name' => 'ACTIVE', 'user_msisdn' => '27677447571', 'affiliate_id' => '0', 'billing_rate' => '300', 'channel_name' => 'WAP_DOI', 'renewal_type' => 'AUTO', 'billing_cycle' => 'DAILY', 'last_billed_at' => '2020-03-25T12:28:04+02:00', 'next_billing_at' => '2020-03-25T12:28:04+02:00', 'subscription_id' => '1232665', 'subscription_started_at' => '2020-03-25T12:28:04+02:00', )


$datasring = json_decode(file_get_contents('php://input'));
console(LOG_LEVEL_TRACE, "Incoming Request ::".var_export($request, true));

$dataPOST = simplexml_load_string($datasring);
$processed = json_encode($dataPOST);
$jsondecode = json_decode($processed, true);
$externalidget = $jsondecode['@attributes']; 
echo "success";
?>