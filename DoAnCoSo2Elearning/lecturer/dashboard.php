<?php
include '../database.php';
$active_menu = 'dashboard';
$page_title = 'Tổng quan';
include 'lecturer_layout.php'; // Gọi giao diện chung

// Lấy số liệu thống kê (Giả định)
$count_khoa_hoc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM khoa_hoc"))['c'] ?? 0;
$count_bai_thi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bai_kiem_tra"))['c'] ?? 0;
?>

<div class="card">
    <h3>Thống kê nhanh</h3>
    <div style="display: flex; gap: 20px;">
        <div style="flex: 1; background: #e6f0ff; padding: 20px; border-radius: 8px; border-left: 5px solid #0056d2;">
            <h4>Tổng số khóa học</h4>
            <h2 style="color: #0056d2; margin: 0;"><?php echo $count_khoa_hoc; ?></h2>
        </div>
        <div style="flex: 1; background: #fff0f5; padding: 20px; border-radius: 8px; border-left: 5px solid #a435f0;">
            <h4>Ngân hàng đề thi</h4>
            <h2 style="color: #a435f0; margin: 0;"><?php echo $count_bai_thi; ?></h2>
        </div>
        <div style="flex: 1; background: #e8f5e9; padding: 20px; border-radius: 8px; border-left: 5px solid #28a745;">
            <h4>Doanh thu tháng này</h4>
            <h2 style="color: #28a745; margin: 0;">399,200 đ</h2>
        </div>
    </div>
</div>

<div class="card">
    <h3>Hoạt động gần đây</h3>
    <p>Chưa có hoạt động nào...</p>
</div>

</div> </body>
</html>