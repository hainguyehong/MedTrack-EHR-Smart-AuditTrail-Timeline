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
        // Bắt đầu transaction
        $con->beginTransaction();

        // 🔍 Lấy dữ liệu cũ (để ghi log)
        $queryOld = "SELECT * FROM `patients` WHERE `id` = :id";
        $stmtOld = $con->prepare($queryOld);
        $stmtOld->execute([':id' => $id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            throw new Exception("Không tìm thấy bệnh nhân với ID $id.");
        }

        $cnic = $oldData['cnic'];

        // 🧩 Soft delete bệnh nhân
        $queryPatient = "UPDATE `patients` 
                         SET `is_deleted` = 1 
                         WHERE `id` = :id";
        $stmtPatient = $con->prepare($queryPatient);
        $stmtPatient->execute([':id' => $id]);

        // 🧩 Soft delete các lần khám
        $queryVisit = "UPDATE `patient_visits` 
                       SET `is_deleted` = 1 
                       WHERE `patient_id` = :id";
        $stmtVisit = $con->prepare($queryVisit);
        $stmtVisit->execute([':id' => $id]);

        // 🧩 Soft delete user bệnh nhân (dựa vào cnic)
        $queryUser = "UPDATE `user_patients` 
                      SET `is_deleted` = 1 
                      WHERE `user_name` = :cnic";
        $stmtUser = $con->prepare($queryUser);
        $stmtUser->execute([':cnic' => $cnic]);

        // ✅ Ghi log audit (chỉ ghi nếu hàm log_audit tồn tại)
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'patients',
                $id,
                'delete',
                $oldData, // giá trị trước khi xóa
                ['is_deleted' => 1]
            );
        }

        $con->commit();

        $_SESSION['success_message'] = 'Bệnh nhân đã được xóa (soft delete) thành công.';
    } catch (Exception $ex) {
        $con->rollBack();
        $_SESSION['error_message'] = "Lỗi khi xóa bệnh nhân: " . $ex->getMessage();
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

<head>
    <?php include './config/site_css_links.php';?>
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
                            <h1>Xóa Bệnh Nhân</h1>
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
                            Xoá bệnh nhân
                        </h3>
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
                                    <label>Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" name="patient_name" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['patient_name'];?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['address'];?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['cnic'];?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Ngày sinh</label>
                                    <input type="text" id="date_of_birth" name="date_of_birth"
                                        class="form-control form-control-sm"
                                        value="<?php echo (!empty($dob)) ? date('d/m/Y', strtotime($dob)) : ''; ?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['phone_number'];?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <select class="form-control form-control-sm" id="gender" name="gender" disabled>
                                        <?php echo getGender($gender);?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12" style="margin-top:20px;">
                                    <button type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal"
                                        data-target="#confirmDeleteModal">Xoá</button>
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
    <script>
    showMenuSelected("#mnu_patients", "#mi_patients");
    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }
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