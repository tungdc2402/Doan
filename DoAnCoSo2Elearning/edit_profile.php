<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// --- XỬ LÝ CẬP NHẬT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $sdt = $_POST['so_dien_thoai'];
    $gioi_thieu = $_POST['gioi_thieu'];

    // 1. Cập nhật thông tin văn bản
    $sql_update = "UPDATE users SET ho_ten = ?, so_dien_thoai = ?, gioi_thieu = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssi", $ho_ten, $sdt, $gioi_thieu, $user_id);

    if($stmt->execute()) {
        $_SESSION['user_name'] = $ho_ten; // Cập nhật lại session tên hiển thị
        $message = "Cập nhật thông tin thành công!";
    }

    // 2. Xử lý Upload Avatar (Nếu có chọn file)
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] == 0) {
        $target_dir = "uploads/avatars/";
        // Tạo thư mục nếu chưa có
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $file_name = time() . "_" . basename($_FILES["avatar_file"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allow_types = array("jpg", "png", "jpeg", "gif");

        if(in_array($imageFileType, $allow_types)){
            if (move_uploaded_file($_FILES["avatar_file"]["tmp_name"], $target_file)) {
                // Cập nhật tên file vào database
                $sql_avatar = "UPDATE users SET avatar = '$file_name' WHERE id = $user_id";
                mysqli_query($conn, $sql_avatar);
                $message = "Cập nhật hồ sơ và ảnh đại diện thành công!";
            }
        } else {
            $message = "Chỉ cho phép file ảnh (JPG, PNG, GIF).";
        }
    }
}

// Lấy dữ liệu mới nhất để hiển thị vào Form
$sql = "SELECT * FROM users WHERE id = $user_id";
$user = mysqli_fetch_assoc(mysqli_query($conn, $sql));
$avatar_url = !empty($user['avatar']) ? "uploads/avatars/" . $user['avatar'] : "https://cdn-icons-png.flaticon.com/512/149/149071.png";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa hồ sơ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; display:flex; justify-content:center; align-items:center; min-height:100vh; }
        .edit-container { background: #fff; width: 600px; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2d2f31; margin-top: 0; }

        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #2d2f31; }
        input[type="text"], textarea { width: 100%; padding: 12px; border: 1px solid #2d2f31; border-radius: 4px; box-sizing: border-box; font-family: inherit; }
        textarea { resize: vertical; min-height: 100px; }

        /* Upload Avatar Style */
        .avatar-upload { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .current-avatar { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd; }

        .btn-save { background: #2d2f31; color: #fff; border: none; padding: 12px 20px; width: 100%; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 16px; }
        .btn-save:hover { background: #000; }

        .alert { padding: 15px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; text-align: center; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>

<div class="edit-container">
    <h2>Chỉnh sửa hồ sơ</h2>

    <?php if ($message): ?>
        <div class="alert"><i class="fa-solid fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <!-- Upload Avatar -->
        <div class="form-group">
            <label>Ảnh đại diện</label>
            <div class="avatar-upload">
                <img src="<?php echo $avatar_url; ?>" class="current-avatar">
                <div>
                    <input type="file" name="avatar_file" accept="image/*">
                    <div style="font-size:12px; color:#666; margin-top:5px;">Hỗ trợ: JPG, PNG, GIF. Tối đa 2MB.</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Họ và tên</label>
            <input type="text" name="ho_ten" value="<?php echo htmlspecialchars($user['ho_ten']); ?>" required>
        </div>

        <div class="form-group">
            <label>Email (Không thể thay đổi)</label>
            <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background:#eee; color:#666; border-color:#ddd;">
        </div>

        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="so_dien_thoai" value="<?php echo htmlspecialchars($user['so_dien_thoai'] ?? ''); ?>" placeholder="Nhập số điện thoại...">
        </div>

        <div class="form-group">
            <label>Giới thiệu bản thân</label>
            <textarea name="gioi_thieu" placeholder="Hãy viết gì đó về bạn..."><?php echo htmlspecialchars($user['gioi_thieu'] ?? ''); ?></textarea>
        </div>

        <button type="submit" class="btn-save">Lưu thay đổi</button>
        <a href="profile.php" class="btn-cancel">Hủy bỏ & Quay lại</a>
    </form>
</div>

</body>
</html>