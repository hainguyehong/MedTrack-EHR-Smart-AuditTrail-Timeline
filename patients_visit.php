<?php 
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
islogin([2]);
$message = '';

if (isset($_POST['submit'])) {
    $patientId = $_POST['patient'];
    $bp = $_POST['bp']; 
    $weight = $_POST['weight']; 
    $height = $_POST['height']; 
    $temperature = $_POST['temperature']; 
    $pulse = $_POST['pulse']; 
    $heartRate = $_POST['heart_rate']; 
    $ultrasound = $_FILES['ultrasound']; 
    $xray = $_FILES['xray']; 

    $tc = $_POST['tc']; 
    $cd = $_POST['cd']; 
    $bienphap = $_POST['bienphap']; 
    $nv = $_POST['nv']; 
    $disease = $_POST['disease']; 

    // --- Xử lý ngày hẹn tái khám ---
    $next_visit_date = $_POST['next_visit_date'] ?? null;
    if (!empty($next_visit_date)) {
        $date = DateTime::createFromFormat('d/m/Y', $next_visit_date);
        $next_visit_date = $date ? $date->format('Y-m-d') : null;
    } else {
        $next_visit_date = null;
    }

    // --- Tạo thư mục upload nếu chưa có ---
    $ultrasoundDir = "uploads/anhsieuam/";
    $xrayDir = "uploads/xquang/";
    if (!is_dir($ultrasoundDir)) mkdir($ultrasoundDir, 0777, true);
    if (!is_dir($xrayDir)) mkdir($xrayDir, 0777, true);

    // --- Upload file ---
    $ultrasoundPath = null;
    if (!empty($ultrasound['name'])) {
        $ultrasoundName = time() . "_" . basename($ultrasound["name"]);
        $ultrasoundPath = $ultrasoundDir . $ultrasoundName;
        move_uploaded_file($ultrasound["tmp_name"], $ultrasoundPath);
    }

    $xrayPath = null;
    if (!empty($xray['name'])) {
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

        // --- Thêm danh sách thuốc ---
        $medicineIds = $_POST['medicineIds'] ?? [];
        $quantities  = $_POST['quantities'] ?? [];
        $dosages     = $_POST['dosages'] ?? [];
        $notes       = $_POST['notes'] ?? [];

        $medLog = [];
        foreach ($medicineIds as $index => $medicineId) {
            $quantity = $quantities[$index] ?? null;
            $dosage   = $dosages[$index] ?? null;
            $note     = $notes[$index] ?? null;

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
                'quantity' => $quantity,
                'dosage' => $dosage,
                'note' => $note
            ];
        }

        // --- Ghi log audit ---
        if (function_exists('log_audit')) {
            log_audit(
                $con,
                $_SESSION['user_id'] ?? 'unknown', // Người thao tác
                'patient_diseases',                // Bảng bị tác động
                $lastInsertId,                     // ID hồ sơ vừa thêm
                'insert',                          // Hành động
                null,                              // Không có dữ liệu cũ
                [
                    'patient_id' => $patientId,
                    'huyet_ap' => $bp,
                    'can_nang' => $weight,
                    'chieu_cao' => $height,
                    'nhiet_do' => $temperature,
                    'mach_dap' => $pulse,
                    'nhip_tim' => $heartRate,
                    'trieu_chung' => $tc,
                    'chuan_doan' => $cd,
                    'bien_phap' => $bienphap,
                    'nhap_vien' => $nv,
                    'tien_su_benh' => $disease,
                    'thuoc' => $medLog,
                    'created_at' => $createdAt
                ]
            );
        }

        $con->commit();
        $_SESSION['success_message'] = 'Thông tin khám bệnh đã được lưu thành công.';

    } catch (PDOException $ex) {
        $con->rollback();
        $_SESSION['error_message'] = 'Lỗi khi lưu dữ liệu: ' . $ex->getMessage();
        exit;
    }

    header("Location: patients_visit.php");
    exit();
}

$patients = getPatients($con);
$medicines = getMedicines($con);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php' ?>
    <!-- <link rel="icon" type="image/png" href="assets/images/logoo.png" /> -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Khám Bệnh - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include './config/header.php';
        include './config/sidebar.php';?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Khám Bệnh</h1>
                            <p class="text-muted">Hệ thống khám bệnh và kê đơn thuốc tích hợp</p>
                        </div>
                        <!-- <div class="col-sm-6">
                            <div class="float-right">
                                <button type="button" class="btn btn-primary" id="btn-kham-benh">
                                    <i class="fas fa-stethoscope"></i> Khám bệnh
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="btn-ke-don-thuoc">
                                    <i class="fas fa-prescription"></i> Kê đơn thuốc
                                </button>
                            </div>
                        </div> -->
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="card">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="medicalTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam"
                                type="button" role="tab">
                                <i class="fas fa-user-md me-2"></i>Thông tin khám bệnh
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prescription-tab" data-bs-toggle="tab"
                                data-bs-target="#prescription" type="button" role="tab">
                                <i class="fas fa-pills me-2"></i>Kê đơn thuốc
                            </button>
                        </li>
                    </ul>

                    <form method="post" enctype="multipart/form-data" id="medicalForm">
                        <div class="tab-content" id="medicalTabContent">
                            <!-- Tab Khám bệnh -->
                            <div class="tab-pane fade show active" id="exam" role="tabpanel">
                                <h5 class="section-title"><i class="fas fa-user-injured"></i> Thông tin bệnh nhân</h5>

                                <div class="row">
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Chọn bệnh nhân *</label>
                                        <select id="patient" name="patient" class="form-control" required>
                                            <?php echo $patients;?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Ngày khám *</label>
                                        <div class="input-group date" id="visit_date" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input"
                                                data-target="#visit_date" name="visit_date" required
                                                data-toggle="datetimepicker" autocomplete="off"
                                                value="<?php echo date('d/m/Y H:i'); ?>" />
                                            <div class="input-group-append" data-target="#visit_date"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Ngày tái khám</label>
                                        <div class="input-group date" id="next_visit_date" data-target-input="nearest">
                                            <input type="text" id="next_visit_date_input"
                                                class="form-control datetimepicker-input" data-target="#next_visit_date"
                                                name="next_visit_date" data-toggle="datetimepicker"
                                                autocomplete="off" />


                                            <div class="input-group-append" data-target="#next_visit_date"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="section-title"><i class="fas fa-heartbeat"></i> Chỉ số sinh hiệu</h5>
                                <div class="info-row">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Huyết áp (mmHg) *</label>

                                            <input type="text" id="bp" class="form-control" name="bp" required
                                                placeholder="120/80" />

                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Cân nặng (kg) *</label>
                                            <input type="number" id="weight" name="weight" class="form-control" required
                                                step="0.1" />
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Chiều cao (cm) *</label>
                                            <input type="number" id="height" name="height" class="form-control"
                                                required />
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Nhiệt độ (°C) *</label>
                                            <input type="number" id="temperature" name="temperature"
                                                class="form-control" required step="0.1" />
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Mạch đập (bpm) *</label>
                                            <input type="number" id="pulse" name="pulse" class="form-control"
                                                placeholder="Số lần/phút" required />
                                        </div>
                                        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                            <label>Nhịp tim (bpm) *</label>
                                            <input type="number" id="heart_rate" name="heart_rate" class="form-control"
                                                placeholder="Số lần/phút" required />
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
                                                    <i class="bi bi-upload"></i>
                                                    <p>Kéo thả hoặc click để tải ảnh siêu âm</p>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Ảnh X-quang -->
                                        <div class="col-lg-6 mb-3">
                                            <label for="xray" class="upload-box">
                                                <input type="file" id="xray" name="xray" accept="image/*" hidden>
                                                <div class="upload-content" id="xray-preview">
                                                    <i class="bi bi-upload"></i>
                                                    <p>Kéo thả hoặc click để tải ảnh X-quang</p>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                <h5 class="section-title"><i class="fas fa-clipboard-check"></i> Chuẩn đoán và điều trị
                                </h5>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label>Triệu chứng *</label>
                                        <textarea id="trieuchung" class="form-control" name="tc" required rows="4"
                                            placeholder="Mô tả triệu chứng của bệnh nhân..."></textarea>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label>Tiền sử bệnh *</label>
                                        <textarea id="disease" required name="disease" class="form-control" rows="4"
                                            placeholder="Tiền sử bệnh của bệnh nhân..."></textarea>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label>Chuẩn đoán *</label>
                                        <textarea id="chuandoan" class="form-control" name="cd" required rows="4"
                                            placeholder="Kết quả chẩn đoán..."></textarea>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label>Biện pháp xử lý *</label>
                                        <textarea id="bienphap" class="form-control" name="bienphap" required rows="4"
                                            placeholder="Phương pháp điều trị..."></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 mb-3">
                                        <label>Yêu cầu nhập viện *</label>
                                        <select class="form-control" id="nv" name="nv" required>
                                            <?php echo Nhapvien();?>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-center mt-4">
                                    <button type="button" class="btn btn-next" id="nextToMedicine">
                                        Tiếp theo: Kê đơn thuốc <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Kê đơn thuốc -->
                            <div class="tab-pane fade" id="prescription" role="tabpanel">
                                <h5 class="section-title"><i class="fas fa-prescription-bottle-alt"></i> Kê đơn thuốc
                                </h5>

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 mb-3">
                                        <label>Chọn loại thuốc</label>
                                        <select id="medicine" class="form-control" name="medicine">
                                            <option value="">-- Chọn thuốc --</option>
                                            <?php echo $medicines;?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-3 mb-3">
                                        <label>Số lượng</label>
                                        <input id="quantity" class="form-control" name="quantity" type="number"
                                            min="1" />
                                    </div>
                                    <div class="col-lg-2 col-md-3 mb-3">
                                        <label>Liều dùng</label>
                                        <input id="dosage" class="form-control" name="dosage"
                                            placeholder="2 viên/ngày" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 mb-3">
                                        <label>Ghi chú</label>
                                        <input id="note" name="note" class="form-control" placeholder="Sau ăn" />
                                    </div>
                                    <div class="col-lg-2 col-md-2 mb-3">
                                        <label>&nbsp;</label>
                                        <button id="add_to_list" type="button" class="btn btn-primary btn-block">
                                            <i class="fa fa-plus"></i> Thêm
                                        </button>
                                    </div>
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
                                    <button type="submit" id="submit" name="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i> Lưu đơn thuốc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <?php include './config/footer.php';
        $message = '';
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
        ?>
    </div>

    <?php include './config/site_js_links.php'; ?>
    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- cho tải ảnh -->
    <script src="date.js"></script>

    <script>
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

    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }

    $(document).ready(function() {
        // Initialize datetime pickers
        $('#visit_date, #next_visit_date').datetimepicker({
            format: 'L'
        });

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
        $('#nextToMedicine').click(function() {
            $('#prescription-tab').click();
            $('#btn-ke-don-thuoc').click();
        });

        // Back button  
        $('#backToExam').click(function() {
            $('#exam-tab').click();
            $('#btn-kham-benh').click();
        });

        // Add medication to list
        $("#add_to_list").click(function() {
            var medicineId = $("#medicine").val();
            var medicineName = $("#medicine option:selected").text();
            var note = $("#note").val().trim();
            var quantity = $("#quantity").val().trim();
            var dosage = $("#dosage").val().trim();

            if (medicineId && quantity && dosage) {
                // Clear the "no medicine" message if it exists
                if ($("#current_medicines_list tr").length == 1 && $("#current_medicines_list td").attr(
                        'colspan')) {
                    $("#current_medicines_list").empty();
                }

                var inputs = '';
                inputs += '<input type="hidden" name="medicineIds[]" value="' + medicineId + '" />';
                inputs += '<input type="hidden" name="notes[]" value="' + note + '" />';
                inputs += '<input type="hidden" name="quantities[]" value="' + quantity + '" />';
                inputs += '<input type="hidden" name="dosages[]" value="' + dosage + '" />';

                var tr = '<tr>';
                tr += '<td class="text-center">' + serial + '</td>';
                tr += '<td>' + medicineName + '</td>';
                tr += '<td class="text-center">' + quantity + '</td>';
                tr += '<td>' + dosage + inputs + '</td>';
                tr += '<td>' + note + '</td>';
                tr += '<td class="text-center">';
                tr +=
                    '<button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCurrentRow(this);">';
                tr += '<i class="fa fa-times"></i></button></td>';
                tr += '</tr>';

                $("#current_medicines_list").append(tr);
                serial++;

                // Clear form
                $("#medicine").val('');
                $("#note").val('');
                $("#quantity").val('');
                $("#dosage").val('');

                // Show success message
                showCustomMessage('Đã thêm thuốc vào đơn!');
            } else {
                showCustomMessage('Vui lòng nhập đầy đủ thông tin thuốc!');
            }
        });

        // Form validation
        $('#medicalForm').on('submit', function(e) {
            var hasError = false;
            var errorMsg = '';

            // Check if patient is selected
            if (!$('#patient').val()) {
                hasError = true;
                errorMsg += 'Vui lòng chọn bệnh nhân\n';
            }

            // Check required fields in exam tab
            var requiredFields = ['bp', 'weight', 'height', 'temperature', 'pulse', 'heart_rate', 'tc',
                'cd', 'bienphap', 'disease', 'nv'
            ];
            requiredFields.forEach(function(field) {
                if (!$('#' + field).val().trim()) {
                    hasError = true;
                    errorMsg += 'Vui lòng điền đầy đủ thông tin khám bệnh\n';
                    return false;
                }
            });

            if (hasError) {
                e.preventDefault();
                alert(errorMsg);
                $('#exam-tab').click(); // Switch to exam tab to show errors
            }
        });
    });

    function deleteCurrentRow(obj) {
        $(obj).closest('tr').remove();

        // If no medicines left, show placeholder message
        if ($("#current_medicines_list tr").length === 0) {
            $("#current_medicines_list").html(
                '<tr><td colspan="6" class="text-center text-muted py-4">Chưa có thuốc nào được thêm vào đơn</td></tr>'
            );
        }
    }
    $('#next_visit_date').datetimepicker({
        format: 'DD/MM/YYYY',
        useCurrent: false,
        locale: 'vi'
    });
    </script>


    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>

</html>