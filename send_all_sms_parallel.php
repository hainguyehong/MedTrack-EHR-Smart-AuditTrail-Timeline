<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$ids = $data['ids'];

if (empty($ids)) {
    echo json_encode(["error" => "No IDs"]);
    exit;
}

include_once __DIR__ . '/common_service/env_loader.php';
include_once __DIR__ . '/config/connection.php';

$base_url = getenv('INFOBIP_BASE_URL');
$api_key  = getenv('INFOBIP_API_KEY');

// Lấy số điện thoại từ DB
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "SELECT id, patient_name, phone_number, next_visit_date 
        FROM patients 
        JOIN patient_diseases ON patients.id = patient_diseases.patient_id
        WHERE patients.id IN ($placeholders)
        AND next_visit_date IS NOT NULL
        ORDER BY next_visit_date ASC";

$stmt = $con->prepare($sql);
$stmt->execute($ids);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ========== CONFIG =============
$MAX_PARALLEL = 5; // tối ưu nhất InfoBip Free
// ===============================


function sendParallel($patients, $base_url, $api_key, $MAX_PARALLEL)
{
    $mh = curl_multi_init();
    $handles = [];
    $index = 0;
    $total = count($patients);
    $sent = 0;

    while ($index < $total || count($handles) > 0) {

        while ($index < $total && count($handles) < $MAX_PARALLEL) {

            $p = $patients[$index];
            $message_text = "Xin chào {$p['patient_name']}, bạn có lịch tái khám vào ngày " .
                date('d/m/Y', strtotime($p['next_visit_date'])) .
                ". Vui lòng liên hệ phòng khám để xác nhận.";

            $payload = json_encode([
                "messages" => [
                    [
                        "destinations" => [["to" => $p['phone_number']]],
                        "text" => $message_text
                    ]
                ]
            ]);

            $ch = curl_init($base_url . "/sms/2/text/advanced");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: App $api_key",
                "Content-Type: application/json",
                "Accept: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_multi_add_handle($mh, $ch);
            $handles[(int)$ch] = $ch;
            $index++;
        }

        $running = null;
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);

        foreach ($handles as $id => $ch) {
            $info = curl_getinfo($ch);

            if ($info['http_code'] !== 0) {
                if ($info['http_code'] == 200) $sent++;
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                unset($handles[$id]);
            }
        }
    }

    curl_multi_close($mh);
    return $sent;
}

$sent = sendParallel($patients, $base_url, $api_key, $MAX_PARALLEL);

echo json_encode([
    "sent" => $sent,
    "total" => count($patients)
]);