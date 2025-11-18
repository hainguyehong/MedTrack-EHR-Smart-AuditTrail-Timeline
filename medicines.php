<?php 
include './config/connection.php';
include './common_service/date.php';
include './common_service/common_functions.php';

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
} catch (PDOException $ex) {
    echo $ex->getMessage();
    echo $ex->getTraceAsString();
    $total = 0;
}

$totalPages = ($total > 0) ? (int)ceil($total / $perPage) : 1;

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
                                        <td class="text-center"><?php echo $serial;?></td>
                                        <td><?php echo $row['medicine_name'];?></td>
                                        <td class="text-center">
                                            <a href="update_medicine.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="delete_medicine.php?id=<?php echo $row['id'];?>"
                                                class="btn btn-danger btn-sm">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $serial++; } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    
                  <!-- /.card-footer-->
                </div>
                <!-- /.card -->
                <!-- Pagination (same style as users.php) -->
                    <?php if ($totalPages > 1) { ?>
                    <div class="d-flex justify-content-between align-items-center mt-3" style="margin-left: 40px;margin-bottom: 50px;">
                        <nav aria-label="Page navigation">
                            <ul class="pagination mb-0">
                                <?php
                                $baseParams = $_GET;
                                $prev = max(1, $page - 1);
                                $baseParams['page'] = $prev;
                                $prevUrl = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($baseParams));
                                ?>
                                <li class="page-item <?php echo ($page<=1)?'disabled':'';?>">
                                    <a class="page-link" href="<?php echo ($page<=1)?'javascript:void(0);':$prevUrl;?>">«</a>
                                </li>
                                <?php
                                for ($p = 1; $p <= $totalPages; $p++) {
                                    $baseParams['page'] = $p;
                                    $url = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($baseParams));
                                    $active = ($p == $page) ? 'active' : '';
                                    echo '<li class="page-item '.$active.'"><a class="page-link" href="'.$url.'">'.$p.'</a></li>';
                                }
                                $next = min($totalPages, $page + 1);
                                $baseParams['page'] = $next;
                                $nextUrl = htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query($baseParams));
                                ?>
                                <li class="page-item <?php echo ($page>=$totalPages)?'disabled':'';?>">
                                    <a class="page-link" href="<?php echo ($page>=$totalPages)?'javascript:void(0);':$nextUrl;?>">»</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php } ?>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php 
include './config/footer.php';

        $message = '';
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Xóa ngay sau khi lấy để F5 không lặp lại
        }
?>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <?php include './config/data_tables_js.php'; ?>


    <script>
    showMenuSelected("#mnu_medicines", "#mi_medicines");

    var message = '<?php echo $message;?>';
    if (message !== '') {
        showCustomMessage(message);
    }



    $(function() {
        $("#all_medicines").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "paging": false, // paging disabled because server-side paging is used
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            "buttons": ["pdf", "print"],
            "language": {
                "info": " Tổng cộng _TOTAL_ loại thuốc",
                "paginate": {
                    "previous": "<span style='font-size:18px;'>&#8592;</span>",
                    "next": "<span style='font-size:18px;'>&#8594;</span>"
                }
            },
        }).buttons().container().appendTo('#all_medicines_wrapper .col-md-6:eq(0)');

    });

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
    </script>
</body>

</html>