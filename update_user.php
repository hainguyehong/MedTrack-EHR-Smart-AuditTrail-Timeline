<?php
include './config/connection.php';
include './common_service/common_functions.php';
isLogin([1]); // Chỉ admin (1) được phép truy cập
$message = '';



// if (isset($_POST['save_user'])) {
//     $displayName = trim($_POST['display_name']);
//     $userName = trim($_POST['username']);
//     $password = $_POST['password'];
//     $role = $_POST['role'];
//     $hiddenId = $_POST['hidden_id'];

//     try {
//         // Lấy dữ liệu cũ để log
//         $stmtOld = $con->prepare("SELECT * FROM `users` WHERE `id` = :id");
//         $stmtOld->execute([':id' => $hiddenId]);
//         $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

//         if (!$oldData) {
//             $_SESSION['error_message'] = 'Không tìm thấy người dùng để cập nhật.';
//             header("Location: users.php");
//             exit();
//         }

//         // Chuẩn bị dữ liệu cập nhật
//         $encryptedPassword = !empty($password) ? md5($password) : $oldData['password'];

//         // Bắt đầu transaction
//         $con->beginTransaction();

//         // ✅ Thực hiện cập nhật
//         $updateQuery = "
//             UPDATE `users`
//             SET `display_name` = :display_name,
//                 `user_name` = :user_name,
//                 `password` = :password,
//                 `role` = :role
//             WHERE `id` = :id
//         ";

//         $stmtUpdate = $con->prepare($updateQuery);
//         $stmtUpdate->execute([
//             ':display_name' => $displayName,
//             ':user_name' => $userName,
//             ':password' => $encryptedPassword,
//             ':role' => $role,
//             ':id' => $hiddenId
//         ]);

//         // ✅ Ghi log audit
//         if (function_exists('log_audit')) {
//             log_audit(
//                 $con,
//                 $_SESSION['user_id'] ?? 'unknown',
//                 'users',
//                 $hiddenId,
//                 'update',
//                 $oldData, // dữ liệu cũ
//                 [
//                     'display_name' => $displayName,
//                     'user_name' => $userName,
//                     'role' => $role
//                 ]
//             );
//         }

//         $con->commit();
//         $_SESSION['success_message'] = 'Cập nhật người dùng thành công.';

//     } catch (PDOException $ex) {
//         $con->rollBack();
//         $_SESSION['error_message'] = "Lỗi khi cập nhật: " . $ex->getMessage();
//     }

//     header("Location: users.php");
//     exit();
// }
if (isset($_POST['save_user'])) {

    $errors = [];

    $displayName = trim($_POST['display_name'] ?? '');
    $userName    = trim($_POST['username'] ?? '');
    $password    = $_POST['password'] ?? '';
    $role        = $_POST['role'] ?? '';
    $hiddenId    = (int)($_POST['hidden_id'] ?? 0);

    // ===== VALIDATE TÊN HIỂN THỊ =====
    if ($displayName === '') {
        $errors['display_name'] = 'Vui lòng nhập tên hiển thị!';
    } elseif (mb_strlen($displayName) < 3) {
        $errors['display_name'] = 'Tên hiển thị phải từ 3 ký tự!';
    }

    // ===== VALIDATE TÊN ĐĂNG NHẬP =====
    if ($userName === '') {
        $errors['username'] = 'Vui lòng nhập tên đăng nhập!';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,30}$/', $userName)) {
        $errors['username'] = 'Tên đăng nhập 4–30 ký tự, chỉ chữ, số, _';
    } else {
        // ❗ Không được trùng user khác (trừ chính nó)
        $stmt = $con->prepare("
            SELECT COUNT(*) FROM users 
            WHERE user_name = ? AND id != ? AND is_deleted = 0
        ");
        $stmt->execute([$userName, $hiddenId]);
        if ($stmt->fetchColumn() > 0) {
            $errors['username'] = 'Tên đăng nhập đã tồn tại!';
        }
    }

    // ===== VALIDATE MẬT KHẨU (KHÔNG BẮT BUỘC) =====
    if ($password !== '' && strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu tối thiểu 6 ký tự!';
    }

    // ===== VALIDATE ROLE =====
    if ($role === '') {
        $errors['role'] = 'Vui lòng chọn vai trò!';
    } elseif (!in_array($role, ['1','2','3'], true)) {
        $errors['role'] = 'Vai trò không hợp lệ!';
    }

    // ===== CÓ LỖI → QUAY LẠI FORM UPDATE =====
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: update_user.php?user_id=".$hiddenId);
        exit;
    }

    // ===== KHÔNG LỖI → UPDATE =====
    try {
        // Lấy dữ liệu cũ
        $stmtOld = $con->prepare("SELECT * FROM users WHERE id = ?");
        $stmtOld->execute([$hiddenId]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            $_SESSION['error_message'] = 'Không tìm thấy người dùng.';
            header("Location: users.php");
            exit;
        }

        $encryptedPassword = ($password !== '')
            ? md5($password)
            : $oldData['password'];

        $con->beginTransaction();

        $stmtUpdate = $con->prepare("
            UPDATE users SET
                display_name = :display_name,
                user_name = :user_name,
                password = :password,
                role = :role
            WHERE id = :id
        ");

        $stmtUpdate->execute([
            ':display_name' => $displayName,
            ':user_name' => $userName,
            ':password' => $encryptedPassword,
            ':role' => $role,
            ':id' => $hiddenId
        ]);

        // Audit log
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'users',
                $hiddenId,
                'update',
                $oldData,
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
        $_SESSION['error_message'] = 'Lỗi khi cập nhật: ' . $ex->getMessage();
    }

    header("Location: users.php");
    exit;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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
                            <!-- <h1>Users</h1> -->
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
                            <i class="fa-solid fa-pen-to-square"></i>
                           CHỈNH SỬA THÔNG TIN NGƯỜI DÙNG
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="hidden_id" value="<?= $user_id ?>">

                            <div class="row">

                                <!-- Tên hiển thị -->
                                <div class="col-md-6 mb-3">
                                    <label>
                                        Tên hiển thị <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="display_name"
                                        class="form-control form-control-sm
                                        <?= isset($_SESSION['form_errors']['display_name']) ? 'is-invalid' : '' ?>"
                                        value="<?= $_SESSION['old']['display_name'] ?? $row['display_name'] ?>">

                                    <?php if (isset($_SESSION['form_errors']['display_name'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $_SESSION['form_errors']['display_name'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Tên đăng nhập -->
                                <div class="col-md-6 mb-3">
                                    <label>
                                        Tên đăng nhập <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="username"
                                        class="form-control form-control-sm
                                        <?= isset($_SESSION['form_errors']['username']) ? 'is-invalid' : '' ?>"
                                        value="<?= $_SESSION['old']['username'] ?? $row['user_name'] ?>">

                                    <?php if (isset($_SESSION['form_errors']['username'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $_SESSION['form_errors']['username'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Mật khẩu -->
                                <div class="col-md-6 mb-3">
                                    <label>
                                        Mật khẩu
                                        <small class="text-muted">(để trống nếu không đổi)</small>
                                    </label>
                                    <input type="password" name="password"
                                        class="form-control form-control-sm
                                        <?= isset($_SESSION['form_errors']['password']) ? 'is-invalid' : '' ?>">

                                    <?php if (isset($_SESSION['form_errors']['password'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $_SESSION['form_errors']['password'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Vai trò -->
                                <div class="col-md-6 mb-3">
                                    <label>
                                        Vai trò <span class="text-danger">*</span>
                                    </label>
                                    <select name="role"
                                        class="form-control form-control-sm
                                        <?= isset($_SESSION['form_errors']['role']) ? 'is-invalid' : '' ?>">
                                        <?php echo getRoles($_SESSION['old']['role'] ?? $row['role']); ?>
                                    </select>

                                    <?php if (isset($_SESSION['form_errors']['role'])): ?>
                                        <div class="invalid-feedback">
                                            <?= $_SESSION['form_errors']['role'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <!-- BUTTON -->
                            <div class="row mt-4">
                                <div class="col-12 text-center">
                                    <button type="submit" name="save_user"
                                        class="btn btn-primary btn-sm px-3">
                                        <i class="fa-solid fa-pen-to-square"></i> CẬP NHẬT
                                    </button>
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
    $(document).ready(function () {

        // Khi focus hoặc nhập → chỉ xoá lỗi của ô đó
        $('input, select').on('focus input change', function () {
            $(this).removeClass('is-invalid');
            $(this).closest('.mb-3').find('.invalid-feedback').hide();
        });

    });
    </script>
</body>

</html>