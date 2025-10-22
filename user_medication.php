<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';

$message = '';
$userId = $_SESSION['user_id']; // lấy id user sau khi login 
// echo "🔍 userId hiện tại: " . htmlspecialchars($userId) . "<br>";
// exit();
$query = "SELECT 
    up.id AS user_patient_id,
    up.user_name,
    up.display_name,
    up.role,
    up.created_at AS user_created_at,
    p.id AS patient_id,
    p.patient_name,
    p.address,
    p.cnic,
    DATE_FORMAT(p.date_of_birth, '%d %b %Y') AS date_of_birth,
    p.phone_number,
    p.gender,
    p.created_at AS patient_created_at
FROM user_patients AS up
JOIN patients AS p ON up.id_patient = p.id
WHERE up.is_deleted = 0 
  AND p.is_deleted = 0
  AND up.id = :user_id
LIMIT 1;";

$stmtPatient1 = $con->prepare($query);
$stmtPatient1->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmtPatient1->execute();

$patient = $stmtPatient1->fetch(PDO::FETCH_ASSOC);

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
        background: #f4f7fb;
    }

    .card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07), 0 1.5px 4px rgba(0, 0, 0, 0.03);
        border: none;
        margin-bottom: 24px;
        transition: box-shadow 0.2s;
    }

    .card:hover {
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.06);
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        border-radius: 14px 14px 0 0;
        border: none;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(90, 156, 248, 0.07);
    }

    .btn-primary,
    .btn-danger {
        border-radius: 22px;
        transition: 0.2s;
        font-weight: 500;
        padding: 7px 22px;
    }

    .btn-primary:hover,
    .btn-danger:hover {
        filter: brightness(1.08);
        box-shadow: 0 2px 12px rgba(90, 156, 248, 0.13);
    }

    .card-title {
        font-weight: 700;
        letter-spacing: 0.7px;
        font-size: 1.15rem;
    }

    label {
        font-weight: 600;
        color: #3b4256;
        margin-bottom: 6px;
        letter-spacing: 0.2px;
    }

    .form-control,
    .form-select,
    textarea.form-control {
        border-radius: 10px !important;
        border: 1.5px solid #e3e7ed;
        background: #fafdff;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-shadow: none;
        font-size: 1rem;
        padding: 8px 14px;
    }

    .form-control:focus,
    .form-select:focus,
    textarea.form-control:focus {
        border-color: #5a9cf8;
        box-shadow: 0 0 0 2px #e3f0ff;
        background: #fff;
    }

    input[readonly],
    textarea[readonly] {
        background: #f4f7fb !important;
        color: #6b7280;
        border-color: #e3e7ed;
    }

    .section-title {
        margin-top: 18px;
        margin-bottom: 10px;
        color: #5b5b5bff;
        font-size: 1.08rem;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .mb-3 {
        margin-bottom: 1.2rem !important;
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .alert-info {
        border-radius: 12px;
        background: #e0f2fe;
        color: #2563eb;
        border: none;
        font-weight: 500;
    }

    .table-striped tbody tr:hover {
        background-color: #f1f5f9;
        transition: background 0.2s;
    }

    [class*="col-"] {
        padding-bottom: 12px;
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
                            <h1>Bệnh Nhân</h1>
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
                        <h3 class="card-title">Thông tin bệnh nhân</h3>

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
                                    <input type="text" id="patient_name" name="patient_name"
                                        class="form-control form-control-sm"
                                        value="<?php echo htmlspecialchars($patient['patient_name'] ?? ''); ?>"
                                        disabled />

                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['address'] ?? ''); ?>" disabled />

                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" name="cnic" disabled
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['cnic'] ?? ''); ?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date" id="date_of_birth" data-target-input="nearest">
                                            <input type="text" class="form-control datetimepicker-input" disabled
                                                value="<?php echo htmlspecialchars(date('d/m/Y', strtotime($patient['date_of_birth'] ?? ''))); ?>" />

                                            <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" name="phone_number" disabled
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['phone_number'] ?? ''); ?>" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <input type="text" id="gender" name="gender" disabled
                                        class="form-control form-control-sm rounded-0"
                                        value="<?php echo htmlspecialchars($patient['gender'] ?? ''); ?>" />

                                </div>
                            </div>

                            <div class="clearfix">&nbsp;</div>

                            <div class="row">
                                <div class="col-lg-11 col-md-10 col-sm-10 xs-hidden">&nbsp;</div>

                                <!-- <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12">
                                    <button type="submit" id="save_Patient" name="save_Patient"
                                        class="btn btn-primary btn-sm btn-flat btn-block">Lưu</button>
                                </div> -->
                            </div>
                        </form>
                    </div>

                </div>

            </section>
            <br />


            <!-- bệnh án select -->
            <section class="content">
                <?php
                    $query = "SELECT 
                                pd.id,
                                pd.created_at,
                                pd.huyet_ap,
                                pd.can_nang,
                                pd.chieu_cao,
                                pd.nhiet_do,
                                pd.mach_dap,
                                pd.nhip_tim,
                                pd.trieu_chung,
                                pd.chuan_doan,
                                pd.bien_phap,
                                pd.nhap_vien,
                                pd.tien_su_benh
                            FROM user_patients AS up
                            JOIN patients AS p ON up.id_patient = p.id
                            JOIN patient_diseases AS pd ON pd.patient_id = p.id
                            WHERE up.is_deleted = 0
                            AND p.is_deleted = 0
                            AND up.id = :user_id
                            ORDER BY pd.created_at ASC"; 
                    $stmt = $con->prepare($query);
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    $diseases = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($diseases) > 0):
                        foreach ($diseases as $index => $row):
                            $visitNumber = $index + 1; // lần 1 2 3 ...
                    ?>
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Lần khám <?php echo $visitNumber; ?> -
                            <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></strong>
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <!-- <div class="col-lg-4 col-md-6 mb-3">
                                        <label>Ngày khám</label>
                                        <input type="text" class="form-control" 
                                            value="<?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?>" readonly>
                                    </div> -->
                            </div>
                            <h5 class="section-title"><i class="fas fa-heartbeat"></i> Chỉ số sinh hiệu</h5>
                            <div class="row">
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Huyết áp (mmHg)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['huyet_ap']); ?>" readonly>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Cân nặng (kg)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['can_nang']); ?>" readonly>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Chiều cao (cm)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['chieu_cao']); ?>" readonly>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Nhiệt độ (°C)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['nhiet_do']); ?>" readonly>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Mạch đập (bpm)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['mach_dap']); ?>" readonly>
                                </div>
                                <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                    <label>Nhịp tim (bpm)</label>
                                    <input type="text" class="form-control"
                                        value="<?php echo htmlspecialchars($row['nhip_tim']); ?>" readonly>
                                </div>
                            </div>
                            <h5 class="section-title"><i class="fas fa-clipboard-check"></i> Chuẩn đoán và điều trị</h5>
                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label>Triệu chứng</label>
                                    <textarea class="form-control" rows="3"
                                        readonly><?php echo htmlspecialchars($row['trieu_chung']); ?></textarea>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label>Tiền sử bệnh</label>
                                    <textarea class="form-control" rows="3"
                                        readonly><?php echo htmlspecialchars($row['tien_su_benh']); ?></textarea>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label>Chuẩn đoán</label>
                                    <textarea class="form-control" rows="3"
                                        readonly><?php echo htmlspecialchars($row['chuan_doan']); ?></textarea>
                                </div>
                                <div class="col-lg-6 mb-3">
                                    <label>Biện pháp xử lý</label>
                                    <textarea class="form-control" rows="3"
                                        readonly><?php echo htmlspecialchars($row['bien_phap']); ?></textarea>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label>Yêu cầu nhập viện</label>
                                    <input type="text" class="form-control" value="<?php 
                                                if (isset($row['nhap_vien'])) {
                                                    if ($row['nhap_vien'] == '1') echo 'Có';
                                                    else if ($row['nhap_vien'] == '2') echo 'Không';
                                                    else echo htmlspecialchars($row['nhap_vien']);
                                                }
                                            ?>" readonly>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                        endforeach;
                    else:
                    ?>
                <div class="alert alert-info text-center">Chưa có bệnh án nào.</div>
                <?php endif; ?>
            </section>

            <!-- đơn thuốc select -->
            <br />
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách đơn thuốc</h3>

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
                                        <th>Thời gian kê thuốc</th>
                                        <th>Tên loại thuốc</th>
                                        <th>Số lượng</th>
                                        <th>Liều dùng</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                        $query = "SELECT 
                                    up.id AS user_id,
                                    up.user_name,
                                    up.display_name,
                                    p.id AS patient_id,
                                    p.patient_name,

                                    pmh.id AS prescription_id,
                                    pmh.quantity,
                                    pmh.dosage,
                                    pmh.note,
                                    pmh.visit_date,
                                    pmh.next_visit_date,
                                    pmh.created_at, 

                                    m.id AS medicine_id,
                                    m.medicine_name
                                FROM user_patients AS up
                                JOIN patients AS p ON up.id_patient = p.id
                                JOIN patient_medication_history AS pmh ON pmh.patient_id = p.id
                                JOIN medicines AS m ON pmh.medicine_id = m.id
                                WHERE up.is_deleted = 0
                                AND p.is_deleted = 0
                                AND up.id = :user_id
                                ORDER BY pmh.visit_date DESC";

                        $stmt = $con->prepare($query);
                        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                        $stmt->execute();

                        $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($prescriptions)):
                            $count = 0;
                            foreach ($prescriptions as $row):
                                $count++;
                            ?>
                                    <tr style="text-align:center;">
                                        <td><?php echo $count; ?></td>
                                        <td>
                                            <?php
                                    echo !empty($row['created_at']) && $row['created_at'] !== '0000-00-00 00:00:00'
                                        ? date('d/m/Y H:i', strtotime($row['created_at']))
                                        : '';
                                    ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['medicine_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                                        <td><?php echo htmlspecialchars($row['note']); ?></td>
                                    </tr>
                                    <?php
                            endforeach;
                        else:
                        ?>
                                    <tr>
                                        <td colspan="6" style="text-align:center;">Chưa có đơn thuốc nào.</td>
                                    </tr>
                                    <?php endif; ?>
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
            $("#medicine_details").DataTable({
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
                }
            }).buttons().container().appendTo('#medicine_details_wrapper .col-md-6:eq(0)');
        });
        </script>
</body>

</html>