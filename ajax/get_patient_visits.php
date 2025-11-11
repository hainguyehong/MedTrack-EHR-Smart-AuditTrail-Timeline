<?php
include '../config/connection.php';
if (!isset($_POST['patient_id'])) {
    echo json_encode([]);
    exit;
}
$patientId = intval($_POST['patient_id']);
$query = "SELECT id, created_at, huyet_ap, can_nang, chieu_cao, nhiet_do, mach_dap, nhip_tim, trieu_chung, chuan_doan, bien_phap, nhap_vien, tien_su_benh
          FROM patient_diseases
          WHERE patient_id = :patient_id
          ORDER BY created_at ASC";
$stmt = $con->prepare($query);
$stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
$stmt->execute();
$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($visits);

