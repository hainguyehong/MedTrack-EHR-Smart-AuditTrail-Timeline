<?php 
include './config/connection.php';

$message = '';

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
    $userName = $_POST['user_name'];
    $password = $_POST['password'];

    $encryptedPassword = md5($password);

    // UNION để kiểm tra cả 2 bảng
    $query = "
        SELECT id, display_name, user_name, profile_picture, role
        FROM users
        WHERE user_name = :uname AND password = :pwd

        UNION

        SELECT id, display_name, user_name, NULL AS profile_picture, 3 AS role
        FROM user_patients
        WHERE user_name = :uname AND password = :pwd
    ";

    try {
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
                // Nếu có role khác, đưa về trang chung
                header("location:index.php");
            }
            exit;
        } else {
            $message = 'Tài khoản hoặc mật khẩu không đúng.';
        }
    } catch (PDOException $ex) {
        echo $ex->getMessage();
        exit;
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - MedTrack-EHR-Smart-AuditTrail-Timeline
    </title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">

    <style>
    .login-box {
        width: 430px;
    }

    #system-logo {
        width: 5em !important;
        height: 5em !important;
        object-fit: cover;
        object-position: center center;
    }
    </style>
</head>

<body class="hold-transition login-page dark-mode">
    <div class="login-box">
        <div class="login-logo mb-4">
            <img src="dist/img/logo.jpg" class="img-thumbnail p-0 border rounded-circle" id="system-logo">
            <div class="text-center h2 mb-0">MedTrack-EHR-Smart-AuditTrail-Timeline</div>
        </div>
        <!-- /.login-logo -->
        <div class="card card-outline card-primary rounded-0 shadow">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Please enter your login credentials</p>
                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg rounded-0 autofocus"
                            placeholder="Username" id="user_name" name="user_name">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-control-lg rounded-0" placeholder="Password"
                            id="password" name="password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button name="login" type="submit" class="btn btn-primary rounded-0 btn-block">Sign
                                In</button>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-danger">
                                <?php 
              if($message != '') {
                echo $message;
              }
              ?>
                            </p>
                        </div>
                    </div>
                </form>


            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <!-- jQuery -->


</body>

<<<<<<< HEAD

</html>
?>

=======
</html>
>>>>>>> 5cef3ef8d91bb61c03dcd686ca6b13e88b8d38e0
