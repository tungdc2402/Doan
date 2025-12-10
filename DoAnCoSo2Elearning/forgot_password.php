<?php
session_start();
include 'database.php';
include 'send_mail.php'; // Nhúng file gửi mail

$msg = "";

if (isset($_POST['btn_gui_otp'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Kiểm tra email có tồn tại không
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        // Tạo OTP 6 số ngẫu nhiên
        $otp = rand(100000, 999999);
        // Hết hạn sau 15 phút
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        // Lưu vào DB
        mysqli_query($conn, "UPDATE users SET reset_token='$otp', reset_expiry='$expiry' WHERE email='$email'");

        // Gửi Email
        if (sendOTP($email, $otp)) {
            $_SESSION['reset_email'] = $email; // Lưu email để qua trang sau dùng
            header("Location: verify_otp.php");
            exit;
        } else {
            $msg = "Lỗi gửi email. Vui lòng thử lại!";
        }
    } else {
        $msg = "Email này không tồn tại trong hệ thống!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0056d2; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="box">
    <h2>Quên mật khẩu?</h2>
    <p style="font-size:14px; color:#666;">Nhập email của bạn để nhận mã xác thực.</p>

    <?php if($msg) echo "<div class='error'>$msg</div>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Nhập email của bạn" required>
        <button type="submit" name="btn_gui_otp">Gửi mã xác thực</button>
    </form>
    <div style="margin-top:15px;"><a href="login.php" style="text-decoration:none; font-size:14px;">Quay lại đăng nhập</a></div>
</div>
</body>
</html>