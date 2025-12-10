<?php
include '../database.php';
$active = 'users';
$title = 'Quản lý Người dùng';

// Xử lý XÓA
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Xóa user (Trừ admin ra cho an toàn)
    mysqli_query($conn, "DELETE FROM users WHERE id=$id AND role != 'admin'");
    header("Location: quan_ly_nguoi_dung.php"); exit;
}

// Xử lý THÊM MỚI (Cấp tài khoản)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $mat_khau = password_hash($_POST['mat_khau'] ? $_POST['mat_khau'] : '123456', PASSWORD_DEFAULT);

    // Kiểm tra email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Email này đã tồn tại!');</script>";
    } else {
        $sql = "INSERT INTO users (ho_ten, email, mat_khau, role, ngay_tao) 
                VALUES ('$ho_ten', '$email', '$mat_khau', '$role', NOW())";
        mysqli_query($conn, $sql);
        header("Location: quan_ly_nguoi_dung.php"); exit;
    }
}

// Lọc danh sách (Mặc định ẩn Admin)
$filter_role = $_GET['role'] ?? 'all';
$where = "WHERE role != 'admin'"; // Mặc định: Chỉ lấy ai KHÔNG PHẢI admin

if ($filter_role == 'lecturer') $where = "WHERE role='lecturer'";
if ($filter_role == 'student') $where = "WHERE role='student'";

include 'admin_layout.php';
?>

<div class="card">
    <div style="margin-bottom: 15px; display: flex; justify-content: space-between;">
        <div>
            <a href="?role=all" class="btn" style="background:<?= $filter_role=='all'?'#007bff':'#6c757d' ?>">Tất cả</a>
            <a href="?role=lecturer" class="btn" style="background:<?= $filter_role=='lecturer'?'#007bff':'#6c757d' ?>">Giảng viên</a>
            <a href="?role=student" class="btn" style="background:<?= $filter_role=='student'?'#007bff':'#6c757d' ?>">Học viên</a>
        </div>
        <button class="btn btn-success" onclick="document.getElementById('modalAdd').style.display='block'">
            <i class="fa-solid fa-plus"></i> Cấp tài khoản
        </button>
    </div>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Truy vấn với điều kiện $where đã loại bỏ admin
        $res = mysqli_query($conn, "SELECT * FROM users $where ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td><b><?= htmlspecialchars($row['ho_ten']) ?></b></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                    <?php
                    if($row['role'] == 'lecturer' || $row['role'] == 'giang_vien')
                        echo '<span style="color:orange; font-weight:bold;">Giảng viên</span>';
                    else
                        echo '<span style="color:green; font-weight:bold;">Học viên</span>';
                    ?>
                </td>
                <td><?= $row['ngay_tao'] ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Xóa tài khoản này?')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div id="modalAdd" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div style="background:#fff; width:400px; margin: 100px auto; padding:20px; border-radius:5px;">
        <h3>Cấp tài khoản mới</h3>
        <form method="POST">
            <div style="margin-bottom:10px;">
                <label>Họ tên:</label>
                <input type="text" name="ho_ten" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:10px;">
                <label>Email:</label>
                <input type="email" name="email" required style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:10px;">
                <label>Mật khẩu:</label>
                <input type="password" name="mat_khau" placeholder="Mặc định: 123456" style="width:100%; padding:8px;">
            </div>
            <div style="margin-bottom:10px;">
                <label>Vai trò:</label>
                <select name="role" style="width:100%; padding:8px;">
                    <option value="student">Học viên</option>
                    <option value="lecturer">Giảng viên</option>
                </select>
            </div>
            <div style="text-align:right; margin-top:20px;">
                <button type="button" onclick="document.getElementById('modalAdd').style.display='none'" class="btn" style="background:#6c757d;">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

</div></body></html>