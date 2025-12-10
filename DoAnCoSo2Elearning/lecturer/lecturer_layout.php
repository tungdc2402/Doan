<?php
// Kiểm tra đăng nhập giảng viên ở đây nếu cần
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Giảng viên</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; background: #f0f2f5; display: flex; height: 100vh; }

        /* Sidebar */
        .sidebar { width: 250px; background: #2d2f31; color: #fff; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; font-size: 20px; font-weight: bold; border-bottom: 1px solid #3e4143; background: #1c1d1f; }
        .sidebar-menu { flex: 1; padding-top: 20px; }
        .sidebar-menu a { display: block; padding: 15px 20px; color: #a1a7b3; text-decoration: none; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #3e4143; color: #fff; border-left: 4px solid #a435f0; } /* Màu tím Udemy */
        .sidebar-menu i { margin-right: 10px; width: 20px; text-align: center; }

        /* Main Content */
        .main-content { flex: 1; overflow-y: auto; padding: 30px; }
        .top-bar { display: flex; justify-content: space-between; margin-bottom: 30px; align-items: center; }
        .page-title { font-size: 24px; font-weight: bold; color: #2d2f31; }

        /* User Profile in Top bar */
        .user-info { display: flex; align-items: center; gap: 10px; }
        .avatar { width: 40px; height: 40px; background: #a435f0; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }

        /* Common Table & Button Styles (Thừa kế từ file cũ của bạn) */
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .btn { padding: 8px 16px; border: none; cursor: pointer; border-radius: 4px; color: #fff; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 500;}
        .btn-primary { background: #a435f0; } /* Tím Udemy */
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }

        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f7f9fa; color: #2d2f31; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header"><i class="fa-solid fa-graduation-cap"></i> Giảng Viên</div>
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?php echo ($active_menu == 'dashboard') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i> Tổng quan
        </a>
        <a href="quan_ly_khoa_hoc.php" class="<?php echo ($active_menu == 'khoa_hoc') ? 'active' : ''; ?>">
            <i class="fa-solid fa-video"></i> Quản lý Khóa học
        </a>
        <a href="bai_kiem_tra.php" class="<?php echo ($active_menu == 'bai_kiem_tra') ? 'active' : ''; ?>">
            <i class="fa-solid fa-list-check"></i> Ngân hàng Đề thi
        </a>
        <a href="quan_ly_hoc_vien.php" class="<?php echo ($active_menu == 'hoc_vien') ? 'active' : ''; ?>">
            <i class="fa-solid fa-users"></i> Học viên
        </a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <div class="page-title"><?php echo $page_title ?? 'Trang quản trị'; ?></div>
        <div class="user-info">
            <span>Xin chào, <b>Đặng Công Tùng</b></span>
            <div class="avatar">T</div>
        </div>
    </div>
