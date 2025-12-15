<?php 
include './config/connection.php';
include './common_service/common_functions.php';

islogin([1]); // chỉ cho admin (1) truy cập

if (isset($_POST['save_user'])) {

    $errors = [];

    $displayName = trim($_POST['display_name'] ?? '');
    $userName    = trim($_POST['user_name'] ?? '');
    $password    = $_POST['password'] ?? '';
    $role        = $_POST['role'] ?? '';

    // ===== VALIDATE TÊN HIỂN THỊ =====
    if ($displayName === '') {
        $errors['display_name'] = "Vui lòng nhập tên hiển thị!";
    } elseif (mb_strlen($displayName) < 3) {
        $errors['display_name'] = "Tên hiển thị phải từ 3 ký tự!";
    }

    // ===== VALIDATE TÊN ĐĂNG NHẬP =====
    if ($userName === '') {
    $errors['user_name'] = "Vui lòng nhập tên đăng nhập!";
} elseif (!preg_match('/^[a-zA-Z0-9_]{4,30}$/', $userName)) {
    $errors['user_name'] = "Tên đăng nhập 4–30 ký tự, chỉ chữ, số, _!";
} else {
    // Check trùng username
    $stmt = $con->prepare(
        "SELECT COUNT(*) FROM users WHERE user_name = ? AND is_deleted = 0"
    );
    $stmt->execute([$userName]);

    if ($stmt->fetchColumn() > 0) {
        $errors['user_name'] = "Tên đăng nhập đã tồn tại!";
    }


    }

    // ===== VALIDATE MẬT KHẨU =====
    if ($password === '') {
        $errors['password'] = "Vui lòng nhập mật khẩu!";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Mật khẩu tối thiểu 6 ký tự!";
    }

    // ===== VALIDATE ROLE =====
    if ($role === '') {
        $errors['role'] = "Vui lòng chọn vai trò!";
    } elseif (!in_array($role, ['1','2','3'], true)) {
        $errors['role'] = "Vai trò không hợp lệ!";
    }

    // ===== CÓ LỖI → QUAY LẠI FORM =====
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: users.php");
        exit;
    }

    $encryptedPassword = md5($password);

    try {
        $con->beginTransaction();

        // Thêm user mới
        $query = "INSERT INTO `users` (`display_name`, `user_name`, `role`, `password`) 
                  VALUES (:display_name, :user_name, :role, :password)";
        $stmtUser = $con->prepare($query);
        $stmtUser->execute([
            ':display_name' => $displayName,
            ':user_name' => $userName,
            ':role' => $role,
            ':password' => $encryptedPassword
        ]);

        // Ghi log audit
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'users',
                $con->lastInsertId(),
                'insert',
                null,
                [
                    'display_name' => $displayName,
                    'user_name' => $userName,
                    'role' => $role
                ]
            );
        }

        $con->commit();
        $_SESSION['success_message'] = 'Tài khoản đã được tạo thành công.';
    }  catch (PDOException $ex) {
    $con->rollBack();

    // Lỗi trùng username
    if ($ex->getCode() == 23000) {
        $_SESSION['error_message'] = "Tên đăng nhập đã tồn tại!";
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi tạo tài khoản. Vui lòng thử lại!";
    }
}


    header("Location: users.php");
    exit();
}

// Pagination
$perPage = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$serialStart = $offset + 1;

// Count total users
try {
    $countSql = "SELECT COUNT(*) FROM `users` WHERE `is_deleted` = 0";
    $stmtCount = $con->prepare($countSql);
    $stmtCount->execute();
    $totalUsers = (int)$stmtCount->fetchColumn();
    $totalPages = ($totalUsers > 0) ? (int)ceil($totalUsers / $perPage) : 1;
} catch (PDOException $ex) {
    $totalUsers = 0;
}

// Fetch users
try {
    $queryUsers = "SELECT `id`, `display_name`, `user_name`, `role` 
                   FROM `users` 
                   WHERE `is_deleted` = 0 
                   ORDER BY `role` ASC
                   LIMIT :limit OFFSET :offset";
    $stmtUsers = $con->prepare($queryUsers);
    $stmtUsers->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmtUsers->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmtUsers->execute();
    $users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $ex) {
    exit($ex->getMessage());
}

$sn = $serialStart;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include './config/site_css_links.php';?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <?php include './config/data_tables_css.php';?>
    <title>Users - MedTrack-EHR</title>
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

        <!-- Thêm user -->
        <section class="content">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-user-plus"></i> THÊM MỚI TÀI KHOẢN</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tên hiển thị <span class="required">*</span></label>
                                <input type="text" name="display_name" class="form-control form-control-sm w-100"
                                       value="<?= $_SESSION['old']['display_name'] ?? '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tên đăng nhập <span class="required">*</span></label>
                                <input type="text" name="user_name" class="form-control form-control-sm w-100"
                                       value="<?= $_SESSION['old']['user_name'] ?? '' ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mật khẩu <span class="required">*</span></label>
                                <input type="password" name="password" class="form-control form-control-sm w-100">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chọn vai trò <span class="required">*</span></label>
                                <select name="role" class="form-control form-control-sm w-100">
                                    <?php echo getRoles($_SESSION['old']['role'] ?? null); ?>
                                </select>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" name="save_user" class="btn btn-primary btn-sm px-4">
                                    <i class="fa-solid fa-floppy-disk"></i> LƯU
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Danh sách user -->
        <section class="content">
            <div class="card card-outline card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-list"></i> DANH SÁCH TÀI KHOẢN</h3>
                </div>
                <div class="card-body table-responsive">
                    <table id="all_users" class="table table-striped dataTable table-bordered">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Vai trò</th>
                                <th>Tên hiển thị</th>
                                <th>Tên đăng nhập</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($users)) { ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không tìm thấy tài khoản nào!</td>
                            </tr>
                        <?php } else {
                            foreach($users as $row) { ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td>
                                    <?php
                                    if ($row['role'] == '1') echo '<span class="badge badge-primary">Admin</span>';
                                    elseif ($row['role'] == '2') echo '<span class="badge badge-success">Bác Sĩ</span>';
                                    elseif ($row['role'] == '3') echo '<span class="badge badge-info">Bệnh Nhân</span>';
                                    else echo '<span class="badge badge-secondary">Chưa phân vai trò</span>';
                                    ?>
                                </td>
                                <td><?= $row['display_name'] ?></td>
                                <td><?= $row['user_name'] ?></td>
                                <td>
                                    <form method="post" action="update_user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>
                                    </form>
                                    <form method="post" action="delete_user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php }} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <?php include './config/footer.php'; ?>
</div>

<?php include './config/site_js_links.php'; ?>
<?php include './config/data_tables_js.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    $('#all_users').DataTable({
        paging: false,
        info: false,
        lengthChange: false,
        searching: true,
        ordering: false,
        language: {
            search: "Tìm kiếm tài khoản:",
            zeroRecords: "Không tìm thấy tài khoản phù hợp",
            emptyTable: "Không có dữ liệu"
        }
    });

    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: '<?= addslashes($_SESSION['success_message']) ?>',
            showConfirmButton: false,
            timer: 1200
        }).then(() => {
            window.location.href = window.location.href; // reload page
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message']) || (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors']))): ?>
        <?php
        $popupMessage = '';
        if (isset($_SESSION['error_message'])) {
            $popupMessage = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        } elseif (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
            $errors = $_SESSION['form_errors'];
            unset($_SESSION['form_errors']);
            unset($_SESSION['old']);
            
            $emptyFields = array_filter([
                $errors['display_name'] ?? '',
                $errors['user_name'] ?? '',
                $errors['password'] ?? '',
                $errors['role'] ?? ''
            ], fn($v) => in_array($v, [
                "Vui lòng nhập tên hiển thị!",
                "Vui lòng nhập tên đăng nhập!",
                "Vui lòng nhập mật khẩu!",
                "Vui lòng chọn vai trò!"
            ]));

            if (!empty($emptyFields)) {
                $popupMessage = "Vui lòng điền đầy đủ thông tin!";
            } else {
                $popupMessage = implode("<br>", $errors);
            }
        }
        ?>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            html: '<?= addslashes($popupMessage) ?>',
            showConfirmButton: true
        });
    <?php endif; ?>
});
</script>


</body>
</html>
