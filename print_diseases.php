<?php
include './config/connection.php';
require_once('./vendor/tecnickcom/tcpdf/tcpdf.php'); // hoặc đường dẫn đúng TCPDF

$reportTitle = "Báo cáo Bệnh Nhân";

// Lấy tham số từ URL
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

// Chuyển định dạng từ mm/dd/yyyy sang yyyy-mm-dd để truy vấn MySQL
if ($from && $to) {
    $fromArr = explode("/", $from);
    $toArr = explode("/", $to);

    $fromMysql = $fromArr[2].'-'.$fromArr[0].'-'.$fromArr[1];
    $toMysql = $toArr[2].'-'.$toArr[0].'-'.$toArr[1];

    // Truy vấn dữ liệu
    $query = "SELECT p.patient_name, p.address, p.phone_number, pv.visit_date, pv.disease
              FROM patients AS p
              JOIN patient_visits AS pv ON pv.patient_id = p.id
              WHERE pv.visit_date BETWEEN :from AND :to
              ORDER BY pv.visit_date ASC";
    $stmt = $con->prepare($query);
    $stmt->execute([':from' => $fromMysql, ':to' => $toMysql]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

// Tạo bảng HTML
$html = '<table border="1" cellpadding="4">
<thead>
<tr style="background-color:#f2f2f2;">
<th width="5%">STT</th>
<th width="15%">Ngày Khám</th>
<th width="25%">Tên Bệnh Nhân</th>
<th width="25%">Địa Chỉ</th>
<th width="15%">SĐT</th>
<th width="15%">Bệnh</th>
</tr>
</thead>
<tbody>';

if(count($results) > 0){
    foreach($results as $i => $r){
        $html .= '<tr>
        <td>'.($i+1).'</td>
        <td>'.$r['visit_date'].'</td>
        <td>'.htmlspecialchars($r['patient_name']).'</td>
        <td>'.htmlspecialchars($r['address']).'</td>
        <td>'.htmlspecialchars($r['phone_number']).'</td>
        <td>'.htmlspecialchars($r['disease']).'</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="6" style="text-align:center;">Không có dữ liệu trong khoảng thời gian này.</td></tr>';
}

$html .= '</tbody></table>';

// Viết HTML vào PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Xuất PDF
$pdf->Output('patients_visits.pdf', 'I'); // 'I' = inline, hiển thị trình duyệt