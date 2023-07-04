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

<head>
    <style>
        /* Shared properties for th and td */
        th,
        td {
            font: 12px 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            padding: 8px;
            border: 1px solid #000;
            text-align: center;
        }

        /* Specific properties for th */
        th {
            background-color: #f8c471;
            color: #000;
            grid-column: span 4;
            font-weight: 600;
        }

        /* Specific properties for td */
        td {
            background-color: #eef2f3;
        }

        /* Specific properties for summary-row td */
        .summary-row td {
            background-color: #f8c471;
            color: #000;
        }

        /* Specific properties for i */
        i {
            font: 13px 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        /* Specific properties for span */
        span {
            color: red;
        }

        tr td {
            padding: 10px;
        }

        h3 {
            margin-top: 8px;
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
                    $distinctAgentIds = $db['Agency_COMMON']->getResults("SELECT DISTINCT agency_id as agentid, agency_name as agent_name FROM agency_list");

                    // Execute queries and retrieve data for each agent
                    foreach ($distinctAgentIds as $row) {
                        $agentId = $row['agentid'];

                        // Get total hit count for the agent ID
                        $tothitcount = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS totalhitcount FROM hit_analysis WHERE agentid = '" . $agentId . "' AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "'");

                        // Get renewal count for the agent ID
                        $rencount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");

                        // Check if total hit count and renewal count are not zero
                        // if ($tothitcount[0]['totalhitcount'] != 0 && $rencount[0]['renewalcount'] != 0) {
                        // Get total hit count for the agent ID

                        $nonmalware = 0;
                        $malware = 0;

                        // Get RTCG count for the agent ID
                        $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt FROM subscription_detail WHERE agid IN ('" . $agentId . "') AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "' AND transactionid LIKE '%SUB%' AND LENGTH(transactionid) = '21'");
                        $rtcgCount = $rtcgcnt[0]['rtcgcnt'];

                        // Get agency success and hold count
                        $agency_success_hold = $db['TELCO_SUB']->getResults("SELECT SUM(IF(postback_status='success',1,0)) AS ag_success,SUM(IF(postback_status='hold',1,0)) AS ag_hold FROM postback_history WHERE agid IN('" . $agentId . "') AND subscribedon >= '" . $fromdate . "' AND subscribedon <= '" . $todate . "'");




                        // Get subscription count
                        $subcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");



                        // Get renewal count


                        // Get deactivation count
                        $deactcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS deactcount FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Deactivate' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");



                        // Get subscription revenue
                        $subrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrevenue FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");



                        // Get renewal revenue
                        $renrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS renrevenue FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");

                        $totalhitcount = $tothitcount[0]['totalhitcount'] ?? 0;
                        $agencyAS = $agency_success_hold[0]['ag_success'] ?? 0;
                        $agencyAH = $agency_success_hold[0]['ag_hold'] ?? 0;
                        $subscriptioncount = $subcount[0]['renewalcount'] ?? 0;
                        $renewalcount = $rencount[0]['renewalcount'] ?? 0;
                        $deactivationcount = $deactcount[0]['deactcount'] ?? 0;
                        $subscriptionrevenue = $subrevenue[0]['subrevenue'] ?? 0;
                        $renewalrevenue = $renrevenue[0]['renrevenue'] ?? 0;

                        // Get total revenue
                        $totalrevenue = $subscriptionrevenue + $renewalrevenue;

                        // Display agent statistics in HTML table cells
                        echo "<tr>";
                        echo "<td>" . $row['agent_name'] . " --- ID: " . $row['agentid'] . "</td>";
                        echo "<td>" . $totalhitcount . "</td>";
                        echo "<td>" . $nonmalware . "</td>";
                        echo "<td>" . $malware . "</td>";
                        echo "<td>" . $rtcgCount . "</td>";
                        echo "<td>" . $agencyAS . "</td>";
                        echo "<td>" . $agencyAH . "</td>";
                        echo "<td>" . $subscriptioncount . "</td>";
                        echo "<td>" . $renewalcount . "</td>";
                        echo "<td>" . $deactivationcount . "</td>";
                        echo "<td>" . $subscriptionrevenue . "</td>";
                        echo "<td>" . $renewalrevenue . "</td>";
                        echo "<td>" . $totalrevenue . "</td>";
                        echo "</tr>";

                        // Update cumulative totals in the $totals array
                        $totals['Total Hits'] += $totalhitcount;
                        $totals['Non Malware Hits'] += $nonmalware;
                        $totals['Malware Hits'] += $malware;
                        $totals['RTCG'] += $rtcgCount;
                        $totals['AS'] += $agencyAS;
                        $totals['AH'] += $agencyAH;
                        $totals['Sub Count'] += $subscriptioncount;
                        $totals['Ren Count'] += $renewalcount;
                        $totals['Deact Count'] += $deactivationcount;
                        $totals['Sub Revenue'] += $subscriptionrevenue;
                        $totals['Ren Revenue'] += $renewalrevenue;
                        $totals['Total Revenue'] += $totalrevenue;
                    }
                    // }

                    // Print the table for cumulative totals
                    echo "<tr>";
                    echo "<th style='font-weight: bold'>Total</th>";
                    echo "<th>" . $totals['Total Hits'] . "</th>";
                    echo "<th>" . $totals['Non Malware Hits'] . "</th>";
                    echo "<th>" . $totals['Malware Hits'] . "</th>";
                    echo "<th>" . $totals['RTCG'] . "</th>";
                    echo "<th>" . $totals['AS'] . "</th>";
                    echo "<th>" . $totals['AH'] . "</th>";
                    echo "<th>" . $totals['Sub Count'] . "</th>";
                    echo "<th>" . $totals['Ren Count'] . "</th>";
                    echo "<th>" . $totals['Deact Count'] . "</th>";
                    echo "<th>" . $totals['Sub Revenue'] . "</th>";
                    echo "<th>" . $totals['Ren Revenue'] . "</th>";
                    echo "<th>" . $totals['Total Revenue'] . "</th>";
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