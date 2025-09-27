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
        $message_user = 'Vui lòng nhập tên đăng nhập';
    }
    if (empty($password)) {
        $message_pass = 'Vui lòng nhập mật khẩu';
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
                $message_user = 'Tên đăng nhập không tồn tại';
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
                    $message_pass = 'Mật khẩu không đúng';
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

  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <style>
    *{
      box-sizing:border-box;
      margin:0;
      padding:0;
      font-family:'Source Sans Pro',sans-serif;
    }

    body {
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      background:#e9eff5;
    }

    .container {
      display:flex;
      width:1000px;
      background:#fff;
      border-radius:12px;
      overflow:hidden;
      box-shadow:0 4px 20px rgba(0,0,0,0.1);
    }

    /* Left side */
    .login-left {
      flex:1;
      background:linear-gradient(135deg,#2563eb,#1e40af);
      color:#fff;
      padding:50px;
    }
    .login-left h2 {
      font-size:28px;
      margin-bottom:20px;
    }
    .login-left ul {
      list-style:none;
    }
    .login-left ul li {
      margin:12px 0;
      font-size:16px;
    }
    .login-left ul li i {
      margin-right:10px;
      color:#cbd5e1;
    }

    /* Right side */
    .login-right {
      flex:1;
      padding:50px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .login-right img {
      width:80px;
      height:80px;
      object-fit:cover;
      border-radius:50%;
      margin:0 auto 15px auto;
      display:block;
    }

    .login-right h3 {
      text-align:center;
      margin-bottom:25px;
      font-size:22px;
      color:#1f2937;
    }

    .form-floating-custom {
      position: relative;
      margin-bottom: 1.5rem;
      width: 100%;
    }
    .form-floating-custom input {
      width: 100%;
      padding: 14px 40px 14px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      background: #f9fafb;
      color: #111;
    }
    .form-floating-custom label {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 16px;
      color: #6b7280;
      pointer-events: none;
      transition: 0.2s ease all;
    }
    .form-floating-custom input:focus + label,
    .form-floating-custom input:not(:placeholder-shown) + label {
      top: 12px;
      font-size: 14px;
      color: #2563eb;
    }
    .form-floating-custom .input-icon {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
    }

    .btn-login {
      width:100%;
      padding:12px;
      background:#2563eb;
      color:#fff;
      font-size:16px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      transition:0.3s;
    }
    .btn-login:hover {
      background:#1e40af;
    }

    .error-input {
      border: 1.5px solid red !important;
    }
    .error-message {
      color: red;
      font-size: 0.85em;
      display: flex;
      align-items: center;
      margin-top: -8px;
      margin-bottom: 10px;
    }
    .error-message i {
      margin-right: 4px;
      font-size: 0.9em;
    }

    /* --- Responsive --- */
@media (max-width: 992px) {
  .container {
    width: 95%;
    flex-direction: column;
  }

  .login-left, .login-right {
    flex: none;
    width: 100%;
    padding: 30px 20px;
    text-align: center;
  }

  .login-left {
    border-radius: 12px 12px 0 0;
  }

  .login-right {
    border-radius: 0 0 12px 12px;
  }

  .login-left h2 {
    font-size: 22px;
  }

  .login-left ul li {
    font-size: 14px;
  }

  .login-right h3 {
    font-size: 18px;
  }

  .form-floating-custom input {
    font-size: 14px;
    padding: 12px 35px 12px 10px;
  }

  .btn-login {
    font-size: 14px;
    padding: 10px;
  }
}

/* Mobile cực nhỏ */
@media (max-width: 576px) {
  body {
    padding: 10px;
  }

  .login-left {
    display: none;
  }

  .container {
    width: 100%;
    box-shadow: none;
  }

  .login-right {
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .login-right img {
    width: 60px;
    height: 60px;
    margin-bottom: 10px;
  }

  .login-right h3 {
    font-size: 16px;
    margin-bottom: 15px;
  }

  .btn-login {
    font-size: 14px;
    padding: 8px;
  }
}

.form-floating-custom input {
  width: 100%;
  padding: 18px 40px 10px 12px; 
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
  background: #f9fafb;
  color: #111;
  line-height: 2.0; 
}

.form-floating-custom input:focus {
  outline: none;             
  border: 1pt solid #2563eb;  
  box-shadow: none;          
}



  </style>
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
      <img src="dist/img/logo.jpg" class="img-thumbnail p-0 border rounded-circle" id="system-logo">
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
