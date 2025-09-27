<?php 
include './config/connection.php';
$date = date('Y-m-d');
$year = date('Y');
$month = date('m');

$queryToday = "SELECT COUNT(*) as `today`
  FROM `patients`
  WHERE DATE(`created_at`) = '$date';";

$queryWeek = "SELECT COUNT(*) as `week`
  FROM `patients`
  WHERE YEARWEEK(`created_at`, 1) = YEARWEEK('$date', 1);";

$queryYear = "SELECT COUNT(*) as `year`
  FROM `patients`
  WHERE YEAR(`created_at`) = YEAR('$date');";

$queryMonth = "SELECT COUNT(*) as `month`
  FROM `patients`
  WHERE YEAR(`created_at`) = $year 
    AND MONTH(`created_at`) = $month;";

$todaysCount = 0;
$currentWeekCount = 0;
$currentMonthCount = 0;
$currentYearCount = 0;

try {
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

} catch(PDOException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    exit;
}


// 7 ngày gần nhất 
$chartData7Days = [];
$dates = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $dates[$d] = 0;
}
$sql7days = "SELECT DATE(`created_at`) as visit_day, COUNT(*) as count
             FROM patients
             WHERE `created_at` >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY visit_day";
$stmt = $con->prepare($sql7days);
$stmt->execute();
$result7days = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($result7days as $row) {
    $dates[$row['visit_day']] = (int)$row['count'];
}
foreach ($dates as $day => $count) {
    $chartData7Days[] = ['visit_day' => $day, 'count' => $count];
}

//  12 tháng của năm
$chartDataMonthly = [];
$months = [];
for ($m = 1; $m <= 12; $m++) {
    $months[$m] = 0;
}
$sqlMonthly = "SELECT MONTH(`created_at`) as month, COUNT(*) as count
               FROM patients
               WHERE YEAR(`created_at`) = ?
               GROUP BY month";
$stmt = $con->prepare($sqlMonthly);
$stmt->execute([$year]);
$resultMonthly = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($resultMonthly as $row) {
    $months[(int)$row['month']] = (int)$row['count'];
}
foreach ($months as $m => $count) {
    $chartDataMonthly[] = ['month' => $m, 'year' => (int)$year, 'count' => $count];
}

//  5 năm gần nhất
$chartDataYearly = [];
$years = [];
$currentYear = (int)$year;
for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
    $years[$y] = 0;
}
$sqlYearly = "SELECT YEAR(`created_at`) as year, COUNT(*) as count
              FROM patients
              WHERE YEAR(`created_at`) >= ?
              GROUP BY year";
$stmt = $con->prepare($sqlYearly);
$stmt->execute([$currentYear - 4]);
$resultYearly = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($resultYearly as $row) {
    $years[(int)$row['year']] = (int)$row['count'];
}
foreach ($years as $y => $count) {
    $chartDataYearly[] = ['year' => $y, 'count' => $count];
}

// Chuyển sang JSON
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
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
                            <h1>
                                Thống kê hôm nay ngày <?php echo date('d/m/Y'); ?>
                            </h1>
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
                                <h3 style="color: #2b2b2bff;">Báo cáo chi tiết</h3>
                                <br/>
                                <div class="chart-controls">
                                    <button type="button" class="btn btn-outline-info active" data-period="7days">Theo tuần
                                    </button>
                                    <button type="button" class="btn btn-outline-info" data-period="monthly">Theo
                                        tháng</button>
                                    <button type="button" class="btn btn-outline-info" data-period="yearly">Theo
                                        năm</button>
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

    
        const chartData7Days = <?php echo $chartData7DaysJson; ?>;
        const chartDataMonthly = <?php echo $chartDataMonthlyJson; ?>;
        const chartDataYearly = <?php echo $chartDataYearlyJson; ?>;

        let currentChart = null;
        const ctx = document.getElementById('chartCanvas').getContext('2d');

        function createChart(data, type) {
            if (currentChart) {
                currentChart.destroy();
            }

            let labels = [];
            let values = [];
            let backgroundColor = [];
            let borderColor = [];

            const colors = [{
                    bg: 'rgba(255, 99, 132, 0.8)',
                    border: 'rgba(255, 99, 132, 1)'
                },
                {
                    bg: 'rgba(54, 162, 235, 0.8)',
                    border: 'rgba(54, 162, 235, 1)'
                },
                {
                    bg: 'rgba(255, 205, 86, 0.8)',
                    border: 'rgba(255, 205, 86, 1)'
                },
                {
                    bg: 'rgba(75, 192, 192, 0.8)',
                    border: 'rgba(75, 192, 192, 1)'
                },
                {
                    bg: 'rgba(153, 102, 255, 0.8)',
                    border: 'rgba(153, 102, 255, 1)'
                },
                {
                    bg: 'rgba(255, 159, 64, 0.8)',
                    border: 'rgba(255, 159, 64, 1)'
                },
                {
                    bg: 'rgba(199, 199, 199, 0.8)',
                    border: 'rgba(199, 199, 199, 1)'
                }
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

        createChart(chartData7Days, '7days');

        $('.chart-controls .btn').on('click', function() {
            $('.chart-controls .btn').removeClass('active');
            $(this).addClass('active');

            const period = $(this).data('period');

            switch (period) {
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