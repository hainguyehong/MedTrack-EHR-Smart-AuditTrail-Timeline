<?php
include './config/connection.php';
include './common_service/common_functions.php';
isLogin([1]);

// Kiểm tra quyền (chỉ role 1 hoặc 2 mới được xoá)
if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2) {
    die("Bạn không có quyền xoá bệnh nhân.");
}
$message = '';
if (isset($_POST['delete_user'])) {
    $id = $_POST['hidden_id'];

    try {
        // Lấy dữ liệu cũ để lưu log
        $stmtOld = $con->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $stmtOld->execute([':id' => $id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            $_SESSION['error_message'] = 'Không tìm thấy người dùng cần xóa.';
            header("Location: users.php");
            exit();
        }

        $con->beginTransaction();

        // Soft delete user
        $query = "UPDATE `users` SET `is_deleted` = 1 WHERE `id` = :id";
        $stmtUser = $con->prepare($query);
        $stmtUser->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtUser->execute();

        // ✅ Ghi log audit
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'users',
                $id,
                'delete',
                $oldData, // giá trị trước khi xóa
                ['is_deleted' => 1]
            );
        }

        $con->commit();
        $_SESSION['success_message'] = 'Người dùng đã được xoá (soft delete).';

    } catch (PDOException $ex) {
        $con->rollBack();
        $_SESSION['error_message'] = "Lỗi khi xóa: " . $ex->getMessage();
    }

    header("Location: users.php");
    exit();
}


$user_id = $_GET['user_id'];

$query = "SELECT `id`, `display_name`, `user_name`, `role` FROM `users` WHERE `id` = :id ";

try {
  $stmtUpdateUser = $con->prepare($query);
  $stmtUpdateUser->bindParam(':id', $user_id, PDO::PARAM_INT);
  $stmtUpdateUser->execute();
  $row = $stmtUpdateUser->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $ex) {
  echo $ex->getTraceAsString();
  echo $ex->getMessage();
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <title>Người Dùng - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
                            <h1>Xóa Người Dùng</h1>
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
                            Xoá người dùng
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" id="deleteForm" enctype="multipart/form-data">
                            <input type="hidden" name="hidden_id" value="<?php echo $user_id;?>">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên hiển thị</label>
                                    <input type="text" id="display_name" name="display_name" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['display_name'];?>"
                                        readonly />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên đăng nhập</label>
                                    <input type="text" id="username" name="username" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['user_name'];?>"
                                        readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Mật khẩu</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-sm" value="********" readonly />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Vai trò</label>
                                    <select name="role" id="role" class="form-control form-control-sm" disabled>
                                        <?php echo getRoles((int)$row['role']); ?>
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
    showMenuSelected("#mnu_users", "#mi_users");
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
                    Bạn có chắc chắn muốn xoá người dùng này <strong><?php echo $row['user_name']; ?></strong> không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Huỷ</button>
                    <!-- Nút xác nhận xoá sẽ submit form -->
                    <button type="submit" form="deleteForm" name="delete_user"
                        class="btn btn-danger btn-sm">Xoá</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>