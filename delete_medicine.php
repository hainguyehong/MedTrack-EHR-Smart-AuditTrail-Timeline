<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

islogin([2]);

// Kiểm tra quyền (chỉ role 1 hoặc 2 mới được xoá)
if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2) {
    die("Bạn không có quyền xoá.");
}
$message = '';
if (isset($_POST['delete_medicine'])) {
  $id = $_POST['hidden_id'];
    $deleted_at = date('Y-m-d H:i:s');
try {

  $con->beginTransaction();
  
     // Soft delete bệnh nhân
        $query = "UPDATE `medicines` SET `is_deleted` = 1, `deleted_at` = :deleted_at WHERE `id` = :id";
        $stmtmedicine = $con->prepare($query);
        $stmtmedicine->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtmedicine->bindParam(':deleted_at', $deleted_at);
        $stmtmedicine->execute();

        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'medicines',
                $id,
                'delete',
                $oldData, // giá trị trước khi xóa
                ['is_deleted' => 1]
            );
        }
    $con->commit();
    $_SESSION['success_message'] = 'Xóa thuốc thành công (soft delete).';

} catch(PDOException $ex) {
  $con->rollback();

  echo $ex->getMessage();
  echo $ex->getTraceAsString();
  exit;
}

    header("Location: medicines.php"); // quay về trang danh sách
    exit();
}
// slect dữ liệu để hiển thị
try {
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
} elseif (isset($_GET['id'])) {
    // fallback nếu ai đó truy cập thủ công bằng GET
    $id = (int)$_GET['id'];
} else {
    header('Location: medicines.php'); // trang danh sách thuốc
    exit;
}

	$query = "SELECT `id`, `medicine_name` from `medicines`
	          where `id` = $id";
	$stmt = $con->prepare($query);
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

} catch(PDOException $ex) {
	echo $ex->getMessage();
	echo $ex->getTraceAsString();
  	exit;	
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <title>Xóa thuốc - MedTrack</title>
    <style>
        * {
    font-family: sans-serif;
}
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
                            <!-- <h1>Xóa Loại Thuốc</h1> -->
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
                            XÓA LOẠI THUỐC
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" id="deleteForm">
                            <input type="hidden" name="hidden_id" id="hidden_id" value="<?php echo $id;?>" />

                            <!-- HÀNG 1: TÊN THUỐC + NÚT XOÁ -->
                            <div class="row align-items-end">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Tên loại thuốc</label>
                                    <input type="text" id="medicine_name" name="medicine_name"
                                        class="form-control form-control-sm" value="<?php echo $row['medicine_name'];?>"
                                        readonly />
                                </div>

                                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-sm px-4" data-toggle="modal"
                                        data-target="#confirmDeleteModal">
                                        <i class="fa-solid fa-trash mr-1"></i> XOÁ
                                    </button>
                                </div>
                            </div>

                            <!-- HÀNG 2: WARNING -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        Hành động này sẽ <strong>xoá loại thuốc (soft delete)</strong>
                                        và không thể hoàn tác ngay lập tức.
                                    </div>
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
    showMenuSelected("#mnu_medicines", "#mi_medicine_details");
    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }
    </script>

    <!-- Modal xác nhận xoá thuốc -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Xác nhận xoá thuốc
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    Bạn có chắc chắn muốn xoá thuốc
                    <strong class="text-danger">
                        <?php echo $row['medicine_name']; ?>
                    </strong> không?
                    <br>
                    <small class="text-muted delete-note">
                        (Thuốc sẽ bị đánh dấu xoá – không hiển thị trong hệ thống)
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-dismiss="modal">
                        <i class="fa-solid fa-xmark mr-1"></i> HỦY
                    </button>

                    <button type="submit" form="deleteForm" name="delete_medicine" class="btn btn-danger btn-sm px-3">
                        <i class="fa-solid fa-trash-can mr-1"></i> XOÁ
                    </button>
                </div>

            </div>
        </div>
    </div>


</body>

</html>