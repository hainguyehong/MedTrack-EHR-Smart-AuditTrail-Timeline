<?php
include './config/connection.php';
include './common_service/common_functions.php';
isLogin([1]); // Chỉ admin (1) được phép truy cập

if (isset($_POST['save_user'])) {

    $displayName = trim($_POST['display_name'] ?? '');
    $userName    = trim($_POST['username'] ?? '');
    $password    = $_POST['password'] ?? '';
    $role        = $_POST['role'] ?? '';
    $hiddenId    = (int)($_POST['hidden_id'] ?? 0);

    // Bắt lỗi hidden_id không hợp lệ
    if ($hiddenId <= 0) {
        $_SESSION['error_message'] = "ID người dùng không hợp lệ.";
        header("Location: users.php");
        exit;
    }

    $errors = [];

    // VALIDATE
    if ($displayName === '') $errors['display_name'] = "Vui lòng nhập tên hiển thị!";
    if ($userName === '') $errors['username'] = "Vui lòng nhập tên đăng nhập!";
    if ($password !== '' && strlen($password) < 6) $errors['password'] = "Mật khẩu tối thiểu 6 ký tự!";
    if ($role === '') $errors['role'] = "Vui lòng chọn vai trò!";

    // Kiểm tra trùng tên đăng nhập (không tính chính user hiện tại)
    if ($userName !== '') {
        $stmt = $con->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE user_name = :user_name AND id != :id AND is_deleted = 0
        ");
        $stmt->execute([':user_name' => $userName, ':id' => $hiddenId]);
        if ($stmt->fetchColumn() > 0) {
            $errors['username'] = "Tên đăng nhập đã tồn tại!";
        }
    }

    // Nếu có lỗi → lưu session, redirect, exit ngay
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: update_user.php?user_id=" . $hiddenId);
        exit;
    }

    // Không có lỗi → cập nhật
    try {
        $stmtOld = $con->prepare("SELECT * FROM users WHERE id = ?");
        $stmtOld->execute([$hiddenId]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            $_SESSION['error_message'] = "Không tìm thấy người dùng.";
            header("Location: users.php");
            exit;
        }

        $encryptedPassword = ($password !== '') ? md5($password) : $oldData['password'];

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

        // Ghi log audit
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
        $_SESSION['success_message'] = "Cập nhật người dùng thành công.";

    } catch (PDOException $ex) {
    $con->rollBack();

    // Lỗi trùng username (UNIQUE KEY)
    if ($ex->getCode() == 23000) {
        $_SESSION['error_message'] = "Tên đăng nhập đã tồn tại!";
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi cập nhật người dùng. Vui lòng thử lại!";
    }
}


    header("Location: update_user.php?user_id=" . $hiddenId);
    exit;
}

// Lấy dữ liệu user để hiển thị
if (isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
} elseif (isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
} else {
    header("Location: users.php");
    exit;
}

$stmtUser = $con->prepare("SELECT id, display_name, user_name, role FROM users WHERE id = :id");
$stmtUser->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmtUser->execute();
$row = $stmtUser->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './config/site_css_links.php';?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <title>Chỉnh sửa người dùng - MedTrack-EHR</title>
    <style>
        body { background: #f8fafc; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .card-header { background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%); color: #fff; border-radius: 12px 12px 0 0; }
        .btn-primary, .btn-danger { border-radius: 20px; transition: 0.2s; }
        .btn-primary:hover, .btn-danger:hover { filter: brightness(1.1); box-shadow: 0 2px 8px rgba(0,123,255,0.15); }
        .form-control, .form-select { border-radius: 8px; }
        .card-title { font-weight: 600; letter-spacing: 0.5px; }
        label { font-weight: 500; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
<div class="wrapper">
    <?php include './config/header.php'; include './config/sidebar.php';?>
    <div class="content-wrapper">
        <section class="content">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-pen-to-square"></i> CHỈNH SỬA NGƯỜI DÙNG</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="hidden_id" value="<?= htmlspecialchars($user_id) ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tên hiển thị <span class="required">*</span></label>
                                <input type="text" name="display_name" class="form-control form-control-sm w-100"
                                       value="<?= $_SESSION['old']['display_name'] ?? $row['display_name'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tên đăng nhập <span class="required">*</span></label>
                                <input type="text" name="username" class="form-control form-control-sm w-100"
                                       value="<?= $_SESSION['old']['username'] ?? $row['user_name'] ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mật khẩu <small class="text-muted">(để trống nếu không đổi)</small></label>
                                <input type="password" name="password" class="form-control form-control-sm w-100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Vai trò <span class="required">*</span></label>
                                <select name="role" class="form-control form-control-sm w-100">
                                    <?= getRoles($_SESSION['old']['role'] ?? $row['role']); ?>
                                </select>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" name="save_user" class="btn btn-primary btn-sm px-4">
                                    <i class="fa-solid fa-pen-to-square"></i> CẬP NHẬT
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <?php include './config/footer.php'; ?>
</div>

<?php include './config/site_js_links.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: '<?= addslashes($_SESSION['success_message']) ?>',
            showConfirmButton: false,
            timer: 1200
        }).then(() => { window.location.href = 'users.php'; });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])): ?>
        <?php
        $errors = $_SESSION['form_errors'];
        unset($_SESSION['form_errors']);
        unset($_SESSION['old']);

        // Nếu chỉ lỗi tên đăng nhập, ưu tiên hiển thị riêng
        if (isset($errors['username']) && $errors['username'] === "Tên đăng nhập đã tồn tại!") {
            $popupMessage = $errors['username'];
        } else {
            $popupMessage = implode("<br>", $errors);
        }
        ?>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            html: '<?= addslashes($popupMessage) ?>',
            showConfirmButton: true
        });
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            html: '<?= addslashes($_SESSION['error_message']) ?>',
            showConfirmButton: true
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
});
</script>
</body>
</html> 