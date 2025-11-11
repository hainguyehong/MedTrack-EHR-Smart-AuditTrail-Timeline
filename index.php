<?php 
include './config/connection.php';
// session_start();

$message_user = '';
$message_pass = '';
$form_submitted = false;
// if (isset($_POST['login'])) {
//     $userName = $_POST['user_name'];
//     $password = $_POST['password'];


//     $encryptedPassword = md5($password);


//     $query = "SELECT `id`, `display_name`, `user_name`, `profile_picture`, `role`
//               FROM `users`
//               WHERE `user_name` = :uname AND `password` = :pwd";


//     try {
//         $stmtLogin = $con->prepare($query);
//         $stmtLogin->bindParam(':uname', $userName, PDO::PARAM_STR);
//         $stmtLogin->bindParam(':pwd', $encryptedPassword, PDO::PARAM_STR);
//         $stmtLogin->execute();


//         if ($stmtLogin->rowCount() == 1) {
//             $row = $stmtLogin->fetch(PDO::FETCH_ASSOC);


//             // Lưu thông tin vào session
//             $_SESSION['user_id']        = $row['id'];
//             $_SESSION['display_name']   = $row['display_name'];
//             $_SESSION['user_name']      = $row['user_name'];
//             $_SESSION['profile_picture']= $row['profile_picture'];
//             $_SESSION['role']           = $row['role'];  


//             header("location:dashboard.php");
//             exit;
//         } else {
//             $message = 'Tài khoản hoặc mật khẩu không đúng.';
//         }
//     } catch (PDOException $ex) {
//         echo $ex->getMessage();
//         exit;
//     }
// }

if (isset($_POST['login'])) {
    $form_submitted = true; 
    $userName = trim($_POST['user_name']);
    $password = trim($_POST['password']);

    // Kiểm tra bỏ trống
    if (empty($userName)) {
        $message_user = 'Vui lòng nhập tên đăng nhập!';
    }
    if (empty($password)) {
        $message_pass = 'Vui lòng nhập mật khẩu!';
    }

    // Chỉ kiểm tra DB nếu có nhập username hoặc password
    if (!empty($userName) || !empty($password)) {
        $encryptedPassword = md5($password);

        try {
            // Kiểm tra user tồn tại
            $checkUserQuery = "
                SELECT user_name FROM users WHERE user_name = :uname
                UNION
                SELECT user_name FROM user_patients WHERE user_name = :uname
            ";
            $stmtCheckUser = $con->prepare($checkUserQuery);
            $stmtCheckUser->bindParam(':uname', $userName, PDO::PARAM_STR);
            $stmtCheckUser->execute();

            if ($stmtCheckUser->rowCount() == 0 && !empty($userName)) {
                $message_user = 'Tên đăng nhập không tồn tại!';
            } else {
                // Kiểm tra user + password
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
                  // Lưu thông tin vào session
                    $_SESSION['user_id']         = $row['id'];
                    $_SESSION['display_name']    = $row['display_name'];
                    $_SESSION['user_name']       = $row['user_name'];
                    $_SESSION['profile_picture'] = $row['profile_picture'];
                    $_SESSION['role']            = $row['role'];  
                    // Điều hướng theo role
                    if ($_SESSION['role'] == 1) {
                        header("location:dashboard.php"); // admin
                    } elseif ($_SESSION['role'] == 3) {
                        header("location:user_medication.php"); // bệnh nhân
                    } elseif ($_SESSION['role'] == 2) {
                        header("location:doctor_patient.php"); // bác sĩ
                    } else {
                        header("location:index.php");
                    }
                    exit;
                } elseif (!empty($password)) {
                    $message_pass = 'Mật khẩu không đúng!';
                }
            }
        } catch (PDOException $ex) {
            echo $ex->getMessage();
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập</title>

  <!-- Thêm favicon -->
  <link rel="icon" type="image/png" href="assets/images/img-tn.png">
  <link rel="apple-touch-icon" href="assets/images/img-tn.png">

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="css/index.css">
</head>
<body>
  <div class="container">
    <!-- Left -->
    <div class="login-left">
      <h2> MEDTRACK MEDICAL</h2>
      <ul>
        <li><i class="fas fa-chart-line"></i> PHÂN TÍCH DỮ LIỆU & BÁO CÁO</li>
        <li><i class="fas fa-calendar-alt"></i> LỊCH HẸN & TÁI KHÁM</li>
        <li><i class="fas fa-file-medical"></i> QUẢN LÝ ĐƠN THUỘC & KẾT QUẢ</li>
        <li><i class="fas fa-stethoscope"></i> TIMELINE ĐIỀU TRỊ & TIỀN SỬ BỆNH</li>
        <li><i class="fas fa-receipt"></i> AUDIT TRAIL & BẢO MẬT DỮ LIỆU</li>
      </ul>
    </div>

    <!-- Right -->
    <div class="login-right">
      <!-- <img src="dist/img/logo.jpg" class="img-thumbnail p-0 border rounded-circle" id="system-logo"> -->
      <img src="assets/images/img-tn.png" class="img-thumbnail p-0 border rounded-circle" id="system-logo">

      <h3>ĐĂNG NHẬP</h3>

      <form method="post" id="loginForm">
        <!-- USERNAME -->
        <div class="form-floating-custom">
          <input type="text" id="user_name" name="user_name"
            class="<?= ($form_submitted && !empty($message_user)) ? 'error-input' : '' ?>"
            value="<?= htmlspecialchars($userName ?? '') ?>" placeholder=" ">
          <label for="user_name">Tên đăng nhập</label>
          <span class="fas fa-user input-icon"></span>
        </div>
        <?php if($form_submitted && !empty($message_user)) : ?>
          <div class="error-message" id="user_error">
            <i class="fas fa-exclamation-circle"></i><?= $message_user ?>
          </div>
        <?php endif; ?>

        <!-- PASSWORD -->
        <div class="form-floating-custom">
          <input type="password" id="password" name="password"
            class="<?= ($form_submitted && !empty($message_pass)) ? 'error-input' : '' ?>"
            placeholder=" ">
          <label for="password">Mật khẩu</label>
          <span class="fas fa-lock input-icon"></span>
        </div>
        <?php if($form_submitted && !empty($message_pass)) : ?>
          <div class="error-message" id="pass_error">
            <i class="fas fa-exclamation-circle"></i><?= $message_pass ?>
          </div>
        <?php endif; ?>

        <button name="login" type="submit" class="btn-login">ĐĂNG NHẬP</button>
      </form>
    </div>
  </div>

<script>
document.getElementById('user_name').addEventListener('focus', function() {
  this.classList.remove('error-input');
  const userError = document.getElementById('user_error');
  if (userError) userError.style.display = 'none';
});
document.getElementById('password').addEventListener('focus', function() {
  this.classList.remove('error-input');
  const passError = document.getElementById('pass_error');
  if (passError) passError.style.display = 'none';
});
</script>
</body>
</html>
