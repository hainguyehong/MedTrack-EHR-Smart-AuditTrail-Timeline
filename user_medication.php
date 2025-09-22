<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

$message = '';
$userId = $_SESSION['user_id']; // lấy id user sau khi login 
// echo "🔍 userId hiện tại: " . htmlspecialchars($userId) . "<br>";
// exit();
$query = "SELECT 
    up.id AS user_patient_id,
    up.user_name,
    up.display_name,
    up.role,
    up.created_at AS user_created_at,
    p.id AS patient_id,
    p.patient_name,
    p.address,
    p.cnic,
    DATE_FORMAT(p.date_of_birth, '%d %b %Y') AS date_of_birth,
    p.phone_number,
    p.gender,
    p.created_at AS patient_created_at
FROM user_patients AS up
JOIN patients AS p ON up.id_patient = p.id
WHERE up.is_deleted = 0 
  AND p.is_deleted = 0
  AND up.id = :user_id
LIMIT 1;";

$stmtPatient1 = $con->prepare($query);
$stmtPatient1->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmtPatient1->execute();

$patient = $stmtPatient1->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>

    <?php include './config/data_tables_css.php';?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>

</head>

<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
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
                            <h1>Bệnh Nhân</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin bệnh nhân</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" name="patient_name" readonly"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['patient_name'] ?? ''); ?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['address'] ?? ''); ?>" disabled />

                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" readonly"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['cnic'] ?? ''); ?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                value="<?php echo htmlspecialchars(date("d/m/Y", strtotime($patient['date_of_birth'] ?? ''))); ?>" />
                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" readonly"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['phone_number'] ?? ''); ?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <input type="text" id="gender" name="gender"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['gender'] ?? ''); ?>" />

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>

                                <!-- <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-flat btn-block">Lưu</button>
                                </div> -->
                            </div>
                        </form>
                    </div>

                </div>

            </section>

            </section>
            <br />
            <br />
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách đơn thuốc</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive">
                            <table id="all_patients" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="all_patients_info">

                                <thead style="text-align:center;">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên loại thuốc</th>
                                        <th>Số lượng</th>
                                        <th>Liều dùng</th>
                                        <th>Ghi chú</th>

                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
$query = "SELECT 
            up.id AS user_id,
            up.user_name,
            up.display_name,
            p.id AS patient_id,
            p.patient_name,

            pmh.id AS prescription_id,
            pmh.quantity,
            pmh.dosage,
            pmh.note,
            pmh.visit_date,
            pmh.next_visit_date,

            m.id AS medicine_id,
            m.medicine_name
        FROM user_patients AS up
        JOIN patients AS p ON up.id_patient = p.id
        JOIN patient_medication_history AS pmh ON pmh.patient_id = p.id
        JOIN medicines AS m ON pmh.medicine_id = m.id
        WHERE up.is_deleted = 0
          AND p.is_deleted = 0
          AND up.id = :user_id
        ORDER BY pmh.visit_date DESC";

$stmt = $con->prepare($query);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();

$prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($prescriptions)):
    $count = 0;
    foreach ($prescriptions as $row):
        $count++;
?>
                                    <tr style="text-align:center;">
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                                        <td><?php echo htmlspecialchars($row['note']); ?></td>
                                    </tr>
                                    <?php
    endforeach;
else:
?>
                                    <tr>
                                        <td colspan="5" style="text-align:center;">Chưa có đơn thuốc nào.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>

                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->


            </section>
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

        var message = '<?php echo $message;?>';
        if (message !== '') {
            showCustomMessage(message);
        }

        $('#date_of_birth').datetimepicker({
            format: 'L'
        });


        $(function() {
            $("#all_patients").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#all_patients_wrapper .col-md-6:eq(0)');

        });
        </script>
</body>

</html>