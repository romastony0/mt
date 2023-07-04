<?php

ini_set("display_errors", 1);

define("DB_CONNECTIONS", "TELCO_WAP,TELCO_SUB,Agency_COMMON");
ini_set('display_errors', 0);
include_once("../framework/initialise/framework.init.php");
date_default_timezone_set("Africa/Johannesburg");
global $library, $request, $db, $curl, $libxml, $log, $viewclass, $mail;
console(LOG_LEVEL_INFO, "Db Configured");
$fromdate = date("Y-m-d 00:00:00", strtotime("now"));
$todate = date("Y-m-d 23:59:59", strtotime("now"));
$today = date('Y-m-d');
// console(LOG_LEVEL_INFO, "File Run Time :: " . $fromdate . " to " . $todate . " :: date: " . $today);

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




$packageid = 928;
$telcoid = 10;



//agentid
$distinctAgentIds = $db['Agency_COMMON']->getResults("SELECT DISTINCT agency_id as agentid, agency_name as agent_name FROM agency_list");
foreach ($distinctAgentIds as $val) {
    $data[$val['agentid']]['agentid'] = $val['agentid'];
    $data[$val['agentid']]['agent_name'] = $val['agent_name'];
}
console(LOG_LEVEL_TRACE, var_export($distinctAgentIds, true));

foreach ($distinctAgentIds as $row) {
    $agentId = $row['agentid'];
    //agency name
    $agencyname = $db['Agency_COMMON']->getResults("SELECT agency_name FROM agency_list WHERE agency_id = '" . $agentId . "'");
    $data[$agentId]['agencyname'] = $agencyname[0]['agency_name'];
    foreach ($agencyname as $val) {
        $data[$agentId]['agencyname'] = $val['agency_name'];
    }
    console(LOG_LEVEL_TRACE, var_export($agencyname, true));

    // TOTAL HIT COUNT
    $tothitcount = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS totalhitcount FROM hit_analysis WHERE agentid = '" . $agentId . "' AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "'");
    foreach ($tothitcount as $val) {
        $data[$val['totalhitcount']]['totalhitcount'] = $val['totalhitcount'];
    }
    console(LOG_LEVEL_TRACE, var_export($tothitcount, true));


    //nonmalware count
    $nonmalware = 0;

    //malware count
    $malware = 0;


    //rtcg count
    $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt FROM subscription_detail WHERE agid IN ('" . $agentId . "') AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "' AND transactionid LIKE '%SUB%' AND LENGTH(transactionid) = '21'");
    foreach ($rtcgcnt as $val) {
        $data[$val['rtcgcnt']]['rtcgcnt'] = $val['rtcgcnt'];
    }
    console(LOG_LEVEL_TRACE, var_export($rtcgcnt, true));


    //get agency success and hold count
    $agency_success_hold = $db['TELCO_SUB']->getResults("SELECT SUM(IF(postback_status='success',1,0)) AS ag_success,SUM(IF(postback_status='hold',1,0)) AS ag_hold FROM postback_history WHERE agid IN('" . $agentId . "') AND subscribedon >= '" . $fromdate . "' AND subscribedon <= '" . $todate . "'");
    foreach ($agency_success_hold as $val) {
        $data[$val['ag_success']]['ag_success'] = $val['ag_success'];
        $data[$val['ag_hold']]['ag_hold'] = $val['ag_hold'];
    }
    console(LOG_LEVEL_TRACE, var_export($agency_success_hold, true));

    //get subscription count
    $subcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");
    foreach ($subcount as $val) {

        $data[$val['renewalcount']]['renewalcount'] = $val['renewalcount'];
    }
    console(LOG_LEVEL_TRACE, var_export($subcount, true));

    //get renewal count

    $rencount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");
    foreach ($rencount as $val) {
        $data[$val['renewalcount']]['renewalcount'] = $val['renewalcount'];
    }
    console(LOG_LEVEL_TRACE, var_export($rencount, true));


    // Get deactivation count
    $deactcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS deactcount FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Deactivate' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");
    foreach ($deactcount as $val) {
        $data[$val['deactcount']]['deactcount'] = $val['deactcount'];
    }
    console(LOG_LEVEL_TRACE, var_export($deactcount, true));

    // Get subscription revenue
    $subrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrevenue FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");
    foreach ($subrevenue as $val) {
        $data[$val['subrevenue']]['subrevenue'] = $val['subrevenue'];
    }
    console(LOG_LEVEL_TRACE, var_export($subrevenue, true));


    // Get renewal revenue
    $renrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS renrevenue FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");
    foreach ($renrevenue as $val) {
        $data[$val['renrevenue']]['renrevenue'] = $val['renrevenue'];
    }
    console(LOG_LEVEL_TRACE, var_export($renrevenue, true));

    $totalrevenue = $subscriptionrevenue + $renewalrevenue;
}



$varMessage = $varMessage . '<h3>TELKOM ZA Agency  Hourly Report - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '</h3> 
<i style="font-family: \'Source Sans Pro\',\'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 13px;">
				<span style="color: red;">RTCG</span> - Redirect to CG,
				<span style="color: red;">AS</span> - Agency Success,
		         <span style="color: red;">AH</span> - Agency Hold,
		         <span style="color: red;">Sub Count</span> - Subscription Count,
		         <span style="color: red;">Ren Count</span> - Renewal Count,		         
				 <span style="color: red;">Deact Count</span> - Deactivation Count,
				 <span style="color: red;">Sub Revenue</span> - Subscription Revenue,
		          <span style="color: red;">Ren Revenue</span> - Renewal Revenue
		       </i>	
<br><br>

	<table style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0" >	  
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" colspan="13">TELKOM ZA AGENCY HOURLY REPORT - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '</th>
	   </tr>
       <tr>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Agency</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Total<br> Hits</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Non Malware<br> Hits</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Malware<br>Hits</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">RTCG</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">AS</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">AH</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Sub<br>Count</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Ren<br>Count</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Deact<br>Count</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Sub<br>Revenue</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Ren<br>Revenue</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center">Total<br>Revenue</th></tr>



       </tr>';



// $ren_avail = $sub_avail = $total_avail = 0;

$totalagencyid = $db['Agency_COMMON']->getResults("SELECT COUNT(agency_id) AS total_count
FROM agency_list");
echo $totalagencyid[0]['total_count'];


//data itereation to table 
for ($i = 0; $i <= $totalagencyid[0]['total_count']; $i++) {

    $agencyname = isset($data[$i]["agencyname"]) ? $data[$i]["agencyname"] : "NA";
    $tothitcount = isset($data[$i]["tothitcount"]) ? $data[$i]["tothitcount"] : "0";
    $nonmalware = isset($data[$i]["nonmalware"]) ? $data[$i]["nonmalware"] : "0";
    $malware = isset($data[$i]["malware"]) ? $data[$i]["malware"] : "0";
    $rtcgcnt = isset($data[$i]["rtcgcnt"]) ? $data[$i]["rtcgcnt"] : "0";
    $ag_success = isset($data[$i]["ag_success"]) ? $data[$i]["ag_success"] : "0";
    $ag_hold = isset($data[$i]["ag_hold"]) ? $data[$i]["ag_hold"] : "0";
    $renewalcount = isset($data[$i]["renewalcount"]) ? $data[$i]["renewalcount"] : "0";
    $deactcount = isset($data[$i]["deactcount"]) ? $data[$i]["deactcount"] : "0";
    $subrevenue = isset($data[$i]["subrevenue"]) ? $data[$i]["subrevenue"] : "0";
    $renrevenue = isset($data[$i]["renrevenue"]) ? $data[$i]["renrevenue"] : "0";

    $totalrevenue = $subrevenue + $renrevenue;

    $varMessage .= '<tr>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $agencyname . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $tothitcount . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $nonmalware . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $malware . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $rtcgcnt . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $ag_success . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $ag_hold . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $renewalcount . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $deactcount . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $subrevenue . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $renrevenue . '</td>';
    $varMessage .= '<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; text-align: center">' . $totalrevenue . '</td>';
    $varMessage .= '</tr>';
}




$varMessage = $varMessage . '</table>';

echo $varMessage;




// $varMessage .= '<tr>
// 		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;" colspan="2"><p align="center"><strong>Total</strong></td>

//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_totalhit) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_uniqhitcount) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_blankhitcount) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_multihitcount) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_dndcnt) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_activecnt) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_rtcgcnt) . '</strong></td>
// 	    <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_uniqrtcgcnt) . '</strong></td>
// 	   	<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_agencysuccesscnt) . '</strong></td>
// 		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_agencyholdcnt) . '</strong></td>
//         <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_subsuccesscnt) . '</strong></td>		
// 		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_subpendingcnt) . '</strong></td>
// 		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_deactcuscnt) . '</strong></td>
// 		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_deactsdcnt) . '</strong></td>



// 	</tr>';



  // $totalhit = isset($data[$i]["totalhitcount"]) ? $data[$i]["totalhitcount"] : "0";
    // $uniqhitcount = isset($data[$i]["uniqhitcount"]) ? $data[$i]["uniqhitcount"] : "0";
    // $blankhitcount = isset($data[$i]["blankhitcount"]) ? $data[$i]["blankhitcount"] : "0";
    // $multihitcount = isset($data[$i]["multihitcount"]) ? $data[$i]["multihitcount"] : "0";
    // $dndcnt = isset($data[$i]["dndcnt"]) ? $data[$i]["dndcnt"] : "0";
    // $activecnt = isset($data[$i]["activecnt"]) ? $data[$i]["activecnt"] : "0";
    // $rtcgcnt = isset($data[$i]["rtcgcnt"]) ? $data[$i]["rtcgcnt"] : "0";
    // $uniqrtcgcnt = isset($data[$i]["uniqrtcgcnt"]) ? $data[$i]["uniqrtcgcnt"] : "0";
    // $agencysuccesscnt = isset($data[$i]["agencysuccesscnt"]) ? $data[$i]["agencysuccesscnt"] : "0";
    // $agencyholdcnt = isset($data[$i]["agencyholdcnt"]) ? $data[$i]["agencyholdcnt"] : "0";
    // $subsuccesscnt = isset($data[$i]["subsuccesscnt"]) ? $data[$i]["subsuccesscnt"] : "0";
    // $subfailedcnt = isset($data[$i]["subfailedcnt"]) ? $data[$i]["subfailedcnt"] : "0";
    // $subpendingcnt = isset($data[$i]["subpendingcnt"]) ? $data[$i]["subpendingcnt"] : "0";
    // $deactcuscnt = isset($data[$i]["deactcuscnt"]) ? $data[$i]["deactcuscnt"] : "0";
    // $deactsdcnt = isset($data[$i]["deactsdcnt"]) ? $data[$i]["deactsdcnt"] : "0";
