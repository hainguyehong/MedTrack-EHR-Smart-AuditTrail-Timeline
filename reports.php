<?php 
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php' ?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Reports - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
    <style>
    body {
        background: #f8fafc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }

    .btn-primary,
    .btn-danger {
        border-radius: 20px;
        transition: 0.2s;
    }

    .btn-primary:hover,
    .btn-danger:hover {
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
    }

    .card-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    label {
        font-weight: 500;
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
include './config/sidebar.php';?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Báo Cáo</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <!-- <div class="card card-outline card-primary rounded-0 shadow"> -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Lịch Sử Khám Bệnh Trong Khoảng Thời Gian</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            echo getDateTextBox('Từ Ngày', 'patients_from');

                            echo getDateTextBox('Đến Ngày', 'patients_to');
                        ?>

                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="button" id="print_visits" class="btn btn-primary btn-sm btn-block">Xem
                                    Trước Báo Cáo</button>
                            </div>
                        </div>
                    </div>
                </div>


            </section>
        </div>

        <?php include './config/footer.php' ?>
    </div>

    <?php include './config/site_js_links.php'; ?>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- cho tải ảnh -->
    <script src="plugins\daterangepicker\date.js"></script>
    <script>
    showMenuSelected("#mnu_reports", "#mi_reports");

    $(document).ready(function() {
        // Nếu có dùng datetimepicker cho các input ngày
        $('#visit_date, #next_visit_date').datetimepicker({
            format: 'L'
        });

        // Nút xem báo cáo lịch sử khám bệnh
        $("#print_visits").click(function() {
            var from = $("#patients_from").val().trim();
            var to = $("#patients_to").val().trim();

            if (!from && !to) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu thông tin',
                    text: 'Vui lòng chọn Khoảng thời gian trước khi xem báo cáo.'
                });
                return;
            }

            if (!from) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu "Từ Ngày"',
                    text: 'Vui lòng chọn "Từ Ngày".'
                });
                return;
            }

            if (!to) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Thiếu "Đến Ngày"',
                    text: 'Vui lòng chọn "Đến Ngày".'
                });
                return;
            }

            var win = window.open(
                "print_patients_visits.php?from=" + encodeURIComponent(from) +
                "&to=" + encodeURIComponent(to),
                "_blank"
            );

            if (win) {
                win.focus();
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Không mở được cửa sổ mới',
                    text: 'Vui lòng cho phép trình duyệt mở popup để xem báo cáo.'
                });
            }
        });
    });
    // Khởi tạo datetimepicker cho các group id kết thúc bằng "_group"
    $(function() {
        $("[id$='_group']").each(function() {
            $(this).datetimepicker({
                format: 'DD/MM/YYYY'
            });
        });
    });
    </script>

</body>

</html>