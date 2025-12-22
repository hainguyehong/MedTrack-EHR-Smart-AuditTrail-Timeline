<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

islogin([2]); // chỉ cho bác sĩ
$old_data = $_SESSION['old_data'] ?? [];
unset($_SESSION['old_data']);


$message = '';
$messageType = '';

if (isset($_POST['save_Patient'])) {

    $patientName   = trim($_POST['patient_name'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $cnic          = trim($_POST['cnic'] ?? '');
    $dateBirthRaw  = $_POST['date_of_birth'] ?? '';
    $phoneNumber   = trim($_POST['phone_number'] ?? '');
    $gender        = $_POST['gender'] ?? '';
    $createdAt     = date("Y-m-d H:i:s");

    $errors = [];

    /* ========= VALIDATE ========= */

/* 1. KIỂM TRA BỎ TRỐNG – CHỈ BÁO 1 LỖI */
if (
    $patientName === '' ||
    $address === '' ||
    $cnic === '' ||
    $dateBirthRaw === '' ||
    $phoneNumber === '' ||
    $gender === ''
) {
    $errors[] = "Vui lòng nhập đầy đủ thông tin bệnh nhân!";
}

/* 2. VALIDATE TÊN (CHỈ 1 LỖI) */
elseif (!preg_match('/^[\p{L}\s]{2,100}$/u', $patientName)) {
    $errors[] = "Tên bệnh nhân không hợp lệ!";
}

/* 3. VALIDATE ĐỊA CHỈ */
elseif (strlen($address) < 5) {
    $errors[] = "Địa chỉ không hợp lệ!";
}

/* 4. VALIDATE CCCD */
elseif (!preg_match('/^\d{12}$/', $cnic) || $cnic === str_repeat('0', 12)) {
    $errors[] = "CCCD không hợp lệ!";
}

/* 5. VALIDATE NGÀY SINH (GỘP HẾT LỖI) */
else {
    $ts = strtotime(str_replace('/', '-', $dateBirthRaw));
    if ($ts === false || $ts > time()) {
        $errors[] = "Ngày sinh không hợp lệ!";
    } else {
        $dateBirth = date("Y-m-d", $ts);
    }
}

/* 6. VALIDATE SỐ ĐIỆN THOẠI */
if (empty($errors) && !preg_match('/^0\d{9,10}$/', $phoneNumber)) {
    $errors[] = "Số điện thoại không hợp lệ!";
}

/* 7. VALIDATE GIỚI TÍNH */
if (empty($errors) && !in_array($gender, ['Nam', 'Nữ', 'Khác'])) {
    $errors[] = "Giới tính không hợp lệ!";
}

   /* ========= CHECK TRÙNG ========= */

if (empty($errors)) {

    // 1. CHECK TRÙNG CCCD (ƯU TIÊN CAO NHẤT)
    $stmt = $con->prepare("
        SELECT id 
        FROM patients 
        WHERE cnic = :cnic
        LIMIT 1
    ");
    $stmt->execute([':cnic' => $cnic]);

    if ($stmt->fetch()) {
        $errors[] = "CCCD đã tồn tại, vui lòng nhập CCCD khác!";
    }
}

/* CHECK TRÙNG SỐ ĐIỆN THOẠI */
if (empty($errors)) {

    $stmt = $con->prepare("
        SELECT id 
        FROM patients 
        WHERE phone_number = :phone
        LIMIT 1
    ");
    $stmt->execute([':phone' => $phoneNumber]);

    if ($stmt->fetch()) {
        $errors[] = "Số điện thoại đã tồn tại, vui lòng nhập số khác!";
    }
}

    if (!empty($errors)) {

    $_SESSION['old_data'] = [
        'patient_name'   => $patientName,
        'address'        => $address,
        'cnic'           => $cnic,
        'date_of_birth'  => $dateBirthRaw,
        'phone_number'   => $phoneNumber,
        'gender'         => $gender
    ];

    $_SESSION['popup_message'] = implode('<br>', $errors);
    $_SESSION['popup_type'] = 'error';

    header("Location: patients.php");
    exit;
}


    /* ========= INSERT ========= */

    try {
        $con->beginTransaction();

        $stmt = $con->prepare("
            INSERT INTO patients
            (patient_name, address, cnic, date_of_birth, phone_number, gender, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            ucwords(strtolower($patientName)),
            $address,
            $cnic,
            $dateBirth,
            $phoneNumber,
            $gender,
            $createdAt
        ]);

        $idPatient = $con->lastInsertId();

        $stmtUser = $con->prepare("
            INSERT INTO user_patients
            (user_name, password, display_name, role, created_at, id_patient)
            VALUES (?, ?, ?, 3, ?, ?)
        ");
        $stmtUser->execute([
            $cnic,
            md5("123456"),
            $patientName,
            $createdAt,
            $idPatient
        ]);

        $con->commit();

        $_SESSION['popup_message'] = "Thêm mới bệnh nhân thành công!";
        $_SESSION['popup_type'] = 'success';

    } catch (PDOException $ex) {
        $con->rollBack();
        $_SESSION['popup_message'] = "Lỗi hệ thống, vui lòng thử lại";
        $_SESSION['popup_type'] = 'error';
    }

    header("Location: patients.php");
    exit;
}

// end if isset save_Patient

// ========= PAGINATION CONFIG =========
$perPage = 10; // số bệnh nhân / trang 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $perPage;

// ========= ĐẾM TỔNG BỆNH NHÂN =========
try {
    $countQuery = "SELECT COUNT(*) as total FROM patients WHERE is_deleted = 0";
    $stmtCount = $con->prepare($countQuery);
    $stmtCount->execute();
    $totalRows = (int)$stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = (int)ceil($totalRows / $perPage);
} catch(PDOException $ex) {
    echo $ex->getMessage();
    exit;
}

// ========= LẤY DS BỆNH NHÂN THEO TRANG =========
try {
    $query = "SELECT `id`, `patient_name`, `address`,
                     `cnic`, date_format(`date_of_birth`, '%d %b %Y') as `date_of_birth`,
                     `phone_number`, `gender`, `created_at`
              FROM `patients`
              WHERE `is_deleted` = 0
              ORDER BY `created_at` DESC
              LIMIT :limit OFFSET :offset";

    $stmtPatient1 = $con->prepare($query);
    $stmtPatient1->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $stmtPatient1->bindValue(':offset', $offset,  PDO::PARAM_INT);
    $stmtPatient1->execute();

} catch(PDOException $ex) {
    echo $ex->getMessage();
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <?php include './config/data_tables_css.php';?>
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">

    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="css/patients.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Thêm bệnh nhân - MedTrack</title>
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
                            <!-- <h1>BỆNH NHÂN</h1>
                            <h7>Quản lý thông tin bệnh nhân và hồ sơ y tế</h7> -->
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
                                        class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($old_data['patient_name'] ?? ''); ?>" />
                                </div>

                                <!-- ĐỊA CHỈ -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Địa chỉ</label>
                                    <input type="text" id="address" name="address" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($old_data['address'] ?? ''); ?>" />

                                </div>

                                <!-- CCCD -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">CCCD</label>
                                    <input type="text" id="cnic" name="cnic" class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($old_data['cnic'] ?? ''); ?>" />

                                </div>

                                <!-- NGÀY SINH -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label class="required">Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" id="date_of_birth_input"
                                                class="form-control form-control-sm datetimepicker-input"
                                                data-target="#date_of_birth" name="date_of_birth"
                                                data-toggle="datetimepicker" autocomplete="off"
                                                value="<?php echo htmlspecialchars($old_data['date_of_birth'] ?? ''); ?>" />


                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <!-- SỐ ĐIỆN THOẠI -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number"
                                        class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($old_data['phone_number'] ?? ''); ?>" />

                                </div>

                                <!-- GIỚI TÍNH -->
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label class="required">Giới tính</label>
                                    <select class="form-control form-control-sm" id="gender" name="gender">
                                        <?php echo getGender($old_data['gender'] ?? ''); ?>
                                    </select>

                                </div>
                            </div>


                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm px-3">
                                        <i class="fa-solid fa-floppy-disk"></i>LƯU
                                    </button>
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
                                        <th>Ngày khám</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                   $count = $offset; // bắt đầu từ offset
                                    while($row = $stmtPatient1->fetch(PDO::FETCH_ASSOC)) {
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
                                            <!-- Nút sửa: POST sang update_patient.php -->
                                            <form method="post" action="update_patient.php" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </form>

                                            <!-- Nút xóa: POST sang delete_patient.php -->
                                            <form method="post" action="delete_patient.php" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

                                        </td>


                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="Patients pagination">
                                <ul class="pagination justify-content-center mt-3">

                                    <!-- Previous -->
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page-1 ?>">«</a>
                                    </li>

                                    <?php
                                    // hiển thị tối đa 5 trang quanh trang hiện tại
                                    $start = max(1, $page - 5);
                                    $end   = min($totalPages, $page + 5);
                                    ?>
                                    <?php for($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page+1 ?>">»</a>
                                    </li>

                                </ul>

                                <div class="text-center text-muted small">
                                    Trang <?= $page ?> / <?= $totalPages ?> (<?= $totalRows ?> bệnh nhân)
                                </div>
                            </nav>
                            <?php endif; ?>
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

        $message = '';
        $messageType = 'info';

        if (isset($_SESSION['popup_message'])) {
            $message = $_SESSION['popup_message'];
            $messageType = $_SESSION['popup_type'] ?? 'info';

            unset($_SESSION['popup_message']);
            unset($_SESSION['popup_type']);
        }

        ?>
        <!-- /.control-sidebar -->
        <?php include './config/site_js_links.php'; ?>
        <?php include './config/data_tables_js.php'; ?>


        <script src="plugins/moment/moment.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>

        </script>
        <script src="plugins/daterangepicker/daterangepicker.js"></script>
        <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="plugins/daterangepicker/date.js"></script>

        <script>
        showMenuSelected("#mnu_patients", "#mi_patients");

        var message = '<?php echo addslashes($message); ?>';
        var messageType = '<?php echo $messageType; ?>';

        if (message !== '') {
            showCustomMessage(message, messageType);
        }


        // Khởi tạo datetimepicker
        $('#date_of_birth').datetimepicker({
            format: 'L'
        });


        document.querySelectorAll("#patientForm input, #patientForm select").forEach(function(el) {
            ["focus", "change"].forEach(function(evt) {
                el.addEventListener(evt, function() {
                    // this.classList.remove("is-invalid");
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
        $(document).ready(function() {
            $('#all_patients').DataTable({
                paging: false,
                info: false,
                lengthChange: false,
                searching: true,
                ordering: false,
                language: {
                    search: "Tìm kiếm bệnh nhân:",
                    zeroRecords: "Không tìm thấy bệnh nhân phù hợp",
                    emptyTable: "Không có dữ liệu"
                }
            });
        });
        </script>

        <?php
        $success = $_SESSION['success_message'] ?? '';
        unset($_SESSION['success_message']);
        ?>

        <script>
        <?php if ($success): ?>
        showCustomMessage("<?php echo $success; ?>", "success");
        <?php endif; ?>
        </script>

</body>

</html>