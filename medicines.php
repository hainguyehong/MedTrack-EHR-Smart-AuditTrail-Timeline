<?php 
include './config/connection.php';
include './common_service/date.php';

$message = '';
if(isset($_POST['save_medicine'])) {
  $message = '';
  $medicineName = trim($_POST['medicine_name']);
  $medicineName = ucwords(strtolower($medicineName));
  $created_at = date('Y-m-d H:i:s');    
  if($medicineName != '') {
   $query = "INSERT INTO `medicines`(`medicine_name`, `created_at`)
   VALUES('$medicineName', '$created_at');";
   
   try {

    $con->beginTransaction();

    $stmtMedicine = $con->prepare($query);
    $stmtMedicine->execute();

    $con->commit();

    // $message = 'Medicine added successfully.';
    $_SESSION['success_message'] = 'Thêm thuốc thành công.';
    
  }catch(PDOException $ex) {
   $con->rollback();

   echo $ex->getMessage();
   echo $ex->getTraceAsString();
   exit;
 }

} else {
 $message = 'Vui lòng điền đấy đủ thông tin.';
}
    header("Location: medicines.php");
    exit();
}else

try {
  $query = "select `id`, `medicine_name` from `medicines` 
   WHERE `is_deleted` = 0 order by `created_at` desc;";
  $stmt = $con->prepare($query);
  $stmt->execute();

} catch(PDOException $ex) {
  echo $ex->getMessage();
  echo $e->getTraceAsString();
  exit;  
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>


    <?php include './config/data_tables_css.php';?>
    <title>Medicines - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
    <style>
    body {
        background: #f8fafc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        /* border: 1.5px solid #007bff; */
        /* box-shadow: 0 2px 8px rgba(0,0,0,0.04); */
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        /* border-radius: 12px 12px 0 0; */
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
        border-radius: 8px;
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
                            <h1>Thêm mới Loại thuốc</h1>
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
                        <h3 class="card-title">Thêm mới Loại thuốc</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên thuốc</label>
                                    <input type="text" id="medicine_name" name="medicine_name" required="required"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="save_medicine" name="save_medicine"
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
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách loại thuốc</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive">

                            <table id="all_medicines" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="all_medicines_info">
                                <colgroup>
                                    <col width="10%">
                                    <col width="80%">
                                    <col width="10%">
                                </colgroup>

                                <thead <tr>
                                    <th class="text-center">STT</th>
                                    <th>Tên thuốc</th>
                                    <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
          $serial = 0;
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
           $serial++;
           ?>
                                    <tr>
                                        <td class="text-center"><?php echo $serial;?></td>
                                        <td><?php echo $row['medicine_name'];?></td>
                                        <td class="text-center">
                                            <a href="update_medicine.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_medicine.php?id=<?php echo $row['id'];?>"
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


    <script>
    showMenuSelected("#mnu_medicines", "#mi_medicines");

    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }



    $(function() {
        $("#all_medicines").DataTable({
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
            },
        }).buttons().container().appendTo('#all_medicines_wrapper .col-md-6:eq(0)');

    });

    $(document).ready(function() {

        $("#medicine_name").blur(function() {
            var medicineName = $(this).val().trim();
            $(this).val(medicineName);

            if (medicineName !== '') {
                $.ajax({
                    url: "ajax/check_medicine_name.php",
                    type: 'GET',
                    data: {
                        'medicine_name': medicineName
                    },
                    cache: false,
                    async: false,
                    success: function(count, status, xhr) {
                        if (count > 0) {
                            showCustomMessage(
                                "Tên thuốc này đã được lưu trữ. Vui lòng chọn tên khác"
                            );
                            $("#save_medicine").attr("disabled", "disabled");
                        } else {
                            $("#save_medicine").removeAttr("disabled");
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