<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="assets/images/img-tn.png">
    <link rel="apple-touch-icon" href="assets/images/img-tn.png">
    <title>403 - Không Có Quyền Truy Cập</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px;
    }

    .container {
        background: white;
        padding: 60px 40px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        text-align: center;
        max-width: 500px;
        width: 100%;
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .lock-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 50px;
        color: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    h1 {
        font-size: 48px;
        color: #333;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .error-code {
        font-size: 24px;
        color: #667eea;
        margin-bottom: 20px;
        font-weight: 600;
    }

    p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .button {
        display: inline-block;
        margin-top: 30px;
        padding: 15px 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.6);
    }

    .info-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
        border-left: 4px solid #667eea;
    }

    .info-box p {
        color: #555;
        font-size: 14px;
        margin: 0;
        text-align: left;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="lock-icon">🔒</div>
        <h1>Truy Cập Bị Từ Chối</h1>
        <p class="error-code">Mã Lỗi: 403 Forbidden</p>
        <p>Rất tiếc, bạn không có quyền truy cập vào trang này.</p>
        <p>Trang web này yêu cầu xác thực hoặc quyền truy cập đặc biệt.</p>

        <div class="info-box">
            <p><strong>Có thể do các lý do sau:</strong></p>
            <p>• Bạn chưa đăng nhập vào hệ thống</p>
            <p>• Tài khoản của bạn không có đủ quyền hạn</p>
            <p>• Trang này chỉ dành cho quản trị viên</p>
            <p>• Địa chỉ IP của bạn bị chặn</p>
        </div>

        <a href="index.php" class="button">Quay Lại Trang Trước</a>
    </div>
</body>

</html>