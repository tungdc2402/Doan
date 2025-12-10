<?php
session_start();
include 'database.php';

// Bảo mật: Phải xác thực OTP rồi mới được vào đây
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: login.php");
    exit;
}

$msg = "";

if (isset($_POST['btn_doipass'])) {
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];

    if ($pass1 !== $pass2) {
        $msg = "Mật khẩu nhập lại không khớp!";
    } else {
        $email = $_SESSION['reset_email'];
        // Mã hóa mật khẩu mới
        $hashed_pass = password_hash($pass1, PASSWORD_DEFAULT);

        // Cập nhật Database và Xóa OTP cũ
        $sql = "UPDATE users SET mat_khau='$hashed_pass', reset_token=NULL, reset_expiry=NULL WHERE email='$email'";
        if (mysqli_query($conn, $sql)) {
            // Xóa session
            session_destroy();
            echo "<script>alert('Đổi mật khẩu thành công! Vui lòng đăng nhập lại.'); window.location.href='login.php';</script>";
        } else {
            $msg = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .box { background: white; padding: 30px; border-radius: 8px; width: 350px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0056d2; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Tạo mật khẩu mới</h3>
    <?php if($msg) echo "<div style='color:red; margin-bottom:10px;'>$msg</div>"; ?>

    <form method="POST">
        <input type="password" name="pass1" placeholder="Mật khẩu mới" required>
        <input type="password" name="pass2" placeholder="Nhập lại mật khẩu" required>
        <button type="submit" name="btn_doipass">Đổi mật khẩu</button>
    </form>
</div>
</body>
</html>