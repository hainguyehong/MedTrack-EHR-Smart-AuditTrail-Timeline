<?php 
include './config/connection.php';
include './common_service/common_functions.php';
$message = '';

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

// final query: join users to show display_name and include role
$sql = "SELECT a.id, a.user_id, COALESCE(u.display_name, CONCAT('User #', a.user_id)) AS display_name, 
        u.role AS user_role,
        a.table_name, a.record_id, a.action, a.old_value, a.new_value, a.changed_at
        FROM audit_logs a
        LEFT JOIN users u ON a.user_id = u.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY a.changed_at DESC
        LIMIT 1000"; // reasonable cap to avoid huge pages

try {
    $stmtLogs = $con->prepare($sql);
    $stmtLogs->execute($params);
    $logs = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    // On error show message and halt
    echo "<div class='alert alert-danger'>Lỗi khi truy vấn audit logs: " . htmlspecialchars($ex->getMessage()) . "</div>";
    $logs = [];
}

// CSV export
if (isset($_GET['export']) && $_GET['export'] == '1') {
    $filename = 'audit_logs_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);

    $out = fopen('php://output', 'w');
    // UTF-8 BOM for Excel
    echo "\xEF\xBB\xBF";
    fputcsv($out, ['ID','Changed At','User ID','User','Table','Record ID','Action','Old Value','New Value']);
    foreach ($logs as $r) {
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
    .filter-row { gap: 12px; display:flex; flex-wrap:wrap; align-items:flex-end; }
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
                <div class="card card-outline card-primary shadow">
                    <div class="card-body">
                        <form id="filterForm" method="get" class="mb-3">
                            <div class="filter-row">
                                <div style="flex:1">
                                    <input type="search" name="search" id="searchInput" placeholder="Tìm kiếm theo từ khóa (bệnh nhân, mã hồ sơ...)" value="<?php echo htmlspecialchars($search);?>" class="form-control">
                                </div>

                                <div style="min-width:160px">
                                    <label class="small text-muted">Khoảng thời gian</label>
                                    <select name="timeRange" id="timeRange" class="form-control">
                                        <option value="all" <?php if($timeRange=='all') echo 'selected';?>>Tất cả</option>
                                        <option value="today" <?php if($timeRange=='today') echo 'selected';?>>Hôm nay</option>
                                        <option value="this_week" <?php if($timeRange=='this_week') echo 'selected';?>>Tuần này</option>
                                        <option value="this_month" <?php if($timeRange=='this_month') echo 'selected';?>>Tháng này</option>
                                    </select>
                                </div>

                                <div style="min-width:180px">
                                    <label class="small text-muted">Người dùng</label>
                                    <select name="userFilter" id="userFilter" class="form-control">
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

                                <div style="min-width:160px">
                                    <label class="small text-muted">Loại hành động</label>
                                    <select name="actionFilter" id="actionFilter" class="form-control">
                                        <option value="all" <?php if($actionFilter=='all') echo 'selected';?>>Tất cả</option>
                                        <option value="insert" <?php if($actionFilter=='insert') echo 'selected';?>>Tạo</option>
                                        <option value="update" <?php if($actionFilter=='update') echo 'selected';?>>Cập nhật</option>
                                        <option value="delete" <?php if($actionFilter=='delete') echo 'selected';?>>Xóa</option>
                                    </select>
                                </div>

                                <div style="display:flex;gap:8px;align-items:center">
                                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                                    <button type="button" id="resetFilters" class="btn btn-light">Đặt lại bộ lọc</button>
                                    <button type="button" id="exportCsv" class="btn btn-dark">Xuất Excel</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="auditTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
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
                                        <tr><td colspan="6" class="text-center text-muted">Không tìm thấy bản ghi nào</td></tr>
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
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal for JSON view -->
        <div id="jsonModal" class="modal" tabindex="-1" role="dialog">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Chi tiết JSON</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeModal()">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <pre id="jsonContent" style="white-space:pre-wrap;word-break:break-all;font-family:Menlo,monospace;"></pre>
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
    document.getElementById('resetFilters').addEventListener('click', function(){
        document.getElementById('searchInput').value = '';
        document.getElementById('timeRange').value = 'all';
        document.getElementById('userFilter').value = 'all';
        document.getElementById('actionFilter').value = 'all';
        document.getElementById('filterForm').submit();
    });

    document.getElementById('exportCsv').addEventListener('click', function(){
        const params = new URLSearchParams(new FormData(document.getElementById('filterForm')));
        params.set('export', '1');
        const url = location.pathname + '?' + params.toString();
        window.location = url;
    });

    // delegate view-json buttons
    document.addEventListener('click', function(e){
        if (e.target && e.target.classList.contains('view-json')) {
            const content = e.target.getAttribute('data-json') || '';
            document.getElementById('jsonContent').textContent = content;
            openModal();
        }
    });

    function openModal(){
        const m = document.getElementById('jsonModal');
        m.style.display = 'block';
        m.classList.add('show');
    }
    function closeModal(){
        const m = document.getElementById('jsonModal');
        m.style.display = 'none';
        m.classList.remove('show');
    }

    // DataTables init for nicer table interactions
    $(function() {
        $("#auditTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 25,
            "ordering": true,
            "order": [[0, "desc"]],
            "language": {
                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ bản ghi",
                "paginate": {
                    "previous": "«",
                    "next": "»"
                }
            }
        });
    });
    </script>
</body>

</html>