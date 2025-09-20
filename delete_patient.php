<?php
include './config/connection.php';
include './common_service/common_functions.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Kiểm tra quyền (chỉ role 1 hoặc 2 mới được xoá)
if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2) {
    die("Bạn không có quyền xoá bệnh nhân.");
}
$message = '';
// if (isset($_POST['delete_Patient'])) {
//   $id = $_POST['hidden_id'];
// try {

//   $con->beginTransaction();
  
//      // Soft delete bệnh nhân
//         $query = "UPDATE `patients` SET `is_deleted` = 1 WHERE `id` = :id";
//         $stmtPatient = $con->prepare($query);
//         $stmtPatient->bindParam(':id', $id, PDO::PARAM_INT);
//         $stmtPatient->execute();

//         // Soft delete các lần khám của bệnh nhân
//         $queryVisit = "UPDATE `patient_visits` SET `is_deleted` = 1 WHERE `patient_id` = :id";
//         $stmtVisit = $con->prepare($queryVisit);
//         $stmtVisit->bindParam(':id', $id, PDO::PARAM_INT);
//         $stmtVisit->execute();
        
//         // Soft delete user bệnh nhân
//         $queryUser = "UPDATE `user_patients` SET `is_deleted` = 1 WHERE `user_name` = :cnic";
//         $stmtUser = $con->prepare($queryUser);
//         $stmtUser->bindParam(':cnic', $cnic, PDO::PARAM_STR);
//         $stmtUser->execute();

//     $con->commit();
//     // $message = 'Bệnh nhân đã được xoá (soft delete).';
//     $_SESSION['success_message'] = 'Bệnh nhân đã được xoá (soft delete).';

// } catch(PDOException $ex) {
//   $con->rollback();

//   echo $ex->getMessage();
//   echo $ex->getTraceAsString();
//   exit;
// }

//     header("Location: patients.php"); // quay về trang danh sách
//     exit();
// }
if (isset($_POST['delete_Patient'])) {
    $id = $_POST['hidden_id'];

    try {
        $con->beginTransaction();

        // Lấy cnic của bệnh nhân để xóa user
        $queryGet = "SELECT cnic FROM `patients` WHERE `id` = :id";
        $stmtGet = $con->prepare($queryGet);
        $stmtGet->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtGet->execute();
        $patient = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $cnic = $patient['cnic'];

            // Soft delete bệnh nhân
            $query = "UPDATE `patients` SET `is_deleted` = 1 WHERE `id` = :id";
            $stmtPatient = $con->prepare($query);
            $stmtPatient->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtPatient->execute();

            // Soft delete các lần khám của bệnh nhân
            $queryVisit = "UPDATE `patient_visits` SET `is_deleted` = 1 WHERE `patient_id` = :id";
            $stmtVisit = $con->prepare($queryVisit);
            $stmtVisit->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtVisit->execute();

            // Soft delete user bệnh nhân (dựa vào cnic)
            $queryUser = "UPDATE `user_patients` SET `is_deleted` = 1 WHERE `user_name` = :cnic";
            $stmtUser = $con->prepare($queryUser);
            $stmtUser->bindParam(':cnic', $cnic, PDO::PARAM_STR);
            $stmtUser->execute();
        }

        $con->commit();
        $_SESSION['success_message'] = 'Bệnh nhân đã được xoá (soft delete).';
    } catch(PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }

    header("Location: patients.php"); // quay về trang danh sách
    exit();
}



try {
$id = $_GET['id'];
$query = "SELECT `id`, `patient_name`, `address`, 
`cnic`, date_format(`date_of_birth`, '%m/%d/%Y') as `date_of_birth`,  `phone_number`, `gender` 
FROM `patients` where `id` = $id;";

  $stmtPatient1 = $con->prepare($query);
  $stmtPatient1->execute();
  $row = $stmtPatient1->fetch(PDO::FETCH_ASSOC);

  $gender = $row['gender'];

$dob = $row['date_of_birth']; 
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

    <?php include './config/data_tables_css.php';?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Delete Pateint Details - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>

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
                            <h1>Xóa Bệnh Nhân</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Xóa Bệnh Nhân</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" id="deleteForm">
                            <input type="hidden" name="hidden_id" value="<?php echo $row['id'];?>">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên Bệnh Nhân</label>
                                    <input type="text" id="patient_name" name="patient_name" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['patient_name'];?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa Chỉ</label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['address'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['cnic'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày Sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                value="<?php echo (!empty($dob)) ? date('d/m/Y', strtotime($dob)) : ''; ?>" />
                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số Điện Thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['phone_number'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới Tính</label>
                                    <!-- $gender -->

                                    <select class="form-control form-control-sm rounded-0" id="gender" name="gender">
                                        <?php echo getGender($gender);?>
                                    </select>

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <button type="button" class="btn btn-danger btn-sm btn-flat btn-block"
                                        data-toggle="modal" data-target="#confirmDeleteModal">
                                        Xóa
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>

                </div>

            </section>
            <br />
            <br />
            <br />


            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php 
 include './config/footer.php';

$message = '';
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
        }
?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

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


    // $(function() {
    //     $("#all_patients").DataTable({
    //         "responsive": true,
    //         "lengthChange": false,
    //         "autoWidth": false,
    //         "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    //     }).buttons().container().appendTo('#all_patients_wrapper .col-md-6:eq(0)');

    // });
    </script>

    <!-- Modal xác nhận xoá -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Xác nhận xoá</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xoá bệnh nhân <strong><?php echo $row['patient_name']; ?></strong> không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Huỷ</button>
                    <!-- Nút xác nhận xoá sẽ submit form -->
                    <button type="submit" form="deleteForm" name="delete_Patient"
                        class="btn btn-danger btn-sm">Xoá</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>