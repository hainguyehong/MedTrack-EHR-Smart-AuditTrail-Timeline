<?php
    include './config/connection.php';
    include './common_service/common_functions.php';
    include './common_service/date.php';
    islogin();
    $message = '';

    if (isset($_POST['submit'])) {
        $patientId = $_SESSION['user_id'];

        $tc = $_POST['tc'];
        $cd = $_POST['nd'];

        // --- Xử lý ngày hẹn khám ---
        $visit_date = $_POST['visit_date'] ?? null;
        if (! empty($visit_date)) {
            $date       = DateTime::createFromFormat('d/m/Y', $visit_date);
            $visit_date = $date ? $date->format('Y-m-d') : null;
        } else {
            $visit_date = null;
        }
        $time_visit = $_POST['time_visit'] ?? null;
        $createdAt  = date("Y-m-d H:i:s");

        try {
            $con->beginTransaction();

            // --- Thêm hồ sơ khám bệnh ---
            $queryVisit = "INSERT INTO book
            (id_patient, date_visit, time_visit, trieu_chung, noi_dung_kham, created_at)
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmtVisit = $con->prepare($queryVisit);
            $stmtVisit->execute([
                $patientId,
                $visit_date, $time_visit, $tc, $cd, $createdAt,
            ]);

            $lastInsertId = $con->lastInsertId();

            // --- Ghi log audit ---
            if (function_exists('log_audit')) {
                log_audit(
                    $con,
                    $_SESSION['user_id'] ?? 'unknown', // Người thao tác
                    'book',                            // Bảng bị tác động
                    $lastInsertId,                     // ID hồ sơ vừa thêm
                    'insert',                          // Hành động
                    null,                              // Không có dữ liệu cũ
                    [
                        'id_benh_nhan'  => $patientId,
                        'trieu_chung'   => $tc,
                        'noi_dung_kham' => $cd,
                        'date_visit'    => $visit_date,
                        'time_visit'    => $time_visit,
                        'created_at'    => $createdAt,
                    ]
                );
            }

            $con->commit();
            $_SESSION['success_message'] = 'Đặt lịch khám thành công.';

        } catch (PDOException $ex) {
            $con->rollback();
            $_SESSION['error_message'] = 'Lỗi khi lưu dữ liệu: ' . $ex->getMessage();
            exit;
        }

        header("Location: book.php");
        exit();
    }
    // xóa  lịch khám
    if (isset($_POST['save'])) {
        $id = $_POST['id'];

        try {
            // Bắt đầu transaction
            $con->beginTransaction();

            // 🔍 Lấy dữ liệu cũ
            $queryOld = "SELECT * FROM `book` WHERE `id` = :id LIMIT 1";
            $stmtOld  = $con->prepare($queryOld);
            $stmtOld->execute([':id' => $id]);
            $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

            if (! $oldData) {
                throw new Exception("Không tìm thấy lịch khám với ID = $id.");
            }

            // 🔥 Cập nhật trạng thái xóa
            $queryDelete = "UPDATE `book` SET `is_deleted` = 1 WHERE `id` = :id";
            $stmtDelete  = $con->prepare($queryDelete);
            $stmtDelete->execute([':id' => $id]);

            // 📝 Ghi log audit (nếu có hàm log_audit)
            if (function_exists('log_audit')) {
                log_audit(
                    $con,
                    $_SESSION['user_id'] ?? 'unknown', // người thực hiện
                    'book',                            // bảng
                    $id,                               // id bản ghi
                    'delete',                          // hành động
                    $oldData,                          // dữ liệu cũ
                    ['is_deleted' => 1]                // dữ liệu mới
                );
            }

            // Hoàn tất
            $con->commit();
            $_SESSION['success_message'] = 'Xóa thành công.';

        } catch (Exception $ex) {
            $con->rollBack();
            $_SESSION['error_message'] = "Lỗi khi xóa: " . $ex->getMessage();
        }

        header("Location: book.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php'?>
    <!-- <link rel="icon" type="image/png" href="assets/images/logoo.png" /> -->


    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title> Đặt lịch khám Bệnh - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
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
        include './config/sidebar.php'; ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <!-- <div class="col-sm-6">
                            <h1>Khám Bệnh</h1>
                            <p class="text-muted">Hệ thống khám bệnh và kê đơn thuốc tích hợp</p>
                        </div> -->
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
                                <i class="fas fa-calendar-alt me-2"></i> Thông tin đặt lịch
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="prescription-tab" data-bs-toggle="tab"
                                data-bs-target="#prescription" type="button" role="tab">
                                <i class="fa-solid fa-list"></i> Danh sách lịch đã đặt
                            </button>
                        </li>
                    </ul>

                    <form method="post" id="medicalForm">
                        <div class="tab-content" id="medicalTabContent">
                            <!-- Tab Khám bệnh -->
                            <div class="tab-pane fade show active" id="exam" role="tabpanel">
                                <h5 class="section-title"><i class="fas fa-user-injured"></i> Thông tin bệnh nhân</h5>

                                <div class="row">
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <label> Bệnh nhân </label>
                                        <input type="text" class="form-control" name="patient_name"
                                            value="<?php echo $_SESSION['display_name']; ?>" readonly>
                                        <!--                                                                                                                                     <?php echo $_SESSION['user_id'] ?> -->
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
                                        <label>Giờ khám</label>
                                        <select name="time_visit" class="form-control" required>
                                            <?php echo getTime(); ?>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <h5 class="section-title"><i class="fa-solid fa-clipboard"></i> Mô tả triệu
                                    chứng
                                </h5>

                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <label> Triệu chứng </label>
                                        <textarea id="trieuchung" class="form-control" name="tc" rows="4"
                                            placeholder="Mô tả triệu chứng của bệnh nhân..."></textarea>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <label>Nội dung khám </label>
                                        <textarea id="nd" name="nd" class="form-control" rows="4"
                                            placeholder="Nội dung khám ( khám tổng quát, khám chuyên khoa,....)"></textarea>
                                    </div>

                                </div>

                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-next" id="nextToMedicine" name="submit">
                                        Đặt lịch
                                    </button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="prescription" role="tabpanel">
                                <!-- <h5 class="section-title"><i class="fa-solid fa-list"></i> Danh sách Lịch đã đặt </h5> -->

                                <div class="mt-4">
                                    <!-- <h6 class="mb-3">Danh sách thuốc đã kê</h6> -->
                                    <div class="table-responsive">
                                        <table id="medication_list" class="table table-striped table-hover">
                                            <thead>
                                                <tr style="text-align: center;">
                                                    <th width="8%">STT</th>
                                                    <th width="10%">Ngày khám</th>
                                                    <th width="10%">Giờ khám</th>
                                                    <th width="15%">Triệu chứng</th>
                                                    <th width="15%">Nội dung khám</th>
                                                    <th width="10%">Hành Động</th>
                                                </tr>
                                            </thead>
                                            <tbody id="current_medicines_list">
                                                <?php
                                                    $query = "SELECT id, date_visit, time_visit, trieu_chung, noi_dung_kham
                                                    FROM book WHERE id_patient = ? AND is_deleted = 0 ORDER BY date_visit DESC, time_visit DESC";
                                                    $stmt = $con->prepare($query);
                                                    $stmt->execute([$_SESSION['user_id']]);
                                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    if ($rows) {
                                                        $count = 1;
                                                        foreach ($rows as $row) {
                                                            $date          = new DateTime($row['date_visit']);
                                                            $formattedDate = $date->format('d/m/Y'); // định dạng: ngày/tháng/năm
                                                            echo '<tr style="text-align: center;">
                                                                <td>' . htmlspecialchars($count) . '</td>
                                                                <td>' . htmlspecialchars($formattedDate) . '</td>
                                                                <td>' . htmlspecialchars($row['time_visit']) . '</td>
                                                                <td>' . htmlspecialchars($row['trieu_chung']) . '</td>
                                                                <td>' . htmlspecialchars($row['noi_dung_kham']) . '</td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteBooking(' . htmlspecialchars($row['id']) . ')">
                                                                    <i class="bi bi-trash"></i> Xóa
                                                                    </button>

                                                                </td>
                                                            </tr>'
                                                            ;
                                                            $count++;
                                                            }
                                                            } else {
                                                            echo '<tr>
                                                                <td colspan="6" class="text-center">Chưa có lịch khám nào được đặt.
                                                                </td>
                                                            </tr>';
                                                            }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <!-- <div class="text-center mt-4">
                                    <button type="submit" id="submit" name="save" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i> Lưu thay đổi
                                    </button>
                                </div> -->
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

    <!--                         <?php include './config/site_js_links.php'; ?> -->
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

    var message = '<?php echo $message; ?>';
    if (message !== '') {
        showCustomMessage(message);
    }

    $(document).ready(function() {
        // Initialize datetime pickers
        $('#visit_date, #next_visit_date').datetimepicker({
            format: 'L'
        });
    });
    $('#next_visit_date').datetimepicker({
        format: 'DD/MM/YYYY',
        useCurrent: false,
        locale: 'vi'
    });

    function deleteBooking(id) {
        if (confirm('Bạn có chắc chắn muốn xóa lịch khám này không?')) {
            fetch('book.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'save=1&id=' + id
            }).then(res => location.reload());
        }
    }
    </script>


    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</body>

</html>