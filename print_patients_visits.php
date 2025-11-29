<?php
include './config/connection.php';
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php'); // hoặc đường dẫn đúng TCPDF

$reportTitle = "Báo cáo Bệnh Nhân";

// Lấy tham số từ URL
$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';

// Hàm parse linh hoạt sang định dạng MySQL Y-m-d
function parseDateToMysql($str) {
    $str = trim($str);
    if ($str === '') return '';
    $formats = ['d/m/Y', 'm/d/Y', 'Y-m-d'];
    foreach ($formats as $fmt) {
        $dt = DateTime::createFromFormat($fmt, $str);
        if ($dt && $dt->format($fmt) === $str) {
            return $dt->format('Y-m-d');
        }
    }
    // thử fallback với date_create để hỗ trợ các biến thể khác
    $dt = date_create($str);
    if ($dt) return $dt->format('Y-m-d');
    return '';
}

// Chuyển định dạng và chuẩn hoá
$fromMysql = parseDateToMysql($from);
$toMysql = parseDateToMysql($to);

if ($fromMysql && $toMysql) {
    // nếu người dùng nhập ngược (from > to) thì hoán đổi để đảm bảo khoảng hợp lệ
    if ($fromMysql > $toMysql) {
        $tmp = $fromMysql;
        $fromMysql = $toMysql;
        $toMysql = $tmp;
    }

    // LẤY DỮ LIỆU KHÁM (không tham chiếu trực tiếp tới cột tên thuốc)
    $query = "
        SELECT 
            p.patient_name, 
            p.address, 
            p.phone_number, 
            pd.id AS pd_id,
            pd.created_at AS visit_date,
            pd.next_visit_date,
            pd.huyet_ap, pd.can_nang, pd.chieu_cao, pd.nhiet_do, pd.mach_dap, pd.nhip_tim,
            pd.trieu_chung, pd.chuan_doan, pd.bien_phap, pd.nhap_vien, pd.tien_su_benh,
            -- meds_raw: mỗi item dạng medicine_id::quantity::dosage::note, phân tách bằng '|'
            GROUP_CONCAT(
                CONCAT(
                    pmh.medicine_id, '::', COALESCE(pmh.quantity,''), '::', COALESCE(pmh.dosage,''), '::', COALESCE(pmh.note,'')
                ) SEPARATOR '|'
            ) AS meds_raw
        FROM patient_diseases pd
        JOIN patients p ON p.id = pd.patient_id
        LEFT JOIN patient_medication_history pmh 
            ON pmh.patient_id = pd.patient_id 
            AND DATE(pmh.created_at) = DATE(pd.created_at)
        WHERE DATE(pd.created_at) BETWEEN :from AND :to
        GROUP BY pd.id
        ORDER BY pd.created_at ASC
    ";
    $stmt = $con->prepare($query);
    $stmt->execute([':from' => $fromMysql, ':to' => $toMysql]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- XỬ LÝ TÊN THUỐC: tìm cột "tên" trong bảng medicines nếu có ---
    $medNameCol = null;
    try {
        // Lấy tên database hiện tại
        $dbName = $con->query("SELECT DATABASE()")->fetchColumn();

        // Tìm cột văn bản đầu tiên (varchar/text/char) trong bảng medicines, loại trừ id
        $colStmt = $con->prepare("
            SELECT COLUMN_NAME 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = :schema 
              AND TABLE_NAME = 'medicines' 
              AND DATA_TYPE IN ('varchar','text','char') 
              AND COLUMN_NAME <> 'id'
            ORDER BY ORDINAL_POSITION
            LIMIT 1
        ");
        $colStmt->execute([':schema' => $dbName]);
        $medNameCol = $colStmt->fetchColumn();
    } catch (Exception $e) {
        $medNameCol = null;
    }

    // Tạo map id -> name nếu có thuốc tồn tại
    $medicineMap = [];
    $allIds = [];
    foreach ($results as $row) {
        if (!empty($row['meds_raw'])) {
            $parts = explode('|', $row['meds_raw']);
            foreach ($parts as $p) {
                if (trim($p) === '') continue;
                $pieces = explode('::', $p);
                $mid = intval($pieces[0]);
                if ($mid > 0) $allIds[$mid] = $mid;
            }
        }
    }

    if (!empty($allIds) && $medNameCol) {
        $placeholders = implode(',', array_fill(0, count($allIds), '?'));
        $idsVals = array_values($allIds);
        $sqlNames = "SELECT id, `$medNameCol` AS med_name FROM medicines WHERE id IN ($placeholders)";
        $nameStmt = $con->prepare($sqlNames);
        $nameStmt->execute($idsVals);
        $rows = $nameStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $medicineMap[intval($r['id'])] = $r['med_name'];
        }
    }
    // Nếu không có cột tên, medicineMap sẽ rỗng -> hiển thị ID#...

    // Chuyển đổi meds_raw thành chuỗi hiển thị, lưu lại vào mỗi kết quả (thay vì truy vấn ban đầu)
    foreach ($results as $k => $row) {
        $medsDisplay = '-';
        if (!empty($row['meds_raw'])) {
            $items = [];
            $parts = explode('|', $row['meds_raw']);
            foreach ($parts as $p) {
                if (trim($p) === '') continue;
                $pieces = explode('::', $p);
                $mid = intval($pieces[0]);
                $qty = $pieces[1] ?? '';
                $dos = $pieces[2] ?? '';
                $note = $pieces[3] ?? '';

                $name = isset($medicineMap[$mid]) ? $medicineMap[$mid] : 'ID#'.$mid;
                $segment = $name;
                if ($qty !== '') $segment .= ' x'.$qty;
                if ($dos !== '') $segment .= ' ('.$dos.')';
                if ($note !== '') $segment .= ' - '.$note;
                $items[] = $segment;
            }
            if (!empty($items)) $medsDisplay = implode('; ', $items);
        }
        $results[$k]['medications'] = $medsDisplay;
    }

} else {
    $results = [];
}

// Khởi tạo TCPDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Hospital System');
$pdf->SetTitle($reportTitle);
$pdf->SetMargins(15, 20, 15);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();

// Set font hỗ trợ tiếng Việt (tích hợp sẵn TCPDF)
$pdf->SetFont('dejavusans', '', 12);

// Tiêu đề báo cáo
$pdf->Cell(0, 10, $reportTitle, 0, 1, 'C');
$pdf->Cell(0, 8, "Từ ngày: $from  -  Đến ngày: $to", 0, 1, 'C');
$pdf->Ln(5);

// Tạo bảng HTML (mở rộng để hiển thị thông tin khám)
$html = '
<style>
td, th {
    font-size: 10px;
    line-height: 1.4;
    padding: 4px;
}
thead th {
    background-color:#EFEFEF;
    text-align:center;
    font-weight:bold;
}
</style>

<table border="1" cellspacing="0" cellpadding="4">
<thead>
<tr>
    <th width="6%">STT</th>
    <th width="10%">Ngày Khám</th>
    <th width="18%">Tên Bệnh Nhân</th>
    <th width="20%">Địa Chỉ</th>
    <th width="10%">SĐT</th>
    <th width="36%">Chuẩn đoán bệnh</th>
</tr>
</thead>
<tbody>
';

if(count($results) > 0){
    foreach($results as $i => $r){
        $vitals = [];
        if (!empty($r['huyet_ap'])) $vitals[] = 'Huyết áp: '.htmlspecialchars($r['huyet_ap']);
        if (!empty($r['can_nang'])) $vitals[] = 'Cân nặng: '.htmlspecialchars($r['can_nang']).' kg';
        if (!empty($r['chieu_cao'])) $vitals[] = 'Chiều cao: '.htmlspecialchars($r['chieu_cao']).' cm';
        if (!empty($r['nhiet_do'])) $vitals[] = 'Nhiệt độ: '.htmlspecialchars($r['nhiet_do']).' °C';
        if (!empty($r['mach_dap'])) $vitals[] = 'Mạch: '.htmlspecialchars($r['mach_dap']).' bpm';
        if (!empty($r['nhip_tim'])) $vitals[] = 'Nhịp tim: '.htmlspecialchars($r['nhip_tim']).' bpm';

        $vitalsText = implode('<br/>', $vitals);
        $diag = !empty($r['chuan_doan']) ? htmlspecialchars($r['chuan_doan']) : '-';
        $symptoms = !empty($r['trieu_chung']) ? htmlspecialchars($r['trieu_chung']) : '-';
        $treatment = !empty($r['bien_phap']) ? htmlspecialchars($r['bien_phap']) : '-';
        $admit = !empty($r['nhap_vien']) ? htmlspecialchars($r['nhap_vien']) : '-';
        $meds = !empty($r['medications']) ? htmlspecialchars($r['medications']) : '-';
        $nextVisit = !empty($r['next_visit_date']) && $r['next_visit_date'] != '0000-00-00' ? date("d/m/Y", strtotime($r['next_visit_date'])) : '-';

        $html .= '
        <tr>
            <td width="6%" style="text-align:center;">'.($i+1).'</td>
            <td width="10%" style="text-align:center;">'.date("d/m/Y H:i", strtotime($r["visit_date"])).'</td>
            <td width="18%">'.htmlspecialchars($r["patient_name"]).'</td>
            <td width="20%">'.htmlspecialchars($r["address"]).'</td>
            <td width="10%" style="text-align:center;">'.htmlspecialchars($r["phone_number"]).'</td>
            <!-- Changed: show only the diagnosis text in this column -->
            <td width="36%">'.$diag.'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">Không có dữ liệu</td></tr>';
}

$html .= '</tbody></table>';

// Viết HTML vào PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Xuất PDF
$pdf->Output('patients_visits.pdf', 'I'); // 'I' = inline, hiển thị trình duyệt