<?php 
include './config/connection.php';
include './common_service/date.php';
include './common_service/common_functions.php';
islogin([2]); // chỉ cho admin (2) truy cập
$message = '';
if (isset($_POST['save_medicine'])) {
    $medicineName = trim($_POST['medicine_name']);
    $medicineName = ucwords(strtolower($medicineName));
    $created_at = date('Y-m-d H:i:s');    

    if ($medicineName != '') {
        try {
            $con->beginTransaction();

            // Thêm thuốc
            $query = "INSERT INTO `medicines` (`medicine_name`, `created_at`)
                      VALUES (:medicine_name, :created_at)";
            $stmtMedicine = $con->prepare($query);
            $stmtMedicine->execute([
                ':medicine_name' => $medicineName,
                ':created_at' => $created_at
            ]);

            // Lấy ID vừa thêm
            $newId = $con->lastInsertId();

            // ✅ Ghi log audit (nếu hàm log_audit tồn tại)
            if (function_exists('log_audit')) {
                log_audit(
                    $con,
                    $_SESSION['user_id'] ?? 'unknown',  // Ai thêm
                    'medicines',                        // Bảng nào
                    $newId,                             // ID bản ghi
                    'insert',                           // Hành động
                    null,                               // Không có dữ liệu cũ
                    [                                   // Dữ liệu mới
                        'medicine_name' => $medicineName,
                        'created_at' => $created_at
                    ]
                );
            }

            $con->commit();

            $_SESSION['success_message'] = 'Thêm thuốc thành công.';
        } catch (PDOException $ex) {
            $con->rollBack();
            $_SESSION['error_message'] = "Lỗi khi thêm thuốc: " . $ex->getMessage();
        }

    } else {
        $_SESSION['error_message'] = 'Vui lòng điền đầy đủ thông tin.';
    }

    header("Location: medicines.php");
    exit();
}

// else
// --- Paginated query (like users.php) ---
$perPage = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

try {
    $countSql = "SELECT COUNT(*) FROM `medicines` WHERE `is_deleted` = 0";
    $stmtCount = $con->prepare($countSql);
    $stmtCount->execute();
    $total = (int)$stmtCount->fetchColumn();
    $totalPages = ($total > 0) ? (int)ceil($total / $perPage) : 1;
} catch (PDOException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    $total = 0;
}


try {
    $query = "SELECT `id`, `medicine_name` FROM `medicines`
              WHERE `is_deleted` = 0
              ORDER BY `created_at` DESC
              LIMIT :limit OFFSET :offset";
    $stmt = $con->prepare($query);
    $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
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
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>Medicines - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>
    <style>
    body {
        background: #f8fafc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        /* border: 1.5px solid #007bff; */
        /* box-shadow: 0 2px 8px rgba(0,0,0,0.04); */
    }

    .card-header {
        background: linear-gradient(90deg, #007bff 60%, #00c6ff 100%);
        color: #fff;
        /* border-radius: 12px 12px 0 0; */
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
                            <h1>Thêm mới Loại thuốc</h1>
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
                        <h3 class="card-title">Thêm mới Loại thuốc</h3>
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
                                    <label>Tên thuốc</label>
                                    <input type="text" id="medicine_name" name="medicine_name" required="required"
                                        class="form-control form-control-sm" />
                                </div>

                                <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2">
                                    <label>&nbsp;</label>
                                    <button type="submit" id="save_medicine" name="save_medicine"
                                        class="btn btn-primary btn-sm btn-block">Lưu</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- /.card -->
            </section>
            <section class="content">
                <!-- Default box -->
                <div class="card card-outline card-primary rounded-0 shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách loại thuốc</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row table-responsive">

                            <table id="all_medicines" class="table table-striped dataTable table-bordered dtr-inline"
                                role="grid" aria-describedby="all_medicines_info">
                                <colgroup>
                                    <col width="10%">
                                    <col width="80%">
                                    <col width="10%">
                                </colgroup>

                                <thead <tr>
                                    <th class="text-center">STT</th>
                                    <th>Tên thuốc</th>
                                    <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                    // STT bắt đầu theo trang
                                    $serial = $offset + 1;
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $serial; ?></td>
                                        <td><?php echo $row['medicine_name']; ?></td>
                                        <td class="text-center">
                                            <!-- Nút sửa thuốc -->
                                            <form method="post" action="update_medicine.php" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </form>

                                            <!-- Nút xóa thuốc -->
                                            <form method="post" action="delete_medicine.php" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                    <?php $serial++; } ?>
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
                                    Trang <?= $page ?> / <?= $totalPages ?> (<?= $total ?> loại thuốc)
                                </div>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card-footer-->
                </div>
            </section>
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
        } elseif (isset($_SESSION['error_message'])) {
            $message = $_SESSION['error_message'];
            $messageType = 'error';
            unset($_SESSION['error_message']);
        }
?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <?php include './config/data_tables_js.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    showMenuSelected("#mnu_medicines", "#mi_medicines");

    var message = '<?php echo addslashes($message); ?>';
    var messageType = '<?php echo $messageType; ?>';

    if (message !== '') {
        showCustomMessage(message, messageType);
    }
    $(document).ready(function() {

        $("#medicine_name").blur(function() {
            var medicineName = $(this).val().trim();
            $(this).val(medicineName);

            if (medicineName !== '') {
                $.ajax({
                    url: "ajax/check_medicine_name.php",
                    type: 'GET',
                    data: {
                        'medicine_name': medicineName
                    },
                    cache: false,
                    async: false,
                    success: function(count, status, xhr) {
                        if (count > 0) {
                            showCustomMessage(
                                "Tên thuốc này đã được lưu trữ. Vui lòng chọn tên khác"
                            );
                            $("#save_medicine").attr("disabled", "disabled");
                        } else {
                            $("#save_medicine").removeAttr("disabled");
                        }
                    },
                    error: function(jqXhr, textStatus, errorMessage) {
                        showCustomMessage(errorMessage);
                    }
                });
            }

        });
    });
    $(document).ready(function() {
        $('#all_medicines').DataTable({
            paging: false,
            info: false,
            lengthChange: false,
            searching: true,
            ordering: false,
            language: {
                search: "Tìm kiếm thuốc:",
                zeroRecords: "Không tìm thấy thuốc phù hợp",
                emptyTable: "Không có dữ liệu"
            }
        });
    });
    </script>
</body>

</html>