<?php
include './config/connection.php';
include './common_service/common_functions.php';
islogin([2]); // chỉ cho và bác sĩ (2) truy cập
$message = '';
// if (isset($_POST['save_Patient'])) {
  
//     $hiddenId = $_POST['hidden_id'];

//     $patientName = trim($_POST['patient_name']);
//     $address = trim($_POST['address']);
//     $cnic = trim($_POST['cnic']);
    
//     $dateBirth = !empty($_POST['date_of_birth']) 
//     ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['date_of_birth']))) 
//     : null;

//     $phoneNumber = trim($_POST['phone_number']);

//     $patientName = ucwords(strtolower($patientName));
//     $address = ucwords(strtolower($address));

//     $gender = $_POST['gender'];
// if ($patientName != '' && $address != '' && 
//   $cnic != '' && $dateBirth != '' && $phoneNumber != '' && $gender != '') {
//       $query = "update `patients` 
//     set `patient_name` = '$patientName', 
//     `address` = '$address', 
//     `cnic` = '$cnic', 
//     `date_of_birth` = '$dateBirth', 
//     `phone_number` = '$phoneNumber', 
//     `gender` = '$gender' 
// where `id` = $hiddenId;";
// try {

//   $con->beginTransaction();

//   $stmtPatient = $con->prepare($query);
//   $stmtPatient->execute();

//   $con->commit();

// //   $message = 'Cập Nhật Dữ liệu thành công.';
// $_SESSION['success_message'] = 'Cập nhật dữ liệu thành công.';

// } catch(PDOException $ex) {
//   $con->rollback();

//   echo $ex->getMessage();
//   echo $ex->getTraceAsString();
//   exit;
// }
// }
// //   header("Location:congratulation.php?goto_page=patients.php&message=$message");
// //   exit();
// header("Location: patients.php"); // quay lại thẳng patients.php
// exit();
// }
if (isset($_POST['save_Patient'])) {
    $hiddenId = $_POST['hidden_id'];

    $patientName = trim($_POST['patient_name']);
    $address = trim($_POST['address']);
    $cnic = trim($_POST['cnic']);
    $dateBirth = !empty($_POST['date_of_birth'])
        ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['date_of_birth'])))
        : null;
    $phoneNumber = trim($_POST['phone_number']);
    $gender = $_POST['gender'];

    // Chuẩn hóa chuỗi
    $patientName = ucwords(strtolower($patientName));
    $address = ucwords(strtolower($address));

    // Kiểm tra dữ liệu đầu vào
    if ($patientName != '' && $address != '' && $cnic != '' && $dateBirth != '' && $phoneNumber != '' && $gender != '') {
        try {
            // 🔹 1. Lấy dữ liệu cũ trước khi cập nhật
            $stmtOld = $con->prepare("SELECT * FROM patients WHERE id = ?");
            $stmtOld->execute([$hiddenId]);
            $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (!$oldData) {
                $_SESSION['error_message'] = "Không tìm thấy hồ sơ bệnh nhân để cập nhật!";
                header("Location: patients.php");
                exit();
            }

            // 🔹 2. Kiểm tra trùng CCCD / username trong user_patients
            $checkQuery = "SELECT COUNT(*) FROM user_patients 
                           WHERE user_name = :user_name AND id_patient != :id_patient";
            $stmtCheck = $con->prepare($checkQuery);
            $stmtCheck->execute([
                ':user_name' => $cnic,
                ':id_patient' => $hiddenId
            ]);
            $exists = $stmtCheck->fetchColumn();

            if ($exists > 0) {
                $_SESSION['error_message'] = "CCCD/Tên đăng nhập đã tồn tại. Vui lòng chọn giá trị khác.";
                header("Location: patients.php");
                exit();
            }

            $con->beginTransaction();

            // ✅ Cập nhật bảng patients
            $queryPatient = "UPDATE `patients` 
                SET `patient_name` = :patient_name, 
                    `address` = :address, 
                    `cnic` = :cnic, 
                    `date_of_birth` = :date_of_birth, 
                    `phone_number` = :phone_number, 
                    `gender` = :gender 
                WHERE `id` = :id";

            $stmtPatient = $con->prepare($queryPatient);
            $stmtPatient->execute([
                ':patient_name' => $patientName,
                ':address' => $address,
                ':cnic' => $cnic,
                ':date_of_birth' => $dateBirth,
                ':phone_number' => $phoneNumber,
                ':gender' => $gender,
                ':id' => $hiddenId
            ]);

            // ✅ Cập nhật bảng user_patients
            $queryUser = "UPDATE `user_patients` 
                SET `user_name` = :user_name, 
                    `display_name` = :display_name 
                WHERE `id_patient` = :id_patient";

            $stmtUser = $con->prepare($queryUser);
            $stmtUser->execute([
                ':user_name' => $cnic,            // CCCD làm username
                ':display_name' => $patientName, // tên hiển thị
                ':id_patient' => $hiddenId
            ]);

            // 🔹 4. Lấy dữ liệu mới sau khi cập nhật
            $stmtNew = $con->prepare("SELECT * FROM patients WHERE id = ?");
            $stmtNew->execute([$hiddenId]);
            $newData = $stmtNew->fetch(PDO::FETCH_ASSOC);

            // 🔹 5. Ghi log audit
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown', // người thao tác
                'patients',                        // bảng bị tác động
                $hiddenId,                         // id bản ghi
                'update',                          // hành động
                $oldData,                          // dữ liệu cũ
                $newData                           // dữ liệu mới
            );

            // 🔹 6. Commit transaction
            $con->commit();
            $_SESSION['success_message'] = 'Cập nhật dữ liệu thành công.';
        } catch (PDOException $ex) {
            $con->rollBack();
            $_SESSION['error_message'] = "Lỗi khi cập nhật: " . $ex->getMessage();
            header("Location: patients.php");
            exit;
        }
    }

    header("Location: patients.php");
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

</head>

<?php include './config/site_css_links.php';?>
<?php include './config/data_tables_css.php';?>
<!-- Thêm favicon -->
<link rel="icon" type="image/png" href="assets/images/img-tn.png">
<link rel="apple-touch-icon" href="assets/images/img-tn.png">
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
<style>
body {
    background: #f8fafc;
}

.card-primary.card-outline {
    border-top: 0px solid #007bff;
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

.table {
    background: #fff;
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
</style>

<head>

</head>

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
                            <h1>Bệnh Nhân</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#FFFFFF" style="vertical-align: middle; margin-right: 8px;">
                                <path
                                    d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80Zm-360-80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0-80Zm0 400Z" />
                            </svg>
                            Chỉnh sửa thông tin bệnh nhân
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="hidden_id" value="<?php echo $row['id'];?>">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" name="patient_name" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['patient_name'];?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['address'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['cnic'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" class="form-control form-control-sm datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                data-toggle="datetimepicker" autocomplete="off"
                                                value="<?php echo (!empty($dob)) ? date('d/m/Y', strtotime($dob)) : ''; ?>" />
                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['phone_number'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <select class="form-control form-control-sm" id="gender" name="gender" required>
                                        <?php echo getGender($gender);?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-block">Cập nhật</button>
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
  if(isset($_GET['message'])) {
    $message = $_GET['message'];
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
    </script>
</body>

</html>