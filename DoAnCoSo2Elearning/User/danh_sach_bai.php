<?php
include '../database.php';

// Lấy danh sách bài kiểm tra từ CSDL
$sql = "SELECT * FROM bai_kiem_tra ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

// Hàm format ngày tháng giống trong ảnh (Tiếng Anh)
function formatLMSDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('l, d F Y, h:i A');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách bài tập - Group7</title>
    <!-- FontAwesome cho icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/home.css">
    <style>

        /* CSS Reset & Font */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: #0f6cb6; /* Màu xanh tiêu đề Moodle */
            font-weight: 300;
            font-size: 2rem;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 10px;
        }

        /* Khối bài tập */
        .activity-item {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 20px;
        }

        /* Icon bên trái */
        .activity-icon {
            width: 40px;
            text-align: center;
            padding-top: 5px;
        }
        .activity-icon i {
            font-size: 32px;
            color: #999; /* Màu xám của icon */
        }
        /* Giả lập icon tờ giấy có bàn tay cầm (kết hợp) hoặc dùng icon đơn giản */
        .custom-icon {
            width: 35px;
            height: 35px;
            background-image: url('https://cdn-icons-png.flaticon.com/512/2921/2921226.png'); /* Hoặc dùng link ảnh icon Moodle thật nếu có */
            background-size: cover;
            display: inline-block;
            opacity: 0.7;
        }

        /* Nội dung bên phải */
        .activity-content {
            flex: 1;
        }

        /* Tiêu đề bài tập */
        .activity-title {
            font-size: 16px;
            font-weight: bold;
            color: #0f6cb6;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 8px;
        }
        .activity-title:hover {
            text-decoration: underline;
            color: #0a4b7e;
        }

        /* Ngày tháng */
        .activity-dates {
            font-size: 13px;
            color: #444;
            margin-bottom: 8px;
            line-height: 1.5;
        }
        .date-label {
            font-weight: bold;
        }

        /* Nút Done */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border: 1px solid #1f7e34; /* Viền xanh lá */
            border-radius: 4px;
            color: #1f7e34;
            font-size: 12px;
            font-weight: bold;
            background-color: #fff;
            margin-bottom: 15px;
            width: fit-content;
        }
        .status-badge i {
            font-size: 14px;
        }

        /* Badge chưa làm */
        .status-badge.todo {
            border-color: #666;
            color: #666;
            background: #f0f0f0;
        }

        /* Mô tả */
        .activity-desc {
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin-left: 0;
        }

    </style>
</head>
<body><div class="a"><?php include 'layout/header.php'; ?></div>
<div class="container">
    <h1>Group7</h1>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="activity-item">
            <!-- Icon -->
            <div class="activity-icon">
                <!-- Dùng icon custom hoặc FontAwesome -->
                <!-- Moodle thường dùng icon Assignment màu xanh/hồng, ở đây dùng FontAwesome tương tự -->
                <i class="fa-solid fa-file-signature" style="color: #6a7c92;"></i>
            </div>

            <div class="activity-content">
                <!-- Tiêu đề có link truyền ID -->
                <a href="lam_bai.php?id=<?php echo $row['id']; ?>" class="activity-title">
                    <i class="fa-solid fa-hand-holding" style="margin-right:5px; color:#ffa500; display:none;"></i> <!-- Nếu muốn icon bàn tay -->
                    <?php echo htmlspecialchars($row['tieu_de']); ?>
                </a>

                <!-- Ngày tháng -->
                <div class="activity-dates">
                    <div>
                        <span class="date-label">Opened:</span>
                        <?php echo formatLMSDate($row['ngay_mo']); ?>
                    </div>
                    <div>
                        <span class="date-label">Due:</span>
                        <?php echo formatLMSDate($row['han_nop']); ?>
                    </div>
                </div>

                <!-- Trạng thái Done/To do -->
                <?php if ($row['trang_thai'] == 1): ?>
                    <div class="status-badge">
                        <i class="fa-solid fa-check"></i> Done
                    </div>
                <?php else: ?>
                    <div class="status-badge todo">
                        <i class="fa-regular fa-square"></i> To do
                    </div>
                <?php endif; ?>

                <!-- Mô tả -->
                <div class="activity-desc">
                    <?php echo $row['mo_ta']; // Cho phép in HTML như <br> ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>