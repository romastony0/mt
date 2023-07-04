<style>
    .color-data {

        color: red;
    }
</style>

<?php

// ini_set("display_errors", 1);

define("DB_CONNECTIONS", "TELCO_WAP,TELCO_SUB");
ini_set('display_errors', 0);
include_once("../framework/initialise/framework.init.php");
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



$packageid = 928;
$telcoid = 10;



// TOTAL HIT COUNT

$tothitcount = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(msisdn) AS totalhitcount FROM hit_analysis 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' GROUP BY hour_num");

foreach ($tothitcount as $val) {
    $data[$val['hour_num']]['totalhitcount'] = $val['totalhitcount'];
}
// console(LOG_LEVEL_TRACE,var_export($tothitcount,true));

//UNIQ HIT COUNT

$uniqhitcount = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(DISTINCT(msisdn)) AS uniqhitcount FROM hit_analysis
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' GROUP BY hour_num");

foreach ($uniqhitcount as $val) {
    $data[$val['hour_num']]['uniqhitcount'] = $val['uniqhitcount'];
}

//BLANK HIT COUNT

$blankhitcount = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num,COUNT(DISTINCT msisdn) AS blankhitcount FROM hit_analysis_bulk
WHERE transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' GROUP BY hour_num");

foreach ($blankhitcount as $val) {
    $data[$val['hour_num']]['blankhitcount'] = $val['blankhitcount'];
}

// MULTIPLE HIT BLOCK COUNT

$multihitcount = $db['TELCO_WAP']->getResults("SELECT HOUR(date_val) AS hour_num,COUNT(DISTINCT msisdn) AS multihitcount FROM filtered_numbers
WHERE date_val>='" . $fromdate . "' AND date_val<='" . $todate . "' GROUP BY hour_num");

foreach ($multihitcount as $val) {
    $data[$val['hour_num']]['multihitcount'] = $val['multihitcount'];
}

// Already Active count

$aaresult = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num, COUNT(msisdn) AS activecnt, user_status 
FROM hit_analysis WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND
 transtime<='" . $todate . "'  GROUP BY hour_num,user_status");
foreach ($aaresult as $val) {
    $data[$val['hour_num']]['activecnt'] = $val['activecnt'];
}

// DND USER COUNT

$dndresult = $db['TELCO_WAP']->getResults("SELECT HOUR(added_time) AS hour_num, COUNT(msisdn) AS dndcnt 
FROM dnd_base WHERE telcoid IN (" . $telcoid . ") AND added_time>='" . $fromdate . "' AND
 added_time<='" . $todate . "'  GROUP BY hour_num");
foreach ($dndresult as $val) {
    
    $data[$val['hour_num']]['dndcnt'] = $val['dndcnt'];
}

// console(LOG_LEVEL_TRACE,"HUIUUUUUIUIUIIUIIUIU".var_export($dndresult,TRUE));

// RTCG COUNT

$rtcgcnt = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num, COUNT(*) AS rtcgcnt FROM subscription_detail 
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' AND transactionid LIKE '%SUB%' 
AND LENGTH(transactionid) = '21' GROUP BY hour_num");
foreach ($rtcgcnt as $val) {
    $data[$val['hour_num']]['rtcgcnt'] = $val['rtcgcnt'];
}



//UNIQ RTCG COUNT

$uniqrtcgcnt = $db['TELCO_WAP']->getResults("SELECT HOUR(transtime) AS hour_num, COUNT(DISTINCT msisdn) AS uniqrtcgcnt FROM subscription_detail
WHERE packageid IN (" . $packageid . ") AND transtime>='" . $fromdate . "' AND transtime<='" . $todate . "' AND transactionid LIKE '%SUB%' 
AND LENGTH(transactionid) = '21' GROUP BY hour_num");
foreach ($uniqrtcgcnt as $val) {
    $data[$val['hour_num']]['uniqrtcgcnt'] = $val['uniqrtcgcnt'];
}


//AGENCY SUCCESS

$agencysuccesscnt = $db['TELCO_WAP']->getResults("SELECT HOUR(dateandtime) AS hour_num, COUNT(*) AS agencysuccesscnt FROM agent_hit_analysis
WHERE dateandtime>='" . $fromdate . "' AND dateandtime<='" . $todate . "' AND response = 'success' GROUP BY hour_num");
foreach ($agencysuccesscnt as $val) {
    $data[$val['hour_num']]['agencysuccesscnt'] = $val['agencysuccesscnt'];
}

// console(LOG_LEVEL_TRACE,"HELLOOOOOOOOO".var_export($agencysuccesscnt,TRUE));


// AGENCY HOLD

$agencyholdcnt = $db['TELCO_WAP']->getResults("SELECT HOUR(dateandtime) AS hour_num, COUNT(*) AS agencyholdcnt FROM agent_hit_analysis
WHERE dateandtime>='" . $fromdate . "' AND
 dateandtime<='" . $todate . "' AND response = 'hold' GROUP BY hour_num");
foreach ($agencyholdcnt as $val) {
    $data[$val['hour_num']]['agencyholdcnt'] = $val['agencyholdcnt'];
}


// SUB SUCCESS COUNT 


$subsuccesscnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) AS subsuccesscnt FROM subscription WHERE 
DATE(subscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."' AND status='success' GROUP BY hour_num ;");
foreach($subsuccesscnt as $val){
	$data[$val['hour_num']]['subsuccesscnt'] = $val['subsuccesscnt'];
}


// SUB FAILED COUNT

$subfailedcnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) AS subfailedcnt FROM subscription WHERE
DATE(subscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."' AND status='failed' GROUP BY hour_num ;");
foreach($subfailedcnt as $val){
    $data[$val['hour_num']]['subfailedcnt'] = $val['subfailedcnt'];
}

// SUB PENDING COUNT

$subpendingcnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) AS subpendingcnt FROM subscription WHERE
DATE(subscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."' AND status='pending' GROUP BY hour_num ;");
foreach($subpendingcnt as $val){
    $data[$val['hour_num']]['subpendingcnt'] = $val['subpendingcnt'];
}

// REN COUNT

$rencnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) AS rencnt FROM subscription WHERE
DATE(unsubscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."' GROUP BY hour_num ;");
foreach($rencnt as $val){
    $data[$val['hour_num']]['rencnt'] = $val['rencnt'];
}
console(LOG_LEVEL_TRACE,"HELLOOOOOOOOO".var_export($rencnt,TRUE));

// REN REVENUE

$renrevenue=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) AS renrevenue FROM subscription WHERE
DATE(unsubscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."' GROUP BY hour_num ;");
foreach($renrevenue as $val){
    $data[$val['hour_num']]['renrevenue'] = $val['renrevenue'];
}


// DEACT COUNT - CUS


$deactcuscnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) deactcuscnt FROM subscription WHERE 
DATE(subscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."'  GROUP BY hour_num ;");
foreach($deactcuscnt as $val){
	$data[$val['hour_num']]['deactcuscnt'] = $val['deactcuscnt'];
}

// console(LOG_LEVEL_TRACE,"HAAAAAAAAAAIIIIIIIIIIIII".var_export($deactcuscnt,TRUE));

// DEACT COUNT - SD

$deactsdcnt=$db ['TELCO_SUB']->getResults("SELECT HOUR(unsubscribedon) hour_num,COUNT(*) deactsdcnt FROM subscription WHERE 
DATE(subscribedon) ='".$today."' AND unsubscribedon >= '".$fromdate."' AND unsubscribedon <='".$todate."'  GROUP BY hour_num ;");
foreach($deactsdcnt as $val){
	$data[$val['hour_num']]['deactsdcnt'] = $val['deactsdcnt'];
}

// console(LOG_LEVEL_TRACE,"HAAAAAAAAAAIIIIIIIIIIIII".var_export($deactsdcnt,TRUE));


$year1 = date("Y", strtotime("now"));
$month1 = date("m", strtotime("now"));
$date1 = date("d", strtotime("now"));
$cur_hour1 = date("H", strtotime("now"));


$varMessage = $varMessage . '<h3>VODACOM ZA Hourly Report - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '</h3> 
	<i style="font-family: \'Source Sans Pro\',\'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 13px;"> 
		<span style="color: red;">IST</span> - Indian Standard Time,
		<span style="color: red;">SAST</span> - South Africa Standard Time,
        <span style="color: red;">Sub Count</span> - Subscription Count,
        <span style="color: red;">Ren Count</span> - Renewal Count,
         <span style="color: red;">Sub Revenue</span> - Subscription Revenue,
         <span style="color: red;">Ren Revenue</span> - Renewal Revenue,
         <span style="color: red;">CUS</span> - Customer,
		         <span style="color: red;">SD</span> - Same Day,
                 <span style="color: red;">RTCG</span> - Redirect to CG,
		         <span style="color: red;">CGR</span> - CG Response,
                 <span style="color: red;">TH</span> - Total hits,
                 <span style="color: red;">UH</span> - Unique hits,
                 <span style="color: red;">BH</span> - Blank hits,
                 <span style="color: red;">MHBC</span> - Multiple Hits Block Count,
		         <span style="color: red;">Deact Count</span> - Deactivation Count,
                 <span style="color: red;">DND</span> - DND User,
                 <span style="color: red;">AA</span> -  Already Active,
                 <span style="color: red;">AS</span> - Agency Success,
		         <span style="color: red;">AH</span> - Agency Hold

				  
				 </i><br><br>

	<table style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0" >	  
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" colspan="32">VODACOM ZA HOURLY REPORT - ' . $fromdate . ' To ' . date("Y-m-d H:i:s", strtotime("now")) . '&nbsp&nbsp&nbsp&nbsp&nbsp<b><p class="color-data">AVAILABLE BASE -' . $base_count['base_count'] . '</p></th>
	   </tr>
	   <tr>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">SAST <br>HOUR</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">IST <br>HOUR</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">TH</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">UH</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">BH</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">MHBC</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">DND</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">AA</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">RTCG</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">UNIQ<br>RTCG</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">AS</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">AH</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="3">SUB COUNT</th>
       <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">DEACT COUNT</th>


     
       </tr>
        <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SUCCESS</th>
        <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">FAILED</th>
         <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">PENDING</th>
         <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">CUS</th>
	    <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SD</th>
		<tr>

       </tr>';



$ren_avail = $sub_avail = $total_avail = 0;
for ($i = 0; $i <= $cur_hour; $i++) {

    $totalhit = isset($data[$i]["totalhitcount"]) ? $data[$i]["totalhitcount"] : "0";
    $uniqhitcount = isset($data[$i]["uniqhitcount"]) ? $data[$i]["uniqhitcount"] : "0";
    $blankhitcount = isset($data[$i]["blankhitcount"]) ? $data[$i]["blankhitcount"] : "0";
    $multihitcount = isset($data[$i]["multihitcount"]) ? $data[$i]["multihitcount"] : "0";
    $dndcnt = isset($data[$i]["dndcnt"]) ? $data[$i]["dndcnt"] : "0";
    $activecnt = isset($data[$i]["activecnt"]) ? $data[$i]["activecnt"] : "0";
    $rtcgcnt = isset($data[$i]["rtcgcnt"]) ? $data[$i]["rtcgcnt"] : "0";
    $uniqrtcgcnt = isset($data[$i]["uniqrtcgcnt"]) ? $data[$i]["uniqrtcgcnt"] : "0";
    $agencysuccesscnt = isset($data[$i]["agencysuccesscnt"]) ? $data[$i]["agencysuccesscnt"] : "0";
    $agencyholdcnt = isset($data[$i]["agencyholdcnt"]) ? $data[$i]["agencyholdcnt"] : "0";
    $subsuccesscnt = isset($data[$i]["subsuccesscnt"]) ? $data[$i]["subsuccesscnt"] : "0";
    $subfailedcnt = isset($data[$i]["subfailedcnt"]) ? $data[$i]["subfailedcnt"] : "0";
    $subpendingcnt = isset($data[$i]["subpendingcnt"]) ? $data[$i]["subpendingcnt"] : "0";
    $deactcuscnt = isset($data[$i]["deactcuscnt"]) ? $data[$i]["deactcuscnt"] : "0";
    $deactsdcnt = isset($data[$i]["deactsdcnt"]) ? $data[$i]["deactsdcnt"] : "0";



    $gmttimeval = date("H:i", strtotime($today . " " . $i . ":00:00 +210 minutes"));

// console(LOG_LEVEL_TRACE,"iuuioiuioiuioiuioioiuioioioioio".var_export($dndcnt,true));
   
$varMessage .= '<tr>
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $i . '</p></td>	
				<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $gmttimeval . '</p></td>	
               
		        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_totalhit[] = $totalhit . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_uniqhitcount[] = $uniqhitcount . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_blankhitcount[] = $blankhitcount . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_multihitcount[] = $multihitcount . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_dndcnt[] = $dndcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_activecnt[] = $activecnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_rtcgcnt[] = $rtcgcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_uniqrtcgcnt[] = $uniqrtcgcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_agencysuccesscnt[] = $agencysuccesscnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_agencyholdcnt[] = $agencyholdcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_subsuccesscnt[] = $subsuccesscnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_subfailedcnt[] = $subfailedcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_subpendingcnt[] = $subpendingcnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_deactcuscnt[] = $deactcuscnt . '</p></td>
                <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">' . $tot_deactsdcnt[] = $deactsdcnt . '</p></td>


		</tr>';
}


$varMessage .= '<tr>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;" colspan="2"><p align="center"><strong>Total</strong></td>

        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_totalhit) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_uniqhitcount) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_blankhitcount) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_multihitcount) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_dndcnt) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_activecnt) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_rtcgcnt) . '</strong></td>
	    <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_uniqrtcgcnt) . '</strong></td>
	   	<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_agencysuccesscnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_agencyholdcnt) . '</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_subsuccesscnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_subfailedcnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_subpendingcnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_deactcuscnt) . '</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>' . array_sum($tot_deactsdcnt) . '</strong></td>

		
	
	</tr>';


$varMessage = $varMessage . '</table>';






// LAST 10 DAYS


$ten_days=   $db['TELCO_WAP']-> getResults("SELECT `date`,SUM(total_hit) AS `total_hit`,SUM(uniq_hit) AS `uniq_hit`,SUM(blank_hit) AS `blank_hit`,
SUM(mhbc_cnt) AS `mhbc_cnt`,SUM(dnd_cnt) AS `dnd_cnt`,SUM(already_active) AS `already_active`,SUM(rtcg_cnt) AS `rtcg_cnt`,
SUM(uniq_rtcg) AS `uniq_rtcg_cnt`,SUM(ag_succ) AS `ag_succ`,SUM(ag_hold) AS `ag_hold`,SUM(sub_success) AS `sub_success`,SUM(sub_failed) AS `sub_failed`,
SUM(ren_count) AS `ren_count`,SUM(deact_cus) AS `deact_cus`,SUM(deact_sd) AS `deact_sd`,SUM(ren_revenue) AS `ren_revenue`
FROM hourly_flat WHERE `date`>=(CURDATE() - INTERVAL 10 DAY)  GROUP BY `date`;") ;






$varMessage = $varMessage.'<h3> VODACOM ZA HOURLY REPORT - Last 10 Days </h3>
<i style="font-family: \'Source Sans Pro\',\'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 13px;">
<span style="color: red;">Sub Count</span> - Subscription Count,
<span style="color: red;">Ren Count</span> - Renewal Count,
<span style="color: red;">Sub Revenue</span> - Subscription Revenue,
<span style="color: red;">Ren Revenue</span> - Renewal Revenue,
<span style="color: red;">CUS</span> - Customer,
<span style="color: red;">SD</span> - Same Day,
<span style="color: red;">RTCG</span> - Redirect to CG,
<span style="color: red;">CGR</span> - CG Response,
<span style="color: red;">TH</span> - Total Hits,
<span style="color: red;">UH</span> - Unique Hits,
<span style="color: red;">BH</span> - Blank Hits,
<span style="color: red;">MHBC</span> - Multiple Hits Block Count,
<span style="color: red;">Deact Count</span> - Deactivation Count,
<span style="color: red;">AS</span> - Agency Success,
<span style="color: red;">AH</span> - Agency Hold,
<span style="color: red;">AA</span> - Already Active


</i> <br><br>


<table style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0" >
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" colspan="29" ;>VODACOM ZA HOURLY REPORT - Last 10 Days</th>
	   </tr>	
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2";>DATE</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>TH</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>UH</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>BH</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>MHBC</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>DND</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>AA</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>RTCG</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>UNIQ RTCG</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>AS</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" rowspan="2";>AH</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">SUB COUNT</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">REN <br> COUNT</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">DEACT COUNT</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">REN <br> REVENUE</th>

     
     
		</tr>
		<tr>
        <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SUCCESS</th>
        <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">FAILED</th>
         <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">CUS</th>
	    <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SD</th>
		
		</tr>';

        foreach($ten_days as $val){
            $data2[$val['date']]['total_hit']=$val['total_hit'];
            $data2[$val['date']]['uniq_hit']=$val['uniq_hit'];
            $data2[$val['date']]['blank_hit']=$val['blank_hit'];
            $data2[$val['date']]['mhbc_cnt']=$val['mhbc_cnt'];
            $data2[$val['date']]['dnd_cnt']=$val['dnd_cnt'];
            $data2[$val['date']]['already_active']=$val['already_active'];
            $data2[$val['date']]['rtcg_cnt']=$val['rtcg_cnt'];
            $data2[$val['date']]['uniq_rtcg_cnt']=$val['uniq_rtcg_cnt'];
            $data2[$val['date']]['ag_succ']=$val['ag_succ'];
            $data2[$val['date']]['ag_hold']=$val['ag_hold'];
            $data2[$val['date']]['sub_success']=$val['sub_success'];
            $data2[$val['date']]['sub_failed']=$val['sub_failed'];
            $data2[$val['date']]['ren_count']=$val['ren_count'];
            $data2[$val['date']]['deact_cus']=$val['deact_cus'];
            $data2[$val['date']]['deact_sd']=$val['deact_sd'];
            $data2[$val['date']]['ren_revenue']=$val['ren_revenue'];

        }

        $date_range=array();
array_push($date_range,$to);
	for($j=1;$j<=8;$j++){
		array_push($date_range,date('Y-m-d', strtotime("-".$j." day",strtotime ($to))));
}
array_push($date_range,$from);

foreach($date_range as $val){
    $ten_total_hit=isset($data2[$val]["total_hit"])?$data2[$val]["total_hit"]:"0";
    $ten_uniq_hit=isset($data2[$val]["uniq_hit"])?$data2[$val]["uniq_hit"]:"0";
    $ten_blank_hit=isset($data2[$val]["blank_hit"])?$data2[$val]["blank_hit"]:"0";
    $ten_mhbc_cnt=isset($data2[$val]["mhbc_cnt"])?$data2[$val]["mhbc_cnt"]:"0";
    $ten_dnd_cnt=isset($data2[$val]["dnd_cnt"])?$data2[$val]["dnd_cnt"]:"0";
    $ten_already_active=isset($data2[$val]["already_active"])?$data2[$val]["already_active"]:"0";
    $ten_rtcg_cnt=isset($data2[$val]["rtcg_cnt"])?$data2[$val]["rtcg_cnt"]:"0";
    $ten_uniq_rtcg_cnt=isset($data2[$val]["uniq_rtcg_cnt"])?$data2[$val]["uniq_rtcg_cnt"]:"0";
    $ten_ag_succ=isset($data2[$val]["ag_succ"])?$data2[$val]["ag_succ"]:"0";
    $ten_ag_hold=isset($data2[$val]["ag_hold"])?$data2[$val]["ag_hold"]:"0";
    $ten_sub_success=isset($data2[$val]["sub_success"])?$data2[$val]["sub_success"]:"0";
    $ten_sub_failed=isset($data2[$val]["sub_failed"])?$data2[$val]["sub_failed"]:"0";
    $ten_ren_count=isset($data2[$val]["ren_count"])?$data2[$val]["ren_count"]:"0";
    $ten_deact_cus=isset($data2[$val]["deact_cus"])?$data2[$val]["deact_cus"]:"0";
    $ten_deact_sd=isset($data2[$val]["deact_sd"])?$data2[$val]["deact_sd"]:"0";
    $ten_ren_revenue=isset($data2[$val]["ren_revenue"])?$data2[$val]["ren_revenue"]:"0";
    

		
$varMessage.='<tr>
		<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.date('d-m-Y',strtotime($val)).'</td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_total_hit[]=$ten_total_hit.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_uniq_hit[]=$ten_uniq_hit.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_blank_hit[]=$ten_blank_hit.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_mhbc_cnt[]=$ten_mhbc_cnt.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_dnd_cnt[]=$ten_dnd_cnt.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_already_active[]=$ten_already_active.'</td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_rtcg_cnt[]=$ten_rtcg_cnt.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_uniq_rtcg_cnt[]=$ten_uniq_rtcg_cnt.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_ag_succ[]=$ten_ag_succ.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_ag_hold[]=$ten_ag_hold.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_sub_success[]=$ten_sub_success.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_sub_failed[]=$ten_sub_failed.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_ren_count[]=$ten_ren_count.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_deact_cus[]=$ten_deact_cus.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_deact_sd[]=$ten_deact_sd.'</td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_ten_ren_revenue[]=$ten_ren_revenue.'</td>

		</tr>';
	} 

    $varMessage.='<tr>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>Total</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_total_hit).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_uniq_hit).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_blank_hit).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_mhbc_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_dnd_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_already_active).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_rtcg_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_uniq_rtcg_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_ag_succ).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_ag_hold).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_sub_success).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_sub_failed).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_ren_count).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_deact_cus).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_deact_sd).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_ten_ren_revenue).'</strong></td>


        </tr>'; 

        $varMessage = $varMessage . '</table>';




    //   LAST 3 MONTHS


    $three_mons=$db['TELCO_WAP']->getResults("SELECT DATE_FORMAT(`date`,'%Y-%m') AS mon_val,SUM(total_hit) AS `total_hit`,SUM(uniq_hit) AS `uniq_hit`,
    SUM(blank_hit) AS `blank_hit`,SUM(mhbc_cnt) AS `mhbc_cnt`,SUM(dnd_cnt) AS `dnd_cnt`,SUM(already_active) AS `already_active`,
    SUM(rtcg_cnt) AS `rtcg_cnt`,SUM(uniq_rtcg) AS `uniq_rtcg_cnt`,SUM(ag_succ) AS `ag_succ`,SUM(ag_hold) AS `ag_hold`,
    SUM(sub_success) AS `sub_success`,SUM(sub_failed) AS `sub_failed`,SUM(ren_count) AS `ren_count`,
    SUM(deact_cus) AS `deact_cus`,SUM(deact_sd) AS `deact_sd`,SUM(ren_revenue) AS `ren_revenue`
    FROM hourly_flat WHERE `date`>=(CURDATE() - INTERVAL 3 MONTH)  GROUP BY MONTH(`date`) ;");

        
    $three_month_base_1=$db['TELCO_WAP']->getResults("SELECT DATE_FORMAT(`date`,'%Y-%m')as mon_val,avail_base as tot_base from hourly_flat where date='".$yesterday."'");
    $three_month_base_2=$db['TELCO_WAP']->getResults("SELECT DATE_FORMAT(`date`,'%Y-%m') as mon_val,avail_base as  tot_base from hourly_flat where date='".$lastmonth."'");
    $three_month_base_3=$db['TELCO_WAP']->getResults("SELECT DATE_FORMAT(`date`,'%Y-%m') as mon_val,avail_base as tot_base from hourly_flat where date='".$lastmonth_two."'");
    
    
        foreach($three_month_base_1 as  $val){
            $data3[$val['mon_val']]['tot_base']=$val['tot_base'];
        }foreach($three_month_base_2 as $val){
            $data3[$val['mon_val']]['tot_base']=$val['tot_base'];
        }foreach($three_month_base_3 as $val){
            $data3[$val['mon_val']]['tot_base']=$val['tot_base'];
        }

    $varMessage = $varMessage.'<h3>VODACOM ZA HOURLY REPORT - Last 3 Months</h3>
                        <i style="font-family: \'Source Sans Pro\',\'Helvetica Neue\',Helvetica,Arial,sans-serif; font-size: 13px;">
                            <span style="color: red;">Sub Count</span> - Subscription Count,
                            <span style="color: red;">Ren Count</span> - Renewal Count,
                            <span style="color: red;">Sub Revenue</span> - Subscription Revenue,
                            <span style="color: red;">Ren Revenue</span> - Renewal Revenue, 
                            <span style="color: red;">CUS</span> - Customer,  
                            <span style="color: red;">SD</span> - Same Day,
                            <span style="color: red;">RTCG</span> - Redirect to CG, 
                            <span style="color: red;">CGR</span> - CG Response,
                            <span style="color: red;">TH</span> - Total Hits,                   
                            <span style="color: red;">UH</span> - Unique Hits,
                            <span style="color: red;">BH</span> - Blank Hits,
                            <span style="color: red;">MHBC</span> - Multiple Hits Block Count,
                            <span style="color: red;">Deact Count</span> - Deactivation Count,
                            <span style="color: red;">AS</span> - Agency Success,
                            <span style="color: red;">AH</span> - Agency Hold,
                            <span style="color: red;">AA</span> - Already Active
                        
                        </i> <br><br>


                        <table style="border-collapse: collapse;" border="1" cellspacing="0" cellpadding="0" >
		<tr>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center" colspan="30 ";>VODACOM ZA HOURLY REPORT - Last 3 Months</th>
	   </tr>	
		<tr>
		<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2";>Month</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>TH</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>UH</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>BH</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>MHBC</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>DND</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>AA</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>RTCG</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>UNIQ <br> RTCG</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>AS</th>
			<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center"  rowspan="2";>AH</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">SUB COUNT</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">REN <br> COUNT</th>

            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" colspan="2">DEACT COUNT</th>
            <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color:#000; text-align: center" rowspan="2">REN <br> REVENUE</th>

     
            </tr>
		<tr>
		<th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SUCCESS</th>
        <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">FAILED</th>
         <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">CUS</th>
	    <th style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center">SD</th>
			
		</tr>';

        foreach($three_mons as $val){
            $data3[$val['mon_val']]['total_hit']=$val['total_hit'];
            $data3[$val['mon_val']]['uniq_hit']=$val['uniq_hit'];
            $data3[$val['mon_val']]['blank_hit']=$val['blank_hit'];
            $data3[$val['mon_val']]['mhbc_cnt']=$val['mhbc_cnt'];
            $data3[$val['mon_val']]['dnd_cnt']=$val['dnd_cnt'];
            $data3[$val['mon_val']]['already_active']=$val['already_active'];
            $data3[$val['mon_val']]['rtcg_cnt']=$val['rtcg_cnt'];
            $data3[$val['mon_val']]['uniq_rtcg_cnt']=$val['uniq_rtcg_cnt'];
            $data3[$val['mon_val']]['ag_succ']=$val['ag_succ'];
            $data3[$val['mon_val']]['ag_hold']=$val['ag_hold'];
            $data3[$val['mon_val']]['sub_success']=$val['sub_success'];
            $data3[$val['mon_val']]['sub_failed']=$val['sub_failed'];
            $data3[$val['mon_val']]['ren_count']=$val['ren_count'];
            $data3[$val['mon_val']]['deact_cus']=$val['deact_cus'];
            $data3[$val['mon_val']]['deact_sd']=$val['deact_sd'];
            $data3[$val['mon_val']]['ren_revenue']=$val['ren_revenue'];
            
            
        }

        $mon_range=array();
	array_push($mon_range,$curr_mon,$prev_mon,$two_month);

foreach($mon_range as $vals){
		$month_val = date("m", strtotime($vals));
		$three_total_hit=isset($data3[$vals]["total_hit"])?$data3[$vals]["total_hit"]:"0";
	    $three_uniq_hit=isset($data3[$vals]["uniq_hit"])?$data3[$vals]["uniq_hit"]:"0";
		$three_blank_hit=isset($data3[$vals]["blank_hit"])?$data3[$vals]["blank_hit"]:"0";
        $three_mhbc_cnt=isset($data3[$vals]["mhbc_cnt"])?$data3[$vals]["mhbc_cnt"]:"0";
        $three_dnd_cnt=isset($data3[$vals]["dnd_cnt"])?$data3[$vals]["dnd_cnt"]:"0";
        $three_already_active=isset($data3[$vals]["already_active"])?$data3[$vals]["already_active"]:"0";
		$three_rtcg_cnt=isset($data3[$vals]["rtcg_cnt"])?$data3[$vals]["rtcg_cnt"]:"0";
        $three_uniq_rtcg_cnt=isset($data3[$vals]["uniq_rtcg_cnt"])?$data3[$vals]["uniq_rtcg_cnt"]:"0";
        $three_ag_succ=isset($data3[$vals]["ag_succ"])?$data3[$vals]["ag_succ"]:"0";
        $three_ag_hold=isset($data3[$vals]["ag_hold"])?$data3[$vals]["ag_hold"]:"0";
        $three_sub_success=isset($data3[$vals]["sub_success"])?$data3[$vals]["sub_success"]:"0";
        $three_sub_failed=isset($data3[$vals]["sub_failed"])?$data3[$vals]["sub_failed"]:"0";
        $three_ren_count=isset($data3[$vals]["ren_count"])?$data3[$vals]["ren_count"]:"0";
        $three_deact_cus=isset($data3[$vals]["deact_cus"])?$data3[$vals]["deact_cus"]:"0";
        $three_deact_sd=isset($data3[$vals]["deact_sd"])?$data3[$vals]["deact_sd"]:"0";
        $three_ren_revenue=isset($data3[$vals]["ren_revenue"])?$data3[$vals]["ren_revenue"]:"0";



        $name_of_month=date("F", strtotime($vals));	
	$varMessage.='<tr>
			<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.date("F", strtotime($vals)).'</td>
			<td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_total_hit[]=$three_total_hit.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_uniq_hit[]=$three_uniq_hit.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_blank_hit[]=$three_blank_hit.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_mhbc_cnt[]=$three_mhbc_cnt.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_dnd_cnt[]=$three_dnd_cnt.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_already_active[]=$three_already_active.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_rtcg_cnt[]=$three_rtcg_cnt.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_uniq_rtcg_cnt[]=$three_uniq_rtcg_cnt.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_ag_succ[]=$three_ag_succ.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_ag_hold[]=$three_ag_hold.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_sub_success[]=$three_sub_success.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_sub_failed[]=$three_sub_failed.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_ren_count[]=$three_ren_count.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_deact_cus[]=$three_deact_cus.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_deact_sd[]=$three_deact_sd.'</td>
            <td style="font-family:sans-serif;font-size: 12px;padding: 3px;"><p align="center">'.$tot_three_ren_revenue[]=$three_ren_revenue.'</td>

        </tr>';
    }


    $varMessage.='<tr>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>Total</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_total_hit).'</strong></td>       
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_uniq_hit).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_blank_hit).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_mhbc_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_dnd_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_already_active).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_rtcg_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_uniq_rtcg_cnt).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_ag_succ).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_ag_hold).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_sub_success).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_sub_failed).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_ren_count).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_deact_cus).'</strong></td>
		<td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_deact_sd).'</strong></td>
        <td style="font-family:sans-serif;font-size: 12px;padding: 5px;border: 1px solid #000; background-color: #f8c471; color: #000; text-align: center;"><p align="center"><strong>'.array_sum($tot_three_ren_revenue).'</strong></td>


        </tr>';

        $varMessage = $varMessage.'</table>';	

echo $varMessage;

?>