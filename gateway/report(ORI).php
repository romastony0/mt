<style>
	.color-data {

		color: red;
	}
</style>

<?php
define("DB_CONNECTIONS", "TELCO_WAP,TELCO_SUB");
ini_set('display_errors', 0);
include_once("../framework/initialise/framework.init.php");
//include_once("../framework/initialise/helper.php");
date_default_timezone_set("Africa/Johannesburg");
global $library, $request, $db, $curl, $libxml, $log, $viewclass, $mail;

$fromdate = date("Y-m-d 00:00:00", strtotime("now"));
$todate = date("Y-m-d 23:59:59", strtotime("now"));
$today = date('Y-m-d');
console(LOG_LEVEL_INFO, "File Run Time :: " . $fromdate . " to " . $todate . " :: date: " . $today);

$to = date('Y-m-d', strtotime('-1 day'));
$from = date('Y-m-d', strtotime('-10 days'));


$to = date('Y-m-d', strtotime('-1 day'));
$from = date('Y-m-d', strtotime('-10 days'));


$lastDateOfThisMonth1 = strtotime('last day of previous  month');

$lastmonth = date('Y-m-d', $lastDateOfThisMonth1);

$lastDateOfThisMonth2 = strtotime('last day of -2 month');

$lastmonth_two = date('Y-m-d', $lastDateOfThisMonth2);


if ($today == date('Y-m-01')) {
	$bt = strtotime(date('Y-m-01', strtotime($today)), time());

	$curr_mon = date('Y-m', strtotime('-1 month', $bt));
	$prev_mon = date('Y-m', strtotime('-2 month', $bt));
	$two_month = date('Y-m', strtotime('-3 month', $bt));
} else {
	$bt = strtotime(date('Y-m-01', strtotime($today)), time());

	$curr_mon = date('Y-m', strtotime('-0 month', $bt));
	$prev_mon = date('Y-m', strtotime('-1 month', $bt));
	$two_month = date('Y-m', strtotime('-2 month', $bt));
}


$cur_hour = date("H", strtotime("now"));

$yesterday = date('Y-m-d', strtotime('-1 day'));
// $rowcnt = $db ['TELCO_SUB']->getOneRow("SELECT count(*) as cnt FROM hourly_flat_agencywise WHERE `date`='".$yesterday."';");
// if($rowcnt['cnt'] == 0){
// 	shell_exec('php tlk_data_insert_withagency.php');
//     shell_exec('php tlk_malwareblockday_wo_royalmobi.php');
// 	shell_exec('php tlk_royalmobi_malwareblock_day.php');
//     shell_exec('php telkom_data_insert.php');
//     shell_exec('php telkom_football_data.php');

// }

$packageid = 928;

// TOTAL HIT COUNT


$he = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(msisdn) AS tothitcount FROM hit_analysis 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' and pageid='1' GROUP BY hour_num");

foreach ($he as $val) {
	$data[$val['hour_num']]['he'] = $val['tothitcount'];
}

$whe = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(msisdn) AS tothitcount FROM hit_analysis 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' and pageid='0' GROUP BY hour_num");

foreach ($whe as $val) {
	$data[$val['hour_num']]['whe'] = $val['tothitcount'];
}


$hitcount = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(msisdn) AS tothitcount FROM hit_analysis 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' GROUP BY hour_num");

foreach ($hitcount as $val) {
	$data[$val['hour_num']]['totalhit'] = $val['tothitcount'];
}

// RTCG COUNT
$rtcgcnt = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num, COUNT(*) AS cgcnt FROM subscription_detail 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' AND transactionid LIKE '%SUB%' 
AND LENGTH(transactionid) = '21' GROUP BY hour_num");
foreach ($rtcgcnt as $val) {
	$data[$val['hour_num']]['rtcgcnt'] = $val['cgcnt'];
}

// CG RESPONSE COUNT
$cgrcnt = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,  COUNT(IF(response='success',1,NULL)) ACCEPT,  
COUNT(IF(response='failed',1,NULL)) DECLINE,  COUNT(IF(response = 'pending',1,NULL)) OTHER FROM subscription_detail WHERE 
packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "'  AND transactionid LIKE '%SUB%' AND LENGTH(transactionid)
 = '21'  GROUP BY hour_num ");
foreach ($cgrcnt as $val) {
	$data[$val['hour_num']]['acceptcnt'] = $val['ACCEPT'];
	$data[$val['hour_num']]['declinecnt'] = $val['DECLINE'];
	$data[$val['hour_num']]['othercnt'] = $val['OTHER'];
}

//AGENCY SUCCES AND HOLD
$agency_success_hold = $db['TELCO_WAP']->getResults("SELECT HOUR(dateandtime) AS hour_num,DATE(dateandtime) AS DATE,SUM(IF(response='Success',1,0)) AS ag_success,SUM(IF(response='HOLD',1,0)) AS ag_hold FROM agent_hit_analysis WHERE agid IN(" . $agid . ") AND dateandtime >= '" . $fromdate . "' AND dateandtime <= '" . $todate . "'  GROUP BY HOUR(dateandtime),DATE(dateandtime)");
//echo "<pre>";print_r($agency_success_hold);echo "</pre>";
foreach ($agency_success_hold as $val) {
	$data[$val['hour_num']]['agencysuccess'] = $val['ag_success'];
	$data[$val['hour_num']]['agencyhold'] = $val['ag_hold'];
}

// DND and Already Active user count
$dndresult = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num, COUNT(msisdn) AS usercnt, user_status 
FROM hit_analysis WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND
 transtime<='" . $todate . "'  GROUP BY hour_num,user_status");
foreach ($dndresult as $val) {
	$data[$val['hour_num']][$val['user_status']] = $val['usercnt'];
}

// subscription success count 

// $base_count=$db['TELCO_SUB']->getOneRow("SELECT COUNT(msisdn) AS base_count FROM subscription WHERE STATUS IN ('active' ,'pending')  AND ptype != '691-RoyalMobiTlkEnt'");
// $varMessage = "";
$year1 = date("Y", strtotime("now"));
$month1 = date("m", strtotime("now"));
$date1 = date("d", strtotime("now"));
$cur_hour1 = date("H", strtotime("now"));
//shell_exec('php  tlk_malwareblockhourly_wo_royalmobi.php');
// $block_link = "http://13.235.101.157/reporting_task/storage/telkom/blocked/hourly/".$year1."/".$month1."/".$date1."/TELKOM_" . $year1 . "_" . $month1 . "_" . $date1 . "_blockedcount_hour_".$cur_hour1.".csv";
// $varMessage.= 'Malware Blocked counts <a href="'.$block_link.'">
//  Click here for details</a><br>';

$varMessage = $varMessage . '<h3>TELKOM-KENYA Hourly Report - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '</h3>
	<i style="font-family: \'Source Sans Pro\',\'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 13px;"> 
		<span style="color: red;">IST</span> - Indian Standard Time,
		<span style="color: red;">SAST</span> - South Africa Standard Time,
				<span style="color: red;">HE</span> - Header Enrichment,
                <span style="color: red;">Wifi</span> - Wifi,
                <span style="color: red;">TH</span> - Total hits,
				<span style="color: red;">RTCG</span> - Redirect to CG,
				<span style="color: red;">AS</span> - Agency Success,
		         <span style="color: red;">AH</span> - Agency Hold,
                 <span style="color: red;">CUS</span> - Customer,
		         <span style="color: red;">SD</span> - Same Day,
		         <span style="color: red;">CGR</span> - CG Response,
		         <span style="color: red;">Deact Count</span> - Deactivation Count,
		         <span style="color: red;">Sub Count</span> - Subscription Count,
		         <span style="color: red;">Ren Count</span> - Renewal Count,
		          <span style="color: red;">Sub Revenue</span> - Subscription Revenue,
		          <span style="color: red;">Ren Revenue</span> - Renewal Revenue,
		          <span style="color: red;">LowBal</span> - Low Balance,	
		          <span style="color: red;">CUS</span> - Customer,
		          <span style="color: red;">SD</span> - Same Day,
		          <span style="color: red;">SYS</span> - System,
		          <span style="color: red;">Deact Mode</span> -Deactivation Mode 
				  
				 </i><br><br>

	<table style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0" >	  
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" colspan="32">TELKOM-KENYA HOURLY REPORT - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '&nbsp&nbsp&nbsp&nbsp&nbsp<b><p class="color-data">AVAILABLE BASE -' . $base_count['base_count'] . '</p></th>
	   </tr>
	   <tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">SAST </br>HOUR</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">IST </br>HOUR</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">HE</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">Wifi</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">TH</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">RTCG</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">AS</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">AH</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">CGR</th>
		</tr>
		<tr>
			</tr>';
$ren_avail = $sub_avail = $total_avail = 0;
for ($i = 0; $i <= $cur_hour; $i++) {
	$he = isset($data[$i]["he"]) ? $data[$i]["he"] : "0";
	$whe = isset($data[$i]["whe"]) ? $data[$i]["whe"] : "0";
	$totalhit = isset($data[$i]["totalhit"]) ? $data[$i]["totalhit"] : "0";

	//	$uniqhit=isset($data[$i]["uniqhit"])?$data[$i]["uniqhit"]:"0";
	$rtcgcnt = isset($data[$i]["rtcgcnt"]) ? $data[$i]["rtcgcnt"] : "0";
	$acceptcnt = isset($data[$i]["acceptcnt"]) ? $data[$i]["acceptcnt"] : "0";
	$declinecnt = isset($data[$i]["declinecnt"]) ? $data[$i]["declinecnt"] : "0";
	$othercnt = isset($data[$i]["othercnt"]) ? $data[$i]["othercnt"] : "0";
	$wapsuccess = isset($data[$i]['WAP_DOI']["subsuccess"]) ? $data[$i]['WAP_DOI']["subsuccess"] : "0";
	$ussdsuccess = isset($data[$i]['USSD']["subsuccess"]) ? $data[$i]['USSD']["subsuccess"] : "0";
	$othersubsuccess = isset($data[$i]['other']["subsuccess"]) ? $data[$i]['other']["subsuccess"] : "0";
	$subsuccess = $wapsuccess + $ussdsuccess + $othersubsuccess;
	$wapsubpending = isset($data[$i]['WAP_DOI']["subpending"]) ? $data[$i]['WAP_DOI']["subpending"] : "0";
	$ussdsubpending = isset($data[$i]['USSD']["subpending"]) ? $data[$i]['USSD']["subpending"] : "0";
	$othersubpending = isset($data[$i]['other']["subpending"]) ? $data[$i]['other']["subpending"] : "0";
	$subpending = $wapsubpending + $ussdsubpending + $othersubpending;
	// $subfailed=isset($data[$i]["subfailed"])?$data[$i]["subfailed"]:"0";



	$total_revenue = $sub_revenue + $ren_revenue;

	$total_avail = $ren_avail + $sub_avail;
	$gmttimeval = date("H:i", strtotime($today . " " . $i . ":00:00 +210 minutes"));
	//$gmttimeval = gmdate("H:i", strtotime($today." ".$i.":00:00") + 3600*($timezone+date("I")));

	//<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_declinecnt[]=$declinecnt.'</p></td>
	$varMessage .= '<tr>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $i . '</p></td>	
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $gmttimeval . '</p></td>	
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $he_count[] = $he . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $whe_count[] = $whe . '</p></td>
		        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_totalhit[] = $totalhit . '</p></td>
			    <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_rtcgcnt[] = $rtcgcnt . '</p></td>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_agsucc[] = $agsucc . '</p></td>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_aghold[] = $aghold . '</p></td>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_acceptcnt[] = $acceptcnt . '</p></td>		
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_declinecnt[] = $declinecnt . '</p></td>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_othercnt[] = $othercnt . '</p></td>
				</tr>';
}


//		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_declinecnt).'</strong></td>
$varMessage .= '<tr>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;" colspan="2"><p align="center"><strong>Total</strong></td>

        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($he_count) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($whe_count) . '</strong></td>
       <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_totalhit) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_rtcgcnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_agsucc) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_aghold) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_acceptcnt) . '</strong></td>
		
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_declinecnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_othercnt) . '</strong></td>
		</tr>';



$varMessage = $varMessage . '</table>';



echo $varMessage;
//exit;
//$to='jeevitha.k@m-tutor.com';  
//$ccto='jeevitha.k@m-tutor.com'; 

?>