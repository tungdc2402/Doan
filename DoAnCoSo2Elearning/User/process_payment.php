<?php
session_start();
include '../database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_pay'])) {
    $user_id = $_SESSION['user_id'];
    $tong_tien = $_POST['total_amount'];

    // 1. Lưu đơn hàng
    $sql = "INSERT INTO don_hang (user_id, tong_tien) VALUES ($user_id, $tong_tien)";

    if (mysqli_query($conn, $sql)) {

        // --- ĐOẠN MỚI THÊM: LƯU KHÓA HỌC VÀO BẢNG 'khoa_hoc_da_mua' ---
        foreach ($_SESSION['cart'] as $course_id => $val) {
            // Kiểm tra xem đã mua chưa để tránh trùng lặp
            $check = mysqli_query($conn, "SELECT * FROM khoa_hoc_da_mua WHERE user_id=$user_id AND khoa_hoc_id=$course_id");
            if (mysqli_num_rows($check) == 0) {
                mysqli_query($conn, "INSERT INTO khoa_hoc_da_mua (user_id, khoa_hoc_id) VALUES ($user_id, $course_id)");
            }
        }
        // ---------------------------------------------------------------

        // 2. Xóa giỏ hàng
        unset($_SESSION['cart']);

        // 3. Thông báo
        echo "<script>alert('Thanh toán thành công! Bạn có thể vào học ngay.'); window.location.href='my_courses.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}
?>