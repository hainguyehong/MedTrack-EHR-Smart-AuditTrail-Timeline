<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
include './sms_twilio.php';
$message = '';
$userId = $_SESSION['user_id']; 

if (isset($_POST['send_sms'])) {
    $patient_id = $_POST['send_sms'];

    $stmt = $con->prepare("
        SELECT p.patient_name, p.phone_number, pd.next_visit_date
        FROM patients p
        JOIN patient_diseases pd ON p.id = pd.patient_id
        WHERE p.id = ?
    ");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        
        $message_text = "Xin chào {$patient['patient_name']}, bạn có lịch tái khám vào ngày " . 
            date('d/m/Y', strtotime($patient['next_visit_date'])) . 
            ". Vui lòng liên hệ phòng khám để xác nhận.";

        // Gửi SMS Twilio (tự convert số)
        $send_result = sendSMS($patient['phone_number'], $message_text, 'OTHER');

        if ($send_result === true) {
            $_SESSION['success_message'] = "Đã gửi SMS tới {$patient['patient_name']}";
        } else {
            $_SESSION['error_message'] = "Gửi SMS thất bại: $send_result";
        }
    } else {
        $_SESSION['error_message'] = "Không tìm thấy bệnh nhân với ID: $patient_id";
    }
header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$query = "SELECT p.id, p.patient_name, p.cnic, p.phone_number,p.created_at, pd.next_visit_date
          FROM patients p
          JOIN patient_diseases pd ON p.id = pd.patient_id
          WHERE pd.next_visit_date IS NOT NULL
          ORDER BY pd.next_visit_date ASC
        ";
$stmt = $con->prepare($query);
// exit();
$infor = $stmt->execute();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>

    <?php include './config/data_tables_css.php';?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <style>
    body {

        background: #f8fafc;

    }

    .card {

        background: #fff;

        border-radius: 12px;

        /* border: 1.5px solid #007bff; */

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

        /* border-radius: 8px; */

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

    .active-bg {
        background-color: #d9f0ff !important;
    }
    </style>
</head>

<!-- <body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed"> -->

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background: #f8fafc;">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <?php include './config/header.php';
include './config/sidebar.php';
?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h1>Danh sách các bệnh nhân tái khám</h1> -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách Bệnh nhân</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($message)) : ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                        <?php endif; ?>

                        <div class="row table-responsive">
                            <!-- Form duy nhất bao quanh toàn bộ table và nút gửi tất cả -->
                            <form action="" method="POST" id="patientsForm" style="display:inline;">
                                <table id="all_patients" class="table table-striped table-bordered">
                                    <thead style="text-align:center;">
                                        <tr>
                                            <th><input type="checkbox" id="checkAll"></th>
                                            <th>STT</th>
                                            <th>Tên bệnh nhân</th>
                                            <th>CCCD</th>
                                            <th>Số điện thoại</th>
                                            <th>Ngày khám</th>
                                            <th>Ngày tái khám</th>
                                            <th>Gửi thông báo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $count = 0;
                                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                                $count++;
                                            ?>
                                        <tr style="text-align:center;">
                                            <td><input type="checkbox" name="patient_ids[]"
                                                    value="<?php echo $row['id']; ?>" class="checkBoxItem"></td>
                                            <td><?php echo $count; ?></td>
                                            <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['cnic']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                            <td><?php echo (!empty($row['created_at']) && $row['created_at'] != '0000-00-00 00:00:00') ? date("d/m/Y", strtotime($row['created_at'])) : ''; ?>
                                            </td>
                                            <td><?php echo (!empty($row['next_visit_date']) && $row['next_visit_date'] != '0000-00-00') ? date("d/m/Y", strtotime($row['next_visit_date'])) : 'NULL'; ?>
                                            </td>
                                            <td>
                                                <button type="submit" name="send_sms" value="<?php echo $row['id']; ?>"
                                                    class="btn btn-primary btn-sm send-sms-btn">
                                                    <i class="fa-solid fa-share-from-square"></i>

                                                </button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <br>
                                <!-- <button type="submit" name="send_all_zalo" class="btn btn-primary btn-sm">Gửi tất
                                    cả</button> -->
                            </form>
                        </div>
                    </div>

                    <script src="jquery.js"></script>
                    <script>
                    $(document).ready(function() {
                        // Check All checkbox
                        $('#checkAll').on('change', function() {
                            var checked = $(this).prop('checked');
                            $('.checkBoxItem').prop('checked', checked);
                            $('.checkBoxItem').each(function() {
                                toggleRowBackground($(this));
                            });
                        });

                        // Khi checkbox từng dòng thay đổi
                        $('.checkBoxItem').on('change', function() {
                            toggleRowBackground($(this));
                            // Nếu tất cả checkbox được check, checkAll sẽ được check
                            $('#checkAll').prop('checked', $('.checkBoxItem:checked').length === $(
                                '.checkBoxItem').length);
                        });

                        // Thay đổi màu nền dòng khi checkbox được check
                        function toggleRowBackground(row) {
                            if (row.prop('checked')) {
                                row.closest('tr').addClass('active-bg');
                            } else {
                                row.closest('tr').removeClass('active-bg');
                            }
                        }
                    });
                    </script>

                    <?php 
        include './config/footer.php';
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
                    <?php include './common_service/loaduser.php';?>
                    <script src="date.js"></script>

                    <script>
                    document.querySelectorAll('.send-sms-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const button = this;
                            const originalHTML = button.innerHTML;

                            // Ẩn nút và thêm thông báo
                            button.style.display = "none";
                            const tempMsg = document.createElement('span');
                            tempMsg.innerHTML = '<i class="fa-solid fa-check"></i> Đã gửi';
                            tempMsg.classList.add('text-muted', 'ms-2');
                            button.parentNode.insertBefore(tempMsg, button);

                            // Hiện lại nút sau 5 giây
                            setTimeout(() => {
                                tempMsg.remove();
                                button.style.display = "inline-block";
                                button.innerHTML =
                                    '<i class="fa-solid fa-share-from-square"></i> Gửi lại';
                            }, 5000); // 5 giây
                        });
                    });
                    </script>
</body>

</html>