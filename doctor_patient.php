<?php
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
$message = '';
$userId = $_SESSION['user_id']; 

if (isset($_POST['action']) && $_POST['action'] === 'get_patient_data') {
    $patientId = intval($_POST['patient_id']);

    // Lấy thông tin bệnh nhân
    $query = "SELECT p.*, DATE_FORMAT(p.date_of_birth, '%d/%m/%Y') AS date_of_birth
              FROM patients p
              WHERE p.id = :patient_id AND p.is_deleted = 0 LIMIT 1";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    // Lấy đơn thuốc
    $query = "SELECT pmh.*, m.medicine_name 
              FROM patient_medication_history pmh
              JOIN medicines m ON pmh.medicine_id = m.id
              WHERE pmh.patient_id = :patient_id
              ORDER BY pmh.created_at ASC";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'patient' => $patient,
        'prescriptions' => $prescriptions
    ]);
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
    <title>Bệnh Nhân - MedTrack-EHR-Smart-AuditTrail-Timeline</title>
    <style>
    body {

        background: #f8fafc;

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

    .form-control,
    .form-select {

        /* border-radius: 8px; */

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
include './config/sidebar.php';
?>

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
                <?php include 'ajax/get_patient_data.php'; ?>
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
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Chọn bệnh nhân</label>
                                    <select id="patient" name="patient"
                                        class="form-control form-control-sm rounded-0 setupSelect2">
                                        <?php echo getPatients($con); ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Tên bệnh nhân</label>
                                    <input type="text" id="patient_name" readonly class="form-control" />
                                </div>
                                <br>
                                <br>
                                <br>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Địa chỉ</label>
                                    <input type="text" id="address" readonly class="form-control" />

                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>CCCD</label>
                                    <input type="text" id="cnic" readonly class="form-control" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày sinh</label>
                                        <div class="input-group date">
                                            <input type="text" id="date_of_birth" readonly class="form-control" />
                                            <!-- <div class="input-group-append" data-target="#date_of_birth"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div> -->
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Số điện thoại</label>
                                    <input type="text" id="phone_number" readonly class="form-control" />
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>Giới tính</label>
                                    <input type="text" id="gender" readonly class="form-control" />

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

            <!-- Thông tin các lần khám bệnh -->
            <section class="content" id="visit-history-section" style="display:none;">
                <div id="visit-history-list"></div>
            </section>
            <br />
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
                                        <th>Lần khám</th>
                                        <th>Thời gian kê thuốc</th>
                                        <th>Tên loại thuốc</th>
                                        <th>Số lượng</th>
                                        <th>Liều dùng</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>

                                <tbody id="prescriptionTable">
                                    <tr>
                                        <td colspan="7" style="text-align:center;">Chưa có đơn thuốc nào.</td>
                                    </tr>
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
        <?php include './common_service/loaduser.php';?>

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

        <script>
        function formatDate(dateStr) {
            if (!dateStr) return "";
            const parts = dateStr.split("-");
            return parts.reverse().join("/");
        }
        $(document).ready(function() {
            $('#patient').change(function() {
                var patientId = $(this).val();
                if (patientId) {
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: {
                            action: 'get_patient_data',
                            patient_id: patientId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.patient) {
                                $('#patient_name').val(response.patient.patient_name);
                                $('#address').val(response.patient.address);
                                $('#cnic').val(response.patient.cnic);
                                $('#date_of_birth').val(formatDate(response.patient
                                    .date_of_birth));
                                $('#phone_number').val(response.patient.phone_number);
                                $('#gender').val(response.patient.gender);
                            }

                            // Đổ bảng đơn thuốc
                            var tbody = '';
                            if (response.prescriptions.length > 0) {
                                // Nhóm các đơn thuốc theo ngày khám
                                const grouped = {};
                                response.prescriptions.forEach(row => {
                                    // Nếu trong DB có trường visit_date -> dùng nó, nếu không thì lấy created_at
                                    const dateRaw = row.visit_date || row
                                        .created_at;
                                    const date = moment(dateRaw).isValid() ? moment(
                                            dateRaw).format("DD/MM/YYYY") :
                                        "Không rõ";
                                    if (!grouped[date]) grouped[date] = [];
                                    grouped[date].push(row);
                                });

                                let index = 1;
                                $.each(grouped, function(date, items) {
                                    $.each(items, function(i, row) {
                                        tbody +=
                                            '<tr style="text-align:center;">' +
                                            '<td>' + index + '</td>' +
                                            '<td>Lần ' + index + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td>' + row.medicine_name +
                                            '</td>' +
                                            '<td>' + row.quantity +
                                            '</td>' +
                                            '<td>' + row.dosage + '</td>' +
                                            '<td>' + (row.note || '') +
                                            '</td>' +
                                            '</tr>';
                                    });
                                    index++;
                                });
                            } else {
                                tbody +=
                                    '<tr><td colspan="7" class="text-center text-muted">Không có đơn thuốc nào.</td></tr>';
                            }


                            $('#prescriptionTable').html(tbody);

                            // Lấy thông tin các lần khám bệnh
                            $.ajax({
                                url: 'ajax/get_patient_visits.php',
                                type: 'POST',
                                data: {
                                    patient_id: patientId
                                },
                                dataType: 'json',
                                success: function(visits) {
                                    var html = '';
                                    if (visits.length > 0) {
                                        visits.forEach(function(visit, idx) {
                                            html += `
                                            <div class="card mb-4 collapsed-card">
                                                <div class="card-header bg-info text-white">
                                                    <strong>Lần khám ${idx + 1} - ${visit.created_at ? moment(visit.created_at).format('DD/MM/YYYY HH:mm') : ''}</strong>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                 <div class="card-body">
                                                     <div class="row">
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Huyết áp (mmHg)</label>
                                                             <input type="text" class="form-control" value="${visit.huyet_ap || ''}" readonly>
                                                         </div>
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Cân nặng (kg)</label>
                                                             <input type="text" class="form-control" value="${visit.can_nang || ''}" readonly>
                                                         </div>
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Chiều cao (cm)</label>
                                                             <input type="text" class="form-control" value="${visit.chieu_cao || ''}" readonly>
                                                         </div>
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Nhiệt độ (°C)</label>
                                                             <input type="text" class="form-control" value="${visit.nhiet_do || ''}" readonly>
                                                         </div>
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Mạch đập (bpm)</label>
                                                             <input type="text" class="form-control" value="${visit.mach_dap || ''}" readonly>
                                                         </div>
                                                         <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                                             <label>Nhịp tim (bpm)</label>
                                                             <input type="text" class="form-control" value="${visit.nhip_tim || ''}" readonly>
                                                         </div>
                                                     </div>
                                                     <div class="row">
                                                         <div class="col-lg-6 mb-3">
                                                             <label>Triệu chứng</label>
                                                             <textarea class="form-control" rows="3" readonly>${visit.trieu_chung || ''}</textarea>
                                                         </div>
                                                         <div class="col-lg-6 mb-3">
                                                             <label>Tiền sử bệnh</label>
                                                             <textarea class="form-control" rows="3" readonly>${visit.tien_su_benh || ''}</textarea>
                                                         </div>
                                                         <div class="col-lg-6 mb-3">
                                                             <label>Chuẩn đoán</label>
                                                             <textarea class="form-control" rows="3" readonly>${visit.chuan_doan || ''}</textarea>
                                                         </div>
                                                         <div class="col-lg-6 mb-3">
                                                             <label>Biện pháp xử lý</label>
                                                             <textarea class="form-control" rows="3" readonly>${visit.bien_phap || ''}</textarea>
                                                         </div>
                                                         <div class="col-lg-4 mb-3">
                                                             <label>Yêu cầu nhập viện</label>
                                                             <input type="text" class="form-control" value="${visit.nhap_vien == '1' ? 'Có' : (visit.nhap_vien == '2' ? 'Không' : (visit.nhap_vien || ''))}" readonly>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                            `;
                                        });
                                    } else {
                                        html =
                                            '<div class="alert alert-info text-center">Chưa có bệnh án nào.</div>';
                                    }
                                    $('#visit-history-list').html(html);
                                    $('#visit-history-section').show();
                                }
                            });
                        }
                    });
                } else {
                    // Clear info if no patient selected
                    $('#patient_name, #address, #cnic, #date_of_birth, #phone_number, #gender').val('');
                    $('#prescriptionTable').html(
                        '<tr><td colspan="6" style="text-align:center;">Chưa có đơn thuốc nào.</td></tr>'
                    );
                    $('#visit-history-list').html('');
                    $('#visit-history-section').hide();
                }
            });
        });
        </script>
        <script>
        // toggle icon + / - cho card collapse (hỗ trợ cả phần tử động)
        $(document).on('click', '[data-card-widget="collapse"]', function(e) {
            var $btn = $(this).find('i');
            var $card = $(this).closest('.card');
            setTimeout(function() {
                if ($card.hasClass('collapsed-card')) {
                    $btn.removeClass('fa-minus').addClass('fa-plus');
                } else {
                    $btn.removeClass('fa-plus').addClass('fa-minus');
                }
            }, 50);
        });
        </script>
</body>

</html>