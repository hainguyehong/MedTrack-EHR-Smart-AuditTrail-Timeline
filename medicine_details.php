<?php 
include './config/connection.php';
include './common_service/common_functions.php';

$message = '';

if (isset($_POST['submit'])) {
    $medicineId = $_POST['medicine'];
    $packing = $_POST['packing'];

    try {
        $con->beginTransaction();

        // Kiểm tra xem thuốc đã có trong bảng chưa
        $checkQuery = "SELECT id, packing 
                       FROM medicine_details 
                       WHERE medicine_id = :medicine_id 
                         AND is_deleted = 0
                       LIMIT 1";
        $stmtCheck = $con->prepare($checkQuery);
        $stmtCheck->bindParam(":medicine_id", $medicineId, PDO::PARAM_INT);
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
            $insertQuery = "INSERT INTO medicine_details (medicine_id, packing) 
                            VALUES (:medicine_id, :packing)";
            $stmtInsert = $con->prepare($insertQuery);
            $stmtInsert->bindParam(":medicine_id", $medicineId, PDO::PARAM_INT);
            $stmtInsert->bindParam(":packing", $packing, PDO::PARAM_INT);
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



$medicines = getMedicines($con);

$query = "SELECT 
              m.medicine_name, 
              md.id, 
              md.packing,  
              md.medicine_id 
          FROM medicines AS m
          JOIN medicine_details AS md 
              ON m.id = md.medicine_id
          WHERE m.is_deleted = 0 
            AND md.is_deleted = 0
          ORDER BY m.id ASC, md.id ASC";


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
    <title>Medicine Details - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
    <style>
    body {
        background: #f8fafc;
    }
    .card {
        background: #fff;
        border-radius: 12px;
        /* border: 1.5px solid #007bff; */
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
    
    .btn-primary:hover, .btn-danger:hover {
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0,123,255,0.15);
    }
    .form-control, .form-select {
        /* border-radius: 8px; */
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
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Chọn Loại Thuốc</label>
                                    <select id="medicine" name="medicine" class="orm-control form-control-sm"
                                        required="required">
                                        <?php echo $medicines;?>
                                    </select>
                                </div>

                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Số Gói</label>
                                    <input id="packing" name="packing" class="form-control form-control-sm rounded-0"
                                        required="required" />
                                </div>

                                <div class="col-lg-1 col-md-2 col-sm-4 col-xs-12">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="submit" name="submit"
                                        class="btn btn-primary btn-sm btn-block">Lưu</button>
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
                  while($row =$stmtDetails->fetch(PDO::FETCH_ASSOC)){
                    $serial++;
                  ?>
                                    <tr>
                                        <td class="text-center"><?php echo $serial; ?></td>
                                        <td><?php echo $row['medicine_name'];?></td>
                                        <td><?php echo $row['packing'];?></td>

                                        <td class="text-center">
                                            <a href="update_medicine_details.php?medicine_id=<?php echo $row['medicine_id'];?>&medicine_detail_id=<?php echo $row['id'];?>&packing=<?php echo $row['packing'];?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_medicine_details.php?medicine_detail_id=<?php echo $row['id'];?>"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
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
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#medicine_details_wrapper .col-md-6:eq(0)');

    });
    </script>
</body>

</html>