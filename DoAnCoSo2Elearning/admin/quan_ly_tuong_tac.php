<?php
include '../database.php';
$active = 'comments';
$title = 'Quản lý Đánh giá & Bình luận';

// Xác định Tab đang chọn (Mặc định là 'review' - Đánh giá)
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'review';

// Xử lý XÓA
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $table = ($_GET['type'] == 'review') ? 'danh_gia' : 'binh_luan';
    mysqli_query($conn, "DELETE FROM $table WHERE id=$id");
    header("Location: quan_ly_tuong_tac.php?tab=" . $_GET['type']);
    exit;
}

include 'admin_layout.php';
?>

<div class="card">
    <div style="border-bottom: 2px solid #ddd; margin-bottom: 15px;">
        <a href="?tab=review" class="tab-btn <?= $tab=='review'?'active':'' ?>">
            <i class="fa-solid fa-star"></i> Đánh giá (Review)
        </a>
        <a href="?tab=comment" class="tab-btn <?= $tab=='comment'?'active':'' ?>">
            <i class="fa-solid fa-comments"></i> Bình luận (Comment)
        </a>
    </div>

    <?php if ($tab == 'review'): ?>
        <table>
            <thead>
                <tr>
                    <th>Khóa học</th>
                    <th>Người dùng</th>
                    <th>Sao</th>
                    <th>Nội dung</th>
                    <th>Ngày tạo</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Join với bảng khoa_hoc để lấy tên khóa học
                $sql = "SELECT dg.*, kh.ten_khoa_hoc 
                        FROM danh_gia dg 
                        LEFT JOIN khoa_hoc kh ON dg.khoa_hoc_id = kh.id 
                        ORDER BY dg.ngay_tao DESC";
                $res = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($res)):
                ?>
                <tr>
                    <td><a href="#" style="color:#007bff; text-decoration:none;"><?= htmlspecialchars($row['ten_khoa_hoc']) ?></a></td>
                    <td>
                        <b><?= htmlspecialchars($row['ten_user']) ?></b><br>
                        <span style="font-size:11px; background:#eee; padding:2px; border-radius:3px;"><?= $row['avatar_text'] ?></span>
                    </td>
                    <td style="color:#ffc107;">
                        <?php for($i=0; $i<$row['so_sao']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                    </td>
                    <td><?= htmlspecialchars($row['noi_dung']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_tao'])) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>&type=review" class="btn btn-danger" onclick="return confirm('Xóa đánh giá này?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Khóa học</th>
                    <th>Người dùng</th>
                    <th>Nội dung bình luận</th>
                    <th>Ngày tạo</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Join với bảng khoa_hoc
                $sql = "SELECT bl.*, kh.ten_khoa_hoc 
                        FROM binh_luan bl 
                        LEFT JOIN khoa_hoc kh ON bl.khoa_hoc_id = kh.id 
                        ORDER BY bl.ngay_tao DESC";
                $res = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($res)):
                ?>
                <tr>
                    <td><a href="#" style="color:#007bff; text-decoration:none;"><?= htmlspecialchars($row['ten_khoa_hoc']) ?></a></td>
                    <td>
                        <b><?= htmlspecialchars($row['ten_user']) ?></b>
                    </td>
                    <td><?= htmlspecialchars($row['noi_dung']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_tao'])) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>&type=comment" class="btn btn-danger" onclick="return confirm('Xóa bình luận này?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</div></body></html>