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


try {
    
if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
} elseif (isset($_GET['user_id'])) { // fallback nếu ai tự gõ link
    $user_id = (int)$_GET['user_id'];
} else {
    header('Location: users.php'); // trang danh sách user
    exit;
}

$query = "SELECT `id`, `display_name`, `user_name`, `role` FROM `users` WHERE `id` = :id ";

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
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
    .btn-danger,
    .btn {
        border-radius: 20px;
        transition: 0.2s;
    }

    .btn-primary:hover,
    .btn-danger:hover,
    .btn:hover {
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

    .delete-note {
        font-size: 0.78rem;
        font-style: italic;
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
                            <!-- <h1>Xóa Người Dùng</h1> -->
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
                            <i class="fa-solid fa-user-slash mr-2"></i>
                            XÓA NGƯỜI DÙNG
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" id="deleteForm">
                            <input type="hidden" name="hidden_id" value="<?php echo $user_id; ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Tên hiển thị</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="<?php echo $row['display_name']; ?>" readonly>
                                </div>

                                <div class="col-md-6">
                                    <label>Tên đăng nhập</label>
                                    <input type="text" class="form-control form-control-sm"
                                        value="<?php echo $row['user_name']; ?>" readonly>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Mật khẩu</label>
                                    <input type="password" class="form-control form-control-sm"
                                        value="********" readonly>
                                </div>

                                <div class="col-md-6 mt-3">
                                    <label>Vai trò</label>
                                    <select class="form-control form-control-sm" disabled>
                                        <?php echo getRoles((int)$row['role']); ?>
                                    </select>
                                </div>
                            </div>

                            <!-- WARNING -->
                            <div class="alert alert-warning mt-4">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Hành động này sẽ <strong>xoá người dùng (soft delete)</strong> và không thể hoàn tác ngay lập tức.
                            </div>

                            <!-- BUTTON -->
                            <div class="text-center mt-4">
                                <button type="button"
                                    class="btn btn-danger btn-sm px-4"
                                    data-toggle="modal"
                                    data-target="#confirmDeleteModal">
                                    <i class="fa-solid fa-trash mr-1"></i> XOÁ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </section>
            <br />
            <br />
            <br />

            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
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
    showMenuSelected("#mnu_users", "#mi_users");
    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }
    </script>

    <!-- Modal xác nhận xoá -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Xác nhận xoá người dùng
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Bạn có chắc chắn muốn xoá người dùng
                    <strong class="text-danger">
                        <?php echo $row['user_name']; ?>
                    </strong> không?
                    <br>
                    <small class="text-muted delete-note">
                        (Người dùng sẽ bị đánh dấu xoá – không hiển thị trong hệ thống)
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-secondary btn-sm px-3"
                        data-dismiss="modal">
                        <i class="fa-solid fa-xmark mr-1"></i>HỦY
                    </button>

                    <button type="submit"
                        form="deleteForm"
                        name="delete_user"
                        class="btn btn-danger btn-sm px-3"> 
                        <i class="fa-solid fa-trash-can mr-1"></i>XÓA
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>