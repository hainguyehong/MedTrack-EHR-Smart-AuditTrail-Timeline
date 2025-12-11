<?php
include './config/connection.php';
include './common_service/common_functions.php';
isLogin([1]); // Chỉ admin (1) được phép truy cập
$message = '';



if (isset($_POST['save_user'])) {
    $displayName = trim($_POST['display_name']);
    $userName = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $hiddenId = $_POST['hidden_id'];

    try {
        // Lấy dữ liệu cũ để log
        $stmtOld = $con->prepare("SELECT * FROM `users` WHERE `id` = :id");
        $stmtOld->execute([':id' => $hiddenId]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            $_SESSION['error_message'] = 'Không tìm thấy người dùng để cập nhật.';
            header("Location: users.php");
            exit();
        }

        // Chuẩn bị dữ liệu cập nhật
        $encryptedPassword = !empty($password) ? md5($password) : $oldData['password'];

        // Bắt đầu transaction
        $con->beginTransaction();

        // ✅ Thực hiện cập nhật
        $updateQuery = "
            UPDATE `users`
            SET `display_name` = :display_name,
                `user_name` = :user_name,
                `password` = :password,
                `role` = :role
            WHERE `id` = :id
        ";

        $stmtUpdate = $con->prepare($updateQuery);
        $stmtUpdate->execute([
            ':display_name' => $displayName,
            ':user_name' => $userName,
            ':password' => $encryptedPassword,
            ':role' => $role,
            ':id' => $hiddenId
        ]);

        // ✅ Ghi log audit
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'users',
                $hiddenId,
                'update',
                $oldData, // dữ liệu cũ
                [
                    'display_name' => $displayName,
                    'user_name' => $userName,
                    'role' => $role
                ]
            );
        }

        $con->commit();
        $_SESSION['success_message'] = 'Cập nhật người dùng thành công.';

    } catch (PDOException $ex) {
        $con->rollBack();
        $_SESSION['error_message'] = "Lỗi khi cập nhật: " . $ex->getMessage();
    }

    header("Location: users.php");
    exit();
}
try {
    if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
} elseif (isset($_GET['user_id'])) { // fallback nếu ai tự gõ link
    $user_id = (int)$_GET['user_id'];
} else {
    header('Location: users.php'); // trang danh sách user
    exit;
}


$query = "SELECT `id`, `display_name`, `user_name`, `role` FROM `users` WHERE `id` = :id";
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
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
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
                            <h1>Users</h1>
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
                            Chỉnh sửa thông tin người dùng
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="hidden_id" value="<?php echo $user_id;?>">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên hiển thị</label>
                                    <input type="text" id="display_name" name="display_name" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['display_name'];?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên đăng nhập</label>
                                    <input type="text" id="username" name="username" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['user_name'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Mật khẩu</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-sm" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Vai trò</label>
                                    <select name="role" id="role" class="form-control form-control-sm">
                                        <?php echo getRoles((int)$row['role']); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_user" name="save_user"
                                        class="btn btn-primary btn-sm btn-block">Cập nhật</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

        </div>

        </section>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <?php 
        include './config/footer.php';

        $message = '';
        $messageType = 'info';

        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            $messageType = 'success';
            unset($_SESSION['success_message']);
        } elseif (isset($_SESSION['error_message'])) {
            $message = $_SESSION['error_message'];
            $messageType = 'error';
            unset($_SESSION['error_message']);
        }
    ?>

        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <script>
    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }
    </script>
</body>

</html>