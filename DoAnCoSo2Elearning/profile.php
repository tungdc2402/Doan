<?php
session_start();
include 'database.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Xử lý hiển thị Avatar (Nếu chưa có thì dùng ảnh mặc định)
$avatar_url = !empty($user['avatar']) ? "uploads/avatars/" . $user['avatar'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f7f9fa; margin: 0; }
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; display: flex; gap: 30px; }

        /* Cột bên trái */
        .profile-sidebar { flex: 1; background: #fff; padding: 30px; border-radius: 8px; border: 1px solid #d1d7dc; text-align: center; height: fit-content; }
        .avatar-frame { width: 150px; height: 150px; margin: 0 auto 20px; border-radius: 50%; overflow: hidden; border: 5px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .avatar-frame img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { font-size: 24px; font-weight: bold; color: #2d2f31; margin-bottom: 5px; }
        .user-role { display: inline-block; background: #e6f2f5; color: #0056d2; padding: 5px 10px; border-radius: 20px; font-size: 14px; font-weight: bold; }

        /* Cột bên phải */
        .profile-content { flex: 2; background: #fff; padding: 40px; border-radius: 8px; border: 1px solid #d1d7dc; }
        .section-title { font-size: 18px; font-weight: bold; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }

        .info-group { margin-bottom: 20px; }
        .info-label { font-size: 13px; color: #6a6f73; font-weight: bold; margin-bottom: 5px; }
        .info-value { font-size: 16px; color: #2d2f31; }

        .btn-edit { display: inline-block; padding: 10px 20px; background: #2d2f31; color: #fff; text-decoration: none; font-weight: bold; border-radius: 4px; margin-top: 20px; transition: 0.3s; }
        .btn-edit:hover { background: #000; }
        .btn-back { color: #0056d2; text-decoration: none; display: block; margin-bottom: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="profile-sidebar">
        <div class="avatar-frame">
            <img src="<?php echo $avatar_url; ?>" alt="Avatar">
        </div>
        <div class="user-name"><?php echo htmlspecialchars($user['ho_ten']); ?></div>
        <div class="user-role">
            <?php echo ($user['role'] == 'admin') ? 'Quản trị viên' : (($user['role'] == 'lecturer') ? 'Giảng viên' : 'Học viên'); ?>
        </div>
        <p style="color:#666; font-size:14px; margin-top:15px;">Tham gia: <?php echo date('d/m/Y', strtotime($user['ngay_tao'])); ?></p>

        <a href="edit_profile.php" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa hồ sơ</a>
        <br><br>
        <a href="logout.php" style="color:red; text-decoration:none; font-size:14px;">Đăng xuất</a>
    </div>

    <!-- Nội dung chính -->
    <div class="profile-content">
        <a href="index.html" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay về trang chủ</a>

        <div class="section-title">Thông tin cơ bản</div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="info-group">
                <div class="info-label">Họ và tên</div>
                <div class="info-value"><?php echo htmlspecialchars($user['ho_ten']); ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Số điện thoại</div>
                <div class="info-value"><?php echo $user['so_dien_thoai'] ? htmlspecialchars($user['so_dien_thoai']) : '<span style="color:#999">Chưa cập nhật</span>'; ?></div>
            </div>
            <div class="info-group">
                <div class="info-label">Vai trò</div>
                <div class="info-value" style="text-transform: capitalize;"><?php echo $user['role']; ?></div>
            </div>
        </div>

        <div class="section-title" style="margin-top: 30px;">Giới thiệu bản thân</div>
        <div class="info-group">
            <div class="info-value" style="line-height: 1.6;">
                <?php echo $user['gioi_thieu'] ? nl2br(htmlspecialchars($user['gioi_thieu'])) : '<span style="color:#999">Chưa có thông tin giới thiệu.</span>'; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>