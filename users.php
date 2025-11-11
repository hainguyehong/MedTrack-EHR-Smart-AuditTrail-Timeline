<?php 
include './config/connection.php';
include './common_service/common_functions.php';
$message = '';

if (isset($_POST['save_user'])) {
    $displayName = $_POST['display_name'];
    $userName = $_POST['user_name'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $encryptedPassword = md5($password);

    try {
        $con->beginTransaction();
        
            $query = "INSERT INTO `users`(`display_name`, `user_name`, `role`, `password`) 
            VALUES(:display_name, :user_name, :role, :password)";

        $stmtUser = $con->prepare($query);
        $stmtUser->bindParam(":display_name", $displayName);
        $stmtUser->bindParam(":user_name", $userName);
        $stmtUser->bindParam(":role", $role);    
        $stmtUser->bindParam(":password", $encryptedPassword);
        // var_dump($_POST['role']); exit;

        $stmtUser->execute();

        $con->commit();

        $_SESSION['success_message'] = 'Tài khoản đã được tạo thành công.';
    } catch (PDOException $ex) {
        $con->rollback();
        echo $ex->getTraceAsString();
        echo $ex->getMessage();
        exit;
    }

    header("Location: users.php");
    exit();
}


$queryUsers = "select `id`, `display_name`, `user_name`, 
`role` from `users` where `is_deleted` = 0 
order by `role` asc;";
$stmtUsers = '';

try {
    $stmtUsers = $con->prepare($queryUsers);
    $stmtUsers->execute();

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

    <?php include './config/data_tables_css.php';?>
    <title>Users - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>

    <style>
    .user-img {
        width: 3em;
        width: 3em;
        object-fit: cover;
        object-position: center center;
    }
    </style>
    <style>
    body {
        background: #f8fafc;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }
    .btn-primary, .btn-danger {
        border-radius: 20px;
        transition: 0.2s;
    }
    .card-primary.card-outline {
    border-top: 0px solid #007bff;
    }
    .btn-primary:hover, .btn-danger:hover {
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    }
    .form-control, .form-select {
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

<!-- <body class="hold-transition sidebar-mini dark-mode layout-fixed layout-navbar-fixed"> -->
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
                            <h1>Tài khoản</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <!-- <div class="card card-outline card-primary rounded-0 shadow"> -->
                    <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thêm mới tài khoản</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row">

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên Hiển Thị</label>
                                    <input type="text" id="display_name" name="display_name" required="required"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên Đăng Nhập</label>
                                    <input type="text" id="user_name" name="user_name" required="required"
                                        class="form-control form-control-sm"/>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Mật Khẩu</label>
                                    <input type="password" id="password" name="password" required="required"
                                       class="form-control form-control-sm" />
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Chọn vai trò</label>
                                    <select class="form-control form-control-sm" id="role" name="role">
                                        <?php echo getRoles();?>
                                    </select>
                                </div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="save_medicine" name="save_user"
                                        class="btn btn-primary btn-sm btn-block">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /.card -->
            </section>
            <section class="content">
                <!-- Default box -->

                <!-- <div class="card card-outline card-primary rounded-0 shadow"> -->
                    <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh Sách Tài Khoản</h3>

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
                                        <th class="p-1 text-center">Tên Hiển Thị</th>
                                        <th class="p-1 text-center">Tên Đăng Nhập</th>
                                        <th class="p-1 text-center">Hành Động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
          $serial = 0;
          while($row = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
           $serial++;
           ?>
                                    <tr>
                                        <td class="px-2 py-1 align-middle text-center"><?php echo $serial;?></td>
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
                                            <a href="update_user.php?user_id=<?php echo $row['id']; ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_user.php?user_id=<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- /.card-footer-->
                </div>

                <!-- /.card -->

            </section>
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
    showMenuSelected("#mnu_users", "");

    var message = '<?php echo $message;?>';

    if (message !== '') {
        showCustomMessage(message);
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
    $(function() {
        $("#all_users").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "buttons": ["pdf", "print"],

            "language": {
              "info": " Tổng cộng _TOTAL_ người dùng",
              "paginate": {
                        "previous": "<span style='font-size:18px;'>&#8592;</span>",
                        "next": "<span style='font-size:18px;'>&#8594;</span>"
                    }
         }
        }).buttons().container().appendTo('#all_users_wrapper .col-md-6:eq(0)');

    });
    </script>
</body>

</html>