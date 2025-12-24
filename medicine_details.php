<?php 
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
$message = '';

if (isset($_POST['submit'])) {
    $medicineId = $_POST['medicine'];
    $packing = $_POST['packing'];
    $medicineName = trim($_POST['medicine_name']);
    $medicineName = ucwords(strtolower($medicineName));
    $createdAt = date('Y-m-d H:i:s');
    try {
        $con->beginTransaction();

        // Kiểm tra xem thuốc đã có trong bảng chưa
        $checkQuery = "SELECT id, packing 
               FROM medicine_details 
               WHERE medicine_name = :medicine_name 
                 AND is_deleted = 0
               LIMIT 1";
                $stmtCheck = $con->prepare($checkQuery);
                $stmtCheck->bindParam(":medicine_name", $medicineName, PDO::PARAM_STR);
                $stmtCheck->execute();
                $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if ($existing) {
            // Nếu đã tồn tại → cập nhật số lượng
            $newPacking = $existing['packing'] + $packing;

            $updateQuery = "UPDATE medicine_details 
                            SET packing = :packing 
                            WHERE id = :id";
            $stmtUpdate = $con->prepare($updateQuery);
            $stmtUpdate->bindParam(":packing", $newPacking, PDO::PARAM_INT);
            $stmtUpdate->bindParam(":id", $existing['id'], PDO::PARAM_INT);
            $stmtUpdate->execute();
        } else {
            // Nếu chưa tồn tại → thêm mới
            $insertQuery = "INSERT INTO medicine_details (medicine_name, packing, created_at) 
                            VALUES (:medicine_name, :packing, :created_at)";
            $stmtInsert = $con->prepare($insertQuery);
            $stmtInsert->bindParam(":medicine_name", $medicineName, PDO::PARAM_STR);
            $stmtInsert->bindParam(":packing", $packing, PDO::PARAM_INT);
            $stmtInsert->bindParam(":created_at", $createdAt, PDO::PARAM_STR);
            $stmtInsert->execute();
        }

        $con->commit();
        $_SESSION['success_message'] = 'Thêm / cập nhật thành công.';

    } catch (PDOException $ex) {
        $con->rollback();
        echo $ex->getMessage();
        echo $ex->getTraceAsString();
        exit;
    }

    header("Location: medicine_details.php");
    exit();
}



// $medicines = getMedicines($con);

$query = "SELECT 
              md.medicine_name, 
              md.id, 
              md.packing,  
              md.id 
          FROM medicine_details AS md
            WHERE md.is_deleted = 0
          ORDER BY md.id ASC;";


 try {
  
    $stmtDetails = $con->prepare($query);
    $stmtDetails->execute();

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
    <?php include './config/data_tables_css.php';?>
      <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Xem chi tiết thuốc - MedTrack</title>
    <style>
    * {
        font-family: sans-serif;
    }
    body {
        background: #f8fafc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        /* border: 1.5px solid #007bff; */
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

    .form-control,
    .form-select {
        /* border-radius: 8px; */
    }

    .card-title {
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    label {
        font-weight: 500;
    }

    .card-primary.card-outline {
        border-top: 0px solid #007bff;
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
                            <h1>Thông Tin Chi Tiết Loại Thuốc</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thêm thông tin chi tiết về thuốc</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row" style="align-items: flex-end;">
                                <div class="col-lg-5 col-md-6 col-sm-12 mb-3">

                                    <!-- <label style="font-weight:600;color:#000000;">Chọn Loại Thuốc</label>

                                    <select id="medicine" name="medicine" class="form-control form-control-sm" required="required" style="border-radius:10px; border:1.5px solid #e3e7ed;">
                                        <?php echo $medicines;?>
                                    </select> -->
                                    <label>Tên thuốc</label>
                                    <input type="text" id="medicine_name" name="medicine_name" required="required"
                                        class="form-control form-control-sm" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12 mb-3">
                                    <label style="font-weight:600;color:#000000;">Số Gói</label>

                                    <input id="packing" name="packing" class="form-control form-control-sm rounded-0"
                                        required="required" style="border-radius:10px; border:1.5px solid #e3e7ed;" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-12 mb-3 d-flex align-items-end">
                                    <button type="submit" id="submit" name="submit" class="btn btn-primary btn-block"
                                        style="border-radius:10px; font-weight:600; font-size:1rem; padding:0px 0; height: 34px">

                                        <i class="fa fa-save"></i> Lưu
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </section>

            <div class="clearfix">&nbsp;</div>
            <div class="clearfix">&nbsp;</div>

            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách chi tiết về thuốc</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row table-responsive">
                            <table id="medicine_details" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="medicine_details_info">
                                <colgroup>
                                    <col width="10%">
                                    <col width="50%">
                                    <col width="30%">
                                    <col width="10%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên Thuốc</th>
                                        <th>Số Gói</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                        $serial = 0;
                                        $hasData = false;

                                        while($row = $stmtDetails->fetch(PDO::FETCH_ASSOC)){
                                            $serial++;
                                            $hasData = true;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $serial; ?></td>
                                        <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['packing']); ?></td>

                                        <td class="text-center">
                                            <a href="update_medicine_details.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_medicine_details.php?id=<?php echo $row['id']; ?>"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                    if (!$hasData) {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Không có thuốc nào trong danh
                                            sách
                                        </td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </section>
            <!-- /.content-wrapper -->
        </div>

        <?php include './config/footer.php';

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
    showMenuSelected("#mnu_medicines", "#mi_medicine_details");

    var message = '<?php echo $message;?>';

    if (message !== '') {
        showCustomMessage(message);
    }
    $(function() {
        $("#medicine_details").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "buttons": ["pdf", "print"],
            "language": {
                "info": " Tổng cộng _TOTAL_ loại thuốc",
                "paginate": {
                    "previous": "<span style='font-size:18px;'>&#8592;</span>",
                    "next": "<span style='font-size:18px;'>&#8594;</span>"
                }
            }
        }).buttons().container().appendTo('#medicine_details_wrapper .col-md-6:eq(0)');

    });
    </script>`
</body>

</html>