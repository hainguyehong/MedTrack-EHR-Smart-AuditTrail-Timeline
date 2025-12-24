<?php
include './config/connection.php';
include './common_service/common_functions.php';
islogin([2]); // chỉ cho và bác sĩ (2) truy cập
$message = '';

// if (isset($_POST['save_Patient'])) {
//     $hiddenId = $_POST['hidden_id'];

//     $patientName = trim($_POST['patient_name']);
//     $address = trim($_POST['address']);
//     $cnic = trim($_POST['cnic']);
//     $dateBirth = !empty($_POST['date_of_birth'])
//         ? date("Y-m-d", strtotime(str_replace('/', '-', $_POST['date_of_birth'])))
//         : null;
//     $phoneNumber = trim($_POST['phone_number']);
//     $gender = $_POST['gender'];

//     // Chuẩn hóa chuỗi
//     $patientName = ucwords(strtolower($patientName));
//     $address = ucwords(strtolower($address));

//     // Kiểm tra dữ liệu đầu vào
//     if ($patientName != '' && $address != '' && $cnic != '' && $dateBirth != '' && $phoneNumber != '' && $gender != '') {
//         try {
//             // 🔹 1. Lấy dữ liệu cũ trước khi cập nhật
//             $stmtOld = $con->prepare("SELECT * FROM patients WHERE id = ?");
//             $stmtOld->execute([$hiddenId]);
//             $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

//             if (!$oldData) {
//                 $_SESSION['error_message'] = "Không tìm thấy hồ sơ bệnh nhân để cập nhật!";
//                 header("Location: patients.php");
//                 exit();
//             }

//             // 🔹 2. Kiểm tra trùng CCCD / username trong user_patients
//             $checkQuery = "SELECT COUNT(*) FROM user_patients 
//                            WHERE user_name = :user_name AND id_patient != :id_patient";
//             $stmtCheck = $con->prepare($checkQuery);
//             $stmtCheck->execute([
//                 ':user_name' => $cnic,
//                 ':id_patient' => $hiddenId
//             ]);
//             $exists = $stmtCheck->fetchColumn();

//             if ($exists > 0) {
//                 $_SESSION['error_message'] = "CCCD/Tên đăng nhập đã tồn tại. Vui lòng chọn giá trị khác.";
//                 header("Location: patients.php");
//                 exit();
//             }

//             $con->beginTransaction();

//             // ✅ Cập nhật bảng patients
//             $queryPatient = "UPDATE `patients` 
//                 SET `patient_name` = :patient_name, 
//                     `address` = :address, 
//                     `cnic` = :cnic, 
//                     `date_of_birth` = :date_of_birth, 
//                     `phone_number` = :phone_number, 
//                     `gender` = :gender 
//                 WHERE `id` = :id";

//             $stmtPatient = $con->prepare($queryPatient);
//             $stmtPatient->execute([
//                 ':patient_name' => $patientName,
//                 ':address' => $address,
//                 ':cnic' => $cnic,
//                 ':date_of_birth' => $dateBirth,
//                 ':phone_number' => $phoneNumber,
//                 ':gender' => $gender,
//                 ':id' => $hiddenId
//             ]);

//             // ✅ Cập nhật bảng user_patients
//             $queryUser = "UPDATE `user_patients` 
//                 SET `user_name` = :user_name, 
//                     `display_name` = :display_name 
//                 WHERE `id_patient` = :id_patient";

//             $stmtUser = $con->prepare($queryUser);
//             $stmtUser->execute([
//                 ':user_name' => $cnic,            // CCCD làm username
//                 ':display_name' => $patientName, // tên hiển thị
//                 ':id_patient' => $hiddenId
//             ]);

//             // 🔹 4. Lấy dữ liệu mới sau khi cập nhật
//             $stmtNew = $con->prepare("SELECT * FROM patients WHERE id = ?");
//             $stmtNew->execute([$hiddenId]);
//             $newData = $stmtNew->fetch(PDO::FETCH_ASSOC);

//             // 🔹 5. Ghi log audit
//             log_audit(
//                 $con,
//                 $_SESSION['user_id'] ?? 'unknown', // người thao tác
//                 'patients',                        // bảng bị tác động
//                 $hiddenId,                         // id bản ghi
//                 'update',                          // hành động
//                 $oldData,                          // dữ liệu cũ
//                 $newData                           // dữ liệu mới
//             );

//             // 🔹 6. Commit transaction
//             $con->commit();
//             $_SESSION['success_message'] = 'Cập nhật dữ liệu thành công.';
//         } catch (PDOException $ex) {
//             $con->rollBack();
//             $_SESSION['error_message'] = "Lỗi khi cập nhật: " . $ex->getMessage();
//             header("Location: patients.php");
//             exit;
//         }
//     }

//     header("Location: patients.php");
//     exit();
// }
if (isset($_POST['save_Patient'])) {

    $hiddenId      = (int)$_POST['hidden_id'];
    $patientName   = trim($_POST['patient_name'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $cnic          = trim($_POST['cnic'] ?? '');
    $dateBirthRaw  = $_POST['date_of_birth'] ?? '';
    $phoneNumber   = trim($_POST['phone_number'] ?? '');
    $gender        = $_POST['gender'] ?? '';

    $errors = [];

    /* ================= VALIDATE ================= */

    /* 1. KIỂM TRA BỎ TRỐNG – CHỈ 1 LỖI */
    if (
        $patientName === '' ||
        $address === '' ||
        $cnic === '' ||
        $dateBirthRaw === '' ||
        $phoneNumber === '' ||
        $gender === ''
    ) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin";
    }

    /* 2. VALIDATE TÊN */
    elseif (!preg_match('/^[\p{L}\s]{2,100}$/u', $patientName)) {
        $errors[] = "Tên bệnh nhân không hợp lệ";
    }

    /* 3. VALIDATE ĐỊA CHỈ */
    elseif (strlen($address) < 5) {
        $errors[] = "Địa chỉ không hợp lệ";
    }

    /* 4. VALIDATE CCCD */
    elseif (!preg_match('/^\d{12}$/', $cnic) || $cnic === str_repeat('0', 12)) {
        $errors[] = "CCCD không hợp lệ";
    }

    /* 5. VALIDATE NGÀY SINH (GỘP LỖI) */
    else {
        $ts = strtotime(str_replace('/', '-', $dateBirthRaw));
        if ($ts === false || $ts > time()) {
            $errors[] = "Ngày sinh không hợp lệ";
        } else {
            $dateBirth = date("Y-m-d", $ts);
        }
    }

    /* 6. VALIDATE SỐ ĐIỆN THOẠI */
    if (empty($errors) && !preg_match('/^0\d{9,10}$/', $phoneNumber)) {
        $errors[] = "Số điện thoại không hợp lệ";
    }

    /* 7. VALIDATE GIỚI TÍNH */
    if (empty($errors) && !in_array($gender, ['Nam', 'Nữ', 'Khác'])) {
        $errors[] = "Giới tính không hợp lệ";
    }

    /* ================= CHECK TRÙNG CCCD ================= */
    if (empty($errors)) {
        $stmtCheck = $con->prepare("
            SELECT COUNT(*) 
            FROM patients 
            WHERE cnic = :cnic AND id != :id
        ");
        $stmtCheck->execute([
            ':cnic' => $cnic,
            ':id'   => $hiddenId
        ]);

        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = "CCCD đã tồn tại";
        }
    }

    /* ================= NẾU CÓ LỖI ================= */
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode('<br>', $errors);
        header("Location: update_patient.php?id=" . $hiddenId);
        exit;
    }

    /* ================= UPDATE ================= */
    try {
        $con->beginTransaction();

        // lấy dữ liệu cũ
        $stmtOld = $con->prepare("SELECT * FROM patients WHERE id = ?");
        $stmtOld->execute([$hiddenId]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            throw new Exception("Không tìm thấy bệnh nhân");
        }

        // cập nhật patients
        $stmt = $con->prepare("
            UPDATE patients SET
                patient_name  = :patient_name,
                address       = :address,
                cnic          = :cnic,
                date_of_birth = :dob,
                phone_number  = :phone,
                gender        = :gender
            WHERE id = :id
        ");
        $stmt->execute([
            ':patient_name' => ucwords(strtolower($patientName)),
            ':address'      => $address,
            ':cnic'         => $cnic,
            ':dob'          => $dateBirth,
            ':phone'        => $phoneNumber,
            ':gender'       => $gender,
            ':id'           => $hiddenId
        ]);

        // cập nhật user_patients
        $stmtUser = $con->prepare("
            UPDATE user_patients
            SET user_name = :user_name,
                display_name = :display_name
            WHERE id_patient = :id
        ");
        $stmtUser->execute([
            ':user_name'    => $cnic,
            ':display_name' => $patientName,
            ':id'           => $hiddenId
        ]);

        // lấy dữ liệu mới
        $stmtNew = $con->prepare("SELECT * FROM patients WHERE id = ?");
        $stmtNew->execute([$hiddenId]);
        $newData = $stmtNew->fetch(PDO::FETCH_ASSOC);

        // log audit
        log_audit(
            $con,
            $_SESSION['user_id'] ?? 'unknown',
            'patients',
            $hiddenId,
            'update',
            $oldData,
            $newData
        );

        $con->commit();

        // $_SESSION['success_message'] = "Cập nhật bệnh nhân thành công";
        // header("Location: patients.php");
        // exit;
        $_SESSION['success_message'] = "Cập nhật bệnh nhân thành công";
header("Location: update_patient.php");
exit;



    } catch (Exception $ex) {
        $con->rollBack();
        $_SESSION['error_message'] = "Lỗi hệ thống, vui lòng thử lại";
        header("Location: update_patient.php?id=" . $hiddenId);
        exit;
    }
}
try {
    // Ưu tiên lấy id từ POST (khi click nút ở patients.php)
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
    }
    // Fallback: nếu ai đó vẫn truy cập kiểu GET cũ thì vẫn hoạt động
    elseif (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } else {
        // Không có id -> quay về danh sách
        header("Location: patients.php");
        exit;
    }

    if (empty($id)) {
        header("Location: patients.php");
        exit;
    }

    $query = "SELECT `id`, `patient_name`, `address`,
                     `cnic`,
                     DATE_FORMAT(`date_of_birth`, '%m/%d/%Y') AS `date_of_birth`,
                     `phone_number`, `gender`
              FROM `patients`
              WHERE `id` = :id";

    $stmtPatient1 = $con->prepare($query);
    $stmtPatient1->execute([':id' => $id]);
    $row = $stmtPatient1->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // không tìm thấy bệnh nhân
        header("Location: patients.php");
        exit;
    }

    $gender = $row['gender'];
    $dob    = $row['date_of_birth'];

} catch (PDOException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

</head>

<?php include './config/site_css_links.php';?>
<?php include './config/data_tables_css.php';?>
<!-- Thêm favicon -->
<link rel="icon" type="image/png" href="assets/images/img-tn.png">
<link rel="apple-touch-icon" href="assets/images/img-tn.png">
<link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<title>Chỉnh sửa bệnh nhân - MedTrack</title>
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

<head>

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
                            <!-- <h1>Bệnh Nhân</h1> -->
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
                            <i class="fa-solid fa-pen-to-square"></i>
                            CHỈNH SỬA THÔNG TIN BỆNH NHÂN
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="hidden_id" value="<?php echo $row['id'];?>">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên bệnh nhân <span class="text-danger">*</span></label>
                                    <input type="text" id="patient_name" name="patient_name" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['patient_name'];?>" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ <span class="text-danger">*</span></label>
                                    <input type="text" id="address" name="address" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['address'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD <span class="text-danger">*</span></label>
                                    <input type="text" id="cnic" name="cnic" required="required"
                                        class="form-control form-control-sm" value="<?php echo $row['cnic'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh <span class="text-danger">*</span></label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" class="form-control form-control-sm datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                data-toggle="datetimepicker" autocomplete="off"
                                                value="<?php echo (!empty($dob)) ? date('d/m/Y', strtotime($dob)) : ''; ?>" />
                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" id="phone_number" name="phone_number" required="required"
                                        class="form-control form-control-sm"
                                        value="<?php echo $row['phone_number'];?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="gender" name="gender" required>
                                        <?php echo getGender($gender);?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm px-3">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>
                                        Cập nhật
                                    </button>
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
    <?php include './config/data_tables_js.php'; ?>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="date.js"></script>
    <script src="dist/js/common_javascript_functions.js"></script>

    <script>
    showMenuSelected("#mnu_patients", "#mi_patients");
    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';
    if (message !== '') {
        Swal.fire({
            icon: messageType,
            title: 'Cập nhật bệnh nhân thành công!',
            // html: message,
            showConfirmButton: false,
            timer: messageType === 'success' ? 1200 : null,
            timerProgressBar: messageType === 'success'
        }).then(() => {
            if (messageType === 'success') {
                window.location.href = 'patients.php';
            }
        });
    }

    $('#date_of_birth').datetimepicker({
        format: 'L'
    });
    </script>
</body>

</html>