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
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Chỉnh sửa thuốc - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
                            <!-- <h1>Loại Thuốc</h1> -->
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
                            <i class="fa-solid fa-pen-to-square mr-2"></i>
                            CHỈNH SỬA LOẠI THUỐC
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
                                        class="btn btn-primary btn-sm btn-block"> <i class="fa-solid fa-pen-to-square"></i>Cập nhật</button>
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
    $messageType = 'info';

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    $messageType = 'success';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    $messageType = 'error';
    unset($_SESSION['error_message']);
}
if ($message == '' && isset($_GET['message'])) {
    $message = $_GET['message'];
    $messageType = 'info';
}

?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    showMenuSelected("#mnu_medicines", "#mi_medicine_details");
    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }
    </script>

</body>

</html>