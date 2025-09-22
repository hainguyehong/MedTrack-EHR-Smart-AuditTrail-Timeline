<?php 
include './config/connection.php';
include './common_service/common_functions.php';
include './common_service/date.php';
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit;

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

    
    $ultrasoundDir = "uploads/anhsieuam/";
    $xrayDir = "uploads/xquang/";

    if (!is_dir($ultrasoundDir)) mkdir($ultrasoundDir, 0777, true);
    if (!is_dir($xrayDir)) mkdir($xrayDir, 0777, true);

    
    $ultrasoundName = basename($ultrasound["name"]);
    $ultrasoundPath = $ultrasoundDir . $ultrasoundName;
    move_uploaded_file($ultrasound["tmp_name"], $ultrasoundPath);

    $xrayName = basename($xray["name"]);
    $xrayPath = $xrayDir . $xrayName;
    move_uploaded_file($xray["tmp_name"], $xrayPath);
    $createdAt = date("Y-m-d H:i:s");
    try {
        $con->beginTransaction();

        $queryVisit = "INSERT INTO `patient_diseases`
            (`patient_id`, `huyet_ap`, `can_nang`, `chieu_cao`, `nhiet_do`, 
             `mach_dap`, `nhip_tim`, `anh_sieu_am`, `anh_chup_xq`, 
             `trieu_chung`, `chuan_doan`, `bien_phap`, `nhap_vien`, `tien_su_benh`, `created_at`
             ) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"; 
        
        $stmtVisit = $con->prepare($queryVisit);
        $stmtVisit->execute([
            $patientId, $bp, $weight, $height, $temperature,
            $pulse, $heartRate, $ultrasoundPath, $xrayPath,
            $tc, $cd, $bienphap, $nv, $disease,$createdAt
        ]);
    $lastInsertId = $con->lastInsertId();//latest patient visit id
    //lưu chi tiết đơn thuốc
    $patientId   = $_POST['patient'];   // ID bệnh nhân
    $medicineIds = $_POST['medicineIds'] ?? [];
    $quantities  = $_POST['quantities'] ?? [];
    $dosages     = $_POST['dosages'] ?? [];
    $notes       = $_POST['notes'] ?? [];

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
    }
    $con->commit();

    // $message = 'Thuốc của bệnh nhân đã được lưu trữ thành công.';
    $_SESSION['success_message'] = 'Đơn Thuốc của bệnh nhân đã được lưu trữ thành công.';

  }catch(PDOException $ex) {
    $con->rollback();

    echo $ex->getTraceAsString();
    echo $ex->getMessage();
    exit;
  }

//   header("location:congratulation.php?goto_page=patients_visit.php&message=$message");
//   exit;
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

    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <title>Khám Bệnh - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
        <style>
            body {
                background: #f8fafc;
            }
            .card-primary.card-outline {
                /* border-top: 0px solid #007bff; */
            }
            .card {
                /* background: #fff; */
                border-radius: 12px;
                /* border: 1.5px solid #007bff; */
                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            }
            .card-header {
                background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
                color: #fff;
                border-radius: 12px 12px 0 0;
            }
            .btn-primary, .btn-danger, .btn-outline-danger {
                border-radius: 20px;
                transition: 0.2s;
            }
            .btn-primary:hover, .btn-danger:hover, .btn-outline-danger:hover {
                filter: brightness(1.1);
                box-shadow: 0 2px 8px rgba(0,123,255,0.15);
            }
            .table {
                /* background: #fff; */
            }
            .form-control, .form-select {
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
        </head>

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
                            <h1>Thông tin chuẩn đoán</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Thông tin bệnh nhân</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- best practices-->
                        <form method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                    <label>Chọn bệnh nhân</label>
                                    <select id="patient" name="patient" class="form-control form-control-sm rounded-0"
                                        required="required">
                                        <?php echo $patients;?>
                                    </select>
                                </div>


                                <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày khám</label>
                                        <div class="input-group date" id="visit_date" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                data-target="#visit_date" name="visit_date" required="required"
                                                data-toggle="datetimepicker" autocomplete="off" />
                                            <div class="input-group-append" data-target="#visit_date"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->



                                <!-- <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                                    <div class="form-group">
                                        <label>Ngày khám tiếp theo</label>
                                        <div class="input-group date" id="next_visit_date" data-target-input="nearest">
                                            <input type="text"
                                                class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                data-target="#next_visit_date" name="next_visit_date"
                                                data-toggle="datetimepicker" autocomplete="off" />
                                            <div class="input-group-append" data-target="#next_visit_date"
                                                data-toggle="datetimepicker">
                                                <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                                <div class="clearfix">&nbsp;</div>
                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                    <label>Triệu chứng</label>
                                    <textarea id="trieuchung" class="form-control form-control-sm rounded-0" name="tc"
                                        required="required"></textarea>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                    <label>Tiền sử bệnh</label>
                                    <textarea id="disease" required="required" name="disease"
                                        class="form-control form-control-sm rounded-0"></textarea>
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Huyết áp</label>
                                    <input type="number" id="bp" class="form-control form-control-sm rounded-0"
                                        name="bp" required="required" />
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Cân nặng</label>
                                    <input type="number" id="weight" name="weight"
                                        class="form-control form-control-sm rounded-0" required="required" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Chiều cao</label>
                                    <input type="number" id="height" name="height"
                                        class="form-control form-control-sm rounded-0" required="required" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Nhiệt độ</label>
                                    <input type="number" id="temperature" name="temperature"
                                        class="form-control form-control-sm rounded-0" required="required" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Mạch đập</label>
                                    <input type="number" id="pulse" name="pulse"
                                        class="form-control form-control-sm rounded-0" required="required" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>nhịp tim</label>
                                    <input type="number" id="heart_rate" name="heart_rate"
                                        class="form-control form-control-sm rounded-0" required="required" />
                                </div>

                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Ảnh siêu âm</label>
                                    <input type="file" id="ultrasound" name="ultrasound"
                                        class="form-control form-control-sm rounded-0" enctype="multipart/form-data" />
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                    <label>Ảnh Chụp XQ</label>
                                    <input type="file" id="xray" name="xray"
                                        class="form-control form-control-sm rounded-0" enctype="multipart/form-data" />
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                    <label>Chuẩn đoán</label>
                                    <textarea id="chuandoan" class="form-control form-control-sm rounded-0" name="cd"
                                        required="required"></textarea>
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12">
                                    <label>Biện pháp xử lý</label>
                                    <textarea id="bienphap" class="form-control form-control-sm rounded-0"
                                        name="bienphap" required="required"></textarea>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
                                    <label>yêu cầu nhập viện</label>
                                    <select class="form-control form-control-sm rounded-0" id="nv" name="nv" required>
                                        <?php echo Nhapvien();?>
                                    </select>

                                </div>

                                <div class="col-md-12">
                                    <hr />
                                </div>
                                <div class="clearfix">&nbsp;</div>
                                <br></br>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                        <label>Chọn Loại Thuốc</label>
                                        <select id="medicine" class="form-control form-control-sm rounded-0"
                                            name="medicine">
                                            <?php echo $medicines;?>
                                        </select>
                                    </div>

                                    <!-- <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <label>Chọn Số gói</label>
                                    <select id="note" class="form-control form-control-sm rounded-0">

                                    </select>
                                </div> -->

                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <label>Số lượng</label>
                                        <input id="quantity" class="form-control form-control-sm rounded-0"
                                            name="quantity" />
                                    </div>

                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <label>Liều Dùng</label>
                                        <input id="dosage" class="form-control form-control-sm rounded-0"
                                            name="dosage" />
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                        <label>ghi chú</label>
                                        <input id="note" name="note" class="form-control form-control-sm rounded-0"
                                            name="note" />
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                                        <div class="form-group">
                                            <label>Ngày khám</label>
                                            <div class="input-group date" id="visit_date" data-target-input="nearest">
                                                <input type="text"
                                                    class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                    data-target="#visit_date" name="visit_date" required="required"
                                                    data-toggle="datetimepicker" autocomplete="off" />
                                                <div class="input-group-append" data-target="#visit_date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
                                        <div class="form-group">
                                            <label>Ngày khám tiếp theo</label>
                                            <div class="input-group date" id="next_visit_date"
                                                data-target-input="nearest">
                                                <input type="text"
                                                    class="form-control form-control-sm rounded-0 datetimepicker-input"
                                                    data-target="#next_visit_date" name="next_visit_date"
                                                    data-toggle="datetimepicker" autocomplete="off" />
                                                <div class="input-group-append" data-target="#next_visit_date"
                                                    data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-1 col-md-1 col-sm-6 col-xs-12">
                                        <label>&nbsp;</label>
                                        <button id="add_to_list" type="button"
                                            class="btn btn-primary btn-sm btn-flat btn-block">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>

                                </div>
                                <br><br><br><br>
                                <div class="clearfix">&nbsp;</div>
                                <div class="row table-responsive">
                                    <table id="medication_list" class="table table-striped table-hover table-bordered">
                                        <colgroup>
                                            <col width="10%">
                                            <col width="30%">
                                            <col width="10%">
                                            <col width="15%">
                                            <col width="15%">
                                            <col width="10%">
                                        </colgroup>
                                        <thead class="bg-primary" style="text-align: center;">
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên Thuốc</th>
                                                <th>Số Lượng</th>
                                                <th>Liều Dùng</th>
                                                <th>Ghi chú</th>
                                                <th>Hành Động</th>
                                            </tr>
                                        </thead>

                                        <tbody id="current_medicines_list">

                                        </tbody>
                                    </table>
                                </div>

                                <div class="clearfix">&nbsp;</div>
                                <div class="row">
                                    <div class="col-md-16">&nbsp;</div>
                                    <div class="col-md-20">
                                        <button type="submit" id="submit" name="submit"
                                            class="btn btn-primary btn-sm btn-block">Lưu</button>
                                    </div>
                                </div>
                        </form>

                    </div>

                </div>
                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include './config/footer.php';
    $message = '';
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
        }
?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php';
?>

    <script src="plugins/moment/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/vi.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="date.js"></script>
    <script>
    var serial = 1;
    showMenuSelected("#mnu_patients", "#mi_patients_visit");


    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }

    $(document).ready(function() {

        $('#medication_list').find('td').addClass("px-2 py-1 align-middle")
        $('#medication_list').find('th').addClass("p-1 align-middle")
        $('#visit_date, #next_visit_date').datetimepicker({
            format: 'L'
        });


        $("#medicine").change(function() {

            var medicineId = $("#medicine").val();
            var medicineId = $(this).val();

            // if (medicineId !== '') {
            //     $.ajax({
            //         url: "ajax/get_notes.php",
            //         type: 'GET',
            //         data: {
            //             'medicine_id': medicineId
            //         },
            //         cache: false,
            //         async: false,
            //         success: function(data, status, xhr) {
            //             $("#note").html(data);
            //         },
            //         error: function(jqXhr, textStatus, errorMessage) {
            //             showCustomMessage(errorMessage);
            //         }
            //     });
            // }
        });


        $("#add_to_list").click(function() {
            var medicineId = $("#medicine").val();
            var medicineName = $("#medicine option:selected").text();

            var medicineDetailId = $("#note").val();
            var note = $("#note").val().trim();

            var quantity = $("#quantity").val().trim();
            var dosage = $("#dosage").val().trim();

            var oldData = $("#current_medicines_list").html();

            if (medicineName !== '' && note !== '' && quantity !== '' && dosage !== '') {
                var inputs = '';
                inputs += '<input type="hidden" name="medicineIds[]" value="' + medicineId + '" />';
                inputs = inputs + '<input type="hidden" name="medicineDetailIds[]" value="' +
                    medicineDetailId + '" />';
                inputs = inputs + '<input type="hidden" name="notes[]" value="' + note + '" />';
                inputs = inputs + '<input type="hidden" name="quantities[]" value="' + quantity +
                    '" />';
                inputs = inputs + '<input type="hidden" name="dosages[]" value="' + dosage + '" />';


                var tr = '<tr>';
                tr = tr + '<td class="px-2 py-1 align-middle">' + serial + '</td>';
                tr = tr + '<td class="px-2 py-1 align-middle">' + medicineName + '</td>';
                tr = tr + '<td class="px-2 py-1 align-middle">' + quantity + '</td>';
                tr = tr + '<td class="px-2 py-1 align-middle">' + dosage + inputs + '</td>';
                tr = tr + '<td class="px-2 py-1 align-middle">' + note + '</td>';

                tr = tr +
                    '<td class="px-2 py-1 align-middle text-center"><button type="button" class="btn btn-outline-danger btn-sm rounded-0" onclick="deleteCurrentRow(this);"><i class="fa fa-times"></i></button></td>';
                tr = tr + '</tr>';
                oldData = oldData + tr;
                serial++;

                $("#current_medicines_list").html(oldData);

                $("#medicine").val('');
                $("#note").val('');
                $("#quantity").val('');
                $("#dosage").val('');

            } else {
                showCustomMessage('Vui lòng nhập tất cả các ô.');
            }

        });

    });

    function deleteCurrentRow(obj) {

        var rowIndex = obj.parentNode.parentNode.rowIndex;

        document.getElementById("medication_list").deleteRow(rowIndex);
    }
    </script>
</body>

</html>