<?php
session_start();
// Kiểm tra quyền Admin (Mở comment khi chạy thật)
// if (!isset($_SESSION['user_id']) || $_SESSION['vai_tro'] !== 'admin') {
//     header("Location: ../login.php"); exit;
// }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; display: flex; margin: 0; height: 100vh; }
        .sidebar { width: 260px; background: #343a40; color: #fff; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; font-weight: bold; background: #212529; text-align: center; border-bottom: 1px solid #4b545c; }
        .sidebar-menu a { display: block; padding: 15px 20px; color: #c2c7d0; text-decoration: none; border-bottom: 1px solid #3d444b; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #007bff; color: #fff; }
        .sidebar-menu i { width: 25px; margin-right: 10px; }
        .main { flex: 1; padding: 20px; overflow-y: auto; }
        .card { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.05); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: left; font-size: 14px; }
        th { background: #f8f9fa; }
        .btn { padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; color: #fff; text-decoration: none; font-size: 13px; display: inline-block;}
        .btn-danger { background: #dc3545; }
        .btn-warning { background: #ffc107; color: #000; }
        .btn-success { background: #28a745; }
        .tab-btn { padding: 10px 20px; border: none; background: #e9ecef; cursor: pointer; font-weight: bold; margin-right: 5px; border-radius: 5px 5px 0 0; }
        .tab-btn.active { background: #007bff; color: white; }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header">ADMIN SYSTEM</div>
    <div class="sidebar-menu">
        <a href="quan_ly_nguoi_dung.php" class="<?= ($active=='users')?'active':'' ?>"><i class="fa-solid fa-users"></i> Quản lý Người dùng</a>
        <a href="quan_ly_khoa_hoc.php" class="<?= ($active=='courses')?'active':'' ?>"><i class="fa-solid fa-book"></i> Quản lý Khóa học</a>
        <a href="quan_ly_tuong_tac.php" class="<?= ($active=='comments')?'active':'' ?>"><i class="fa-solid fa-comments"></i> Đánh giá & Bình luận</a>
        <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
    </div>
</div>
<div class="main">
    <h2 style="margin-top:0; border-bottom: 2px solid #007bff; padding-bottom: 10px; display:inline-block;"><?= $title ?></h2>