<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
islogin([3]);

$message = '';
$userId = $_SESSION['user_id'];  // lấy id user sau khi login

// 1) Thông báo lịch khám lại từ bảng patient_diseases (qua ngày thì ẩn ngày cũ)
//     $query = " cũ
//     SELECT pd.id, pd.next_visit_date
//     FROM user_patients AS up
//     JOIN patient_diseases AS pd ON up.id_patient = pd.patient_id
//     WHERE up.id = :userId
//       AND pd.next_visit_date IS NOT NULL
//       AND pd.next_visit_date <> ''
//       AND DATE(pd.next_visit_date) >= CURDATE()
//     ORDER BY pd.next_visit_date ASC
//     LIMIT 1;
// ";
$query = "
        SELECT pd.id, pd.next_visit_date
        FROM user_patients AS up
        JOIN patient_diseases AS pd ON up.id_patient = pd.patient_id
        WHERE up.id = :userId
        AND pd.next_visit_date IS NOT NULL
        AND pd.next_visit_date <> ''
        AND DATE(pd.next_visit_date) >= CURDATE()
        ORDER BY pd.next_visit_date ASC
        LIMIT 1;
    ";

$stmt = $con->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Lịch khám đã All (qua ngày thì ẩn ngày cũ) + trạng thái xác nhận/từ chối
//    Sửa lại: dùng user_patients để map user -> patient -> book,
//    đồng thời join appointment_status_log để lấy trạng thái mới nhất.
$querylich = "
    SELECT 
        b.id, 
        b.date_visit, 
        b.time_visit, 
        b.trieu_chung, 
        b.noi_dung_kham,
        COALESCE(s.status, 'pending') AS current_status,
        s.doctor_note
    FROM book AS b
    LEFT JOIN appointment_status_log AS s
        ON s.id = (
            SELECT MAX(id) 
            FROM appointment_status_log 
            WHERE book_id = b.id
        )
    WHERE b.id_patient = :userId
      AND b.is_deleted = 0
      AND CONCAT(DATE(b.date_visit), ' ', b.time_visit) >= NOW()
    ORDER BY b.date_visit DESC, b.time_visit DESC
";
//    AND DATE(b.date_visit) >= CURDATE() nếu muốn ẩn ngày cũ ( không cần chính xác cả thời gian ) thay concat

$stmtlich = $con->prepare($querylich);
$stmtlich->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtlich->execute();
$rows = $stmtlich->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php'; ?>
    <?php include './config/data_tables_css.php'; ?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <!-- Thêm favicon giống dashboard.php -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <style>
        * {
    font-family: sans-serif;
}
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

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background: #f8fafc;">
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
                            <!-- <h1>Thông báo</h1> -->
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Thông báo lịch khám lại -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-bell mr-1"></i>THÔNG BÁO LỊCH KHÁM LẠI</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (!empty($notifications)) {
                            foreach ($notifications as $row) {
                                $date = new DateTime($row['next_visit_date']);
                                $formattedDate = $date->format('d/m/Y');  // định dạng: ngày/tháng/năm

                                echo '<p>Bạn có lịch khám lại vào ngày <strong>' . htmlspecialchars($formattedDate) . '</strong>.</p>';
                            }
                        } else {
                            echo '<div class="alert alert-info" role="alert">
                                        Không có thông báo lịch khám lại.
                                      </div>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Thông báo trạng thái lịch khám đã đặt -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-bell mr-1"></i>TRẠNG THÁI LỊCH KHÁM ĐÃ ĐẶT</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                $date = new DateTime($row['date_visit']);
                                $formattedDate = $date->format('d/m/Y');
                                $time = htmlspecialchars($row['time_visit']);
                                $status = $row['current_status'];

                                $statusText = '';
                                $statusClass = '';

                                switch ($status) {
                                    case 'confirmed':
                                        $statusText = 'Lịch khám của bạn đã được bác sĩ <strong>xác nhận</strong>.';
                                        $statusClass = 'text-success';
                                        break;
                                    case 'rejected':
                                        $statusText = 'Rất tiếc, lịch khám của bạn đã bị <strong>từ chối</strong>. Vui lòng liên hệ phòng khám để đặt lại.';
                                        $statusClass = 'text-danger';
                                        break;
                                    default:
                                        $statusText = 'Lịch khám của bạn đang ở trạng thái <strong>chờ bác sĩ xác nhận</strong>.';
                                        $statusClass = 'text-warning';
                                        break;
                                }

                                echo '<p>Bạn đã đặt lịch khám vào ngày <strong>' . htmlspecialchars($formattedDate) . '</strong> lúc <strong>' . $time . '</strong>.</p>';

                                if (!empty($row['trieu_chung']) || !empty($row['noi_dung_kham'])) {
                                    echo '<p><strong> Triệu chứng:</strong> ' . htmlspecialchars($row['trieu_chung']) . '</p>';
                                    echo '<p><strong>Lý do khám :</strong> ' . htmlspecialchars($row['noi_dung_kham']) . '</p>';
                                }

                                echo '<p class="' . $statusClass . '">' . $statusText . '</p>';
                                echo '<hr>';
                            }
                        } else {
                            echo '<div class="alert alert-info" role="alert">
                                        Hiện tại bạn chưa có lịch khám nào được đặt.
                                      </div>';
                        }
                        ?>
                    </div>
                </div>

            </section>
            <br />
        </div>
        <!-- /.content-wrapper -->

        <?php
        include './config/footer.php';

        $message = '';
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']);  // Xóa ngay sau khi lấy để F5 không lặp lại
        }
        ?>

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

        // nếu không dùng #date_of_birth thì đoạn này có thể bỏ đi
        $('#date_of_birth').datetimepicker({
            format: 'L'
        });

        $(function() {
            $("#medicine_details").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
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