<?php
// include './config/connection.php';

if (isset($_POST['patient_id'])) {
    $patientId = intval($_POST['patient_id']);
    echo $patientId;
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
              ORDER BY pmh.visit_date DESC";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
    $stmt->execute();
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'patient' => $patient,
        'prescriptions' => $prescriptions
    ]);
}