<?php
session_start();
include 'database.php';

// Nếu chưa nhập email ở bước trước thì đá về
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$msg = "";
$email = $_SESSION['reset_email'];

if (isset($_POST['btn_verify'])) {
    $otp_input = $_POST['otp'];

    // Kiểm tra OTP và Thời gian hết hạn
    $sql = "SELECT * FROM users WHERE email='$email' AND reset_token='$otp_input'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $hien_tai = date("Y-m-d H:i:s");

        if ($hien_tai <= $user['reset_expiry']) {
            // OTP Đúng và còn hạn -> Chuyển sang trang đổi pass
            $_SESSION['otp_verified'] = true; // Đánh dấu đã xác thực
            header("Location: reset_password.php");
            exit;
        } else {
            $msg = "Mã OTP đã hết hạn!";
        }
    } else {
        $msg = "Mã OTP không chính xác!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập mã xác thực</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .box { background: white; padding: 30px; border-radius: 8px; width: 350px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; text-align: center; letter-spacing: 5px; font-size: 20px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="box">
    <h3>Nhập mã OTP</h3>
    <p style="font-size:14px;">Mã đã được gửi tới: <b><?php echo $email; ?></b></p>

    <?php if($msg) echo "<div style='color:red; margin-bottom:10px;'>$msg</div>"; ?>

    <form method="POST">
        <input type="text" name="otp" placeholder="6 số OTP" required maxlength="6">
        <button type="submit" name="btn_verify">Xác nhận</button>
    </form>
</div>
</body>
</html>