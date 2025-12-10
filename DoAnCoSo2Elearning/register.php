<?php
session_start();
include 'database.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // MẶC ĐỊNH LÀ STUDENT
    $role = 'student';

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email này đã được sử dụng!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (ho_ten, email, mat_khau, role) VALUES ('$ho_ten', '$email', '$hashed_password', '$role')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['success'] = "Đăng ký thành công! Hãy đăng nhập.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Lỗi hệ thống, vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký Học viên</title>
    <style>
        /* Giữ nguyên CSS cũ */
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f0f2f5; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #0056d2; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #003c96; }
        .error { color: red; font-size: 14px; text-align: center; }
        .link { text-align: center; margin-top: 15px; font-size: 14px; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Đăng Ký Học Viên</h2>
    <?php if($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="ho_ten" placeholder="Họ và tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <!-- Đã xóa phần chọn Role -->

        <button type="submit">Đăng ký</button>
    </form>
    <div class="link">Đã có tài khoản? <a href="login.php">Đăng nhập</a></div>
</div>
</body>
</html>