<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Reports Dashboard</title>
    <link rel="icon" href="slogo.png">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Stylesheet -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://reports.symbioticinfo.com/resources/css/style.css?v=3.1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Scripts -->
    <script type="text/javascript" src="https://reports.symbioticinfo.com/resources/js/bootstrap-table.js" charset="utf-8"></script>
    <style>
        /* Shared properties for th and td */
        th,
        td {
            font-family: "Poppins", sans-serif;
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
            /* font-family: 'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif; */
            font-family: "Poppins", sans-serif;
            font-size: 13px;
        }

        body {
            font-family: "Poppins", sans-serif;
        }

        /* Specific properties for span */
        span {
            color: red;
        }

        tr td {
            text-align: center;
            padding: 10px;
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
                <h3>TELKOM Agency Hourly Report - 2023-06-26 00:00:00 To 2023-06-26 00:00:46</h3>
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
                        <th colspan="13">TELKOM AGENCY HOURLY REPORT - 2023-06-26 00:00:00 To 2023-06-26 00:00:46</th>
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
                    <!-- Table Content -->
                    <tr>
                        <td>Adstation</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Mobplus - Innovate Entertainment</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    <tr>
                        <td>Mobplus - Innovate Entertainment</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                    </tr>
                    <!-- table footer -->
                    <tr class="summary-row">
                        <td><strong>Total</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                        <td><strong>0</strong></td>
                    </tr>
                </table>
            </div>
        </main>
        <!-- Content End -->
        <!-- Footer Start -->
        <br>
        <footer id="site-footer" role="contentinfo"></footer>
    </div>
</body>

</html>