<?php
include_once("../framework/initialise/framework.init.php");
global $library,$request,$db,$curl;
$library->loadLibrary ( "curl" );

//Incoming Request ::array ( 'bc_id' => '188', 'mno_id' => '4', 'svc_id' => '83', 'ext_ref' => '2339202003251227', 'user_id' => '669454', 'svc_name' => 'Innovate Entertainment ', 'status_id' => '2', 'created_at' => '2020-03-25T12:28:04+02:00', 'expires_at' => '2020-03-25T12:28:04+02:00', 'updated_at' => '2020-03-25T12:28:08+02:00', 'campaign_id' => '0', 'status_name' => 'ACTIVE', 'user_msisdn' => '27677447571', 'affiliate_id' => '0', 'billing_rate' => '300', 'channel_name' => 'WAP_DOI', 'renewal_type' => 'AUTO', 'billing_cycle' => 'DAILY', 'last_billed_at' => '2020-03-25T12:28:04+02:00', 'next_billing_at' => '2020-03-25T12:28:04+02:00', 'subscription_id' => '1232665', 'subscription_started_at' => '2020-03-25T12:28:04+02:00', )


$req_data = json_decode(file_get_contents('php://input'), true);
console(LOG_LEVEL_TRACE, "Incoming Request ::".var_export($req_data, true));
//echo "success";
$resp_ar = array('Code' => '200', 'Message' => 'OK');
echo json_encode($resp_ar);
console(LOG_LEVEL_TRACE, "Subscription callback response ::".json_encode($resp_ar, true));


$msisdn = $req_data['user_msisdn'];
$svc_id = $req_data['svc_id'];
$channel = $req_data['channel_name'];
$subscription_id = $req_data['subscription_id'];
$status_name = $req_data['status_name'];
$transactionid = $req_data['ext_ref'];
$duplicate_qry = "SELECT COUNT(*) AS cnt FROM subscription WHERE msisdn = '".$msisdn."' AND op_subscription_id = '".$transactionid."' and status='pending'";
$is_duplicate = $db['TELK_SUB']->getOneRow($duplicate_qry);
if($is_duplicate['cnt'] != '0') {
	console(LOG_LEVEL_TRACE, "Duplicate Subscription callback ".$msisdn);
	exit();
}



if($status_name == 'ACTIVE') {
	
	
	
	$sub_ins_qry = "INSERT INTO `subscription`
				(`idkeyword`,`msisdn`,`subscribedon`,`subscribemode`,`status`,`op_subscription_id`)
		 VALUES ('1', '".$msisdn."', now(), '".$channel."', 'pending', '".$subscription_id."')";

	$db['TELK_SUB']->query($sub_ins_qry);
	
	$wap_update_url = "13.235.101.157/telk-wap/gateway/renewal_unsub_notify.php?msisdn=".$msisdn."&packageid=691&validity=1&transactionid=".$transactionid."&op_subscription_id=".$subscription_id."&type=SUBSCRIPTION";
	$wap_update_res = $curl->get($wap_update_url);
}


if($status_name == 'CANCELLED') {
	$sub_update_qry = "UPDATE `subscription` SET 
	  `status` = 'inactive',
	  `unsubscribemode` = '".$channel."',
	   unsubscribedon = now()
	WHERE `op_subscription_id` = '".$subscription_id."' AND `msisdn` = '".$msisdn."'";
	
	$db['TELK_SUB']->query($sub_update_qry);
	
	$wap_update_url = "13.235.101.157/telk-wap/gateway/renewal_unsub_notify.php?msisdn=".$msisdn."&packageid=691&validity=1&type=UNSUB";
	$wap_update_res = $curl->get($wap_update_url);
}
?>