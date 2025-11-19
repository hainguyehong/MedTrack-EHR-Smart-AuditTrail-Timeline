<?php
include './config/connection.php';
include './common_service/date.php';
include './common_service/common_functions.php';
islogin([2]); // chỉ cho admin (2) truy cập
 $message = '';
if (isset($_POST['save_medicine'])) {
    $medicineName = trim($_POST['medicine_name']);
    $medicineName = ucwords(strtolower($medicineName));
    $id = $_POST['hidden_id'];

    if ($medicineName !== '') {
        try {
            $con->beginTransaction();

            // --- Lấy dữ liệu cũ để ghi log ---
            $oldQuery = "SELECT * FROM medicines WHERE id = :id";
            $stmtOld = $con->prepare($oldQuery);
            $stmtOld->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtOld->execute();
            $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (!$oldData) {
                $con->rollBack();
                $_SESSION['error_message'] = 'Không tìm thấy thuốc để cập nhật.';
                header("Location: medicines.php");
                exit();
            }

            // --- Kiểm tra trùng tên thuốc (ngoại trừ chính nó) ---
            $checkQuery = "SELECT COUNT(*) FROM medicines 
                           WHERE medicine_name = :medicine_name AND id != :id";
            $stmtCheck = $con->prepare($checkQuery);
            $stmtCheck->bindParam(':medicine_name', $medicineName);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            $exists = $stmtCheck->fetchColumn();

            if ($exists > 0) {
                $con->rollBack();
                $_SESSION['error_message'] = 'Tên thuốc này đã tồn tại, vui lòng nhập tên khác.';
                header("Location: medicines.php");
                exit();
            }

            // --- Cập nhật thuốc ---
            $updateQuery = "UPDATE medicines 
                            SET medicine_name = :medicine_name, 
                                updated_at = :updated_at 
                            WHERE id = :id";
            $stmtUpdate = $con->prepare($updateQuery);
            $updatedAt = date('Y-m-d H:i:s');
            $stmtUpdate->bindParam(':medicine_name', $medicineName);
            $stmtUpdate->bindParam(':updated_at', $updatedAt);
            $stmtUpdate->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtUpdate->execute();

            // --- Ghi log audit ---
            if (function_exists('log_audit')) {
                log_audit(
                    $con,
                    $_SESSION['user_id'] ?? 'unknown', // Ai thực hiện
                    'medicines',                        // Bảng nào
                    $id,                                // ID bản ghi
                    'update',                           // Hành động
                    $oldData,                           // Dữ liệu cũ
                    [                                   // Dữ liệu mới
                        'medicine_name' => $medicineName,
                        'updated_at' => $updatedAt
                    ]
                );
            }

            $con->commit();
            $_SESSION['success_message'] = 'Cập nhật thuốc thành công.';

        } catch (PDOException $ex) {
            $con->rollBack();
            $_SESSION['error_message'] = 'Lỗi khi cập nhật thuốc: ' . $ex->getMessage();
        }
    } else {
        $_SESSION['error_message'] = 'Tên thuốc không được để trống.';
    }

    header("Location: medicines.php");
    exit();
}




try {

 $id = $_GET['id'];
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
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Thuốc - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
                            <h1>Loại Thuốc</h1>
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
                            Chỉnh sửa loại thuốc
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="hidden_id" id="hidden_id" value="<?php echo $id;?>" />
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên loại thuốc</label>
                                    <input type="text" id="medicine_name" name="medicine_name" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['medicine_name'];?>" />
                                </div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="save_medicine" name="save_medicine"
                                        class="btn btn-primary btn-sm btn-block">Cập nhật</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->
        <?php 
 include './config/footer.php';

	$message = '';
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    } 
    // elseif (isset($_SESSION['error_message'])) {
    //     $message = $_SESSION['error_message'];
    //     unset($_SESSION['error_message']);
    // }

?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <script>
    showMenuSelected("#mnu_medicines", "#mi_medicine_details");
    var message = '<?php echo $message;?>';
    if (message !== '') {
        <?php if (isset($_SESSION['error_message'])): ?>
        showCustomMessage(message, 'error');
        <?php else: ?>
        showCustomMessage(message, 'success');
        <?php endif; ?>
    }
    </script>

</body>

</html>