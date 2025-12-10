<?php
include '../database.php';
$sql = "SELECT * FROM khoa_hoc";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPro - Nền tảng học trực tuyến hàng đầu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css">

</head>
<body>
<?php
include 'layout/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <h1>Khơi dậy tiềm năng của bạn</h1>
        <p>Học từ những chuyên gia hàng đầu với hàng nghìn khóa học đa dạng.</p>
        <div class="search-box">
            <input type="text" placeholder="Bạn muốn học gì hôm nay?">
            <button type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
        </div>
    </div>
</section>

<!-- PHẦN 3: DANH MỤC KHÓA HỌC (CHỦ ĐỀ MÔN HỌC) -->
<section class="categories container">
    <h2 class="section-title">Môn học phổ biến</h2>
    <div class="category-grid">
        <!-- Môn Toán -->
        <div class="cat-item">
            <i class="fas fa-calculator"></i>
            <h3>Toán Học</h3>
        </div>
        <!-- Môn Vật Lý -->
        <div class="cat-item">
            <i class="fas fa-atom"></i>
            <h3>Vật Lý</h3>
        </div>
        <!-- Môn Hóa Học -->
        <div class="cat-item">
            <i class="fas fa-flask"></i>
            <h3>Hóa Học</h3>
        </div>
        <!-- Môn Sinh Học -->
        <div class="cat-item">
            <i class="fas fa-dna"></i>
            <h3>Sinh Học</h3>
        </div>
        <!-- Môn Ngữ Văn -->
        <div class="cat-item">
            <i class="fas fa-book-open"></i>
            <h3>Ngữ Văn</h3>
        </div>
        <!-- Môn Tiếng Anh -->
        <div class="cat-item">
            <i class="fas fa-language"></i>
            <h3>Tiếng Anh</h3>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container">
        <h2 class="section-title">Khóa học nổi bật</h2>
        <div class="course-grid">
            <?php while ($row = mysqli_fetch_assoc($result)) :
                $gia_giam = $row['gia'] * (100 - $row['giam_gia']) / 100;
            ?>
                <a href="detail_course.php?id=<?php echo $row['id']; ?>" class="course-card">
                    <div class="course-img-wrapper">
                        <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Course" class="course-img">
                    </div>
                    <div class="course-title"><?php echo htmlspecialchars($row['ten_khoa_hoc']); ?></div>
                    <div class="course-author"><?php echo htmlspecialchars($row['giang_vien']); ?></div>
                    <div class="course-rating">
                        <span>4.9</span>
                        <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        <span class="reviews">(1,234)</span>
                    </div>
                    <div class="course-price">
                        <span><?php echo number_format($gia_giam); ?>$</span>
                        <span class="old-price"><?php echo number_format($row['gia']); ?>$</span>
                    </div>
                    <span class="badge">Bestseller</span>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</div>


<!-- PHẦN 5: FOOTER -->
<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-col">
            <h3>EduPro</h3>
            <p>Nền tảng học tập trực tuyến hàng đầu, kết nối tri thức mọi lúc mọi nơi.</p>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h3>Liên kết</h3>
            <ul>
                <li><a href="#">Về chúng tôi</a></li>
                <li><a href="#">Khóa học</a></li>
                <li><a href="#">Giảng viên</a></li>
                <li><a href="#">Chính sách bảo mật</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Hỗ trợ</h3>
            <ul>
                <li><a href="#">Câu hỏi thường gặp</a></li>
                <li><a href="#">Điều khoản dịch vụ</a></li>
                <li><a href="#">Hỗ trợ kỹ thuật</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h3>Liên hệ</h3>
            <ul>
                <li><i class="fas fa-map-marker-alt"></i> Hà Nội, Việt Nam</li>
                <li><i class="fas fa-envelope"></i> contact@edupro.com</li>
                <li><i class="fas fa-phone"></i> 1900 1234</li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; 2023 EduPro. All rights reserved.</p>
    </div>
</footer>

</body>
</html>