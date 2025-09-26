<?php 
include './config/connection.php';

$date = date('Y-m-d');
$year = date('Y'); 
$month = date('m');

// Queries for different time periods
$queryToday = "SELECT count(*) as `today` 
  from `patient_visits` 
  where `visit_date` = '$date';";

$queryWeek = "SELECT count(*) as `week` 
  from `patient_visits` 
  where YEARWEEK(`visit_date`) = YEARWEEK('$date');";

$queryYear = "SELECT count(*) as `year` 
  from `patient_visits` 
  where YEAR(`visit_date`) = YEAR('$date');";

$queryMonth = "SELECT count(*) as `month` 
  from `patient_visits` 
  where YEAR(`visit_date`) = $year and 
  MONTH(`visit_date`) = $month;";

// Query for 7 days chart data
$query7Days = "SELECT 
    DATE(`visit_date`) as visit_day,
    COUNT(*) as count
    FROM `patient_visits` 
    WHERE `visit_date` >= DATE_SUB('$date', INTERVAL 6 DAY) 
    AND `visit_date` <= '$date'
    GROUP BY DATE(`visit_date`)
    ORDER BY `visit_date`";

// Query for monthly chart data (last 12 months)
$queryMonthly = "SELECT 
    YEAR(`visit_date`) as year,
    MONTH(`visit_date`) as month,
    COUNT(*) as count
    FROM `patient_visits` 
    WHERE `visit_date` >= DATE_SUB('$date', INTERVAL 11 MONTH)
    GROUP BY YEAR(`visit_date`), MONTH(`visit_date`)
    ORDER BY YEAR(`visit_date`), MONTH(`visit_date`)";

// Query for yearly chart data (last 5 years)
$queryYearly = "SELECT 
    YEAR(`visit_date`) as year,
    COUNT(*) as count
    FROM `patient_visits` 
    WHERE `visit_date` >= DATE_SUB('$date', INTERVAL 4 YEAR)
    GROUP BY YEAR(`visit_date`)
    ORDER BY YEAR(`visit_date`)";

$todaysCount = 0;
$currentWeekCount = 0;
$currentMonthCount = 0;
$currentYearCount = 0;
$chartData7Days = [];
$chartDataMonthly = [];
$chartDataYearly = [];

try {
    // Get summary counts
    $stmtToday = $con->prepare($queryToday);
    $stmtToday->execute();
    $r = $stmtToday->fetch(PDO::FETCH_ASSOC);
    $todaysCount = $r['today'];

    $stmtWeek = $con->prepare($queryWeek);
    $stmtWeek->execute();
    $r = $stmtWeek->fetch(PDO::FETCH_ASSOC);
    $currentWeekCount = $r['week'];

    $stmtYear = $con->prepare($queryYear);
    $stmtYear->execute();
    $r = $stmtYear->fetch(PDO::FETCH_ASSOC);
    $currentYearCount = $r['year'];

    $stmtMonth = $con->prepare($queryMonth);
    $stmtMonth->execute();
    $r = $stmtMonth->fetch(PDO::FETCH_ASSOC);
    $currentMonthCount = $r['month'];

    // Get 7 days data with complete date range
    $stmt7Days = $con->prepare($query7Days);
    $stmt7Days->execute();
    $dbData7Days = [];
    while($row = $stmt7Days->fetch(PDO::FETCH_ASSOC)) {
        $dbData7Days[$row['visit_day']] = $row['count'];
    }
    
    // Generate complete 7 days data
    for($i = 6; $i >= 0; $i--) {
        $checkDate = date('Y-m-d', strtotime("-$i days"));
        $chartData7Days[] = [
            'visit_day' => $checkDate,
            'count' => isset($dbData7Days[$checkDate]) ? $dbData7Days[$checkDate] : 0
        ];
    }

    // Get monthly data with complete month range
    $stmtMonthly = $con->prepare($queryMonthly);
    $stmtMonthly->execute();
    $dbDataMonthly = [];
    while($row = $stmtMonthly->fetch(PDO::FETCH_ASSOC)) {
        $key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        $dbDataMonthly[$key] = $row['count'];
    }
    
    // Generate complete 12 months data
    for($i = 11; $i >= 0; $i--) {
        $checkDate = date('Y-m-d', strtotime("-$i months"));
        $checkYear = date('Y', strtotime($checkDate));
        $checkMonth = date('m', strtotime($checkDate));
        $key = $checkYear . '-' . $checkMonth;
        
        $chartDataMonthly[] = [
            'year' => $checkYear,
            'month' => $checkMonth,
            'count' => isset($dbDataMonthly[$key]) ? $dbDataMonthly[$key] : 0
        ];
    }

    // Get yearly data with complete year range
    $stmtYearly = $con->prepare($queryYearly);
    $stmtYearly->execute();
    $dbDataYearly = [];
    while($row = $stmtYearly->fetch(PDO::FETCH_ASSOC)) {
        $dbDataYearly[$row['year']] = $row['count'];
    }
    
    // Generate complete 5 years data
    for($i = 4; $i >= 0; $i--) {
        $checkYear = date('Y', strtotime("-$i years"));
        $chartDataYearly[] = [
            'year' => $checkYear,
            'count' => isset($dbDataYearly[$checkYear]) ? $dbDataYearly[$checkYear] : 0
        ];
    }

} catch(PDOException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    exit;
}

// Prepare data for JavaScript
$chartData7DaysJson = json_encode($chartData7Days);
$chartDataMonthlyJson = json_encode($chartDataMonthly);
$chartDataYearlyJson = json_encode($chartDataYearly);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <title>Thống kê - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
    .dark-mode .bg-fuchsia,
    .dark-mode .bg-maroon {
        color: #fff !important;
    }
    .dark-mode .content-wrapper {
        background-color: #F4F6F9;
        color: #fff;
    }
    .dark-mode .main-footer {
        background-color: #F4F6F9;
        border-color: #a5a5a5ff;
    }
    .chart-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .chart-controls {
        margin-bottom: 15px;
    }
    .chart-controls .btn {
        margin-right: 10px;
    }
    .chart-controls .btn.active {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    #chartCanvas {
        max-height: 400px;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php 
        include './config/header.php';
        include './config/sidebar.php';
        ?>
        
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6" style="color:black">
                            <h1>Thống kê</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <!-- Summary boxes -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $todaysCount;?></h3>
                                    <p>Số bệnh nhân hôm nay</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-purple">
                                <div class="inner">
                                    <h3><?php echo $currentWeekCount;?></h3>
                                    <p>Tuần hiện tại</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-calendar-week"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-fuchsia text-reset">
                                <div class="inner">
                                    <h3><?php echo $currentMonthCount;?></h3>
                                    <p>Tháng hiện tại</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-maroon text-reset">
                                <div class="inner">
                                    <h3><?php echo $currentYearCount;?></h3>
                                    <p>Năm hiện tại</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-user-injured"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="chart-container">
                                <h3>Biểu đồ thống kê bệnh nhân</h3>
                                <div class="chart-controls">
                                    <button type="button" class="btn btn-outline-info active" data-period="7days">7 ngày gần nhất</button>
                                    <button type="button" class="btn btn-outline-info" data-period="monthly">Theo tháng</button>
                                    <button type="button" class="btn btn-outline-info" data-period="yearly">Theo năm</button>
                                </div>
                                <canvas id="chartCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include './config/footer.php';?>
    </div>

    <?php include './config/site_js_links.php';?>
    <script>
    $(function() {
        showMenuSelected("#mnu_dashboard", "");
        
        // Chart data from PHP
        const chartData7Days = <?php echo $chartData7DaysJson; ?>;
        const chartDataMonthly = <?php echo $chartDataMonthlyJson; ?>;
        const chartDataYearly = <?php echo $chartDataYearlyJson; ?>;
        
        let currentChart = null;
        const ctx = document.getElementById('chartCanvas').getContext('2d');
        
        // Function to create chart
        function createChart(data, type) {
            if (currentChart) {
                currentChart.destroy();
            }
            
            let labels = [];
            let values = [];
            let backgroundColor = [];
            let borderColor = [];
            
            // Generate vibrant colors for bars
            const colors = [
                { bg: 'rgba(255, 99, 132, 0.8)', border: 'rgba(255, 99, 132, 1)' },
                { bg: 'rgba(54, 162, 235, 0.8)', border: 'rgba(54, 162, 235, 1)' },
                { bg: 'rgba(255, 205, 86, 0.8)', border: 'rgba(255, 205, 86, 1)' },
                { bg: 'rgba(75, 192, 192, 0.8)', border: 'rgba(75, 192, 192, 1)' },
                { bg: 'rgba(153, 102, 255, 0.8)', border: 'rgba(153, 102, 255, 1)' },
                { bg: 'rgba(255, 159, 64, 0.8)', border: 'rgba(255, 159, 64, 1)' },
                { bg: 'rgba(199, 199, 199, 0.8)', border: 'rgba(199, 199, 199, 1)' }
            ];
            
            if (type === '7days') {
                data.forEach((item, index) => {
                    const date = new Date(item.visit_day);
                    labels.push(date.getDate() + '/' + (date.getMonth() + 1));
                    values.push(parseInt(item.count));
                    const colorIndex = index % colors.length;
                    backgroundColor.push(colors[colorIndex].bg);
                    borderColor.push(colors[colorIndex].border);
                });
            } else if (type === 'monthly') {
                data.forEach((item, index) => {
                    labels.push('Tháng ' + item.month + '/' + item.year);
                    values.push(parseInt(item.count));
                    const colorIndex = index % colors.length;
                    backgroundColor.push(colors[colorIndex].bg);
                    borderColor.push(colors[colorIndex].border);
                });
            } else if (type === 'yearly') {
                data.forEach((item, index) => {
                    labels.push('Năm ' + item.year);
                    values.push(parseInt(item.count));
                    const colorIndex = index % colors.length;
                    backgroundColor.push(colors[colorIndex].bg);
                    borderColor.push(colors[colorIndex].border);
                });
            }
            
            currentChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số bệnh nhân',
                        data: values,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 2,
                        borderRadius: 5,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutBounce'
                    }
                }
            });
        }
        
        // Initialize with 7 days chart
        createChart(chartData7Days, '7days');
        
        // Handle period change
        $('.chart-controls .btn').on('click', function() {
            $('.chart-controls .btn').removeClass('active');
            $(this).addClass('active');
            
            const period = $(this).data('period');
            
            switch(period) {
                case '7days':
                    createChart(chartData7Days, '7days');
                    break;
                case 'monthly':
                    createChart(chartDataMonthly, 'monthly');
                    break;
                case 'yearly':
                    createChart(chartDataYearly, 'yearly');
                    break;
            }
        });
    });
    </script>
</body>

</html>