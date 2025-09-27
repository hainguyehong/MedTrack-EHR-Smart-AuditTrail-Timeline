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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <title>Thống kê - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
    <style>
    .dark-mode .bg-fuchsia,
    .dark-mode .bg-maroon {
        color: #fff !important;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->

        <?php 

include './config/header.php';
include './config/sidebar.php';
?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Thống kê</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
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
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
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
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
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
                        <!-- ./col -->
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
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
                </div>
            </section>

            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include './config/footer.php';?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php';?>
    <script>
    $(function() {
        showMenuSelected("#mnu_dashboard", "");
    })
    </script>

</body>

</html>