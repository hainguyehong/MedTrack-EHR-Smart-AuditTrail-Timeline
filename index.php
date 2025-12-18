<?php 
// session_start();
include './config/connection.php';
include './common_service/common_functions.php';

$form_submitted = false;
$message = '';
$login_success = false;
$redirect_url = '';

if (isset($_POST['login'])) {
    $form_submitted = true; 
    $userName = trim($_POST['user_name']);
    $password = trim($_POST['password']);

    if (empty($userName) || empty($password)) {
        $message = 'Vui lòng điền đầy đủ thông tin!';
    } else {
        $encryptedPassword = md5($password);

        try {
            $query = "
                SELECT id, display_name, user_name, profile_picture, role
                FROM users
                WHERE user_name = :uname AND password = :pwd
                UNION
                SELECT id, display_name, user_name, NULL AS profile_picture, 3 AS role
                FROM user_patients
                WHERE user_name = :uname AND password = :pwd
            ";
            $stmtLogin = $con->prepare($query);
            $stmtLogin->bindParam(':uname', $userName, PDO::PARAM_STR);
            $stmtLogin->bindParam(':pwd', $encryptedPassword, PDO::PARAM_STR);
            $stmtLogin->execute();

            if ($stmtLogin->rowCount() == 1) {
                $row = $stmtLogin->fetch(PDO::FETCH_ASSOC);

                $_SESSION['user_id']         = $row['id'];
                $_SESSION['display_name']    = $row['display_name'];
                $_SESSION['user_name']       = $row['user_name'];
                $_SESSION['profile_picture'] = $row['profile_picture'];
                $_SESSION['role']            = $row['role'];  

                // Xác định URL redirect theo role
                if ($row['role'] == 1) $redirect_url = 'dashboard.php';
                elseif ($row['role'] == 3) $redirect_url = 'user_medication.php';
                elseif ($row['role'] == 2) $redirect_url = 'doctor_patient.php';
                else $redirect_url = 'index.php';

                $login_success = true; // Đánh dấu login thành công
            } else {
                $message = 'Tên đăng nhập hoặc mật khẩu không đúng!';
            }
        } catch (PDOException $ex) {
            $message = 'Lỗi hệ thống: ' . $ex->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đăng nhập - MedTrack</title>
<link rel="icon" type="image/png" href="assets/images/img-tn.png">
<link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">
    <!-- Left -->
    <div class="login-left">
        <h2> MEDTRACK MEDICAL</h2> 
        <ul>
            <li><i class="fas fa-chart-line"></i> PHÂN TÍCH DỮ LIỆU & BÁO CÁO</li>
            <li><i class="fas fa-calendar-alt"></i> LỊCH HẸN & TÁI KHÁM</li>
            <li><i class="fas fa-file-medical"></i> QUẢN LÝ ĐƠN THUỐC & KẾT QUẢ</li>
            <li><i class="fas fa-stethoscope"></i> TIMELINE ĐIỀU TRỊ & TIỀN SỬ BỆNH</li>
            <li><i class="fas fa-receipt"></i> AUDIT TRAIL & BẢO MẬT DỮ LIỆU</li>
        </ul>
    </div>

    <!-- Right -->
    <div class="login-right">
        <img src="assets/images/img-tn.png" class="img-thumbnail p-0 border rounded-circle" id="system-logo">
        <h3>ĐĂNG NHẬP</h3>

        <form method="post" id="loginForm">
            <div class="form-floating-custom">
                <input type="text" id="user_name" name="user_name" placeholder=" " value="<?= htmlspecialchars($userName ?? '') ?>">
                <label for="user_name">Tên đăng nhập</label>
                <span class="fas fa-user input-icon"></span>
            </div>

            <div class="form-floating-custom">
                <input type="password" id="password" name="password" placeholder=" ">
                <label for="password">Mật khẩu</label>
                <span class="fas fa-lock input-icon"></span>
            </div>

            <button name="login" type="submit" class="btn-login">ĐĂNG NHẬP</button>
        </form>
    </div>
</div>

<<?php if ($form_submitted && !empty($message)): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Lỗi đăng nhập',
    text: '<?= addslashes($message) ?>',
    confirmButtonText: 'Đã hiểu'
});
</script>
<?php endif; ?>


<?php if ($login_success && !empty($redirect_url)): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Đăng nhập thành công!',
    showConfirmButton: false,
    timer: 1200
}).then(() => {
    window.location.href = '<?= $redirect_url ?>';
});
</script>
<?php endif; ?>

</body>
</html>
