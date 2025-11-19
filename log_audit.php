<?php
/**
 * Hàm ghi log audit vào bảng audit_logs
 * 
 * @param PDO    $con         - kết nối CSDL
 * @param mixed  $userId      - người thực hiện hành động (vd: admin, doctor)
 * @param string $tableName   - bảng tác động
 * @param int    $recordId    - ID bản ghi bị tác động
 * @param string $action      - hành động: insert / update / delete
 * @param array|null $oldData - dữ liệu trước khi thay đổi
 * @param array|null $newData - dữ liệu sau khi thay đổi
 * 
 * @return void
 */
function log_audit($con, $userId, $tableName, $recordId, $action, $oldData = null, $newData = null)
{
    try {
        // Đảm bảo tồn tại bảng audit_logs
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255),
                table_name VARCHAR(255),
                record_id INT,
                action VARCHAR(50),
                old_data TEXT,
                new_data TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        $con->exec($createTableSQL);

        // Chuyển old_data và new_data thành JSON trước khi lưu
        $oldJson = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
        $newJson = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;

        $sql = "
            INSERT INTO audit_logs (user_id, table_name, record_id, action, old_data, new_data)
            VALUES (:user_id, :table_name, :record_id, :action, :old_data, :new_data)
        ";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':table_name', $tableName);
        $stmt->bindParam(':record_id', $recordId, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':old_data', $oldJson);
        $stmt->bindParam(':new_data', $newJson);

        $stmt->execute();
    } catch (Exception $e) {
        // Nếu cần debug thì mở comment dòng này
        // error_log("Audit Log Error: " . $e->getMessage());
    }
}