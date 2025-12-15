<?php
include './config/connection.php';
include './common_service/common_functions.php';
islogin([2]);

// Kiểm tra quyền (chỉ role 1 hoặc 2 mới được xoá)
if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2) {
    die("Bạn không có quyền xoá bệnh nhân.");
}
$message = '';
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
                 SET `is_deleted` = 1,
                     `deleted_at` = NOW()
                 WHERE `id` = :id";

        $stmtPatient = $con->prepare($queryPatient);
        $stmtPatient->execute([':id' => $id]);


        // 🧩 Soft delete các lần khám
        // $queryVisit = "UPDATE `patient_visits` 
        //                SET `is_deleted` = 1 
        //                WHERE `patient_id` = :id";
        // $stmtVisit = $con->prepare($queryVisit);
        // $stmtVisit->execute([':id' => $id]);

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
// Ưu tiên lấy id từ POST (khi click nút ở patients.php)
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
    }
    // Fallback: nếu ai đó vẫn truy cập kiểu GET cũ thì vẫn hoạt động
    elseif (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        // Không có id -> quay về danh sách
        header("Location: patients.php");
        exit;
    }

    if (empty($id)) {
        header("Location: patients.php");
        exit;
    }
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
                            <!-- <h1>Xóa Bệnh Nhân</h1> -->
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
                            <i class="fa-solid fa-user-slash"></i>
                            XÓA BỆNH NHÂN
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
                            <!-- <div class="clearfix">&nbsp;</div> -->
                            <!-- <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12" style="margin-top:20px;">
                                    <button type="button" class="btn btn-danger btn-sm btn-block" data-toggle="modal"
                                        data-target="#confirmDeleteModal"><i class="fa-solid fa-trash me-1"></i>Xoá</button>
                                </div>
                            </div> -->
                            <!-- WARNING -->
                            <div class="alert alert-warning mt-4">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Hành động này sẽ <strong>xoá bệnh nhân (soft delete)</strong> và không thể hoàn tác ngay
                                lập tức.
                            </div>
                            <div class="row mt-2">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-danger btn-sm px-4" data-toggle="modal"
                                        data-target="#confirmDeleteModal">
                                        <i class="fa-solid fa-trash mr-1"></i>
                                        Xoá
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
    <!-- <script>
    showMenuSelected("#mnu_patients", "#mi_patients");
    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message, 'success');
    }
    </script> -->
    <script>
showMenuSelected("#mnu_patients", "#mi_patients");

<?php if (!empty($message)) : ?>
Swal.fire({
    icon: 'success',
    title: 'Bệnh nhân đã được xóa (soft delete) thành công.',
    showConfirmButton: false,   // ❌ không có nút OK
    timer: 1200,                // ⏱ tự đóng sau 1.5 giây
    timerProgressBar: true
});
<?php endif; ?>
</script>


    <!-- Modal xác nhận xoá bệnh nhân -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <!-- Header -->
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-circle-exclamation mr-2"></i>
                        Xác nhận xoá bệnh nhân
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Body -->
                <div class="modal-body">
                    Bạn có chắc chắn muốn xoá bệnh nhân
                    <strong class="text-danger">
                        <?php echo $row['patient_name']; ?>
                    </strong>
                    không?
                    <br>
                    <small class="text-muted font-italic">
                        (Bệnh nhân sẽ bị đánh dấu xoá – không hiển thị trong hệ thống)
                    </small>
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                        <i class="fa-solid fa-xmark mr-1"></i>
                        HUỶ
                    </button>

                    <button type="submit" form="deleteForm" name="delete_Patient" class="btn btn-danger btn-sm px-3">
                        <i class="fa-solid fa-trash-can mr-1"></i>
                        XOÁ
                    </button>
                </div>

            </div>
        </div>
    </div>


</body>

</html>