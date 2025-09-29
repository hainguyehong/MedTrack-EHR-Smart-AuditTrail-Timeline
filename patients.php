<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

// session_start();

$form_errors = $_SESSION['form_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_data']);

$message = '';

if (isset($_POST['save_Patient'])) {
    // Lấy dữ liệu POST
    $patientName = trim($_POST['patient_name']);
    $address = trim($_POST['address']);
    $cnic = trim($_POST['cnic']);
    $dateBirthRaw = $_POST['date_of_birth'] ?? '';
    $phoneNumber = trim($_POST['phone_number']);
    $gender = $_POST['gender'] ?? '';

    $patientName = ucwords(strtolower($patientName));
    $address = ucwords(strtolower($address));
    $createdAt = date("Y-m-d H:i:s");

    $errors = [];

    // Validate tên bệnh nhân 
    if ($patientName === '') {
        $errors['patient_name'] = "Vui lòng nhập tên bệnh nhân!";
    } elseif (mb_strlen($patientName) < 2) {
        $errors['patient_name'] = "Tên phải có ít nhất 2 ký tự!";
    } elseif (mb_strlen($patientName) > 100) {
        $errors['patient_name'] = "Tên không được vượt quá 100 ký tự!";
    } elseif (!preg_match('/^[\p{L}\s]+$/u', $patientName)) {
        $errors['patient_name'] = "Tên không hợp lệ (chỉ gồm chữ và khoảng trắng)";
    }

    // Validate địa chỉ
    if ($address === '') {
        $errors['address'] = "Vui lòng nhập địa chỉ!";
    } elseif (mb_strlen($address) < 5) {
        $errors['address'] = "Địa chỉ quá ngắn!";
    } elseif (mb_strlen($address) > 100) {
        $errors['address'] = "Địa chỉ quá dài!";
    } elseif (preg_match('/^\d+$/', $address)) {
        $errors['address'] = "Địa chỉ không hợp lệ!";
    }

    // Validate CCCD
    if ($cnic === '') {
        $errors['cnic'] = "Vui lòng nhập CCCD!";
    } elseif (!preg_match('/^\d{12}$/', $cnic)) {
        $errors['cnic'] = "CCCD phải gồm 12 số!";
    } elseif ($cnic === str_repeat('0', 12)) {
        $errors['cnic'] = "CCCD không hợp lệ (toàn số 0)!";
    }

    // Validate ngày sinh
    if ($dateBirthRaw === '') {
        $errors['date_of_birth'] = "Vui lòng nhập ngày sinh!";
        $dateBirth = null;
    } else {
        $ts = strtotime(str_replace('/', '-', $dateBirthRaw));
        if ($ts === false) {
            $errors['date_of_birth'] = "Ngày không đúng định dạng!";
            $dateBirth = null;
        } elseif ($ts > time()) {
            $errors['date_of_birth'] = "Ngày sinh không hợp lệ (trong tương lai)!";
            $dateBirth = null;
        } else {
            $age = (int)((time() - $ts) / (365*24*60*60));
            if ($age < 1) {
                $errors['date_of_birth'] = "Ngày sinh không hợp lệ (trẻ hơn 1 tuổi)!";
            } elseif ($age > 120) {
                $errors['date_of_birth'] = "Ngày sinh không hợp lệ (lớn hơn 120 tuổi)!";
            }
            $dateBirth = date("Y-m-d", $ts);
        }
    }

    // Validate số điện thoại
    if ($phoneNumber === '') {
        $errors['phone_number'] = "Vui lòng nhập số điện thoại!";
    } elseif (!preg_match('/^0\d{9,10}$/', $phoneNumber)) {
        $errors['phone_number'] = "Số điện thoại không hợp lệ (bắt đầu bằng 0, gồm 10-11 số)!";
    } elseif ($phoneNumber === str_repeat('0', strlen($phoneNumber))) {
        $errors['phone_number'] = "Số điện thoại không hợp lệ (toàn số 0)!";
    }

    // Validate giới tính
    $validGenders = ['Nam', 'Nữ', 'Khác'];
    if ($gender === '') {
        $errors['gender'] = "Vui lòng chọn giới tính!";
    } elseif (!in_array($gender, $validGenders)) {
        $errors['gender'] = "Giới tính không hợp lệ!";
    }

    // Nếu chưa có lỗi -> kiểm tra trùng CCCD & SĐT
    if (empty($errors)) {
        try {
            $checkQuery = "SELECT cnic, phone_number 
                           FROM patients 
                           WHERE cnic = :cnic OR phone_number = :phone_number";
            $stmtCheck = $con->prepare($checkQuery);
            $stmtCheck->execute([
                ':cnic' => $cnic,
                ':phone_number' => $phoneNumber
            ]);
            $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                if ($row['cnic'] === $cnic) {
                    $errors['cnic'] = "CCCD đã tồn tại, vui lòng nhập CCCD khác!";
                }
                if ($row['phone_number'] === $phoneNumber) {
                    $errors['phone_number'] = "Số điện thoại đã tồn tại, vui lòng nhập số khác!";
                }
            }
        } catch (PDOException $ex) {
            $errors['system'] = "Lỗi hệ thống: " . $ex->getMessage();
        }
    }

    // Nếu có lỗi -> lưu errors + old_data lên session và redirect
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_data'] = $_POST;
        header("Location: patients.php");
        exit();
    }

    // Nếu không có lỗi -> THỰC HIỆN INSERT (giữ nguyên logic transaction và insert của bạn)
    try {
        // kiểm tra cccd đã tồn tại chưa (giữ nguyên code gốc)
        $checkQuery = "SELECT COUNT(*) as cnt FROM `patients` WHERE `cnic` = :cnic";
        $stmtCheck = $con->prepare($checkQuery);
        $stmtCheck->execute([':cnic' => $cnic]);
        $row = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($row['cnt'] > 0) {
            $_SESSION['error_message'] = 'CCCD đã tồn tại, vui lòng nhập CCCD khác.';
            header("Location: patients.php");
            exit();
        }
        // query insert patient (giữ nguyên)
        $queryPatient = "INSERT INTO `patients`(`patient_name`,
                `address`, `cnic`, `date_of_birth`, `phone_number`, `gender`, `created_at`)
                VALUES(:patient_name, :address, :cnic, :date_of_birth,
                :phone_number, :gender, :created_at)";
        // query insert user (giữ nguyên)
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
        // bạn có thể log $ex->getMessage() vào log file, ở đây tạm echo / set session error
        $_SESSION['error_message'] = 'Lỗi hệ thống: ' . $ex->getMessage();
        header("Location: patients.php");
        exit;
    }
    header("Location: patients.php");
    exit();
} // end if isset save_Patient

try { // lấy danh sách bệnh nhân (giữ nguyên)
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
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/patients.css">
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
                            <h1>BỆNH NHÂN</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <!-- THÊM BỆNH NHÂN -->
                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <!-- <h3 class="card-title">Thêm mới bệnh nhân</h3> -->
                        <h3 class="card-title"><i class="fa-solid fa-user-plus"></i>THÊM MỚI BỆNH NHÂN</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>

                    <!-- FORM -->
                    <div class="card-body">
                        <form method="post" id="patientForm" novalidate>
                            <div class="row">
                                <!-- TÊN BỆNH NHÂN -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" name="patient_name"
                                        class="form-control form-control-sm <?php echo isset($form_errors['patient_name']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old_data['patient_name'] ?? ''); ?>" />
                                    <?php if (isset($form_errors['patient_name'])): ?>
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i>
                                        <?php echo $form_errors['patient_name']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- ĐỊA CHỈ -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Địa chỉ</label>
                                    <input type="text" id="address" name="address"
                                        class="form-control form-control-sm <?php echo isset($form_errors['address']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old_data['address'] ?? ''); ?>" />
                                    <?php if (isset($form_errors['address'])): ?>
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i>
                                        <?php echo $form_errors['address']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- CCCD -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">CCCD</label>
                                    <input type="text" id="cnic" name="cnic"
                                        class="form-control form-control-sm <?php echo isset($form_errors['cnic']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old_data['cnic'] ?? ''); ?>" />
                                    <?php if (isset($form_errors['cnic'])): ?>
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i>
                                        <?php echo $form_errors['cnic']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- NGÀY SINH -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label class="required">Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" id="date_of_birth_input"
                                                class="form-control form-control-sm datetimepicker-input <?php echo isset($form_errors['date_of_birth']) ? 'is-invalid' : ''; ?>"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                data-toggle="datetimepicker" autocomplete="off"
                                                value="<?php echo htmlspecialchars($old_data['date_of_birth'] ?? ''); ?>" />


                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                        <?php if (isset($form_errors['date_of_birth'])): ?>
                                        <span class="error-message" id="dob-error"><i
                                                class="fas fa-exclamation-circle"></i>
                                            <?php echo $form_errors['date_of_birth']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- SỐ ĐIỆN THOẠI -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number"
                                        class="form-control form-control-sm <?php echo isset($form_errors['phone_number']) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo htmlspecialchars($old_data['phone_number'] ?? ''); ?>" />
                                    <?php if (isset($form_errors['phone_number'])): ?>
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i>
                                        <?php echo $form_errors['phone_number']; ?></span>
                                    <?php endif; ?>
                                </div>

                                <!-- GIỚI TÍNH -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Giới tính</label>
                                    <select
                                        class="form-control form-control-sm <?php echo isset($form_errors['gender']) ? 'is-invalid' : ''; ?>"
                                        id="gender" name="gender" required>
                                        <?php echo getGender(); ?>
                                    </select>
                                    <?php if (isset($form_errors['gender'])): ?>
                                    <span class="error-message"><i class="fas fa-exclamation-circle"></i>
                                        <?php echo $form_errors['gender']; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>
                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>
                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-block"><i
                                            class="fa-solid fa-floppy-disk"></i>Lưu</button>
                                </div>
                            </div>
                        </form>
                        <!-- END FORM -->
                    </div>
                </div>
            </section>
            <!-- </section> -->
            <br />
            <br />
            <br />

            <!-- DANH SÁCH BỆNH NHÂN -->
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-list"></i>DANH SÁCH BỆNH NHÂN</h3>

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

        // Khởi tạo datetimepicker
        $('#date_of_birth').datetimepicker({
            format: 'L'
        });

        //Client-side validation: Hiển thị lỗi đỏ và xóa lỗi khi user thao tác lại
        (function() {
            var form = document.getElementById('patientForm');
            form.addEventListener('submit', function(e) {
                // reset lỗi
                document.querySelectorAll('.error-text').forEach(function(el) {
                    el.textContent = '';
                });
                var hasError = false;
                // TÊN BỆNH NHÂN
                var name = document.getElementById('patient_name');
                if (!name.value.trim()) {
                    document.getElementById('error_patient_name').textContent =
                        'Vui lòng nhập tên bệnh nhân!';
                    hasError = true;
                } else if (!/^[a-zA-ZÀ-ỹ\s]+$/.test(name.value.trim())) {
                    document.getElementById('error_patient_name').textContent =
                        'Tên bệnh nhân không hợp lệ!';
                    hasError = true;
                }
                // ĐỊA CHỈ
                var address = document.getElementById('address');
                if (!address.value.trim()) {
                    document.getElementById('error_address').textContent = 'Vui lòng nhập địa chỉ!';
                    hasError = true;
                }
                // CCCD
                var cnic = document.getElementById('cnic');
                if (!cnic.value.trim()) {
                    document.getElementById('error_cnic').textContent = 'Vui lòng nhập CCCD!';
                    hasError = true;
                } else if (!/^\d{12}$/.test(cnic.value.trim())) {
                    document.getElementById('error_cnic').textContent = 'CCCD phải gồm 12 số!';
                    hasError = true;
                }
                // NGÀY SINH
                var dobInput = document.querySelector("input[name='date_of_birth']");
                if (!dobInput.value.trim()) {
                    document.getElementById('error_date_of_birth').textContent = 'Vui lòng nhập ngày sinh!';
                    hasError = true;
                } else {
                    // kiểm tra ngày không lớn hơn ngày hiện tại (hỗ trợ dd/mm/yyyy hoặc yyyy-mm-dd)
                    var s = dobInput.value.trim();
                    var parts;
                    var dt;
                    if (s.indexOf('/') !== -1) {
                        parts = s.split('/');
                        // dd/mm/yyyy
                        dt = new Date(parts[2], parts[1] - 1, parts[0]);
                    } else if (s.indexOf('-') !== -1) {
                        parts = s.split('-');
                        // yyyy-mm-dd
                        dt = new Date(parts[0], parts[1] - 1, parts[2]);
                    } else {
                        dt = new Date(s);
                    }
                    var now = new Date();
                    // set giờ về 0:0:0 so sánh ngày
                    dt.setHours(0, 0, 0, 0);
                    now.setHours(0, 0, 0, 0);
                    if (isNaN(dt.getTime())) {
                        document.getElementById('error_date_of_birth').textContent =
                            'Ngày không đúng định dạng!';
                        hasError = true;
                    } else if (dt > now) {
                        document.getElementById('error_date_of_birth').textContent =
                            'Ngày sinh không hợp lệ!';
                        hasError = true;
                    }
                }
                // SĐT
                var phone = document.getElementById('phone_number');
                if (!phone.value.trim()) {
                    document.getElementById('error_phone_number').textContent =
                        'Vui lòng nhập số điện thoại!';
                    hasError = true;
                } else if (!/^\d{10,11}$/.test(phone.value.trim())) {
                    document.getElementById('error_phone_number').textContent =
                        'Số điện thoại không hợp lệ (10 hoặc 11 số)!';
                    hasError = true;
                }
                // GIỚI TÍNH
                var gender = document.getElementById('gender');
                if (!gender.value.trim()) {
                    document.getElementById('error_gender').textContent = 'Vui lòng chọn giới tính!';
                    hasError = true;
                }
                if (hasError) {
                    e.preventDefault();
                }
            });

            // Clear error when user types/selects
            document.querySelectorAll('#patientForm input, #patientForm select').forEach(function(el) {
                el.addEventListener('input', function() {
                    var eid = this.id ? 'error_' + this.id : null;
                    if (!eid && this.name === 'date_of_birth') eid = 'error_date_of_birth';
                    if (eid) {
                        var errEl = document.getElementById(eid);
                        if (errEl) errEl.textContent = '';
                    }
                });
                // clear on focus as well
                el.addEventListener('focus', function() {
                    var eid = this.id ? 'error_' + this.id : null;
                    if (!eid && this.name === 'date_of_birth') eid = 'error_date_of_birth';
                    if (eid) {
                        var errEl = document.getElementById(eid);
                        if (errEl) errEl.textContent = '';
                    }
                });
            });
        })();



        // document.getElementById('patient_name').addEventListener('focus', function() {
        //     this.classList.remove('error-input');
        //     const userError = document.getElementById('user_error');
        //     if (userError) userError.style.display = 'none';
        // });
        document.querySelectorAll("#patientForm input, #patientForm select").forEach(function(el) {
            ["focus", "change"].forEach(function(evt) {
                el.addEventListener(evt, function() {
                    this.classList.remove("is-invalid");
                    var next = this.nextElementSibling;
                    if (next && next.classList.contains("error-message")) {
                        next.remove();
                    }
                });
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const dobInput = document.getElementById("date_of_birth_input");
            const dobError = document.getElementById("dob-error"); // chỗ bạn đặt span error


            if (dobInput) {
                // khi gõ tay
                dobInput.addEventListener("input", function() {
                    if (dobInput.value.trim() !== "") {
                        dobInput.classList.remove("is-invalid");
                        if (dobError) dobError.style.display = "none";
                    }
                });


                // khi chọn từ datetimepicker
                $('#date_of_birth').on("change.datetimepicker", function() {
                    if (dobInput.value.trim() !== "") {
                        dobInput.classList.remove("is-invalid");
                        if (dobError) dobError.style.display = "none";
                    }
                });
            }
        });
        // Danh sách bệnh nhân
        $(function() {
            $("#all_patients").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
                "buttons": ["pdf", "print"],

                "language": {
                    "info": " Tổng cộng _TOTAL_ người dùng",
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