<?php 
include './config/connection.php';

$message = '';

if (isset($_POST['save_user'])) {
    $displayName = $_POST['display_name'];
    $userName = $_POST['user_name'];
    $password = $_POST['password'];

    $encryptedPassword = md5($password);

    $targetFile = null;
    if (!empty($_FILES["profile_picture"]["name"])) {
        $baseName = basename($_FILES["profile_picture"]["name"]);
        $targetFile = time() . $baseName;
        $status = move_uploaded_file($_FILES["profile_picture"]["tmp_name"], 'user_images/' . $targetFile);

        if (!$status) {
            $targetFile = null; 
        }
    }

    try {
        $con->beginTransaction();

        if ($targetFile) {
            $query = "INSERT INTO `users`(`display_name`, `user_name`, `password`, `profile_picture`) 
                      VALUES(:display_name, :user_name, :password, :profile_picture)";
        } else {
            $query = "INSERT INTO `users`(`display_name`, `user_name`, `password`) 
                      VALUES(:display_name, :user_name, :password)";
        }

        $stmtUser = $con->prepare($query);
        $stmtUser->bindParam(":display_name", $displayName);
        $stmtUser->bindParam(":user_name", $userName);
        $stmtUser->bindParam(":password", $encryptedPassword);

        if ($targetFile) {
            $stmtUser->bindParam(":profile_picture", $targetFile);
        }

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
`profile_picture` from `users` 
order by `display_name` asc;";
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
                            <h1>Tài khoản</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <!-- Main content -->
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
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
                                        class="form-control form-control-sm rounded-0" />
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên Đăng Nhập</label>
                                    <input type="text" id="user_name" name="user_name" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Mật Khẩu</label>
                                    <input type="password" id="password" name="password" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Ảnh Đại Diện</label>
                                    <input type="file" id="profile_picture" name="profile_picture"
                                        class=" form-control form-control-sm rounded-0" />
                                </div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="save_medicine" name="save_user"
                                        class="btn btn-primary btn-sm btn-flat btn-block">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /.card -->
            </section>
            <section class="content">
                <!-- Default box -->

                <div class="card card-outline card-primary rounded-0 shadow">
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
                                    <col width="10%">
                                    <col width="50%">
                                    <col width="25%">
                                    <col width="10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="p-1 text-center">STT</th>
                                        <th class="p-1 text-center">Hình Ảnh</th>
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
                                            <?php if (!empty($row['profile_picture'])): ?>
                                            <img class="img-thumbnail rounded-circle p-0 border user-img"
                                                src="user_images/<?php echo $row['profile_picture']; ?>">
                                            <?php endif; ?>
                                        </td>


                                        <td class="px-2 py-1 align-middle"><?php echo $row['display_name'];?></td>
                                        <td class="px-2 py-1 align-middle"><?php echo $row['user_name'];?></td>

                                        <td class="px-2 py-1 align-middle text-center">
                                            <a href="update_user.php?user_id=<?php echo $row['id']; ?>"
                                                class="btn btn-primary btn-sm btn-flat">
                                                <i class="fa fa-edit"></i>
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
                                "This user name exists. Please choose another username");
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
    </script>
</body>

</html>