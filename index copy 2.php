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


?>

<!-- 
<!DOCTYPE html>
<html> -->

<head>
    <!-- <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Reports Dashboard</title>
    <link rel="icon" href="slogo.png">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content=""> -->
    <!-- Stylesheet -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css"> -->
    <!-- <link rel="stylesheet" type="text/css" href="https://reports.symbioticinfo.com/resources/css/style.css?v=3.1"> -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
    <!-- Scripts -->
    <!-- <script type="text/javascript" src="https://reports.symbioticinfo.com/resources/js/bootstrap-table.js" charset="utf-8"></script> -->
    <style>
        /* Shared properties for th and td */
        th,
        td {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            padding: 5px;
            border: 1px solid #000;
            text-align: center;
        }

        /* Specific properties for th */
        th {
            background-color: #f8c471;
            color: #000;
            grid-column: span 4;
        }

        /* Specific properties for td */
        td {
            background-color: #EEF2F3;
            /* or whatever default color you want */
        }

        /* Specific properties for summary-row td */
        .summary-row td {
            background-color: #f8c471;
            color: #000;
        }

        /* Specific properties for i */
        i {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-family: sans-serif;
            font-size: 13px;
        }

        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        /* Specific properties for span */
        span {
            color: red;
        }

        tr td {
            text-align: center;
            padding: 10px;
        }

        h3 {
            margin-top: 30px;
        }
    </style>
</head>

<body style="background-color:#eef2f3;">
    <!-- Ajax Loader -->
    <div id="site" class="clearfix">
        <main id="main-content" class="clearfix" role="main">
            <div class="mt-container">
                <div class="overlay-timeout"></div>
                <br>
                <h3 class="fw-bold">TELKOM-ZA Agency Hourly Report - <?php echo $fromdate . " To " . date("Y-m-d H:i:s", strtotime("now")) ?></h3>
                <i>
                    <span>RTCG</span> - Redirect to CG,
                    <span>AS</span> - Agency Success,
                    <span>AH</span> - Agency Hold,
                    <span>Sub Count</span> - Subscription Count,
                    <span>Ren Count</span> - Renewal Count,
                    <span>Deact Count</span> - Deactivation Count,
                    <span>Sub Revenue</span> - Subscription Revenue,
                    <span>Ren Revenue</span> - Renewal Revenue
                </i>
                <table style="border-collapse: collapse;" border="1" cellspacing="" cellpadding="0">
                    <!-- Table Head -->
                    <tr>
                        <th colspan="13 ">TELKOM-ZA Agency Hourly Report - <?php echo $fromdate . " To " . date("Y-m-d H:i:s", strtotime("now")) ?></th>
                    </tr>
                    <!-- Table Content Heading -->
                    <tr>
                        <th>Agency</th>
                        <th>Total<br> Hits</th>
                        <th>Non Malware<br> Hits</th>
                        <th>Malware<br>Hits</th>
                        <th>RTCG</th>
                        <th>AS</th>
                        <th>AH</th>
                        <th>Sub<br>Count</th>
                        <th>Ren<br>Count</th>
                        <th>Deact<br>Count</th>
                        <th>Sub<br>Revenue</th>
                        <th>Ren<br>Revenue</th>
                        <th>Total<br>Revenue</th>
                    </tr>
                    <?php
                    // Get distinct agent IDs
                    // $distinctAgentIds = $db['Agency_COMMON']->getResults("SELECT DISTINCT agency_id as agentid,agency_name as agent_name
                    // FROM agency_list ");

                    $distinctAgentIds = $db['TELCO_WAP']->getResults("SELECT DISTINCT agentid
                    FROM hit_analysis
                    WHERE (transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "')
                       OR agentid IS NULL
                       OR agentid = '';");

                    // Variable to store the totals
                    $totals = array(
                        'Total Hits' => 0,
                        'Non Malware Hits' => 0,
                        'Malware Hits' => 0,
                        'RTCG' => 0,
                        'AS' => 0,
                        'AH' => 0,
                        'Sub Count' => 0,
                        'Ren Count' => 0,
                        'Deact Count' => 0,
                        'Sub Revenue' => 0,
                        'Ren Revenue' => 0,
                        'Total Revenue' => 0
                    );

                    // Iterate over each agent ID
                    foreach ($distinctAgentIds as $row) {
                        $agentId = $row['agentid'];




                        // Get agency name for the agent ID
                        $agencyNameResult = $db['Agency_COMMON']->getResults("SELECT agency_name FROM agency_list WHERE agency_id =  '" . $agentId . "'");
                        $agencyName = (!empty($agencyNameResult)) ? $agencyNameResult[0]['agency_name'] : $agentId;
                        echo "<tr><td>" . $agencyName . "</td>";


                        //Get total hit count for the agent ID
                        $tothitcount = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS totalhitcount FROM hit_analysis WHERE agentid = '" . $agentId . "' AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "'");
                        echo "<td>" . $tothitcount[0]['totalhitcount'] . "</td>";
                        $totals['Total Hits'] += $tothitcount[0]['totalhitcount'];
                        // $tothitcount = $db['TELCO_WAP']->getResults("select count(*) AS totalhitcount,agentid from hit_analysis where date(transtime) = date(NOW())  group by agentid");
                        // //$totalHits = '0';
                        // foreach ($tothitcount as $row1) {
                        //     $agencynametest = '';
                        //     // echo $row['agentid'];
                        //     if ($agentId == $row1['agentid']) {
                        //         $totalHits = $row1['totalhitcount'];

                        //         // echo $totalHits;
                        //         $agencynametest =  $row['agency_name'];
                        //     }
                        // }
                        // echo $totalHits;
                        // // $totalHits = $tothitcount[0]['totalhitcount'];

                        // if ($agencynametest != '') {
                        //     echo "<td>" . $agencynametest . "</td>";
                        // }

                        // echo "<td>" . $totalHits . "</td>";
                        // $totals['Total Hits'] += $totalhit;

                        $nonmalware = 0;
                        echo "<td>" . $nonmalware . "</td>";
                        $totals['Non Malware Hits'] += $nonmalware;

                        $malware = 0;
                        echo "<td>" . $malware . "</td>";
                        $totals['Malware Hits'] += $malware;

                        // Get RTCG count for the agent ID
                        // $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt
                        // FROM subscription_detail sd
                        // JOIN hit_analysis ha ON sd.hit_id = ha.id
                        // WHERE ha.agentid = '" . $agentId . "'
                        // AND sd.transtime >= '" . $fromdate . "'
                        // AND sd.transtime <= '" . $todate . "'
                        // AND sd.transactionid LIKE '%SUB%'
                        // AND LENGTH(sd.transactionid) = '21'");
                        $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt FROM subscription_detail WHERE agid IN ('" . $agentId . "') AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "' AND transactionid LIKE '%SUB%' AND LENGTH(transactionid) = '21'");

                        $rtcgCount = $rtcgcnt[0]['rtcgcnt'];
                        echo "<td>" . $rtcgCount . "</td>";
                        $totals['RTCG'] += $rtcgCount;

                        // Get agency success and hold count
                        $agency_success_hold = $db['TELCO_SUB']->getResults("SELECT SUM(IF(postback_status='success',1,0)) AS ag_success,SUM(IF(postback_status='hold',1,0)) AS ag_hold FROM postback_history WHERE agid IN('" . $agentId . "') AND subscribedon >= '" . $fromdate . "' AND subscribedon <= '" . $todate . "'  ");

                        $agencyAS = $agency_success_hold[0]['ag_success'] ?? 0;
                        echo "<td>" . $agencyAS . "</td>";
                        $totals['AS'] += $agencyAS;

                        $agencyAH = $agency_success_hold[0]['ag_hold'] ?? 0;
                        echo "<td>" . $agencyAH . "</td>";
                        $totals['AH'] += $agencyAH;



                        //Get subscription count
                        $subcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
                      
                           AND ct.requestedon <= '" . $todate . "'
                           AND s.agent_id = '" . $agentId . "'  
                           AND ct.type = 'Subscription'
                           AND ct.`status` = 'success'
                           AND ct.`status` != 'pending'
                           AND s.op_subscription_id = ct.subscriptionid");

                        $subscriptioncount = $subcount[0]['renewalcount'] ?? 0;
                        echo "<td>" . $subscriptioncount . "</td>";
                        $totals['Sub Count'] += $subscriptioncount;





                        //Get renewal count
                        $rencount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
                      
                          AND ct.requestedon <= '" . $todate . "'
                          AND s.agent_id = '" . $agentId . "'
                          AND ct.type = 'Renewal'
                          AND ct.`status` = 'success'
                          AND s.op_subscription_id = ct.subscriptionid");
                        $renewalcount = $rencount[0]['renewalcount'] ?? 0;
                        echo "<td>" . $renewalcount . "</td>";
                        $totals['Ren Count'] += $renewalcount;
                        // echo "<td>" . $rencount[0]['renewalcount'] . "</td>";
                        // $totals['Ren Count'] += $rencount[0]['renewalcount'];


                        //Get deactivation count
                        $deactcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*)modecount FROM subscription WHERE  agent_id='" . $agentId . "' AND unsubscribedon >= '" . $fromdate . "'  AND unsubscribedon <= '" . $todate . "'");
                        $deactcount = $deactcount[0]['modecount'] ?? 0;
                        echo "<td>" . $deactcount . "</td>";
                        $totals['Deact Count'] += $deactcount;

                        // echo "<td>" . $deactcount[0]['modecount'] . "</td>";
                        // $totals['Deact Count'] += $deactcount[0]['modecount'];


                        //Sub Revenue
                        $subrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrev  FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
                      
                        AND ct.requestedon <= '" . $todate . "'
                        AND s.agent_id = '" . $agentId . "'
                        AND ct.type = 'Subscription'
                        AND ct.`status` = 'success'
                        AND s.op_subscription_id = ct.subscriptionid");
                        $subrev = $subrevenue[0]['subrev'] ?? 0;
                        echo "<td>" . $subrev . "</td>";
                        $totals['Sub Revenue'] += $subrev;

                        //Ren Revenue
                        $renrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrev  FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
                      
                        AND ct.requestedon <= '" . $todate . "'
                        AND s.agent_id = '" . $agentId . "'
                        AND ct.type = 'renewal'
                        AND ct.`status` = 'success'
                        AND s.op_subscription_id = ct.subscriptionid");
                        $renrev = $renrevenue[0]['subrev'] ?? 0;
                        echo "<td>" . $renrev . "</td>";
                        $totals['Ren Revenue'] += $renrev;
                        $totalRevenue = $subrev + $renrev;
                        echo "<td>" . $totalRevenue . "</td>";
                        $totals['Total Revenue'] += $totalRevenue;

                        echo "</tr>";
                    }

                    // Display the summary row
                    echo "<tr class='summary-row'>";
                    echo "<td><strong>TOTAL</strong></td>";
                    foreach ($totals as $total) {
                        echo "<td><strong>" . $total . "</strong></td>";
                    }
                    echo "</tr>";
                    ?>
                </table>


            </div>
        </main>
        <!-- Content End -->
        <!-- Footer Start -->
        <br>
        <footer id="site-footer" role="contentinfo"></footer>
    </div>
</body>
<!-- 
</html> -->




<?php
// Get distinct agent IDs
$distinctAgentIds = $db['Agency_COMMON']->getResults("SELECT DISTINCT agency_id as agentid,agency_name as agent_name FROM agency_list ");

// Variable to store the totals
$totals = array(
    'Total Hits' => 0,
    'Non Malware Hits' => 0,
    'Malware Hits' => 0,
    'RTCG' => 0,
    'AS' => 0,
    'AH' => 0,
    'Sub Count' => 0,
    'Ren Count' => 0,
    'Deact Count' => 0,
    'Sub Revenue' => 0,
    'Ren Revenue' => 0,
    'Total Revenue' => 0
);

// Iterate over each agent ID
foreach ($distinctAgentIds as $row) {
    $agentId = $row['agentid'];

    // Get total hit count for the agent ID
    $tothitcount = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS totalhitcount FROM hit_analysis WHERE agentid = '" . $agentId . "' AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "'");

    // Get renewal count for the agent ID
    $rencount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");

    // Check if total hit count and renewal count are not zero
    if ($tothitcount[0]['totalhitcount'] != 0 && $rencount[0]['renewalcount'] != 0) {

        // Get total hit count for the agent ID
        $totalhitcount = $tothitcount[0]['totalhitcount'] ?? 0;
        echo "<td>" . $totalhitcount . "</td>";

        $nonmalware = 0;
        echo "<td>" . $nonmalware . "</td>";
        $totals['Non Malware Hits'] += $nonmalware;

        $malware = 0;
        echo "<td>" . $malware . "</td>";
        $totals['Malware Hits'] += $malware;

        // Get RTCG count for the agent ID
        // $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt
        // FROM subscription_detail sd
        // JOIN hit_analysis ha ON sd.hit_id = ha.id
        // WHERE ha.agentid = '" . $agentId . "'
        // AND sd.transtime >= '" . $fromdate . "'
        // AND sd.transtime <= '" . $todate . "'
        // AND sd.transactionid LIKE '%SUB%'
        // AND LENGTH(sd.transactionid) = '21'");
        $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt FROM subscription_detail WHERE agid IN ('" . $agentId . "') AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "' AND transactionid LIKE '%SUB%' AND LENGTH(transactionid) = '21'");

        $rtcgCount = $rtcgcnt[0]['rtcgcnt'];
        echo "<td>" . $rtcgCount . "</td>";
        $totals['RTCG'] += $rtcgCount;

        // Get agency success and hold count
        $agency_success_hold = $db['TELCO_SUB']->getResults("SELECT SUM(IF(postback_status='success',1,0)) AS ag_success,SUM(IF(postback_status='hold',1,0)) AS ag_hold FROM postback_history WHERE agid IN('" . $agentId . "') AND subscribedon >= '" . $fromdate . "' AND subscribedon <= '" . $todate . "'  ");

        $agencyAS = $agency_success_hold[0]['ag_success'] ?? 0;
        echo "<td>" . $agencyAS . "</td>";
        $totals['AS'] += $agencyAS;

        $agencyAH = $agency_success_hold[0]['ag_hold'] ?? 0;
        echo "<td>" . $agencyAH . "</td>";
        $totals['AH'] += $agencyAH;



        //Get subscription count
        $subcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
          
               AND ct.requestedon <= '" . $todate . "'
               AND s.agent_id = '" . $agentId . "'  
               AND ct.type = 'Subscription'
               AND ct.`status` = 'success'
               AND ct.`status` != 'pending'
               AND s.op_subscription_id = ct.subscriptionid");

        $subscriptioncount = $subcount[0]['renewalcount'] ?? 0;
        echo "<td>" . $subscriptioncount . "</td>";
        $totals['Sub Count'] += $subscriptioncount;





        // //Get renewal count
        // $rencount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
          
        //       AND ct.requestedon <= '" . $todate . "'
        //       AND s.agent_id = '" . $agentId . "'
        //       AND ct.type = 'Renewal'
        //       AND ct.`status` = 'success'
        //       AND s.op_subscription_id = ct.subscriptionid");
        $renewalcount = $rencount[0]['renewalcount'] ?? 0;
        echo "<td>" . $renewalcount . "</td>";
        $totals['Ren Count'] += $renewalcount;
        // echo "<td>" . $rencount[0]['renewalcount'] . "</td>";
        // $totals['Ren Count'] += $rencount[0]['renewalcount'];


        //Get deactivation count
        $deactcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*)modecount FROM subscription WHERE  agent_id='" . $agentId . "' AND unsubscribedon >= '" . $fromdate . "'  AND unsubscribedon <= '" . $todate . "'");
        $deactcount = $deactcount[0]['modecount'] ?? 0;
        echo "<td>" . $deactcount . "</td>";
        $totals['Deact Count'] += $deactcount;

        // echo "<td>" . $deactcount[0]['modecount'] . "</td>";
        // $totals['Deact Count'] += $deactcount[0]['modecount'];


        //Sub Revenue
        $subrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrev  FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
          
            AND ct.requestedon <= '" . $todate . "'
            AND s.agent_id = '" . $agentId . "'
            AND ct.type = 'Subscription'
            AND ct.`status` = 'success'
            AND s.op_subscription_id = ct.subscriptionid");
        $subrev = $subrevenue[0]['subrev'] ?? 0;
        echo "<td>" . $subrev . "</td>";
        $totals['Sub Revenue'] += $subrev;

        //Ren Revenue
        $renrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrev  FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "'
          
            AND ct.requestedon <= '" . $todate . "'
            AND s.agent_id = '" . $agentId . "'
            AND ct.type = 'renewal'
            AND ct.`status` = 'success'
            AND s.op_subscription_id = ct.subscriptionid");
        $renrev = $renrevenue[0]['subrev'] ?? 0;
        echo "<td>" . $renrev . "</td>";
        $totals['Ren Revenue'] += $renrev;
        $totalRevenue = $subrev + $renrev;
        echo "<td>" . $totalRevenue . "</td>";
        $totals['Total Revenue'] += $totalRevenue;

        echo "</tr>";
    }
}
?>