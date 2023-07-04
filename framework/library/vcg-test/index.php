<?php
/**
 * Created by PhpStorm.
 * User: TSDC-User
 * Date: 2/3/2017
 * Time: 12:32 PM
 */


date_default_timezone_set("Asia/Colombo");
require_once('libraries/Vcg.php');
$vcg = new Vcg();
$vcg->setVendorKey('a1b2c3d4e5f6g362');
$vcg->setVendorId('100003');
$vcg->setVcgUrl('http://online.hutch.lk/vcg/vcg-connect/cg-connect');
$vcg->setMSISDN('0784400544');
$vcg->setTransactionId('4325we341');
$vcg->setAmount('5.00');
$vcg->setContentType('AUDIO');
$vcg->setContentId('10');
$vcg->setSubscriptionType('1');
$aResult = $vcg->setInitialize();
//exit;
if(isset($aResult->target_activity) && $aResult->target_activity == 1){
    header("Location: ".$aResult->iframe_url);
    exit();
}



if(isset($aResult->response_url)){
$responseData = base64_encode(json_encode($aResult));
$responseUrl .= '?params='.$responseData;
header("Location: ".$aResult->response_url.$responseUrl);
}else{
	var_dump($aResult);
}