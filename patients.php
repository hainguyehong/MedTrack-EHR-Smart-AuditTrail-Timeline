<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

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
    $createdAt = date("Y-m-d H:i:s");

    if ($patientName != '' && $address != '' && 
        $cnic != '' && $dateBirth != '' && $phoneNumber != '' && $gender != '') {

        try {
            // kiểm tra cccd đã tồn tại chưa
            $checkQuery = "SELECT COUNT(*) as cnt FROM `patients` WHERE `cnic` = :cnic";
            $stmtCheck = $con->prepare($checkQuery);
            $stmtCheck->execute([':cnic' => $cnic]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($row['cnt'] > 0) {
                $_SESSION['error_message'] = 'CCCD đã tồn tại, vui lòng nhập CCCD khác.';
                header("Location: patients.php");
                exit();
            }

            // query insert patient
            $queryPatient = "INSERT INTO `patients`(`patient_name`, 
                `address`, `cnic`, `date_of_birth`, `phone_number`, `gender`, `created_at`)
                VALUES(:patient_name, :address, :cnic, :date_of_birth,
                :phone_number, :gender, :created_at)";

            // query insert user
            $queryUser = "INSERT INTO `user_patients`(`user_name`, `password`, 
                `display_name`, `role`, `created_at`, `id_patient`) 
                VALUES(:user_name, :password, :display_name, :role, :created_at, :id_patient)";

            $con->beginTransaction();

            // insert patient
            $stmtPatient = $con->prepare($queryPatient);
            $stmtPatient->execute([
                ':patient_name' => $patientName,
                ':address' => $address,
                ':cnic' => $cnic,
                ':date_of_birth' => $dateBirth,
                ':phone_number' => $phoneNumber,
                ':gender' => $gender,
                ':created_at' => $createdAt
            ]);

            // lấy id bệnh nhân vừa thêm
            $idPatient = $con->lastInsertId();  

            // insert default user account
            $stmtUser = $con->prepare($queryUser);
            $stmtUser->execute([
                ':user_name' => $cnic,
                ':password' => md5("1"),
                ':display_name' => $patientName,
                ':role' => 3,
                ':created_at' => $createdAt,
                ':id_patient' => $idPatient
            ]);

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



try { // lấy danh sách bệnh nhân

$query = "SELECT `id`, `patient_name`, `address`, 
`cnic`, date_format(`date_of_birth`, '%d %b %Y') as `date_of_birth`, 
`phone_number`, `gender`, `created_at`
FROM `patients` WHERE `is_deleted` = 0 ORDER BY `created_at` DESC;";

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
    <style>
    body {
        background: #f8fafc;
    }

    .card-primary.card-outline {
        /* border-top: 0px solid #007bff; */
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

    .card-primary.card-outline {
        border-top: 0px solid #007bff;
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
                            <h1>Bệnh Nhân</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <!-- <h3 class="card-title">Thêm mới bệnh nhân</h3> -->
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                                fill="#FFFFFF" style="vertical-align: middle; margin-right: 8px;">
                                <path
                                    d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80Zm-360-80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0-80Zm0 400Z" />
                            </svg>
                            Thêm mới bệnh nhân
                        </h3>


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
                                        class="form-control form-control-sm" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" class="form-control form-control-sm datetimepicker-input"
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
                                        class="form-control form-control-sm" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <select class="form-control form-control-sm" id="gender" name="gender" required>
                                        <?php echo getGender();?>
                                    </select>

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-block">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </section>

            </section>
            <br />
            <br />
            <br />
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
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
                            <table id="all_patients"
                                class="table table-striped table-hover dataTable table-bordered dtr-inline" role="grid"
                                aria-describedby="all_patients_info">

                                <thead style="text-align:center;">
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên bệnh nhân</th>
                                        <th>Địa chỉ</th>
                                        <th>CCCD</th>
                                        <th>Ngày sinh</th>
                                        <th>Số điện thoại</th>
                                        <th>Giới tính</th>
                                        <th>Thời gian tạo</th>
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
                                            <?php 
                                                if (!empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00') {
                                                    echo date("d/m/Y H:i:s", strtotime($row['created_at']));
                                                } else {
                                                    echo ""; // hoặc echo "Chưa có dữ liệu";
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <a href="update_patient.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_patient.php?id=<?php echo $row['id'];?>"
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
}elseif (isset($_SESSION['error_message'])) {
    $message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
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
                // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "buttons": ["pdf", "print"],
                "language": {
                    "info": " Tổng cộng _TOTAL_ bệnh nhân",
                    "paginate": {
                        "previous": "<span style='font-size:18px;'>&#8592;</span>",
                        "next": "<span style='font-size:18px;'>&#8594;</span>"
                    }
                }
            }).buttons().container().appendTo('#all_patients_wrapper .col-md-6:eq(0)');

        });
        </script>
</body>

</html>