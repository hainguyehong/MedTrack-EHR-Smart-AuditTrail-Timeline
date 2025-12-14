<?php 
include './config/connection.php';
include './common_service/common_functions.php';
$message = '';
islogin([1]); // chỉ cho admin (1) truy cập
// if (isset($_POST['save_user'])) {
//     $displayName = trim($_POST['display_name']);
//     $userName = trim($_POST['user_name']);
//     $password = $_POST['password'];
//     $role = $_POST['role'];
//     $encryptedPassword = md5($password);
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
        $stmt = $con->prepare("SELECT COUNT(*) FROM users WHERE user_name = ? AND is_deleted = 0");
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

        // 🧩 Thêm user mới
        $query = "INSERT INTO `users` (`display_name`, `user_name`, `role`, `password`) 
                  VALUES (:display_name, :user_name, :role, :password)";
        $stmtUser = $con->prepare($query);
        $stmtUser->execute([
            ':display_name' => $displayName,
            ':user_name' => $userName,
            ':role' => $role,
            ':password' => $encryptedPassword
        ]);

        // ✅ Lấy ID user mới thêm
        $newUserId = $con->lastInsertId();

        // ✅ Ghi log audit
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',  // Người đang đăng nhập
                'users',                            // Bảng bị ảnh hưởng
                $newUserId,                         // ID user vừa thêm
                'insert',                           // Hành động
                null,                               // Không có dữ liệu cũ
                [                                   // Dữ liệu mới
                    'display_name' => $displayName,
                    'user_name' => $userName,
                    'role' => $role
                ]
            );
        }

        $con->commit();
        $_SESSION['success_message'] = 'Tài khoản đã được tạo thành công.';
    } catch (PDOException $ex) {
        $con->rollBack();
        $_SESSION['error_message'] = "Lỗi khi tạo tài khoản: " . $ex->getMessage();
    }

    header("Location: users.php");
    exit();
}

// REPLACE original queryUsers block with paginated query
$perPage = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$serialStart = $offset + 1;

try {
    // count total users
    $countSql = "SELECT COUNT(*) FROM `users` WHERE `is_deleted` = 0";
    $stmtCount = $con->prepare($countSql);
    $stmtCount->execute();
    $totalUsers = (int)$stmtCount->fetchColumn();
    $totalPages = ($totalUsers > 0) ? (int)ceil($totalUsers / $perPage) : 1;
} catch (PDOException $ex) {
    echo $ex->getTraceAsString();
    echo $ex->getMessage();
    $totalUsers = 0;
}


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
    echo $ex->getTraceAsString();
    echo $ex->getMessage();
    exit;
}

// set row serial counter
$sn = $serialStart;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">

    <?php include './config/data_tables_css.php';?>
     <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- <link rel="stylesheet" href="css/users.css"> -->
    <title>Users - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <style>
    body {
        background: #f8fafc;
    }

    .user-img {
        width: 3em;
        width: 3em;
        object-fit: cover;
        object-position: center center;
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

    .card-primary.card-outline {
        border-top: 0px solid #007bff;
    }

    .btn-primary:hover,
    .btn-danger:hover {
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
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
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        font-size: 13px;
    }
    .required {
        color: #dc3545;
        margin-left: 2px;
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
                            <!-- <h1>TÀI KHOẢN</h1> -->
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-user-plus"></i>THÊM MỚI TÀI KHOẢN</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                   <div class="card-body">

                    <form method="post">
                        <div class="row">

                            <!-- Tên hiển thị -->
                            <div class="col-md-6 mb-3">
                                <label>Tên hiển thị <span class="required">*</span></label>
                                <input type="text" name="display_name"
                                    class="form-control form-control-sm w-100
                                    <?= isset($_SESSION['form_errors']['display_name']) ? 'is-invalid' : '' ?>"
                                    value="<?= $_SESSION['old']['display_name'] ?? '' ?>">

                                <?php if (isset($_SESSION['form_errors']['display_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['form_errors']['display_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Tên đăng nhập -->
                            <div class="col-md-6 mb-3">
                                <label>Tên đăng nhập <span class="required">*</span></label>
                                <input type="text" name="user_name"
                                    class="form-control form-control-sm w-100
                                    <?= isset($_SESSION['form_errors']['user_name']) ? 'is-invalid' : '' ?>"
                                    value="<?= $_SESSION['old']['user_name'] ?? '' ?>">

                                <?php if (isset($_SESSION['form_errors']['user_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['form_errors']['user_name'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Mật khẩu -->
                            <div class="col-md-6 mb-3">
                                <label>Mật khẩu <span class="required">*</span></label>
                                <input type="password" name="password"
                                    class="form-control form-control-sm w-100
                                    <?= isset($_SESSION['form_errors']['password']) ? 'is-invalid' : '' ?>">

                                <?php if (isset($_SESSION['form_errors']['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['form_errors']['password'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Vai trò -->
                            <div class="col-md-6 mb-3">
                                <label>Chọn vai trò <span class="required">*</span></label>
                                <select name="role"
                                    class="form-control form-control-sm w-100
                                    <?= isset($_SESSION['form_errors']['role']) ? 'is-invalid' : '' ?>">
                                    <?php echo getRoles($_SESSION['old']['role'] ?? null); ?>
                                </select>

                                <?php if (isset($_SESSION['form_errors']['role'])): ?>
                                    <div class="invalid-feedback">
                                        <?= $_SESSION['form_errors']['role'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Button -->
                            <div class="col-12 text-center mt-3">
                                <button type="submit" name="save_user"
                                    class="btn btn-primary btn-sm px-4">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    LƯU
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </section>
            <section class="content">
                <!-- Default box -->

                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"> <i class="fa-solid fa-list"></i> DANH SÁCH TÀI KHOẢN</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive">

                            <table id="all_users" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="all_users_info">
                                <colgroup>
                                    <col width="5%">
                                    <col width="15%">
                                    <col width="30%">
                                    <col width="25%">
                                    <col width="10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="p-1 text-center">STT</th>
                                        <th class="p-1 text-center">Vai trò</th>
                                        <th class="p-1 text-center">Tên hiển thị</th>
                                        <th class="p-1 text-center">Tên đăng nhập</th>
                                        <th class="p-1 text-center">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                    if (empty($users)) { ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không tìm thấy tài khoản nào!</td>
                                    </tr>
                                    <?php } 
                                    else {
                                        foreach($users as $row) {
                                    ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle text-center"><?php echo $sn++;?></td>
                                        <td class="px-2 py-1 align-middle text-center">
                                            <?php 
                                            if (!empty($row['role'])) {
                                                if($row['role'] == '1') {
                                                    echo '<span class="badge badge-primary" style="font-size: 13px;">Admin</span>';
                                                } else if($row['role'] == '2') {
                                                    echo '<span class="badge badge-success" style="font-size: 13px;">Bác Sĩ</span>';
                                                } else if($row['role'] == '3') {
                                                    echo '<span class="badge badge-info" style="font-size: 13px;">Bệnh Nhân</span>';
                                                } else {
                                                    echo '<span class="badge badge-secondary" style="font-size: 13px;">'.htmlspecialchars($row['role']).'</span>';
                                                }
                                            }else{
                                                echo '<span class="badge badge-secondary" style="font-size: 14px;">Chưa phân vai trò</span>';
                                            }
                                                 
                                            ?>
                                        </td>
                                        <td class="px-2 py-1 align-middle"><?php echo $row['display_name'];?></td>
                                        <td class="px-2 py-1 align-middle"><?php echo $row['user_name'];?></td>

                                        <td class="px-2 py-1 align-middle text-center">
                                            <!-- Nút sửa user -->
                                            <form method="post" action="update_user.php" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </form>

                                            <!-- Nút xóa user -->
                                            <form method="post" action="delete_user.php" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                    <?php } } ?>
                                </tbody>
                            </table>
                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="Patients pagination">
                                <ul class="pagination justify-content-center mt-3">

                                    <!-- Previous -->
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page-1 ?>">«</a>
                                    </li>

                                    <?php
                                    // hiển thị tối đa 5 trang quanh trang hiện tại
                                    $start = max(1, $page - 10);
                                    $end   = min($totalPages, $page + 10);
                                    ?>
                                    <?php for($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page+1 ?>">»</a>
                                    </li>

                                </ul>

                                <div class="text-center text-muted small">
                                    Trang <?= $page ?> / <?= $totalPages ?> (<?= $totalUsers ?> người dùng)
                                </div>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </section>
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
    <?php include './config/data_tables_js.php'; ?>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="plugins\daterangepicker\date.js"></script>

    <script>
    showMenuSelected("#mnu_users", "");

    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }

    $(document).ready(function() {

        $("#user_name").blur(function() {
            var userName = $(this).val().trim();
            $(this).val(userName);

            if (userName !== '') {
                $.ajax({
                    url: "ajax/check_user_name.php",
                    type: 'GET',
                    data: {
                        'user_name': userName
                    },
                    cache: false,
                    async: false,
                    success: function(count, status, xhr) {
                        if (count > 0) {
                            showCustomMessage(
                                "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.");
                            $("#save_user").attr("disabled", "disabled");

                        } else {
                            $("#save_user").removeAttr("disabled");
                        }
                    },
                    error: function(jqXhr, textStatus, errorMessage) {
                        showCustomMessage(errorMessage);
                    }
                });
            }

        });
    });
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
    });
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