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

	$i = 0;
	$size = sizeof($arr);

	for($i = 0; $i < $size; $i++) {
		if($gender == $arr[$i]) {
			$data = $data .'<option selected="selected" value="'.$arr[$i].'">'.$arr[$i].'</option>';
		} else {
		$data = $data .'<option value="'.$arr[$i].'">'.$arr[$i].'</option>';
		}
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
function Nhapvien($nv = '') {
    $nv = [
        1 => "Có",
        2 => "Không",
    ];

    $data = '<option value="">Nhập viện</option>';
    foreach ($nv as $key => $name) {
        // So sánh key (số) với giá trị role từ DB
        $selected = ($nv == $key) ? ' selected="selected"' : '';
        $data .= '<option value="'.$key.'"'.$selected.'>'.$name.'</option>';
    }
    return $data;
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


function getPatients($con) {
$query = "select `id`, `patient_name`, `phone_number` 
from `patients` where `is_deleted` = 0 order by `patient_name` asc;";

	$stmt = $con->prepare($query);
	try {
		$stmt->execute();

	} catch(PDOException $ex) {
		echo $ex->getTraceAsString();
		echo $ex->getMessage();
		exit;
	}

	$data = '<option value="">Chọn bệnh nhân</option>';

	while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$data = $data.'<option value="'.$row['id'].'">'.$row['patient_name'].' ('.$row['phone_number'].')'.'</option>';
	}

	return $data;
}


function getDateTextBox($label, $dateId) { 
	$d = '<div class="col-lg-3 col-md-3 col-sm-4 col-xs-10"> <div class="form-group"> <label>'.$label.'</label> 
	<div class="input-group rounded-0 date" id="'.$dateId.'_group" data-target-input="nearest"> <input type="text" class="form-control form-control-sm rounded-0 datetimepicker-input" 
	data-toggle="datetimepicker" data-target="#'.$dateId.'_group" name="'.$dateId.'" id="'.$dateId.'" required="required" autocomplete="off" data-date-format="DD/MM/YYYY"/> 
	<div class="input-group-append rounded-0" data-target="#'.$dateId.'_group" data-toggle="datetimepicker">
	 <div class="input-group-text"><i class="fa fa-calendar"></i></div> </div> </div> </div> </div>'; 
	 return $d; 
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

// function islogin() {
// 	if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
// 		return true;
// 	} else {
// 		header('Location: index.php');
// 		exit();
// 	}
// }
function islogin(array $allowedRoles = [])
{
    // Chưa login -> về trang login
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: index.php"); // trang login của bạn
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