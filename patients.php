<?php
include './config/connection.php';
include './common_service/common_functions.php';


$message = '';
if (isset($_POST['save_Patient'])) {

    $patientName = trim($_POST['patient_name']);
    $address = trim($_POST['address']);
    $cnic = trim($_POST['cnic']);
    
    $dateBirth = !empty($_POST['date_of_birth']) 
    ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['date_of_birth']))) 
    : null;


    $phoneNumber = trim($_POST['phone_number']);

    $patientName = ucwords(strtolower($patientName));
    $address = ucwords(strtolower($address));

    $gender = $_POST['gender'];
if ($patientName != '' && $address != '' && 
  $cnic != '' && $dateBirth != '' && $phoneNumber != '' && $gender != '') {
      $query = "INSERT INTO `patients`(`patient_name`, 
    `address`, `cnic`, `date_of_birth`, `phone_number`, `gender`)
VALUES('$patientName', '$address', '$cnic', '$dateBirth',
'$phoneNumber', '$gender');";
try {

  $con->beginTransaction();

  $stmtPatient = $con->prepare($query);
  $stmtPatient->execute();

  $con->commit();

  
$_SESSION['success_message'] = 'Thêm mới bệnh nhân thành công.';


} catch(PDOException $ex) {
  $con->rollback();

  echo $ex->getMessage();
  echo $ex->getTraceAsString();
  exit;
}
}
header("Location: patients.php");
exit();
}



try {

$query = "SELECT `id`, `patient_name`, `address`, 
`cnic`, date_format(`date_of_birth`, '%d %b %Y') as `date_of_birth`, 
`phone_number`, `gender` 
FROM `patients` WHERE `is_deleted` = 0 ORDER BY `patient_name` ASC;";

  $stmtPatient1 = $con->prepare($query);
  $stmtPatient1->execute();

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

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>

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
                            <h1>Bệnh Nhân</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thêm mới bệnh nhân</h3>

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
                                    <label>Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" name="patient_name" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                data-toggle="datetimepicker" autocomplete="off" />
                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" required="required"
                                        class="form-control form-control-sm rounded-0" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <select class="form-control form-control-sm rounded-0" id="gender" name="gender"
                                        required>
                                        <?php echo getGender();?>
                                    </select>

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-flat btn-block">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </section>

            <br />
            <br />
            <br />

            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách bệnh nhân</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive">
                            <table id="all_patients" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="all_patients_info">

                                <thead style="text-align:center;">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bệnh nhân</th>
                                        <th>Địa chỉ</th>
                                        <th>CCCD</th>
                                        <th>Ngày sinh</th>
                                        <th>Số điện thoại</th>
                                        <th>Giới tính</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                  $count = 0;
                  while($row =$stmtPatient1->fetch(PDO::FETCH_ASSOC)){
                    $count++;
                  ?>
                                    <tr style="text-align:center;">
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $row['patient_name'];?></td>
                                        <td><?php echo $row['address'];?></td>
                                        <td><?php echo $row['cnic'];?></td>
                                        <td><?php echo date("d/m/Y", strtotime($row['date_of_birth'])); ?></td>
                                        <td><?php echo $row['phone_number'];?></td>
                                        <td><?php echo $row['gender'];?></td>
                                        <td>
                                            <a href="update_patient.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-primary btn-sm btn-flat">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_patient.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-danger btn-sm btn-flat">
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

                    <!-- /.card-footer-->
                </div>
                <!-- /.card -->


            </section>
        </div>
        <!-- /.content -->

        <!-- /.content-wrapper -->
        <?php 
 include './config/footer.php';

//   $message = '';
//   if(isset($_GET['message'])) {
//     $message = $_GET['message'];
//   }
$message = '';
if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
}
?>
        <!-- /.control-sidebar -->


        <?php include './config/site_js_links.php'; ?>
        <?php include './config/data_tables_js.php'; ?>


        <script src="plugins/moment/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="date.js"></script>

        <script>
        showMenuSelected("#mnu_patients", "#mi_patients");

        var message = '<?php echo $message;?>';
        if (message !== '') {
            showCustomMessage(message);
        }

        $('#date_of_birth').datetimepicker({
            format: 'L'
        });


        $(function() {
            $("#all_patients").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#all_patients_wrapper .col-md-6:eq(0)');

        });
        </script>
</body>

</html>