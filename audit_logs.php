<?php
declare(strict_types=1);

require './config/connection.php';
require './common_service/common_functions.php';

islogin([1]); // chỉ admin

const AUDIT_IGNORE_FIELDS = [
    'updated_at',
    'deleted_at'
];

/**
 * =============================
 * INPUT FILTERS
 * =============================
 */
$search       = trim($_GET['search'] ?? '');
$timeRange    = $_GET['timeRange'] ?? 'all';
$userFilter   = $_GET['userFilter'] ?? 'all';
$actionFilter = $_GET['actionFilter'] ?? 'all';
$export       = ($_GET['export'] ?? '0') === '1';

/**
 * =============================
 * BUILD WHERE (CHUẨN AUDIT LOG)
 * =============================
 */
$where  = [];
$params = [];

/* chỉ log user role 1,2 */
$where[] = "u.role IN (1,2)";

/* search theo table / record */
if ($search !== '') {

    //  nếu toàn số → record_id
    if (ctype_digit($search)) {
        $where[] = "a.record_id = :record_id";
        $params[':record_id'] = (int)$search;
    }
    else {
        // 2 serach chung
        $where[] = "(
            a.table_name   LIKE :kw
            OR a.action    LIKE :kw
            OR u.display_name LIKE :kw
            OR a.old_value LIKE :kw
            OR a.new_value LIKE :kw
        )";

        $params[':kw'] = '%' . $search . '%';
    }
}


/* theo user */
if ($userFilter !== 'all' && is_numeric($userFilter)) {
    $where[] = "a.user_id = :user_id";
    $params[':user_id'] = (int)$userFilter;
}

/* theo action */
if ($actionFilter !== 'all') {
    $where[] = "a.action = :action";
    $params[':action'] = $actionFilter;
}

/* theo time */
switch ($timeRange) {
    case 'today':
        $where[] = "DATE(a.changed_at) = CURDATE()";
        break;
    case 'this_week':
        $where[] = "YEARWEEK(a.changed_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'this_month':
        $where[] = "MONTH(a.changed_at) = MONTH(CURDATE())
                    AND YEAR(a.changed_at) = YEAR(CURDATE())";
        break;
}

/* build final where */
$whereSql = 'WHERE ' . implode(' AND ', $where);

/**
 * =============================
 * PAGINATION
 * =============================
 */
$perPage = 10;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$sn = $offset + 1;

/**
 * =============================
 * COUNT TOTAL
 * =============================
 */
$countSql = "
    SELECT COUNT(*)
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    $whereSql
";

$stmt = $con->prepare($countSql);
$stmt->execute($params);
$totalLogs  = (int)$stmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalLogs / $perPage));

/**
 * =============================
 * EXPORT CSV (DÙNG CHUNG WHERE)
 * =============================
 */
if ($export) {

    $sql = "
        SELECT a.id, a.changed_at, a.user_id,
               COALESCE(u.display_name, CONCAT('User #', a.user_id)) AS display_name,
               u.role AS user_role,
               a.table_name, a.record_id, a.action,
               a.old_value, a.new_value
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        $whereSql
        ORDER BY a.changed_at DESC
    ";

    $stmt = $con->prepare($sql);
    $stmt->execute($params);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=audit_logs_' . date('Ymd_His') . '.csv');

    // BOM cho Excel
    echo "\xEF\xBB\xBF";

    $out = fopen('php://output', 'w');

    // Header CSV
    fputcsv($out, [
        'ID',
        'Changed At',
        'User ID',
        'User',
        'Table',
        'Record ID',
        'Action',
        'Old Value',
        'New Value'
    ]);

    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $prefix = match ((int)$r['user_role']) {
            1 => 'AD ',
            2 => 'BS ',
            default => ''
        };

       
        $old = auditCleanFields(
            json_decode($r['old_value'] ?? '{}', true)
        );

        $new = auditCleanFields(
            json_decode($r['new_value'] ?? '{}', true)
        );

        fputcsv($out, [
            $r['id'],
            $r['changed_at'],
            $r['user_id'],
            $prefix . $r['display_name'],
            $r['table_name'],
            $r['record_id'],
            $r['action'],
            json_encode($old, JSON_UNESCAPED_UNICODE),
            json_encode($new, JSON_UNESCAPED_UNICODE),
        ]);
    }

    fclose($out);
    exit;
}


/**
 * =============================
 * FETCH DATA
 * =============================
 */
$sql = "
    SELECT a.id, a.changed_at, a.user_id,
           COALESCE(u.display_name, CONCAT('User #', a.user_id)) AS display_name,
           u.role AS user_role,
           a.table_name, a.record_id, a.action,
           a.old_value, a.new_value
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.id
    $whereSql
    ORDER BY a.changed_at DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $con->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* giữ filter khi phân trang */
$q = $_GET;
unset($q['page']);
$baseQuery = http_build_query($q);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include './config/site_css_links.php';?>
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <?php include './config/data_tables_css.php';?>
    <title>Audit Trail - MedTrack</title>

    <style>
    * {
        font-family: sans-serif;
    }

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

    .action-btn {
        border-radius: 20px;
        padding: 6px 18px;
        font-weight: 500;
    }

    label {
        font-weight: 700;
        color: #000;
        font-size: 18px;
    }

    .card-body label {
        font-size: 16px;
    }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed" style="background:#f8fafc;">
    <div class="wrapper">
        <?php include './config/header.php'; include './config/sidebar.php';?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid"></div>
            </section>

            <section class="content">
                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-filter"></i> LỌC DỮ LIỆU</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <form id="filterForm" method="get" class="mb-0">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="small">Tìm kiếm</label>
                                    <input type="search" name="search" id="searchInput"
                                        class="form-control form-control-sm" placeholder="Từ khóa, bảng, mã hồ sơ..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="small">Thời gian</label>
                                    <select name="timeRange" id="timeRange" class="form-control form-control-sm">
                                        <option value="all" <?= $timeRange=='all'?'selected':'' ?>>Tất cả</option>
                                        <option value="today" <?= $timeRange=='today'?'selected':'' ?>>Hôm nay</option>
                                        <option value="this_week" <?= $timeRange=='this_week'?'selected':'' ?>>Tuần này
                                        </option>
                                        <option value="this_month" <?= $timeRange=='this_month'?'selected':'' ?>>Tháng
                                            này</option>
                                    </select>
                                </div>

                                <!-- ✅ FIX: đổ users vào dropdown -->
                                <div class="col-md-6 mb-3">
                                    <label class="small">Người dùng</label>
                                    <select name="userFilter" id="userFilter" class="form-control form-control-sm">
                                        <option value="all">Tất cả</option>
                                        <?php
                                    try {
                                        $stmtUsers = $con->prepare("
                                            SELECT id, display_name, role
                                            FROM users
                                            WHERE is_deleted = 0 AND role IN (1,2)
                                            ORDER BY display_name
                                        ");
                                        $stmtUsers->execute();

                                        while ($u = $stmtUsers->fetch(PDO::FETCH_ASSOC)) {
                                            $sel = ($userFilter == $u['id']) ? 'selected' : '';
                                            $prefix = '';
                                            if ((int)$u['role'] === 1) $prefix = 'AD ';
                                            elseif ((int)$u['role'] === 2) $prefix = 'BS ';

                                            echo '<option value="'.(int)$u['id'].'" '.$sel.'>'
                                                . htmlspecialchars($prefix . $u['display_name'])
                                                . '</option>';
                                        }
                                    } catch (Exception $e) {
                                        echo '<option value="all" disabled>'
                                            . 'Lỗi load users: ' . htmlspecialchars($e->getMessage())
                                            . '</option>';
                                    }
                                    ?>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="small">Hành động</label>
                                    <select name="actionFilter" id="actionFilter" class="form-control form-control-sm">
                                        <option value="all" <?= $actionFilter=='all'?'selected':'' ?>>Tất cả</option>
                                        <option value="CREATE" <?= $actionFilter=='CREATE'?'selected':'' ?>>Tạo</option>
                                        <option value="UPDATE" <?= $actionFilter=='UPDATE'?'selected':'' ?>>Cập nhật
                                        </option>
                                        <option value="DELETE" <?= $actionFilter=='DELETE'?'selected':'' ?>>Xoá</option>
                                    </select>

                                </div>

                                <div class="col-12 text-center mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm px-4 mx-1">
                                        <i class="fa-solid fa-filter me-1"></i>Áp dụng
                                    </button>

                                    <button type="button" id="resetFilters"
                                        class="btn btn-secondary btn-sm px-4 mx-1 action-btn">
                                        <i class="fa-solid fa-rotate-left me-1"></i>Đặt lại
                                    </button>

                                    <button type="button" id="exportCsv"
                                        class="btn btn-outline-dark btn-sm px-4 mx-1 action-btn">
                                        <i class="fa-solid fa-file-excel me-1"></i>Xuất Excel
                                    </button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="card card-outline card-primary shadow">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fa-solid fa-list"></i>DANH SÁCH</h3>
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
                                    <tr class="p-1 text-center">
                                        <th>STT</th>
                                        <th>Thời gian</th>
                                        <th>Người dùng</th>
                                        <th>Hành động</th>
                                        <th>Hồ sơ</th>
                                        <!-- <th>Chi tiết</th> -->
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)) { ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Không tìm thấy bản ghi nào</td>
                                    </tr>
                                    <?php } else {
                                        foreach ($logs as $row) {

                                            $prefix = match ((int)$row['user_role']) {
                                                1 => 'AD ',
                                                2 => 'BS ',
                                                default => ''
                                            };

                                            echo '<tr>';
                                            echo '<td class="text-center">'.($sn++).'</td>';
                                            echo '<td>'.htmlspecialchars($row['changed_at']).'</td>';
                                            echo '<td>'.htmlspecialchars($prefix . $row['display_name']).'</td>';

                                            echo '<td class="fw-bold">'.htmlspecialchars($row['action']).'</td>';

                                            echo '<td>'
                                                .htmlspecialchars($row['table_name'])
                                                .' #'
                                                .htmlspecialchars((string)$row['record_id'])

                                                .'</td>';

                                            // echo '<td>'.renderAuditDetail($row).'</td>';

                                            echo '<td class="text-center">
                                                    <button class="btn btn-sm btn-outline-primary view-json"
                                                        data-json="'.htmlspecialchars(
                                                            json_encode([
                                                                'old' => auditCleanFields(json_decode($row['old_value'], true)),
                                                                'new' => auditCleanFields(json_decode($row['new_value'], true)),
                                                            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                                                            ENT_QUOTES
                                                        ).'">
                                                        Xem chi tiết
                                                    </button>
                                                    </td>';

                                            echo '</tr>';
                                        }
                                        } ?>

                                </tbody>
                            </table>

                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="Patients pagination">
                                <ul class="pagination justify-content-center mt-3">

                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?<?= $baseQuery ?>page=<?= $page-1 ?>">«</a>
                                    </li>

                                    <?php
                                $start = max(1, $page - 10);
                                $end   = min($totalPages, $page + 10);
                                for ($i = $start; $i <= $end; $i++):
                                ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= $baseQuery ?>page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?<?= $baseQuery ?>page=<?= $page+1 ?>">»</a>
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

        <?php include './config/footer.php'; ?>

        <!-- Modal for JSON view -->
        <div id="jsonModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lịch sử thay đổi</h5>
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

    <?php include './config/site_js_links.php'; ?>
    <?php include './config/data_tables_js.php'; ?>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- ⚠️ Nếu site_js_links.php đã có jQuery rồi thì BỎ dòng CDN jQuery bên dưới -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
    $(function() {
        $('#userFilter').select2({
            width: '100%',
            placeholder: 'Tất cả / gõ tên để tìm',
            allowClear: false,
            minimumResultsForSearch: 0
        });
    });
    </script>

    <script>
    document.getElementById('resetFilters').addEventListener('click', function() {
        $('#searchInput').val('');
        $('#timeRange').val('all').trigger('change');
        $('#userFilter').val('all').trigger('change');
        $('#actionFilter').val('all').trigger('change');
        document.getElementById('filterForm').submit();
    });

    document.getElementById('exportCsv').addEventListener('click', function() {
        const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
        params.set('export', '1');
        const url = location.pathname + '?' + params.toString();
        window.location = url;
    });

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