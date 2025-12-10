<?php
include '../database.php';
$active = 'courses';
$title = 'Quản lý Khóa học';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM khoa_hoc WHERE id=$id");
    header("Location: quan_ly_khoa_hoc.php"); exit;
}

include 'admin_layout.php';
?>

<div class="card">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Tên khóa học</th>
            <th>Giảng viên</th>
            <th>Giá & Giảm giá</th>
            <th>Thống kê</th>
            <th>Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM khoa_hoc ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)):
            ?>
            <tr>
                <td>#<?= $row['id'] ?></td>
                <td>
                    <div style="font-weight:bold; color:#0056d2;"><?= htmlspecialchars($row['ten_khoa_hoc']) ?></div>
                    <small style="color:#666;">Danh mục: <?= $row['danh_muc'] ?></small>
                </td>
                <td><?= htmlspecialchars($row['giang_vien']) ?></td>
                <td>
                    <?= number_format($row['gia'], 0, ',', '.') ?> đ<br>
                    <?php if($row['giam_gia'] > 0) echo '<small style="color:red;">-'.$row['giam_gia'].'%</small>'; ?>
                </td>
                <td>
                    <i class="fa-solid fa-users"></i> <?= $row['so_luong_hoc_vien'] ?><br>
                    <i class="fa-solid fa-star" style="color:#ffc107;"></i> <?= $row['danh_gia_tb'] ?>
                </td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger" onclick="return confirm('Xóa khóa học này?')"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</div></body></html>