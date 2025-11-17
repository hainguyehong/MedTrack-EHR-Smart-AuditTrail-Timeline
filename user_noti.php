<?php
    include './config/connection.php';
    include './common_service/common_functions.php';
    include './common_service/date.php';
    islogin();
    $message = '';
    $userId  = $_SESSION['user_id']; // lấy id user sau khi login
    // echo "🔍 userId hiện tại: " . htmlspecialchars($userId) . "<br>";
    // exit();
    $query = "
SELECT pd.id, pd.next_visit_date
FROM user_patients AS up
JOIN patient_diseases AS pd ON up.id_patient = pd.patient_id
WHERE up.id = :userId
  AND pd.next_visit_date IS NOT NULL
  AND pd.next_visit_date <> ''
ORDER BY pd.next_visit_date DESC LIMIT 1;
";

    $stmt = $con->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // var_dump($userId);

    $querylich = "SELECT id, date_visit, time_visit, trieu_chung, noi_dung_kham
                  FROM book WHERE id_patient = ? AND is_deleted = 0 ORDER BY date_visit DESC, time_visit DESC";
    $stmtlich = $con->prepare($querylich);
    $stmtlich->execute([$_SESSION['user_id']]);
    $rows = $stmtlich->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php'; ?>

    <?php include './config/data_tables_css.php'; ?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <style>
    body {
        background: #f4f7fb;
    }

    .card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07), 0 1.5px 4px rgba(0, 0, 0, 0.03);
        border: none;
        margin-bottom: 24px;
        transition: box-shadow 0.2s;
    }

    .card:hover {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        border-radius: 14px 14px 0 0;
        border: none;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(90, 156, 248, 0.07);
    }

    .btn-primary,
    .btn-danger {
        border-radius: 22px;
        transition: 0.2s;
        font-weight: 500;
        padding: 7px 22px;
    }

    .btn-primary:hover,
    .btn-danger:hover {
        filter: brightness(1.08);
        box-shadow: 0 2px 12px rgba(90, 156, 248, 0.13);
    }

    .card-title {
        font-weight: 700;
        letter-spacing: 0.7px;
        font-size: 1.15rem;
    }

    label {
        font-weight: 600;
        color: #3b4256;
        margin-bottom: 6px;
        letter-spacing: 0.2px;
    }

    .form-control,
    .form-select,
    textarea.form-control {
        border-radius: 10px !important;
        border: 1.5px solid #e3e7ed;
        background: #fafdff;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: none;
        font-size: 1rem;
        padding: 8px 14px;
    }

    .form-control:focus,
    .form-select:focus,
    textarea.form-control:focus {
        border-color: #5a9cf8;
        box-shadow: 0 0 0 2px #e3f0ff;
        background: #fff;
    }

    input[readonly],
    textarea[readonly] {
        background: #f4f7fb !important;
        color: #6b7280;
        border-color: #e3e7ed;
    }

    .section-title {
        margin-top: 18px;
        margin-bottom: 10px;
        color: #5b5b5bff;
        font-size: 1.08rem;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .mb-3 {
        margin-bottom: 1.2rem !important;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .alert-info {
        border-radius: 12px;
        background: #e0f2fe;
        color: #2563eb;
        border: none;
        font-weight: 500;
    }

    .table-striped tbody tr:hover {
        background-color: #f1f5f9;
        transition: background 0.2s;
    }

    [class*="col-"] {
        padding-bottom: 12px;
    }

    .card-primary.card-outline {
        border-top: 0px solid #007bff;
    }
    </style>
</head>

<!-- <body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed"> -->

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background: #f8fafc;">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <?php include './config/header.php';
        include './config/sidebar.php'; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h1>Thông báo</h1> -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <!-- <div class="card card-outline card-primary rounded-0 shadow"> -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thông báo lịch khám lại </h3>
                    </div>
                    <div class="card-body">
                        <?php
                            if (! empty($notifications)) {
                                foreach ($notifications as $row) {
                                    $date          = new DateTime($row['next_visit_date']);
                                    $formattedDate = $date->format('d/m/Y'); // định dạng: ngày/tháng/năm

                                    echo '<p>Bạn có lịch khám lại vào ngày ' . htmlspecialchars($formattedDate) . ' </p>';
                                }
                            } else {
                                echo '<div class="alert alert-info" role="alert">
                            Không có thông báo lịch khám lại.
                          </div>';
                            }
                        ?>

                    </div>
                </div>

                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thông báo Khác </h3>
                    </div>
                    <div class="card-body">
                        <?php
                            if (! empty($rows)) {
                                foreach ($rows as $row) {
                                    $date          = new DateTime($row['date_visit']);
                                    $formattedDate = $date->format('d/m/Y'); // định dạng: ngày/tháng/năm

                                    echo '<p>Bạn có lịch khám vào ngày ' . htmlspecialchars($formattedDate) . ' </p>';
                                }
                            } else {
                                echo '<div class="alert alert-info" role="alert">
                            Không có thông báo khác.
                          </div>';
                            }
                        ?>
                    </div>
                </div>

            </section>
            <br />


            <!-- bệnh án select -->
            <br />
        </div>
        <!-- /.content -->

        <!-- /.content-wrapper -->
        <?php
            include './config/footer.php';

            //   $message = '';
            //   if(isset($_GET['message'])) {
            //     $message = $_GET['message'];
            //   }
            $message = '';
            if (isset($_SESSION['success_message'])) {
                $message = $_SESSION['success_message'];
                unset($_SESSION['success_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
            }
        ?>
        <!-- /.control-sidebar -->


        <?php include './config/site_js_links.php'; ?>
        <?php include './config/data_tables_js.php'; ?>


        <script src="plugins/moment/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="date.js"></script>

        <script>
        showMenuSelected("#mnu_patients", "#mi_patients");

        var message = '<?php echo $message; ?>';
        if (message !== '') {
            showCustomMessage(message);
        }

        $('#date_of_birth').datetimepicker({
            format: 'L'
        });


        $(function() {
            $("#medicine_details").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "buttons": ["pdf", "print"],
                "language": {
                    "info": " Tổng cộng _TOTAL_ loại thuốc",
                    "paginate": {
                        "previous": "<span style='font-size:18px;'>&#8592;</span>",
                        "next": "<span style='font-size:18px;'>&#8594;</span>"
                    }
                }
            }).buttons().container().appendTo('#medicine_details_wrapper .col-md-6:eq(0)');
        });
        </script>
</body>

</html>