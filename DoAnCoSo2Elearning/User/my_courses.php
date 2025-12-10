<?php
session_start();
include '../database.php';

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Lấy danh sách khóa học đã mua
// JOIN bảng khoa_hoc_da_mua với bảng khoa_hoc để lấy thông tin chi tiết
$sql = "SELECT k.*, d.ngay_mua 
        FROM khoa_hoc k 
        JOIN khoa_hoc_da_mua d ON k.id = d.khoa_hoc_id 
        WHERE d.user_id = $user_id 
        ORDER BY d.ngay_mua DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khóa học của tôi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css"> <!-- Sử dụng lại CSS trang chủ -->
    <style>
        body { background-color: #f7f9fa; }
        .my-course-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .page-title { font-size: 28px; font-weight: 700; color: #2d2f31; margin-bottom: 30px; }

        /* Grid Layout */
        .course-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 cột */
            gap: 20px;
        }

        /* Card Khóa học */
        .my-course-card {
            background: #fff; border: 1px solid #d1d7dc; border-radius: 8px;
            overflow: hidden; transition: 0.3s; display: flex; flex-direction: column;
        }
        .my-course-card:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        .card-img img { width: 100%; height: 160px; object-fit: cover; }

        .card-body { padding: 15px; flex: 1; display: flex; flex-direction: column; }
        .course-title { font-size: 16px; font-weight: bold; color: #2d2f31; margin-bottom: 10px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .instructor { font-size: 12px; color: #6a6f73; margin-bottom: 15px; }

        /* Thanh tiến độ (Giả lập) */
        .progress-wrapper { margin-top: auto; }
        .progress-bar-bg { width: 100%; height: 4px; background: #d1d7dc; border-radius: 2px; margin-bottom: 10px; }
        .progress-fill { width: 0%; height: 100%; background: #0056d2; border-radius: 2px; } /* Mới mua nên 0% */

        .btn-start {
            display: block; width: 100%; text-align: center;
            background: #2d2f31; color: white; padding: 10px 0;
            text-decoration: none; font-weight: bold; font-size: 14px;
            border-radius: 4px; transition: 0.2s;
        }
        .btn-start:hover { background: #000; }

        /* Responsive */
        @media (max-width: 992px) { .course-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .course-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .course-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<!-- Include Header (Sử dụng lại header bạn đã có) -->
<?php include 'layout/header.php'; ?>

<div class="my-course-container">
    <h1 class="page-title">Khóa học của tôi</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="course-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="my-course-card">
                    <div class="card-img">
                        <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Course">
                    </div>
                    <div class="card-body">
                        <div class="course-title"><?php echo htmlspecialchars($row['ten_khoa_hoc']); ?></div>
                        <div class="instructor"><?php echo htmlspecialchars($row['giang_vien']); ?></div>

                        <div class="progress-wrapper">
                            <div style="font-size: 12px; margin-bottom: 5px;">Tiến độ: 0%</div>
                            <div class="progress-bar-bg">
                                <div class="progress-fill" style="width: 0%"></div>
                            </div>

                            <!-- Link tới trang xem video/bài giảng -->
                            <!-- Giả sử vào học sẽ mở bài đầu tiên, hoặc trang chi tiết bài giảng -->
                            <a href="../watch.php?course_id=<?php echo $row['id']; ?>" class="btn-start">Vào học ngay</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 50px;">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="100" style="opacity: 0.5;">
            <p style="margin-top: 20px; font-size: 18px; color: #666;">Bạn chưa đăng ký khóa học nào.</p>
            <a href="home.php" style="color: #0056d2; font-weight: bold;">Khám phá các khóa học ngay</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>