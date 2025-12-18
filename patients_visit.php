<?php 
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
islogin([2]);
$message = '';
// ===== RESET TRẠNG THÁI KHI LOAD FORM KHÁM MỚI =====
// if ($_SERVER['REQUEST_METHOD'] === 'GET') {
//     unset($_SESSION['last_visit_id']);
//     unset($_SESSION['last_patient_id']);
// }
// reset CHỈ khi chọn bệnh nhân mới
if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['new_exam'])
) {
    unset($_SESSION['last_visit_id']);
    unset($_SESSION['last_patient_id']);
    unset($_SESSION['exam_old']);
}


// ====================== SAU KHI TÁCH 2 FORM ======================

// ---------- FORM 1: LƯU KHÁM BỆNH ----------
if (isset($_POST['submit_exam'])) {

    // lấy dữ liệu khám
    $patientId   = $_POST['patient'] ?? null;
    $bp          = $_POST['bp'] ?? '';
    $weight      = $_POST['weight'] ?? '';
    $height      = $_POST['height'] ?? '';
    $temperature = $_POST['temperature'] ?? '';
    $pulse       = $_POST['pulse'] ?? '';
    $heartRate   = $_POST['heart_rate'] ?? '';
    $tc          = $_POST['tc'] ?? '';
    $cd          = $_POST['cd'] ?? '';
    $bienphap    = $_POST['bienphap'] ?? '';
    $nv          = $_POST['nv'] ?? '';
    $disease     = $_POST['disease'] ?? '';

    // bắt buộc chọn bệnh nhân
    // if (empty($patientId)) {
    //     $_SESSION['error_message'] = 'Vui lòng chọn bệnh nhân trước khi lưu khám bệnh.';
    //     header("Location: patients_visit.php");
    //     exit();
    // }
    // ===== VALIDATE BACKEND BẮT BUỘC =====
// ===== VALIDATE UPLOAD HÌNH ẢNH Y TẾ (ƯU TIÊN CAO) =====
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
$maxSize = 2 * 1024 * 1024; // 2MB

$uploadFiles = [
    'ultrasound' => $_FILES['ultrasound'] ?? null,
    'xray'       => $_FILES['xray'] ?? null
];

foreach ($uploadFiles as $file) {
    if ($file && !empty($file['name'])) {

        // kiểm tra định dạng
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error_message'] =
                "Vui lòng chọn file có định dạng ảnh như .JPEG, .PNG hoặc .JPG";
            $_SESSION['exam_old'] = $_POST;
            header("Location: patients_visit.php");
            exit();
        }

        // kiểm tra kích thước
        if ($file['size'] > $maxSize) {
            $_SESSION['error_message'] =
                "Vui lòng chọn file kích thước < 2MB!";
            $_SESSION['exam_old'] = $_POST;
            header("Location: patients_visit.php");
            exit();
        }
    }
}

// ===== VALIDATE LOGIC NGÀY (ƯU TIÊN CAO NHẤT) =====
$visit_date      = $_POST['visit_date'] ?? null;
$next_visit_date = $_POST['next_visit_date'] ?? null;

if (!empty($visit_date) && !empty($next_visit_date)) {
    $visitDateObj = DateTime::createFromFormat('d/m/Y', $visit_date);
    $nextDateObj  = DateTime::createFromFormat('d/m/Y', $next_visit_date);

    if ($visitDateObj && $nextDateObj && $nextDateObj < $visitDateObj) {
        $_SESSION['error_message'] = "Ngày tái khám không được nhỏ hơn ngày khám!";
        $_SESSION['exam_old'] = $_POST;
        header("Location: patients_visit.php");
        exit();
    }
}

    $errors = [];
// ===== VALIDATE CHỈ SỐ SINH HIỆU =====
if ($weight <= 0) {
    $_SESSION['error_message'] = "Cân nặng phải lớn hơn 0!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}

if ($height <= 0) {
    $_SESSION['error_message'] = "Chiều cao phải lớn hơn 0!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}

if ($temperature < 34 || $temperature > 42) {
    $_SESSION['error_message'] = "Nhiệt độ cơ thể không hợp lệ (34–42°C)!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}

if ($pulse < 30 || $pulse > 200) {
    $_SESSION['error_message'] = "Mạch đập không hợp lệ (30–200 bpm)!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}

if ($heartRate < 30 || $heartRate > 200) {
    $_SESSION['error_message'] = "Nhịp tim không hợp lệ (30–200 bpm)!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}


if (empty($bp))          $errors[] = 1;
if (empty($weight))      $errors[] = 1;
if (empty($height))      $errors[] = 1;
if (empty($temperature)) $errors[] = 1;
if (empty($pulse))       $errors[] = 1;
if (empty($heartRate))   $errors[] = 1;
if (empty($tc))          $errors[] = 1;
if (empty($cd))          $errors[] = 1;
if (empty($bienphap))    $errors[] = 1;
if (empty($disease))     $errors[] = 1;
if ($nv === '')          $errors[] = 1;

if (!empty($errors)) {
    $_SESSION['error_message'] = "Vui lòng điền đầy đủ thông tin khám bệnh!";
    $_SESSION['exam_old'] = $_POST;
    header("Location: patients_visit.php");
    exit();
}


    // --- Xử lý ngày hẹn tái khám ---
    $next_visit_date = $_POST['next_visit_date'] ?? null;
    if (!empty($next_visit_date)) {
        $date = DateTime::createFromFormat('d/m/Y', $next_visit_date);
        $next_visit_date = $date ? $date->format('Y-m-d') : null;
    } else {
        $next_visit_date = null;
    }

    // --- file upload ---
    $ultrasound = $_FILES['ultrasound'] ?? null;
    $xray       = $_FILES['xray'] ?? null;

    $ultrasoundDir = "uploads/anhsieuam/";
    $xrayDir        = "uploads/xquang/";
    if (!is_dir($ultrasoundDir)) mkdir($ultrasoundDir, 0777, true);
    if (!is_dir($xrayDir)) mkdir($xrayDir, 0777, true);

    $ultrasoundPath = null;
    if ($ultrasound && !empty($ultrasound['name'])) {
        $ultrasoundName = time() . "_" . basename($ultrasound["name"]);
        $ultrasoundPath = $ultrasoundDir . $ultrasoundName;
        move_uploaded_file($ultrasound["tmp_name"], $ultrasoundPath);
    }

    $xrayPath = null;
    if ($xray && !empty($xray['name'])) {
        $xrayName = time() . "_" . basename($xray["name"]);
        $xrayPath = $xrayDir . $xrayName;
        move_uploaded_file($xray["tmp_name"], $xrayPath);
    }

    $createdAt = date("Y-m-d H:i:s");

    try {
        $con->beginTransaction();

        // --- Thêm hồ sơ khám bệnh ---
        $queryVisit = "INSERT INTO patient_diseases
            (patient_id, huyet_ap, can_nang, chieu_cao, nhiet_do, 
             mach_dap, nhip_tim, anh_sieu_am, anh_chup_xq, 
             trieu_chung, chuan_doan, bien_phap, nhap_vien, tien_su_benh, created_at, next_visit_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtVisit = $con->prepare($queryVisit);
        $stmtVisit->execute([
            $patientId, $bp, $weight, $height, $temperature,
            $pulse, $heartRate, $ultrasoundPath, $xrayPath,
            $tc, $cd, $bienphap, $nv, $disease, $createdAt, $next_visit_date
        ]);

        $lastInsertId = $con->lastInsertId();

        // --- Ghi log audit cho KHÁM ---
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'patient_diseases',
                $lastInsertId,
                'insert',
                null,
                [
                    'patient_id'     => $patientId,
                    'huyet_ap'       => $bp,
                    'can_nang'       => $weight,
                    'chieu_cao'      => $height,
                    'nhiet_do'       => $temperature,
                    'mach_dap'       => $pulse,
                    'nhip_tim'       => $heartRate,
                    'trieu_chung'    => $tc,
                    'chuan_doan'     => $cd,
                    'bien_phap'      => $bienphap,
                    'nhap_vien'      => $nv,
                    'tien_su_benh'   => $disease,
                    'created_at'     => $createdAt,
                    'next_visit_date'=> $next_visit_date,
                    'anh_sieu_am'    => $ultrasoundPath,
                    'anh_chup_xq'    => $xrayPath
                ]
            );
        }

        $con->commit();

        // lưu session để form kê thuốc dùng
        $_SESSION['last_visit_id']   = $lastInsertId;
        $_SESSION['last_patient_id'] = $patientId;

        $_SESSION['success_message'] = 'Thông tin khám bệnh đã được lưu thành công. Bạn có thể chuyển sang kê đơn thuốc.';
        $_SESSION['exam_old'] = $_POST;

        // lưu lại path ảnh để hiển thị lại
        if (!empty($ultrasoundPath)) {
            $_SESSION['exam_old']['ultrasound_path'] = $ultrasoundPath;
        }
        if (!empty($xrayPath)) {
            $_SESSION['exam_old']['xray_path'] = $xrayPath;
        }
        
    } catch (PDOException $ex) {
        $con->rollback();
        $_SESSION['error_message'] = 'Lỗi khi lưu khám bệnh: ' . $ex->getMessage();
        // ✅ lưu data để vẫn hiện lại khi lỗi
        $_SESSION['exam_old'] = $_POST;
    }

    header("Location: patients_visit.php");
    exit();
}


// ---------- FORM 2: LƯU ĐƠN THUỐC ----------
if (isset($_POST['submit_prescription'])) {

    // nếu chưa lưu khám bệnh trước đó thì chặn
    $patientId = $_SESSION['last_patient_id'] ?? null;
    $visitId   = $_SESSION['last_visit_id'] ?? null;

    if (empty($patientId) || empty($visitId)) {
        $_SESSION['error_message'] = 'Bạn phải lưu thông tin khám bệnh (chọn bệnh nhân) trước khi lưu đơn thuốc!';
        header("Location: patients_visit.php");
        exit();
    }

    // lấy thuốc từ form kê đơn
    $medicineIds = $_POST['medicineIds'] ?? [];
    $quantities  = $_POST['quantities'] ?? [];
    $dosages     = $_POST['dosages'] ?? [];
    $notes       = $_POST['notes'] ?? [];

    if (count($medicineIds) == 0) {
        $_SESSION['error_message'] = 'Bạn chưa thêm thuốc nào vào đơn. Vui lòng thêm thuốc trước khi lưu!';
         // ✅ lưu nháp để đổ lại
    $_SESSION['prescription_old'] = [
        'medicineIds' => $medicineIds,
        'quantities'  => $quantities,
        'dosages'     => $dosages,
        'notes'       => $notes,
        'last' => [
            'medicine' => $_POST['last_medicine'] ?? '',
            'quantity' => $_POST['last_quantity'] ?? '',
            'dosage'   => $_POST['last_dosage'] ?? '',
            'note'     => $_POST['last_note'] ?? '',
        ]
    ];  
        header("Location: patients_visit.php");
        exit();
    }

    try {
        $con->beginTransaction();

        $medLog = [];

        foreach ($medicineIds as $index => $medicineId) {
            $quantity = $quantities[$index] ?? null;
            $dosage   = $dosages[$index] ?? null;
            $note     = $notes[$index] ?? null;

            // bạn đang lưu vào patient_medication_history theo patient_id (giữ nguyên)
            $query = "INSERT INTO patient_medication_history 
                      (patient_id, medicine_id, quantity, dosage, note, created_at) 
                      VALUES (:patient_id, :medicine_id, :quantity, :dosage, :note, NOW())";

            $stmt = $con->prepare($query);
            $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
            $stmt->bindParam(':medicine_id', $medicineId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':dosage', $dosage);
            $stmt->bindParam(':note', $note);
            $stmt->execute();

            $medLog[] = [
                'medicine_id' => $medicineId,
                'quantity'    => $quantity,
                'dosage'      => $dosage,
                'note'        => $note
            ];
        }

        // log audit cho ĐƠN THUỐC
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown',
                'patient_medication_history',
                $visitId, // gắn với lần khám gần nhất
                'insert',
                null,
                [
                    'patient_id' => $patientId,
                    'visit_id'   => $visitId,
                    'thuoc'      => $medLog,
                    'created_at' => date("Y-m-d H:i:s")
                ]
            );
        }

        $con->commit();

        // sau khi lưu xong có thể clear session lần khám
        unset($_SESSION['last_visit_id']);
        unset($_SESSION['last_patient_id']);
        unset($_SESSION['exam_old']);
        
        $_SESSION['success_message'] = 'Đơn thuốc đã được lưu thành công!';

        
        // ✅ cờ để JS biết đã lưu xong -> xóa localStorage draft
        $_SESSION['prescription_saved_flag'] = 1;

        // ✅ Xóa nháp session (đơn thuốc)
        unset($_SESSION['prescription_old']);

    } catch (PDOException $ex) {
        $con->rollback();
        $_SESSION['error_message'] = 'Lỗi khi lưu đơn thuốc: ' . $ex->getMessage();
        
        $lastMedicine = $_POST['last_medicine'] ?? '';
        $lastQuantity = $_POST['last_quantity'] ?? '';
        $lastDosage   = $_POST['last_dosage'] ?? '';
        $lastNote     = $_POST['last_note'] ?? '';

        $_SESSION['prescription_old']['last'] = [
            'medicine' => $lastMedicine,
            'quantity' => $lastQuantity,
            'dosage'   => $lastDosage,
            'note'     => $lastNote,
        ];

    }
    

    header("Location: patients_visit.php");
    exit();
}

$patients = getPatients($con, $old['patient'] ?? '');
$nvOptions = Nhapvien($old['nv'] ?? '');

$medicines = getMedicines($con);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php' ?>
    <!-- <link rel="icon" type="image/png" href="assets/images/logoo.png" /> -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <title>Khám Bệnh - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <!-- Thêm favicon giống dashboard.php -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <style>
    body {
        background: #f8fafc;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: none;
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
    }

    .nav-tabs {
        border-bottom: 2px solid #007bff;
        background: #fff;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs .nav-link {
        border: none;
        padding: 12px 24px;
        font-weight: 500;
        color: #6c757d;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs .nav-link.active {
        background: #007bff;
        color: #fff;
        border: none;
    }

    .tab-content {
        background: #fff;
        border-radius: 0 0 12px 12px;
        padding: 20px;
    }

    .btn-primary,
    .btn-success {
        border-radius: 20px;
        transition: 0.2s;
        padding: 8px 24px;
    }

    .btn-primary:hover,
    .btn-success:hover {
        filter: brightness(1.1);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    label {
        font-weight: 500;
        margin-bottom: 5px;
        color: #333;
    }

    .table {
        border-radius: 8px;
        overflow: hidden;
    }

    .table thead {
        background: #007bff;
        color: #fff;
    }

    .section-title {
        color: #5c5c5cff;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 5px;
        border-bottom: 2px solid #e9ecef;
    }

    .btn-next {
        background: linear-gradient(45deg, #285ba7, #20c997);
        border: none;
        color: white;
        padding: 10px 30px;
        border-radius: 25px;
        font-weight: 500;
    }

    .info-row {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    </style>
    <!-- style của upload ảnh -->
    <style>
    .medical-images {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        background: #f9fbfd;
    }

    .upload-box {
        display: block;
        border: 2px dashed #cbd5e0;
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        height: 150px;
        position: relative;
        overflow: hidden;
    }

    .upload-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6b7280;
        font-size: 14px;
    }

    .upload-content i {
        font-size: 32px;
        margin-bottom: 8px;
        color: #007bff;
    }

    .upload-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        position: absolute;
        top: 0;
        left: 0;
    }

    .fas,
    .fa,
    .bi {
        /* color: #007bff !important; */
        font-size: 0.9em;
    }

    /* Chiều cao giống form-control */
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }

    /* Căn giữa text */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }

    /* Mũi tên */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }

    /* Khi focus */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25) !important;
    }

    .calendar-btn {
        cursor: pointer;
        border-left: 0;
        border-radius: 0 10px 10px 0;
    }

    .input-group.date .form-control {
        border-right: 0;
        border-radius: 10px 0 0 10px;
    }

    .calendar-btn i {
        font-size: 16px;
        color: #6b7280;
        transition: .2s;
    }

    .calendar-btn:hover i {
        color: #3b82f6;
        transform: scale(1.1);
    }

    .upload-box {
        position: relative;
        display: block;
        width: 100%;
        cursor: pointer;
    }

    /* nút X hủy ảnh */
    .btn-remove-upload {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        display: none;
        /* mặc định ẩn */
        align-items: center;
        justify-content: center;
        z-index: 5;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-remove-upload:hover {
        background: rgba(220, 53, 69, 0.9);
        transform: scale(1.06);
    }

    /* khi có ảnh thì show nút */
    .upload-box.has-file .btn-remove-upload {
        display: flex;
    }

    /* preview ảnh */
    .upload-box .preview-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 8px;
        display: block;
    }

    /* text mặc định */
    .upload-content {
        min-height: 220px;
        border: 2px dashed #d1d5db;
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        background: #fafafa;
        transition: .2s ease;
    }

    .upload-box.has-file .upload-content {
        border-style: solid;
        padding: 0;
        background: #fff;
    }

    .upload-content img.preview-img {
        cursor: zoom-in;
        border-radius: 8px;
    }

    .img-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .75);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 24px;
        z-index: 9999;
    }

    .img-modal.show {
        display: flex;
    }

    .img-modal__box {
        position: relative;
        max-width: min(1000px, 95vw);
        max-height: 90vh;
    }

    .img-modal__box img {
        max-width: 100%;
        max-height: 90vh;
        display: block;
        border-radius: 12px;
        background: #fff;
    }

    .img-modal__close {
        position: absolute;
        top: -12px;
        right: -12px;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 999px;
        background: #fff;
        cursor: pointer;
        font-size: 22px;
        line-height: 36px;
    }
    </style>
</head>
<?php $old = $_SESSION['exam_old'] ?? [];  
$ultrasoundOld = $old['ultrasound_path'] ?? '';
$xrayOld = $old['xray_path'] ?? '';
// $oldPrescription = $_SESSION['prescription_old'] ?? [];
// $last = $oldPrescription['last'] ?? [];
?>
<?php
$prescriptionSavedFlag = isset($_SESSION['prescription_saved_flag']) ? 1 : 0;
unset($_SESSION['prescription_saved_flag']); // ✅ unset ở đây là chuẩn nhất
?>

<?php //  cờ đã lưu khám bệnh
$examSaved = isset($_SESSION['last_visit_id']) && isset($_SESSION['last_patient_id']);
 ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include './config/header.php';
        include './config/sidebar.php';?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <!-- <h1>Khám Bệnh</h1>
                            <p class="text-muted">Hệ thống khám bệnh và kê đơn thuốc tích hợp</p> -->
                        </div>
                    </div>
                </div>
            </section>


            <section class="content">
                <div class="card">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="medicalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam"
                                type="button" role="tab" aria-controls="exam" aria-selected="true">
                                <i class="fas fa-user-md me-2"></i>Thông tin khám bệnh
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <!-- <button class="nav-link" id="prescription-tab" data-bs-toggle="tab"
                                data-bs-target="#prescription" type="button" role="tab" aria-controls="prescription"
                                aria-selected="false">
                                <i class="fas fa-pills me-2"></i>Kê đơn thuốc
                            </button> -->
                            <button class="nav-link" id="prescription-tab" data-bs-toggle="tab"
                                data-bs-target="#prescription" type="button" role="tab"
                                <?php echo $examSaved ? '' : 'disabled'; ?>>
                                <i class="fas fa-pills me-2"></i>Kê đơn thuốc
                            </button>

                        </li>
                    </ul>

                    <!-- Tab content (CHỈ 1 CÁI DUY NHẤT) -->
                    <div class="tab-content p-3" id="medicalTabContent">

                        <!-- ================= TAB 1: KHÁM BỆNH ================= -->
                        <div class="tab-pane fade show active" id="exam" role="tabpanel" aria-labelledby="exam-tab">

                            <form method="post" enctype="multipart/form-data" id="examForm">

                                <h5 class="section-title"><i class="fas fa-user-injured"></i> Thông tin bệnh nhân</h5>

                                <div class="row">
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Chọn bệnh nhân <span class="text-danger">*</span></label>
                                        <select id="patient" name="patient" class="form-control setupSelect2">
                                            <?php echo $patients; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Ngày khám <span class="text-danger">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="visit_date"
                                                id="visit_date_input" autocomplete="off"
                                                value="<?= htmlspecialchars($old['visit_date'] ?? '') ?>">
                                            <span class="input-group-text bg-white calendar-btn" id="visit_date_btn">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Ngày tái khám <span class="text-danger">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="next_visit_date"
                                                id="next_visit_date_input" autocomplete="off"
                                                value="<?= htmlspecialchars($old['next_visit_date'] ?? '') ?>">
                                            <span class="input-group-text bg-white calendar-btn"
                                                id="next_visit_date_btn">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="section-title"><i class="fas fa-heartbeat"></i> Chỉ số sinh hiệu</h5>
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Huyết áp (mmHg) <span class="text-danger">*</span></label>
                                            <input type="text" id="bp" class="form-control" name="bp"
                                                placeholder="120/80"
                                                value="<?= htmlspecialchars($old['bp'] ?? '') ?>" />
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Cân nặng (kg) <span class="text-danger">*</span></label>
                                            <input type="number" id="weight" name="weight" class="form-control"
                                                placeholder="50" step="0.1"
                                                value="<?= htmlspecialchars($old['weight'] ?? '') ?>" />
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Chiều cao (cm) <span class="text-danger">*</span></label>
                                            <input type="number" id="height" name="height" class="form-control"
                                                placeholder="170"
                                                value="<?= htmlspecialchars($old['height'] ?? '') ?>" />
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Nhiệt độ (°C) <span class="text-danger">*</span></label>
                                            <input type="number" id="temperature" name="temperature"
                                                class="form-control" step="0.1" placeholder="36.5"
                                                value="<?= htmlspecialchars($old['temperature'] ?? '') ?>" />
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Mạch đập (bpm) <span class="text-danger">*</span></label>
                                            <input type="number" id="pulse" name="pulse" class="form-control"
                                                placeholder="70" value="<?= htmlspecialchars($old['pulse'] ?? '') ?>" />
                                        </div>

                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Nhịp tim (bpm) <span class="text-danger">*</span></label>
                                            <input type="number" id="heart_rate" name="heart_rate" class="form-control"
                                                placeholder="70"
                                                value="<?= htmlspecialchars($old['heart_rate'] ?? '') ?>" />
                                        </div>
                                    </div>
                                </div>

                                <h5 class="section-title"><i class="fas fa-file-medical"></i> Hình ảnh y tế</h5>
                                <div class="medical-images">
                                    <h6><i class="bi bi-image"></i> Hình ảnh y tế</h6>
                                    <div class="row">

                                        <!-- Ảnh siêu âm -->
                                        <div class="col-lg-6 mb-3">
                                            <label for="ultrasound" class="upload-box">
                                                <input type="file" id="ultrasound" name="ultrasound" accept="image/*"
                                                    hidden>

                                                <div class="upload-content" id="ultrasound-preview">
                                                    <?php if (!empty($ultrasoundOld) && file_exists($ultrasoundOld)): ?>
                                                    <img class="preview-img"
                                                        src="<?php echo htmlspecialchars($ultrasoundOld); ?>"
                                                        alt="Ảnh siêu âm"
                                                        style="max-width:100%;height:auto;display:block;">
                                                    <?php else: ?>
                                                    <i class="bi bi-upload"></i>
                                                    <p>Kéo thả hoặc click để tải ảnh siêu âm</p>
                                                    <?php endif; ?>
                                                </div>


                                                <!-- nút thoát upload -->
                                                <button type="button" class="btn-remove-upload" data-target="ultrasound"
                                                    title="Hủy ảnh">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </label>
                                        </div>

                                        <!-- Ảnh X-quang -->
                                        <div class="col-lg-6 mb-3">
                                            <label for="xray" class="upload-box">
                                                <input type="file" id="xray" name="xray" accept="image/*" hidden>

                                                <div class="upload-content" id="xray-preview">
                                                    <?php if (!empty($xrayOld) && file_exists($xrayOld)): ?>
                                                    <img class="preview-img"
                                                        src="<?php echo htmlspecialchars($xrayOld); ?>"
                                                        alt="Ảnh X-quang"
                                                        style="max-width:100%;height:auto;display:block;">
                                                    <?php else: ?>
                                                    <i class="bi bi-upload"></i>
                                                    <p>Kéo thả hoặc click để tải ảnh X-quang</p>
                                                    <?php endif; ?>
                                                </div>


                                                <!-- nút thoát upload -->
                                                <button type="button" class="btn-remove-upload" data-target="xray"
                                                    title="Hủy ảnh">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="img-modal" id="imgModal" aria-hidden="true">
                                        <div class="img-modal__box">
                                            <button type="button" class="img-modal__close" id="imgModalClose"
                                                aria-label="Đóng">&times;</button>
                                            <img id="imgModalSrc" alt="Xem ảnh" />
                                        </div>
                                    </div>
                                </div>

                                <h5 class="section-title">
                                    <i class="fas fa-clipboard-check"></i> Chuẩn đoán và điều trị
                                </h5>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label>Triệu chứng <span class="text-danger">*</span></label>
                                        <textarea id="trieuchung" class="form-control" name="tc" rows="4"
                                            placeholder="Mô tả triệu chứng của bệnh nhân..."><?= htmlspecialchars($old['tc'] ?? '') ?></textarea>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label>Tiền sử bệnh <span class="text-danger">*</span></label>
                                        <textarea id="disease" class="form-control" name="disease" rows="4"
                                            placeholder="Tiền sử bệnh của bệnh nhân..."><?= htmlspecialchars($old['disease'] ?? '') ?></textarea>

                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label>Chuẩn đoán <span class="text-danger">*</span></label>
                                        <textarea id="chuandoan" class="form-control" name="cd" rows="4"
                                            placeholder="Kết quả chẩn đoán..."><?= htmlspecialchars($old['cd'] ?? '') ?></textarea>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label>Biện pháp xử lý <span class="text-danger">*</span></label>
                                        <textarea id="bienphap" class="form-control" name="bienphap" rows="4"
                                            placeholder="Phương pháp điều trị..."><?= htmlspecialchars($old['bienphap'] ?? '') ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label>Yêu cầu nhập viện <span class="text-danger">*</span></label>
                                        <select class="form-control" id="nv" name="nv">
                                            <?php echo $nvOptions; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary" id="submitExam" name="submit_exam"
                                        <?php echo $examSaved ? 'disabled' : ''; ?>>
                                        <i class="fas fa-save me-2"></i> Lưu khám bệnh
                                    </button>


                                    <!-- <button type="button" class="btn btn-next ms-2" id="nextToMedicine" disabled>
                                        Tiếp theo: Kê đơn thuốc <i class="fas fa-arrow-right ms-2"></i>
                                    </button> -->
                                    <button type="button" class="btn btn-next ms-2" id="nextToMedicine"
                                        <?php echo $examSaved ? '' : 'disabled'; ?>>
                                        Tiếp theo: Kê đơn thuốc <i class="fas fa-arrow-right ms-2"></i>
                                    </button>

                                </div>

                            </form>
                        </div>
                        <!-- ================= /TAB 1 ================= -->


                        <!-- ================= TAB 2: KÊ ĐƠN THUỐC ================= -->
                        <div class="tab-pane fade" id="prescription" role="tabpanel" aria-labelledby="prescription-tab">

                            <form method="post" id="prescriptionForm">

                                <h5 class="section-title"><i class="fas fa-prescription-bottle-alt"></i> Kê đơn thuốc
                                </h5>
                                <!-- ===== FORM NHẬP THUỐC ===== -->

                                <!-- DÒNG 1 -->
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label>Chọn loại thuốc</label>
                                        <select id="medicine" class="form-control setupSelect3" name="medicine">
                                            <option value="">-- Chọn thuốc --</option>
                                            <?php echo $medicines; ?>
                                        </select>
                                        <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            const oldMed = "<?= htmlspecialchars($last['medicine'] ?? '') ?>";
                                            if (oldMed) {
                                                const sel = document.getElementById("medicine");
                                                sel.value = oldMed;
                                                // nếu dùng select2
                                                if (window.jQuery && jQuery(sel).hasClass("setupSelect3")) {
                                                    jQuery(sel).trigger("change");
                                                }
                                            }
                                        });
                                        </script>
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label>Số lượng</label>
                                        <input id="quantity" class="form-control" name="quantity" type="number" min="1"
                                            value="<?= htmlspecialchars($last['quantity'] ?? '') ?>"
                                            placeholder="Ví dụ: 10" />
                                    </div>
                                </div>

                                <!-- DÒNG 2 -->
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label>Liều dùng</label>
                                        <input id="dosage" class="form-control" name="dosage" type="text"
                                            value="<?= htmlspecialchars($last['dosage'] ?? '') ?>"
                                            placeholder="Ví dụ: 2" />
                                    </div>

                                    <div class="col-lg-6 mb-3">
                                        <label>Ghi chú</label>
                                        <input id="note" name="note" class="form-control"
                                            value="<?= htmlspecialchars($last['note'] ?? '') ?>"
                                            placeholder="Ví dụ: Sau ăn" />
                                    </div>
                                </div>

                                <!-- NÚT THÊM -->
                                <div class="text-center mt-3">
                                    <button id="add_to_list" type="button" class="btn btn-primary px-5">
                                        <i class="fa fa-plus me-2"></i> Thêm thuốc
                                    </button>
                                </div>


                                <div class="mt-4">
                                    <h6 class="mb-3">Danh sách thuốc đã kê</h6>
                                    <div class="table-responsive">
                                        <table id="medication_list" class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="8%">STT</th>
                                                    <th width="35%">Tên Thuốc</th>
                                                    <th width="12%">Số Lượng</th>
                                                    <th width="20%">Liều Dùng</th>
                                                    <th width="15%">Ghi chú</th>
                                                    <th width="10%">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody id="current_medicines_list">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        Chưa có thuốc nào được thêm vào đơn
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-secondary me-3" id="backToExam">
                                        <i class="fas fa-arrow-left me-2"></i> Quay lại khám bệnh
                                    </button>

                                    <input type="hidden" name="last_medicine" id="last_medicine">
                                    <input type="hidden" name="last_quantity" id="last_quantity">
                                    <input type="hidden" name="last_dosage" id="last_dosage">
                                    <input type="hidden" name="last_note" id="last_note">

                                    <button type="submit" id="submitPrescription" name="submit_prescription"
                                        class="btn btn-success">
                                        <i class="fas fa-save me-2"></i> Lưu đơn thuốc
                                    </button>
                                </div>

                            </form>
                        </div>
                        <!-- ================= /TAB 2 ================= -->

                    </div>
                    <!-- /tab-content -->

                </div>
            </section>

        </div>

        <?php include './config/footer.php';
        
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

        ?>
    </div>

    <?php include './config/site_js_links.php'; ?>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/common_javascript_functions.js"></script>
    <?php include './common_service/loaduser.php';?>

    <script>
    var hasError = false;
    var errorMsg = "";

    $(document).ready(function() {
        const oldPatient = "<?= addslashes($old['patient'] ?? '') ?>";
        const oldNv = "<?= addslashes($old['nv'] ?? '') ?>";

        // ép lại selected cho Select2
        if (oldPatient) {
            $('#patient').val(oldPatient).trigger('change.select2');
        }
        if (oldNv !== '') {
            $('#nv').val(oldNv).trigger('change');
        }
    });
    $(document).ready(function() {

        // ✅ 1) Nếu vừa lưu đơn thuốc thành công -> xóa nháp localStorage + reset UI
        const savedFlag = <?= (int)$prescriptionSavedFlag ?>;

        if (savedFlag === 1) {
            localStorage.removeItem("prescription_draft");

            $("#medicine").val("").trigger("change");
            $("#quantity").val("");
            $("#dosage").val("");
            $("#note").val("");

            $("#current_medicines_list").html(`
      <tr>
        <td colspan="6" class="text-center text-muted py-4">
          Chưa có thuốc nào được thêm vào đơn
        </td>
      </tr>
    `);

            serial = 1;
        } else {
            // ✅ 2) Chỉ khi CHƯA lưu thành công mới load nháp
            loadPrescriptionDraft();
        }

        // ✅ bind autosave 1 lần
        $("#medicine, #quantity, #dosage, #note").on("change keyup", savePrescriptionDraft);

    });

    function previewImage(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);

        input.addEventListener("change", function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="preview">`;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    previewImage("ultrasound", "ultrasound-preview");
    previewImage("xray", "xray-preview");
    </script>
    <script>
    var serial = 1;
    showMenuSelected("#mnu_patients", "#mi_patients_visit");

    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }




    $(document).ready(function() {
        // Initialize datetime pickers
        // $('#visit_date, #next_visit_date').datetimepicker({
        //     format: 'L'
        // });

        // Tab navigation
        $('#btn-kham-benh').click(function() {
            $('#exam-tab').click();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn-ke-don-thuoc').removeClass('btn-primary').addClass('btn-outline-primary');
        });

        $('#btn-ke-don-thuoc').click(function() {
            $('#prescription-tab').click();
            $(this).removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn-kham-benh').removeClass('btn-primary').addClass('btn-outline-primary');
        });

        // Next button
        $("#nextToMedicine").on("click", function(e) {
            e.preventDefault();
            let tab = new bootstrap.Tab(document.querySelector('#prescription-tab'));
            tab.show();
        });


        $("#backToExam").on("click", function(e) {
            e.preventDefault();
            let tab = new bootstrap.Tab(document.querySelector('#exam-tab'));
            tab.show();
        });


        $("#add_to_list").click(function() {

            var medicineId = $("#medicine").val();
            var medicineName = $("#medicine option:selected").text();
            var quantity = $("#quantity").val().trim();
            var dosage = $("#dosage").val().trim();
            var note = $("#note").val().trim();
            var dosageDisplay = dosage + " viên/ngày";

            $("#last_medicine").val(medicineId);
            $("#last_quantity").val(quantity);
            $("#last_dosage").val(dosage);
            $("#last_note").val(note);


            // ===== 1. VALIDATE RỖNG – CHỈ 1 CÂU =====
            if (!medicineId || !quantity || !dosage) {
                return showCustomMessage(
                    "Vui lòng điền đầy đủ thông tin thuốc!",
                    "error"
                );
            }

            // ===== 2. VALIDATE SỐ LƯỢNG =====
            if (isNaN(quantity) || Number(quantity) <= 0) {
                return showCustomMessage(
                    "Số lượng thuốc phải là số lớn hơn 0!",
                    "error"
                );
            }

            // if (isNaN(dosage) || Number(dosage) <= 0) {
            //     return showCustomMessage(
            //         "Liều dùng phải là số lớn hơn 0!",
            //         "error"
            //     );
            // }


            // không cho ký tự đặc biệt
            if (!/^\d+$/.test(quantity)) {
                return showCustomMessage(
                    "Số lượng thuốc không được chứa ký tự đặc biệt!",
                    "error"
                );
            }

            // ===== 3. VALIDATE LIỀU DÙNG =====
            // cho phép chữ, số, /, khoảng trắng
            // if (!/^[\w\s\/\-\.]+$/.test(dosage)) {
            //     return showCustomMessage(
            //         "Liều dùng không được chứa ký tự đặc biệt!",
            //         "error"
            //     );
            // }

            // ===== 4. THÊM THUỐC (GIỮ NGUYÊN CODE CŨ) =====
            if ($("#current_medicines_list tr").length == 1 &&
                $("#current_medicines_list td").attr('colspan')) {
                $("#current_medicines_list").empty();
            }

            var inputs = '';
            inputs += '<input type="hidden" name="medicineIds[]" value="' + medicineId + '" />';
            inputs += '<input type="hidden" name="quantities[]" value="' + quantity + '" />';
            inputs += '<input type="hidden" name="dosages[]" value="' + dosage + '" />';
            inputs += '<input type="hidden" name="notes[]" value="' + note + '" />';

            var tr = `
        <tr>
            <td class="text-center">${serial}</td>
            <td>${medicineName}</td>
            <td class="text-center">${quantity}</td>
            <td>${dosageDisplay}${inputs}</td>

            <td>${note}</td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm"
                    onclick="deleteCurrentRow(this)">
                    <i class="fa fa-times"></i>
                </button>
            </td>
        </tr>
    `;

            $("#current_medicines_list").append(tr);
            savePrescriptionDraft();

            serial++;

            // $("#medicine").val('');
            // $("#quantity").val('');
            // $("#dosage").val('');
            // $("#note").val('');

            // vẫn lưu lat_* để PHP giữ lại nếu submit lỗi
            $("#last_medicine").val(medicineId);
            $("#last_quantity").val(quantity);
            $("#last_dosage").val(dosage);
            $("#last_note").val(note);

            showCustomMessage("Đã thêm thuốc vào đơn!", "success");
        });





        // Form validation
        $('#examForm').on('submit', function(e) {

            // lấy giá trị
            const weight = parseFloat($('#weight').val());
            const height = parseFloat($('#height').val());
            const temperature = parseFloat($('#temperature').val());
            const pulse = parseInt($('#pulse').val());
            const heartRate = parseInt($('#heart_rate').val());

            // ===== ƯU TIÊN VALIDATE CHỈ SỐ TRƯỚC =====
            if (weight <= 0) {
                e.preventDefault();
                return showCustomMessage("Cân nặng phải lớn hơn 0!", "error");
            }

            if (height <= 0) {
                e.preventDefault();
                return showCustomMessage("Chiều cao phải lớn hơn 0!", "error");
            }

            if (temperature < 34 || temperature > 42) {
                e.preventDefault();
                return showCustomMessage("Nhiệt độ cơ thể không hợp lệ (34–42°C)!", "error");
            }

            if (pulse < 30 || pulse > 200) {
                e.preventDefault();
                return showCustomMessage("Mạch đập không hợp lệ (30–200 bpm)!", "error");
            }

            if (heartRate < 30 || heartRate > 200) {
                e.preventDefault();
                return showCustomMessage("Nhịp tim không hợp lệ (30–200 bpm)!", "error");
            }

            // ===== SAU CÙNG MỚI CHECK RỖNG =====
            const requiredFields = [
                'bp', 'weight', 'height', 'temperature',
                'pulse', 'heart_rate', 'tc', 'cd', 'bienphap', 'disease', 'nv'
            ];

            for (let field of requiredFields) {
                if (!$('#' + field).val().trim()) {
                    e.preventDefault();
                    return showCustomMessage(
                        "Vui lòng điền đầy đủ thông tin khám bệnh!",
                        "error"
                    );
                }
            }
        });


        if (hasError) {
            e.preventDefault();
            alert(errorMsg);
            $('#exam-tab').click(); // Switch to exam tab to show errors
        }
    });

    function deleteCurrentRow(btn) {
        // nếu có Swal thì confirm đẹp
        if (typeof window.Swal === "function") {
            Swal.fire({
                title: "Xác nhận xóa",
                text: "Bạn có chắc chắn muốn xóa thuốc này không?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xóa",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(btn).closest('tr').remove();

                    if ($("#current_medicines_list tr").length === 0) {
                        $("#current_medicines_list").html(
                            '<tr><td colspan="6" class="text-center text-muted py-4">Chưa có thuốc nào được thêm vào đơn</td></tr>'
                        );
                        serial = 1;
                    }

                    showCustomMessage("Đã xóa thuốc khỏi đơn.", "success");
                    savePrescriptionDraft();

                }
            });
            return;
        }

        // fallback confirm thường
        if (!confirm("Bạn có chắc chắn muốn xóa thuốc này không?")) return;
        savePrescriptionDraft();

        $(btn).closest('tr').remove();
        if ($("#current_medicines_list tr").length === 0) {
            $("#current_medicines_list").html(
                '<tr><td colspan="6" class="text-center text-muted py-4">Chưa có thuốc nào được thêm vào đơn</td></tr>'
            );
            serial = 1;
        }
    }
    $(document).ready(function() {

        // Khi chọn thuốc khác -> clear các ô còn lại
        $("#medicine").on("change", function() {
            $("#quantity").val("");
            $("#dosage").val("");
            $("#note").val("");

            // cập nhật hidden last_* (để submit lưu đơn thuốc vẫn giữ đúng)
            $("#last_medicine").val($(this).val() || "");
            $("#last_quantity").val("");
            $("#last_dosage").val("");
            $("#last_note").val("");

            // lưu draft lại
            savePrescriptionDraft();
        });

    });


    $(document).ready(function() {
        const fpVisit = flatpickr("#visit_date_input", {
            dateFormat: "d/m/Y",
            locale: "vn",
            allowInput: true
        });

        const fpNext = flatpickr("#next_visit_date_input", {
            dateFormat: "d/m/Y",
            locale: "vn",
            allowInput: true
        });

        $("#visit_date_btn").on("click", function() {
            fpVisit.open();
        });
        $("#next_visit_date_btn").on("click", function() {
            fpNext.open();
        });

        // click vào input cũng open luôn (cho chắc)
        $("#visit_date_input").on("click", function() {
            fpVisit.open();
        });
        $("#next_visit_date_input").on("click", function() {
            fpNext.open();
        });
    });
    $(document).ready(function() {

        function bindUpload(inputId, previewId, labelText) {
            const $input = $("#" + inputId);
            const $preview = $("#" + previewId);
            const $box = $input.closest(".upload-box");

            // khi chọn file -> show preview
            $input.on("change", function() {
                const file = this.files && this.files[0];
                if (!file) return;

                // if (!file.type.startsWith("image/")) {
                //     alert("Vui lòng chọn file ảnh!");
                //     this.value = "";
                //     return;
                // }
                const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
                const maxSize = 2 * 1024 * 1024; // 2MB

                if (!allowedTypes.includes(file.type)) {
                    showCustomMessage("Vui lòng chọn file có định dạng ảnh như .JPEG, .PNG hoặc .JPG",
                        "error");
                    this.value = "";
                    return;
                }

                if (file.size > maxSize) {
                    showCustomMessage("Vui lòng chọn file kích thước < 2MB!", "error");
                    this.value = "";
                    return;
                }


                const reader = new FileReader();
                reader.onload = function(e) {
                    $preview.html(`
                    <img src="${e.target.result}" class="preview-img" alt="preview">
                `);
                    $box.addClass("has-file");
                };
                reader.readAsDataURL(file);
            });

            // nút X -> hủy ảnh
            $box.find(".btn-remove-upload").on("click", function(e) {
                e.preventDefault();
                e.stopPropagation();

                $input.val(""); // reset file

                // trả UI về mặc định
                $preview.html(`
                <i class="bi bi-upload"></i>
                <p>${labelText}</p>
            `);

                $box.removeClass("has-file");
            });
        }

        bindUpload("ultrasound", "ultrasound-preview", "Kéo thả hoặc click để tải ảnh siêu âm");
        bindUpload("xray", "xray-preview", "Kéo thả hoặc click để tải ảnh X-quang");

    });
    $(document).ready(function() {
        $('#patient').trigger('change');
        $('#nv').trigger('change');
    });
    $(document).ready(function() {
        const examSaved = <?php echo $examSaved ? 'true' : 'false'; ?>;

        if (examSaved) {
            $("#submitExam")
                .prop("disabled", true)
                .removeClass("btn-primary")
                .addClass("btn-secondary")
                .html('<i class="fas fa-check me-2"></i> Đã lưu khám bệnh');
        }
    });

    // hiển thị ảnh
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('imgModalSrc');
    const btnClose = document.getElementById('imgModalClose');

    function openModal(src) {
        modalImg.src = src;
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('show');
        modalImg.src = '';
        document.body.style.overflow = '';
    }

    // ✅ BẮT CLICK ẢNH PREVIEW + CHẶN MỞ FILE PICKER (vì ảnh nằm trong label)
    document.addEventListener('click', function(e) {
        const img = e.target.closest('img.preview-img');
        if (!img) return;

        e.preventDefault();
        e.stopPropagation();

        openModal(img.src);
    }, true); // <-- dùng capture để chặn sớm

    btnClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    function savePrescriptionDraft() {
        const rows = [];
        $("#current_medicines_list tr").each(function() {
            const mid = $(this).find("input[name='medicineIds[]']").val();
            if (!mid) return;

            rows.push({
                medicineId: mid,
                quantity: $(this).find("input[name='quantities[]']").val() || "",
                dosage: $(this).find("input[name='dosages[]']").val() || "",
                note: $(this).find("input[name='notes[]']").val() || ""
            });
        });

        const draft = {
            input: {
                medicine: $("#medicine").val() || "",
                quantity: $("#quantity").val() || "",
                dosage: $("#dosage").val() || "",
                note: $("#note").val() || ""
            },
            rows: rows
        };

        localStorage.setItem("prescription_draft", JSON.stringify(draft));
    }

    function loadPrescriptionDraft() {
        const raw = localStorage.getItem("prescription_draft");
        if (!raw) return;

        const draft = JSON.parse(raw);

        // set lại input
        if (draft.input) {
            $("#medicine").val(draft.input.medicine).trigger("change");
            $("#quantity").val(draft.input.quantity);
            $("#dosage").val(draft.input.dosage);
            $("#note").val(draft.input.note);
        }

        // render lại table
        if (draft.rows && draft.rows.length) {
            $("#current_medicines_list").empty();
            serial = 1;
            draft.rows.forEach(r => {
                const medicineName = $("#medicine option[value='" + r.medicineId + "']").text() || ("Thuốc #" +
                    r.medicineId);
                const dosageDisplay = r.dosage + " viên/ngày";

                let inputs = '';
                inputs += '<input type="hidden" name="medicineIds[]" value="' + r.medicineId + '" />';
                inputs += '<input type="hidden" name="quantities[]" value="' + r.quantity + '" />';
                inputs += '<input type="hidden" name="dosages[]" value="' + r.dosage + '" />';
                inputs += '<input type="hidden" name="notes[]" value="' + r.note + '" />';

                const tr = `
        <tr>
          <td class="text-center">${serial}</td>
          <td>${medicineName}</td>
          <td class="text-center">${r.quantity}</td>
          <td>${dosageDisplay}${inputs}</td>
          <td>${r.note}</td>
          <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCurrentRow(this)">
              <i class="fa fa-times"></i>
            </button>
          </td>
        </tr>
      `;
                $("#current_medicines_list").append(tr);
                serial++;
            });
        }
    }
    </script>

    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>



</body>

</html>