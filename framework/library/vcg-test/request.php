<?php
/**
 * Created by PhpStorm.
 * User: TSDC-User
 * Date: 2/3/2017
 * Time: 12:32 PM
 */

date_default_timezone_set("Asia/Colombo");
require_once('libraries/Vcg.php');
if(isset($_GET['submit'])){
    $vcg = new Vcg();
    $vcg->setMSISDN($_GET['msisdn']);
    $vcg->setTransactionId($_GET['ti']);
    $vcg->setAmount($_GET['amount']);
    $vcg->setContentType($_GET['ct']);
    $vcg->setContentId($_GET['ci']);
    $vcg->setSubscriptionType($_GET['st']);
    $aResult = $vcg->setInitialize();


    if(isset($aResult->target_activity) && $aResult->target_activity == 1){
        header("Location: ".$aResult->iframe_url);
        exit();
    }
    if(isset($aResult->response_url) && !empty($aResult->response_url)) {
        $responseData = base64_encode(json_encode($aResult));
        $responseUrl .= '?params=' . $responseData;
        header("Location: " . $aResult->response_url . $responseUrl);
    }else{
        var_dump($aResult);
    }
}

