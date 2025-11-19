<?php
include './config/connection.php';
include './common_service/common_functions.php';
islogin();

// Đảm bảo đã load hàm log_audit()
// Nếu hàm nằm file khác thì include thêm ở đây:
// include './common_service/log_audit.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

$bookId = isset($data['id']) ? (int)$data['id'] : 0;
$action = isset($data['action']) ? $data['action'] : '';
$note   = isset($data['note']) ? trim($data['note']) : '';

if ($bookId <= 0 || !in_array($action, ['pending','confirmed','rejected'], true)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

try {

    /*
    |--------------------------------------------------------------------------
    | 1) Lưu trạng thái lịch khám vào bảng appointment_status_log
    |--------------------------------------------------------------------------
    */
    
    $sql = "INSERT INTO appointment_status_log (book_id, status, doctor_note)
            VALUES (:book_id, :status, :doctor_note)";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmt->bindParam(':status', $action, PDO::PARAM_STR);
    $stmt->bindParam(':doctor_note', $note, PDO::PARAM_STR);
    $stmt->execute();

    /*
    |--------------------------------------------------------------------------
    | 2) Lấy dữ liệu cũ của lịch (để ghi vào log)
    |--------------------------------------------------------------------------
    */
    $sqlOld = "SELECT status, doctor_note FROM appointment_status_log 
               WHERE book_id = :id ORDER BY id DESC LIMIT 2";
    $stmtOld = $con->prepare($sqlOld);
    $stmtOld->bindParam(':id', $bookId);
    $stmtOld->execute();
    $logs = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

    $prevStatus = $logs[1]['status'] ?? null;
    $prevNote   = $logs[1]['doctor_note'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | 3) Ghi Audit Log
    |--------------------------------------------------------------------------
    |
    | user_id     = người bác sĩ đang thao tác (từ session)
    | table_name  = "appointment_status_log"
    | record_id   = $bookId (lịch khám)
    | action      = update / insert tùy ngữ cảnh (ở đây là update)
    | old_data    = trạng thái cũ
    | new_data    = trạng thái mới
    |
    */

    log_audit(
        $con,
        $_SESSION['user_id'] ?? 'unknown',
        'appointment_status_log',
        $bookId,
        'update',
        // old data
        [
            'status'      => $prevStatus,
            'doctor_note' => $prevNote
        ],
        // new data
        [
            'status'      => $action,
            'doctor_note' => $note
        ]
    );

    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu trạng thái lịch hẹn + Audit Log thành công',
        'data' => [
            'book_id' => $bookId,
            'status' => $action,
            'doctor_note' => $note
        ]
    ]);

} catch (Exception $ex) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lưu vào CSDL: ' . $ex->getMessage()
    ]);
}