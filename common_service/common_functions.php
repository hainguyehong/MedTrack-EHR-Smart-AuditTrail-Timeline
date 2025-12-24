<?php 
function getTime($time = '') {
	$data = '<option value="">Chọn khung giờ</option>';
	$timeArr = array(
		"07:00 - 08:00 ",
		"08:00 - 09:00 ",
		"09:00 - 10:00 ",
		"10:00 - 11:00 ",
		"11:00 - 12:00 ",
		"13:00 - 14:00 ",
		"14:00 - 15:00 ",
		"15:00 - 16:00 ",
		"16:00 - 17:30 "
	);
	foreach($timeArr as $t) {
		if($time == $t) {
			$data = $data .'<option selected="selected" value="'.$t.'">'.$t.'</option>';
		} else {
		$data = $data .'<option value="'.$t.'">'.$t.'</option>';
		}
	}
	return $data;
}

function getGender($gender = '') {
    $data = '<option value="">Chọn giới tính</option>';

    $arr = array("Nam", "Nữ", "Khác");

    $genderNorm = mb_strtolower(trim((string)$gender), 'UTF-8');

    for ($i = 0; $i < count($arr); $i++) {
        $opt = $arr[$i];

        $optNorm = mb_strtolower(trim($opt), 'UTF-8');

        if ($genderNorm === 'nu') $genderNorm = 'nữ';

        $selected = ($genderNorm === $optNorm) ? ' selected="selected"' : '';
        $data .= '<option value="'.$opt.'"'.$selected.'>'.$opt.'</option>';
    }

    return $data;
}

function getRoles($role = '') {
    $roles = [
        1 => "Admin",
        2 => "Bác sĩ",
        // 3 => "Bệnh nhân"
    ];

    $data = '<option value="">Chọn vai trò</option>';
    foreach ($roles as $key => $name) {
        // So sánh key (số) với giá trị role từ DB
        $selected = ($role == $key) ? ' selected="selected"' : '';
        $data .= '<option value="'.$key.'"'.$selected.'>'.$name.'</option>';
    }
    return $data;
}
function Nhapvien($selectedId = '') {
    $options = [
        1 => "Có",
        2 => "Không",
    ];

    $html  = '<option value="">Nhập viện</option>';
    foreach($options as $val => $label) {
        $selected = ($selectedId !== '' && $selectedId == $val) ? 'selected' : '';
        $html .= '<option value="'.$val.'" '.$selected.'>'.$label.'</option>';
    }
    return $html;
}


// hàm cũ
function getMedicines($con, $medicineId = 0) {

	$query = "select `id`, `medicine_name` from `medicines` 
	where is_deleted = 0 order by `medicine_name` asc;";

	$stmt = $con->prepare($query);
	try {
		$stmt->execute();

	} catch(PDOException $ex) {
		echo $ex->getTraceAsString();
		echo $ex->getMessage();
		exit;
	}

	$data = '<option value="">Chọn loại thuốc</option>';

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		if($medicineId == $row['id']) {
			$data = $data.'<option selected="selected" value="'.$row['id'].'">'.$row['medicine_name'].'</option>';

		} else {
		$data = $data.'<option value="'.$row['id'].'">'.$row['medicine_name'].'</option>';
		}
	}

	return $data;
	
}


function getPatients($con, $selectedId = '') {
    $query = "SELECT `id`, `patient_name`, `phone_number`
              FROM `patients`
              WHERE `is_deleted` = 0
              ORDER BY `patient_name` ASC;";

    $stmt = $con->prepare($query);

    try {
        $stmt->execute();
    } catch(PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }

    $data = '<option value="">Chọn bệnh nhân</option>';

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $selected = ($selectedId !== '' && $selectedId == $row['id']) ? 'selected' : '';
        $data .= '<option value="'.$row['id'].'" '.$selected.'>'
              .  $row['patient_name'].' ('.$row['phone_number'].')'
              .  '</option>';
    }

    return $data;
}



// function getDateTextBox($label, $dateId) { 
// 	$d = '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10"> <div class="form-group"> <label>'.$label.'</label> 
// 	<div class="input-group rounded-0 date" id="'.$dateId.'_group" data-target-input="nearest"> <input type="text" class="form-control form-control-sm rounded-0 datetimepicker-input" 
// 	data-toggle="datetimepicker" data-target="#'.$dateId.'_group" name="'.$dateId.'" id="'.$dateId.'" required="required" autocomplete="off" data-date-format="DD/MM/YYYY"/> 
// 	<div class="input-group-append rounded-0" data-target="#'.$dateId.'_group" data-toggle="datetimepicker">
// 	 <div class="input-group-text"><i class="fa fa-calendar"></i></div> </div> </div> </div> </div>'; 
// 	 return $d; 
// }
function getDateTextBox($label, $dateId, $required = false) {

    $requiredStar = $required ? ' <span class="text-danger">*</span>' : '';

    return '
    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-10">
        <div class="form-group">
            <label>'.$label.$requiredStar.'</label>
            <div class="input-group date" id="'.$dateId.'_group" data-target-input="nearest">
                <input type="text"
                    class="form-control form-control-sm datetimepicker-input"
                    data-toggle="datetimepicker"
                    data-target="#'.$dateId.'_group"
                    name="'.$dateId.'"
                    id="'.$dateId.'"
                    '.($required ? 'required' : '').'
                    autocomplete="off"
                    data-date-format="DD/MM/YYYY"/>

                <div class="input-group-append"
                     data-target="#'.$dateId.'_group"
                     data-toggle="datetimepicker">
                    <div class="input-group-text">
                        <i class="fa fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>';
}


// bảng log_audit
function log_audit($pdo, $user, $table, $record_id, $action, $old, $new) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_logs (user_id, table_name, record_id, action, old_value, new_value)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user,
            $table,
            $record_id,
            $action,
            json_encode($old, JSON_UNESCAPED_UNICODE),
            json_encode($new, JSON_UNESCAPED_UNICODE)
        ]);
    } catch (PDOException $e) {
        error_log("Audit log failed: " . $e->getMessage());
    }
}

function islogin(array $allowedRoles = [])
{
    // Chưa login -> về trang login
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: index.php"); 
        exit();
    }

    // Nếu không truyền $allowedRoles -> chỉ cần login là đủ
    if (empty($allowedRoles)) {
        return;
    }

    // Không có role trong session -> không cho vào
    if (!isset($_SESSION['role'])) {
        header('HTTP/1.1 403 Forbidden');
        header('Location: no_permission.php');
        exit();
    }

    $currentRole = (int) $_SESSION['role'];

    // Role không nằm trong danh sách cho phép
    if (!in_array($currentRole, $allowedRoles, true)) {
        header('HTTP/1.1 403 Forbidden');
        header('Location: no_permission.php'); // tạo trang này để báo "không có quyền"
        exit();
    }
}
function statusToVietnamese($status) {
    return match($status) {
        'pending'   => 'Chờ xác nhận',
        'rejected'  => 'Từ chối',
        'confirmed'  => 'Đã xác nhận',
        default     => 'Không rõ'
    };
}
// function uploadImageAndGetRelativePath(string $field, string $relDir, int $maxBytes = 5242880): ?string
// {
//     if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
//         return null;
//     }

//     $f = $_FILES[$field];

//     if ($f['error'] !== UPLOAD_ERR_OK) return null;
//     if ($f['size'] > $maxBytes) return null;

//     // chỉ cho phép ảnh
//     $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
//     $allowExt = ['jpg','jpeg','png','webp','gif'];
//     if (!in_array($ext, $allowExt, true)) return null;

//     // xác thực thêm bằng mime (tránh file giả ảnh)
//     $mime = @mime_content_type($f['tmp_name']);
//     if ($mime === false || strpos($mime, 'image/') !== 0) return null;

//     // đường dẫn vật lý tuyệt đối (không phụ thuộc ổ C/D)
//     $absDir = rtrim(__DIR__, '/\\') . DIRECTORY_SEPARATOR . trim($relDir, '/\\') . DIRECTORY_SEPARATOR;
//     if (!is_dir($absDir)) mkdir($absDir, 0777, true);

//     // tên file tránh trùng + an toàn
//     $safeBase = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
//     $newName  = uniqid($field . "_", true) . "_" . $safeBase . "." . $ext;

//     $absPath = $absDir . $newName;

//     if (!move_uploaded_file($f['tmp_name'], $absPath)) return null;

//     // trả về path tương đối để lưu DB/session và dùng làm src
//     $relDir = rtrim($relDir, '/\\') . '/';
//     return $relDir . $newName;
// }