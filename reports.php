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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Báo cáo - MedTrack</title>
    <style>
        * {
    font-family: sans-serif;
}

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

    .card {
    background: #fff;
    border-radius: 12px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.card-outline.card-primary {
    border-radius: 12px !important;
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
                            <!-- <h1>Báo Cáo</h1> -->
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
                        <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left mr-1"></i>LỊCH SỬ KHÁM BỆNH TRONG KHOẢNG THỜI GIAN</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <!-- HÀNG CHỌN NGÀY -->
                        <div class="row justify-content-center">
                            <?php 
                                echo getDateTextBox('Từ ngày', 'patients_from','true');
                                echo getDateTextBox('Đến ngày', 'patients_to','true');
                            ?>
                        </div>
                        <!-- HÀNG BUTTON -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="button"
                                        id="print_visits"
                                        class="btn btn-primary btn-sm px-4 rounded-pill">
                                   <i class="fa-solid fa-file-pdf"></i> XEM TRƯỚC BÁO CÁO
                                </button>
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
                    icon: 'error',
                    title: 'Lỗi',
                    iconColor: '#dc3545',
                    confirmButtonText: 'Đã hiểu',
                    text: 'Vui lòng chọn Khoảng thời gian trước khi xem báo cáo.'
                });
                return;
            }

            if (!from) {
                Swal.fire({
                    icon: 'error',
                    title: 'Thiếu "Từ Ngày"',
                    iconColor: '#dc3545',
                    confirmButtonText: 'Đã hiểu',
                    text: 'Vui lòng chọn "Từ Ngày".'
                });
                return;
            }

            if (!to) {
                Swal.fire({
                    icon: 'error',
                    title: 'Thiếu "Đến Ngày"',
                    iconColor: '#dc3545',
                    confirmButtonText: 'Đã hiểu',
                    text: 'Vui lòng chọn "Đến Ngày".'
                });
                return;
            }
            // Convert DD/MM/YYYY → Date
            function parseDate(dateStr) {
                var parts = dateStr.split("/");
                return new Date(parts[2], parts[1] - 1, parts[0]);
            }

            var fromDate = parseDate(from);
            var toDate   = parseDate(to);

            // Từ ngày > Đến ngày 
            if (fromDate > toDate) {
                Swal.fire({
                    icon: 'error',
                    iconColor: '#dc3545',
                    title: 'Khoảng thời gian không hợp lệ',
                    text: 'Từ ngày không được lớn hơn Đến ngày.',
                    confirmButtonText: 'Đã hiểu'
                });
                return;
            }

            // Mở báo cáo (Từ = Đến hoặc ngày tương lai đều OK)
            var win = window.open(
                "print_patients_visits.php?from=" + encodeURIComponent(from) +
                "&to=" + encodeURIComponent(to),
                "_blank"
            );

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