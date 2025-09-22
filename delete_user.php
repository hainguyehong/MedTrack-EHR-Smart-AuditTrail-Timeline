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
if (isset($_POST['delete_user'])) {
  $id = $_POST['hidden_id'];
try {

  $con->beginTransaction();
  
     // Soft delete bệnh nhân
        $query = "UPDATE `users` SET `is_deleted` = 1 WHERE `id` = :id";
        $stmtuser = $con->prepare($query);
        $stmtuser->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtuser->execute();

    $con->commit();
    // $message = 'Bệnh nhân đã được xoá (soft delete).';
    $_SESSION['success_message'] = 'Bệnh nhân đã được xoá (soft delete).';

} catch(PDOException $ex) {
  $con->rollback();

  echo $ex->getMessage();
  echo $ex->getTraceAsString();
  exit;
}

    header("Location: users.php"); // quay về trang danh sách
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

    <?php include './config/data_tables_css.php';?>

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Delete Users Details - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>

</head>

<body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed">
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
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Xóa Người Dùng</h3>

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
                                    <label>Tên Hiển Thị</label>
                                    <input type="text" id="display_name" name="display_name" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['display_name'];?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên Đăng Nhập</label>
                                    <input type="text" id="username" name="username" required="required"
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo $row['user_name'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Mật Khẩu</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-sm rounded-0" />

                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Vai trò</label>
                                    <select name="role" id="role" class="form-control form-control-sm rounded-0">
                                        <?php echo getRoles((int)$row['role']); ?>
                                    </select>
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="margin-top:20px;">
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#confirmDeleteModal">Xoá</button>

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
    <?php include './config/data_tables_js.php'; ?>


    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="date.js"></script>
    <script>
    showMenuSelected("#mnu_users", "#mi_users");

    var message = '<?php echo $message;?>';

    if (message !== '') {
        showCustomMessage(message);
    }
    $('#date_of_birth').datetimepicker({
        format: 'L'
    });


    // $(function() {
    //     $("#all_users").DataTable({
    //         "responsive": true,
    //         "lengthChange": false,
    //         "autoWidth": false,
    //         "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    //     }).buttons().container().appendTo('#all_users_wrapper .col-md-6:eq(0)');

    // });
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