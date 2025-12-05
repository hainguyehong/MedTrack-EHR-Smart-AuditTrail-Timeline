<?php
include './config/connection.php';
include './common_service/common_functions.php';
islogin([2]); // Chỉ cho phép bác sĩ truy cập

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
    $con->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | 1) Kiểm tra xem book_id đã có trong appointment_status_log chưa
    |    Lấy dòng mới nhất (nếu có)
    |-------------------------------------------------------------------------- 
    */
    $sqlCheck = "SELECT id, status, doctor_note 
                 FROM appointment_status_log
                 WHERE book_id = :book_id
                 ORDER BY id DESC
                 LIMIT 1";
    $stmtCheck = $con->prepare($sqlCheck);
    $stmtCheck->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmtCheck->execute();
    $currentRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    $prevStatus = $currentRow['status'] ?? null;
    $prevNote   = $currentRow['doctor_note'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | 2) Nếu chưa có -> INSERT
    |    Nếu có rồi -> UPDATE dòng mới nhất
    |-------------------------------------------------------------------------- 
    */
    if (!$currentRow) {
        // chưa có book_id => insert mới
        $sqlInsert = "INSERT INTO appointment_status_log (book_id, status, doctor_note)
                      VALUES (:book_id, :status, :doctor_note)";
        $stmt = $con->prepare($sqlInsert);
        $stmt->bindParam(':book_id', $bookId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $action, PDO::PARAM_STR);
        $stmt->bindParam(':doctor_note', $note, PDO::PARAM_STR);
        $stmt->execute();

        $actionType = 'insert';
        $recordIdForAudit = $bookId; // vẫn log theo lịch khám
    } else {
        // đã có => update dòng mới nhất theo id
        $logId = (int)$currentRow['id'];

        $sqlUpdate = "UPDATE appointment_status_log
                      SET status = :status,
                          doctor_note = :doctor_note
                      WHERE id = :id";
        $stmt = $con->prepare($sqlUpdate);
        $stmt->bindParam(':status', $action, PDO::PARAM_STR);
        $stmt->bindParam(':doctor_note', $note, PDO::PARAM_STR);
        $stmt->bindParam(':id', $logId, PDO::PARAM_INT);
        $stmt->execute();

        $actionType = 'update';
        $recordIdForAudit = $bookId;
    }

    /*
    |--------------------------------------------------------------------------
    | 3) Ghi Audit Log
    |-------------------------------------------------------------------------- 
    */
    log_audit(
        $con,
        $_SESSION['user_id'] ?? 'unknown',
        'appointment_status_log',
        $recordIdForAudit,
        $actionType,
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

    $con->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Đã lưu trạng thái lịch hẹn thành công',
        'data' => [
            'book_id' => $bookId,
            'status' => $action,
            'doctor_note' => $note,
            'action_type' => $actionType
        ]
    ]);

} catch (Exception $ex) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi lưu vào CSDL: ' . $ex->getMessage()
    ]);
}