<?php
ini_set("display_errors", 1);
define("DB_CONNECTIONS", "TELCO_WAP,TELCO_SUB,Agency_COMMON");
ini_set('display_errors', 0);
include_once("../framework/initialise/framework.init.php");
date_default_timezone_set("Africa/Johannesburg");
global $library, $request, $db, $curl, $libxml, $log, $viewclass, $mail;
// $fromdate = "2023-06-30 00:00:00";
// $todate = "2023-06-30 23:59:59";
$today = date('Y-m-d');
$fromdate = date('Y-m-d 00:00:00');
$todate = date('Y-m-d 23:59:59', strtotime('now'));
?>

<head>
    <title>TELKOM-ZA Cumulative Agency Report</title>
    <style>
        th,
        td {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            padding: 5px;
            border: 1px solid #000;
            text-align: center;
        }

        th {
            background-color: #f8c471;
            color: #000;
            grid-column: span 4;
            /* padding: 6px; */
            padding: 6px 10px 6px 10px;
        }

        td {
            background-color: #eef2f3;
        }

        .summary-row td {
            background-color: #f8c471;
            color: #000;
        }

        i {
            font-size: 13px;
        }

        body {
            font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

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
    <div>
        <main>
            <div>
                <div></div>
                <br>
                <h3>TELKOM-ZA Cumulative Agency Report - <?php echo $fromdate . " To " . date("Y-m-d H:i:s", strtotime("now")) ?></h3>
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
                <table style="border-collapse: collapse; margin-top: 10px;" border="1" cellspacing="" cellpadding="0">
                    <!-- Table Head -->
                    <tr>
                        <th colspan="13 ">TELKOM-ZA Cumulative Agency Report - <?php echo $fromdate . " To " . date("Y-m-d H:i:s", strtotime("now")) ?></th>
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
                        if (($tothitcount[0]['totalhitcount'] != 0 && $rencount[0]['renewalcount'] != 0) || ($tothitcount[0]['totalhitcount'] == 0 && $rencount[0]['renewalcount'] != 0) || ($tothitcount[0]['totalhitcount'] != 0 && $rencount[0]['renewalcount'] == 0)) {
                            // Get total hit count for the agent ID
                            $nonmalware = 0;
                            $malware = 0;
                            // Get RTCG count for the agent ID
                            $rtcgcnt = $db['TELCO_WAP']->getResults("SELECT COUNT(*) AS rtcgcnt FROM subscription_detail WHERE agid IN ('" . $agentId . "') AND transtime >= '" . $fromdate . "' AND transtime <= '" . $todate . "' AND transactionid LIKE '%SUB%' AND LENGTH(transactionid) = '21'");
                            // Get agency success and hold count
                            $agency_success_hold = $db['TELCO_SUB']->getResults("SELECT SUM(IF(postback_status='success',1,0)) AS ag_success,SUM(IF(postback_status='hold',1,0)) AS ag_hold FROM postback_history WHERE agid IN('" . $agentId . "') AND subscribedon >= '" . $fromdate . "' AND subscribedon <= '" . $todate . "'");
                            // Get subscription count
                            $subcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) AS renewalcount FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");
                            // Get deactivation count
                            $deactcount = $db['TELCO_SUB']->getResults("SELECT COUNT(*) modecount FROM subscription WHERE agent_id = '" . $agentId . "' AND  unsubscribedon >= '" . $fromdate . "'  AND unsubscribedon <= '" . $todate . "'");
                            // Get subscription revenue
                            $subrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS subrevenue FROM chargingtransaction AS ct,subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Subscription' AND ct.`status` = 'success' AND ct.`status` != 'pending' AND s.op_subscription_id = ct.subscriptionid");
                            // Get renewal revenue
                            $renrevenue = $db['TELCO_SUB']->getResults("SELECT SUM(ct.charging_amount)/100 AS renrevenue FROM chargingtransaction AS ct, subscription s WHERE ct.requestedon >= '" . $fromdate . "' AND ct.requestedon <= '" . $todate . "' AND s.agent_id = '" . $agentId . "' AND ct.type = 'Renewal' AND ct.`status` = 'success' AND s.op_subscription_id = ct.subscriptionid");
                            $totalhitcount = $tothitcount[0]['totalhitcount'] ?? 0;
                            $rtcgCount = $rtcgcnt[0]['rtcgcnt'] ?? 0;
                            $agencyAS = $agency_success_hold[0]['ag_success'] ?? 0;
                            $agencyAH = $agency_success_hold[0]['ag_hold'] ?? 0;
                            $subscriptioncount = $subcount[0]['renewalcount'] ?? 0;
                            $renewalcount = $rencount[0]['renewalcount'] ?? 0;
                            $deactivationcount = $deactcount[0]['modecount'] ?? 0;
                            $subscriptionrevenue = $subrevenue[0]['subrevenue'] ?? 0;
                            $renewalrevenue = $renrevenue[0]['renrevenue'] ?? 0;
                            // Get total revenue
                            $totalrevenue = $subscriptionrevenue + $renewalrevenue;
                            // Display agent statistics in HTML table cells
                            echo "<tr>";
                            echo "<td>" . $row['agent_name'] . " (" . $row['agentid'] . ")</td>";
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
                    }
                    // Print the table for cumulative totals
                    echo "<tr>";
                    echo "<th>Total</th>";
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
                <H1>Romas</H1>
            </div>
        </main>
    </div>
</body>