<?php 
include './config/connection.php';
include './common_service/common_functions.php';
$message = '';
islogin([1]); // chỉ cho admin (1)
// Build filters from GET
$search = trim($_GET['search'] ?? '');
$timeRange = $_GET['timeRange'] ?? 'all';
$userFilter = $_GET['userFilter'] ?? 'all';
$actionFilter = $_GET['actionFilter'] ?? 'all';

// Prepare WHERE clauses
$where = ["1=1"];
$params = [];

// search across record_id, table_name, new_value, old_value
if ($search !== '') {
    $where[] = "(
        a.record_id LIKE :search
        OR a.table_name LIKE :search
        OR a.new_value LIKE :search
        OR a.old_value LIKE :search
    )";
    $params[':search'] = "%{$search}%";
}

// time ranges
if ($timeRange === 'today') {
    $start = date('Y-m-d') . ' 00:00:00';
    $where[] = "a.changed_at >= :start";
    $params[':start'] = $start;
} elseif ($timeRange === 'this_week') {
    // Week starting Monday
    $monday = date('Y-m-d', strtotime('monday this week'));
    $where[] = "a.changed_at >= :start_week";
    $params[':start_week'] = $monday . ' 00:00:00';
} elseif ($timeRange === 'this_month') {
    $first = date('Y-m-01') . ' 00:00:00';
    $where[] = "a.changed_at >= :start_month";
    $params[':start_month'] = $first;
}

// user filter
if ($userFilter !== 'all' && is_numeric($userFilter)) {
    $where[] = "a.user_id = :user_id";
    $params[':user_id'] = (int)$userFilter;
}

// action filter
if ($actionFilter !== 'all') {
    $where[] = "a.action = :action";
    $params[':action'] = $actionFilter;
}

// only include logs from users with role 1 or 2
$where[] = "(u.role IN (1,2))";

// --- REPLACE: compute total, handle export, then fetch paginated rows ---
$perPage = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

// add serial start for pagination-aware numbering
$serialStart = $offset + 1;

// count total matching rows
$countSql = "SELECT COUNT(*) FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id WHERE " . implode(' AND ', $where);
try {
    $stmtCount = $con->prepare($countSql);
    $stmtCount->execute($params);
    $totalLogs = (int)$stmtCount->fetchColumn();
} catch (PDOException $ex) {
    echo "<div class='alert alert-danger'>Lỗi khi đếm audit logs: " . htmlspecialchars($ex->getMessage()) . "</div>";
    $totalLogs = 0;
}

$totalPages = ($totalLogs > 0) ? (int)ceil($totalLogs / $perPage) : 1;

// If export requested -> export ALL matching rows (no limit)
if (isset($_GET['export']) && $_GET['export'] == '1') {
    $exportSql = "SELECT a.id, a.user_id, COALESCE(u.display_name, CONCAT('User #', a.user_id)) AS display_name, 
            u.role AS user_role,
            a.table_name, a.record_id, a.action, a.old_value, a.new_value, a.changed_at
            FROM audit_logs a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.changed_at DESC";
    try {
        $stmtExport = $con->prepare($exportSql);
        $stmtExport->execute($params);
        $exportRows = $stmtExport->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $ex) {
        echo "<div class='alert alert-danger'>Lỗi khi xuất CSV: " . htmlspecialchars($ex->getMessage()) . "</div>";
        exit;
    }

    $filename = 'audit_logs_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');
    echo "\xEF\xBB\xBF";
    fputcsv($out, ['ID','Changed At','User ID','User','Table','Record ID','Action','Old Value','New Value']);
    foreach ($exportRows as $r) {
        $pref = '';
        if (isset($r['user_role'])) {
            if ($r['user_role'] == 1) $pref = 'AD ';
            elseif ($r['user_role'] == 2) $pref = 'BS ';
        }
        $displayWithPrefix = $pref . ($r['display_name'] ?? '');
        fputcsv($out, [
            $r['id'],
            $r['changed_at'],
            $r['user_id'],
            $displayWithPrefix,
            $r['table_name'],
            $r['record_id'],
            $r['action'],
            $r['old_value'],
            $r['new_value']
        ]);
    }
    fclose($out);
    exit;
}

// fetch paginated rows (latest first)
$sql = "SELECT a.id, a.user_id, COALESCE(u.display_name, CONCAT('User #', a.user_id)) AS display_name, 
        u.role AS user_role,
        a.table_name, a.record_id, a.action, a.old_value, a.new_value, a.changed_at
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY a.changed_at DESC
        LIMIT :limit OFFSET :offset";

try {
    $stmtLogs = $con->prepare($sql);
    // bind existing filter params
    foreach ($params as $k => $v) {
        $stmtLogs->bindValue($k, $v);
    }
    $stmtLogs->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
    $stmtLogs->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmtLogs->execute();
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    echo "<div class='alert alert-danger'>Lỗi khi truy vấn audit logs: " . htmlspecialchars($ex->getMessage()) . "</div>";
    $logs = [];
}

// set row serial counter
$sn = $serialStart;
// --- END REPLACE ---

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <!-- Thêm favicon -->
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">

    <?php include './config/data_tables_css.php';?>
    <title>Audit Trail - MedTrack</title>

    <style>
    /* added .user-img to match users.php visuals */
    .user-img {
        width: 3em;
        object-fit: cover;
        object-position: center center;
    }

    body {
        background: #f8fafc;
    }

    .card {
        background: #fff;
        border-radius: 12px;
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

    .card-primary.card-outline {
        border-top: 0px solid #007bff;
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

    .json-preview {
        max-height: 80px;
        overflow: hidden;
        white-space: pre-wrap;
        word-break: break-all;
        font-family: Menlo, Monaco, monospace;
        font-size: 13px;
    }

    .filter-row {
        gap: 12px;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
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
            <section class="content-header">
                <div class="container-fluid">
                    <h2 class="mb-2">Audit Trail</h2>
                    <p class="text-muted">Lịch sử thay đổi hồ sơ bệnh án</p>
                </div>
            </section>

            <section class="content">
                <!-- Filter card — converted to have same header style as users.php -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Lọc dữ liệu</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="filterForm" method="get" class="mb-0">
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                    <label>Tìm kiếm</label>
                                    <input type="search" name="search" id="searchInput"
                                        placeholder="Tìm kiếm theo từ khóa (bệnh nhân, mã hồ sơ...)"
                                        value="<?php echo htmlspecialchars($search);?>"
                                        class="form-control form-control-sm">
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                                    <label class="small text-muted">Khoảng thời gian</label>
                                    <select name="timeRange" id="timeRange" class="form-control form-control-sm">
                                        <option value="all" <?php if($timeRange=='all') echo 'selected';?>>Tất cả
                                        </option>
                                        <option value="today" <?php if($timeRange=='today') echo 'selected';?>>Hôm nay
                                        </option>
                                        <option value="this_week" <?php if($timeRange=='this_week') echo 'selected';?>>
                                            Tuần này</option>
                                        <option value="this_month"
                                            <?php if($timeRange=='this_month') echo 'selected';?>>Tháng này</option>
                                    </select>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6 mb-2">
                                    <label class="small text-muted">Người dùng</label>
                                    <select name="userFilter" id="userFilter" class="form-control form-control-sm">
                                        <option value="all">Tất cả</option>
                                        <?php 
                                        // refill users dropdown — only role 1 and 2, include role for prefix
                                        try {
                                            $stmtUsers = $con->prepare("SELECT id, display_name, role FROM users WHERE is_deleted = 0 AND role IN (1,2) ORDER BY display_name");
                                            $stmtUsers->execute();
                                            while($u = $stmtUsers->fetch(PDO::FETCH_ASSOC)){
                                                $sel = ($userFilter == $u['id']) ? 'selected' : '';
                                                $prefix = '';
                                                if (isset($u['role'])) {
                                                    if ($u['role'] == 1) $prefix = 'AD. ';
                                                    elseif ($u['role'] == 2) $prefix = 'BS. ';
                                                }
                                                echo '<option value="'.intval($u['id']).'" '.$sel.'>'.htmlspecialchars($prefix . $u['display_name']).'</option>';
                                            }
                                        } catch (Exception $e){
                                            // ignore
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col-lg-2 col-md-6 col-sm-6 mb-2">
                                    <label class="small text-muted">Loại hành động</label>
                                    <select name="actionFilter" id="actionFilter" class="form-control form-control-sm">
                                        <option value="all" <?php if($actionFilter=='all') echo 'selected';?>>Tất cả
                                        </option>
                                        <option value="insert" <?php if($actionFilter=='insert') echo 'selected';?>>Tạo
                                        </option>
                                        <option value="update" <?php if($actionFilter=='update') echo 'selected';?>>Cập
                                            nhật</option>
                                        <option value="delete" <?php if($actionFilter=='delete') echo 'selected';?>>Xóa
                                        </option>
                                    </select>
                                </div>

                                <div class="col-lg-1 col-md-12 col-sm-12 mb-2 d-flex align-items-end">
                                    <div style="width:100%;">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block">Áp dụng</button>
                                    </div>
                                </div>

                                <div class="col-12 d-flex gap-2 mt-2">
                                    <button type="button" id="resetFilters" class="btn btn-success btn-sm">Đặt lại bộ
                                        lọc</button>
                                    <button type="button" id="exportCsv" class="btn btn-dark btn-sm">Xuất Excel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results card — add header to match users.php visual -->
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title">Danh Sách</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="auditTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="p-1 text-center">STT</th>
                                        <th>Thời gian</th>
                                        <th>Người dùng</th>
                                        <th>Hành động</th>
                                        <th>Hồ sơ</th>
                                        <th>Chi tiết</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)) { ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Không tìm thấy bản ghi nào</td>
                                    </tr>
                                    <?php } else {
                                        foreach($logs as $row) {
                                            // prepare brief preview of new_value (prefer readable json)
                                            $preview = $row['new_value'] ?? $row['old_value'] ?? '';
                                            $pretty = $preview;
                                            // try decode to pretty json
                                            $decoded = json_decode($preview, true);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $pretty = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                                            }
                                            $short = nl2br(htmlspecialchars(mb_strimwidth($pretty, 0, 240, '...')));

                                            // prefix display name based on role
                                            $prefix = '';
                                            if (isset($row['user_role'])) {
                                                if ($row['user_role'] == 1) $prefix = 'AD ';
                                                elseif ($row['user_role'] == 2) $prefix = 'BS ';
                                            }
                                            $displayNamePref = htmlspecialchars($prefix . ($row['display_name'] ?? ''));

                                            echo '<tr>';
                                            // serial number column
                                            echo '<td class="px-2 py-1 align-middle text-center">'.($sn++).'</td>';
                                            echo '<td>'.htmlspecialchars($row['changed_at']).'</td>';
                                            echo '<td>'.$displayNamePref.'</td>';
                                            echo '<td>'.htmlspecialchars($row['action']).'</td>';
                                            echo '<td>'.htmlspecialchars($row['table_name']).' #'.htmlspecialchars($row['record_id']).'</td>';
                                            echo '<td><div class="json-preview">'.$short.'</div></td>';
                                            echo '<td><button class="btn btn-sm btn-outline-primary view-json" data-json="'.htmlspecialchars($pretty, ENT_QUOTES).'">Xem</button></td>';
                                            echo '</tr>';
                                        }
                                    } ?>
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
                                    $start = max(1, $page - 10);
                                    $end   = min($totalPages, $page + 10 );
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
                                    Trang <?= $page ?> / <?= $totalPages ?> (<?= $totalLogs ?> bản log)
                                </div>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->
        <?php include './config/footer.php'; ?>

        <!-- Modal for JSON view -->
        <div id="jsonModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết JSON</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                            onclick="closeModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <pre id="jsonContent"
                            style="white-space:pre-wrap;word-break:break-all;font-family:Menlo,monospace;"></pre>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Đóng</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.wrapper -->

    <?php include './config/site_js_links.php'; ?>
    <?php include './config/data_tables_js.php'; ?>
    <script>
    // Keep existing helper usage
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('timeRange').value = 'all';
        document.getElementById('userFilter').value = 'all';
        document.getElementById('actionFilter').value = 'all';
        document.getElementById('filterForm').submit();
    });

    document.getElementById('exportCsv').addEventListener('click', function() {
        const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
        params.set('export', '1');
        const url = location.pathname + '?' + params.toString();
        window.location = url;
    });

    // delegate view-json buttons
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('view-json')) {
            const content = e.target.getAttribute('data-json') || '';
            document.getElementById('jsonContent').textContent = content;
            openModal();
        }
    });

    function openModal() {
        const m = document.getElementById('jsonModal');
        m.style.display = 'block';
        m.classList.add('show');
    }

    function closeModal() {
        const m = document.getElementById('jsonModal');
        m.style.display = 'none';
        m.classList.remove('show');
    }
    </script>
</body>

</html>