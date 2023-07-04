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
              
                </table>
            </div>
        </main>
    </div>
</body>