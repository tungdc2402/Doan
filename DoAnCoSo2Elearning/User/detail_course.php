<?php
session_start();
include '../database.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_submit_review'])) {
    if ($user_id == 0) {
        echo "<script>alert('Vui lòng đăng nhập để gửi đánh giá!'); window.location.href='../login.php';</script>";
        exit;
    }
    $so_sao = isset($_POST['rating_value']) ? (int)$_POST['rating_value'] : 5;
    $noi_dung = $_POST['cam_nhan'];
    $ho_ten = $_SESSION['user_name'];

    $hinh_anh = NULL;
    if (isset($_FILES['review_img']) && $_FILES['review_img']['error'] == 0) {
        //tạo thư mục nếu chưa tồn tại
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["review_img"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allow_types = array("jpg", "png", "jpeg", "gif");

        if(in_array($imageFileType, $allow_types)){
            if (move_uploaded_file($_FILES["review_img"]["tmp_name"], $target_file)) {
                $hinh_anh = $file_name;
            }
        }
    }

    // Tạo avatar
    $words = explode(" ", $ho_ten);
    $last_word = end($words);
    $avatar_text = strtoupper(substr($ho_ten, 0, 1) . substr($last_word, 0, 1));

    if (!empty($noi_dung)) {
        $stmt = $conn->prepare("INSERT INTO danh_gia (khoa_hoc_id, ten_user, avatar_text, so_sao, noi_dung, hinh_anh) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississ", $id, $ho_ten, $avatar_text, $so_sao, $noi_dung, $hinh_anh);
        $stmt->execute();
        header("Location: ?id=$id");
        exit;
    }
}


//bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_submit_qa'])) {

    // --- KIỂM TRA ĐĂNG NHẬP ---
    if ($user_id == 0) {
        echo "<script>alert('Vui lòng đăng nhập để bình luận!'); window.location.href='../login.php';</script>";
        exit;
    }
    // --------------------------

    $noi_dung_qa = $_POST['noidung_qa'];
    $ten_user_qa = $_SESSION['user_name'];
    $avatar_qa = strtoupper(substr($ten_user_qa, 0, 2));

    if (!empty($noi_dung_qa)) {
        $stmt = $conn->prepare("INSERT INTO binh_luan (khoa_hoc_id, ten_user, avatar_text, noi_dung) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $ten_user_qa, $avatar_qa, $noi_dung_qa);
        $stmt->execute();
        header("Location: ?id=$id");
        exit;
    }
}

// =================================================================
// 3. LẤY DỮ LIỆU HIỂN THỊ
// =================================================================
$result = mysqli_query($conn, "SELECT * FROM khoa_hoc WHERE id = $id");
$result2 = mysqli_query($conn, "SELECT * FROM noi_dung_hoc_duoc WHERE khoa_hoc_id = $id");
$result3 = mysqli_query($conn, "SELECT * FROM chuong_hoc WHERE khoa_hoc_id = $id ORDER BY thu_tu ASC");
$result_bl = mysqli_query($conn, "SELECT * FROM binh_luan WHERE khoa_hoc_id = $id ORDER BY ngay_tao DESC");
$result_dg = mysqli_query($conn, "SELECT * FROM danh_gia WHERE khoa_hoc_id = $id ORDER BY ngay_tao DESC");

$reviews = [];
$star_counts = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0];
while ($r = mysqli_fetch_assoc($result_dg)) {
    $reviews[] = $r;
    $star_counts[$r['so_sao']]++;
}
$total_reviews = count($reviews);
$avg_rating = 0;
if ($total_reviews > 0) {
    $sum = 0;
    foreach ($star_counts as $star => $count) $sum += $star * $count;
    $avg_rating = round($sum / $total_reviews, 1);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết khóa học</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Giữ nguyên như cũ */
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 14px; flex-shrink: 0; }
        .qa-section { margin-top: 40px; border-top: 1px solid #d1d7dc; padding-top: 20px; }
        .qa-form { display: flex; gap: 15px; margin-bottom: 30px; }
        .qa-input-wrap { flex: 1; }
        .qa-textarea { width: 100%; padding: 10px; border: 1px solid #2d2f31; border-radius: 4px; min-height: 80px; font-family: inherit; }
        .btn-qa-submit { margin-top: 10px; background: #2d2f31; color: #fff; border: none; padding: 10px 20px; font-weight: bold; cursor: pointer; border-radius: 4px; }
        .qa-item { display: flex; gap: 15px; margin-bottom: 20px; }
        .qa-name { font-weight: bold; font-size: 15px; color: #2d2f31; }
        .qa-text { margin-top: 5px; color: #2d2f31; line-height: 1.4; }

        .review-section { margin-top: 50px; }
        .review-header { font-size: 24px; font-weight: bold; margin-bottom: 20px; color: #00235a; }
        .rating-dashboard { display: flex; align-items: center; gap: 30px; background: #f7f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .big-score { font-size: 64px; font-weight: 700; color: #b4690e; line-height: 1; }
        .stars-gold { color: #e59819; font-size: 18px; margin-top: 5px; }
        .progress-bg { flex: 1; height: 8px; background: #d1d7dc; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; background: #00b0ff; }
        .bar-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; color: #0056d2; font-size: 14px; }

        .btn-open-review { background-color: #00235a; color: #fff; padding: 10px 30px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block;}
        .form-review-container { background-color: #f7f9fa; padding: 20px; border-radius: 8px; margin-top: 20px; border: 1px solid #d1d7dc; display: none; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; font-family: inherit; margin-top: 5px; }
        .star-rating-input { font-size: 24px; color: #e59819; cursor: pointer; margin-top: 5px; }

        .btn-submit-review { background-color: #00235a; color: #fff; padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .btn-camera { background-color: #00b0ff; color: #fff; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }

        .review-item { border-top: 1px solid #eee; padding: 20px 0; display:flex; gap:15px; }
        .review-content { flex: 1; }
        .review-date { font-size:12px; color:#666; margin-top:5px; }
        .review-image { margin-top: 10px; max-width: 200px; max-height: 200px; border-radius: 4px; border: 1px solid #ddd; display: block; cursor: pointer;}
        .file-name-display { font-size: 13px; color: #0056d2; margin-left: 10px; font-style: italic; }
    </style>
</head>
<body>

<header>
    <a href="../../index.html" class="logo">Udem<span>y</span></a>
    <div class="nav-actions">
        <?php if ($user_id > 0): ?>
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="profile.php" style="text-decoration:none; font-weight:bold; color:#000;">
                    <i class="fa-solid fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                </a>
                <a href="logout.php" class="btn btn-white">Đăng xuất</a>
            </div>
        <?php else: ?>
            <a href="../login.php" class="btn btn-white" style="text-decoration:none;">Đăng nhập</a>
            <a href="register.php" class="btn btn-black" style="text-decoration:none;">Đăng ký</a>
        <?php endif; ?>
    </div>
</header>

<div class="main-content" style="margin-top:72px;">

    <?php while ($row = mysqli_fetch_assoc($result)) :
        $gia_giam = $row['gia'] * (100 - $row['giam_gia']) / 100;
        ?>
        <section class="course-hero">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title"><?php echo htmlspecialchars($row['ten_khoa_hoc']); ?></h1>
                    <p class="hero-desc"><?php echo htmlspecialchars($row['mo_ta']); ?></p>
                    <div class="hero-stats">
                        <span>Được tạo bởi <?php echo htmlspecialchars($row['giang_vien']); ?></span>
                        <span style="margin-left:10px; color:#f3ca8c;">
                            <i class="fa-solid fa-star"></i> <?php echo $avg_rating; ?> (<?php echo $total_reviews; ?> đánh giá)
                        </span>
                    </div>
                </div>
                <div class="sidebar-container">
                    <div class="sidebar-wrapper">
                        <div class="sidebar-card">
                            <div class="sidebar-media"><img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>"></div>
                            <div class="sidebar-content">
                                <div style="display:flex; align-items:center;">
                                    <span class="price-large"><?php echo number_format($gia_giam); ?> đ</span>
                                    <span class="old-price" style="margin-left:10px;"><?php echo number_format($row['gia']); ?> đ</span>
                                </div>
                                <a href="cart.php?action=add&id=<?php echo $id; ?>" class="btn-purple" style="text-decoration:none; display:block; text-align:center;">
                                    Đăng ký học ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="detail-container">
            <div class="detail-left">

                <div class="box-learn">
                    <h3>Bạn sẽ học được gì</h3>
                    <div class="learn-grid">
                        <?php while ($l = mysqli_fetch_assoc($result2)) : ?>
                            <div><i class="fa-solid fa-check"></i> <?php echo htmlspecialchars($l['noi_dung']); ?></div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div style="margin-bottom:40px;">
                    <h3>Nội dung khóa học</h3>
                    <div class="accordion-group">
                        <?php while ($r3 = mysqli_fetch_assoc($result3)) :
                            $cid = $r3['id'];
                            $res4 = mysqli_query($conn, "SELECT * FROM bai_giang WHERE chuong_id = $cid");
                            ?>
                            <div class="accordion-item">
                                <div class="accordion-trigger">
                                    <div style="display:flex; gap:10px; align-items:center;">
                                        <i class="fa-solid fa-chevron-down trigger-icon"></i>
                                        <span><?php echo htmlspecialchars($r3['ten_chuong']); ?></span>
                                    </div>
                                    <span><?php echo mysqli_num_rows($res4); ?> bài</span>
                                </div>
                                <div class="accordion-content">
                                    <div class="lecture-list">
                                        <?php while ($bai = mysqli_fetch_assoc($res4)) : ?>
                                            <a href="#" class="lecture-item" style="text-decoration:none; color:inherit; display:flex; justify-content:space-between;">
                                                <span><i class="fa-regular fa-circle-play"></i> <?php echo htmlspecialchars($bai['ten_bai']); ?></span>
                                                <span>05:00</span>
                                            </a>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div>
                    <h3>Mô tả</h3>
                    <p><?php echo htmlspecialchars($row['mo_ta']); ?></p>
                </div>

                <!-- QA Section -->
                <div class="qa-section">
                    <h3>Bình Luận</h3>
                    <form method="POST" action="" class="qa-form">
                        <div class="user-avatar" style="background:#2d2f31;">
                            <?php echo ($user_id > 0) ? strtoupper(substr($_SESSION['user_name'],0,2)) : '?'; ?>
                        </div>
                        <div class="qa-input-wrap">
                            <?php if ($user_id > 0): ?>
                                <textarea name="noidung_qa" class="qa-textarea" placeholder="Bạn có thắc mắc gì không? Hãy hỏi ngay..." required></textarea>
                                <button type="submit" name="btn_submit_qa" class="btn-qa-submit">Gửi câu hỏi</button>
                            <?php else: ?>
                                <div style="background:#f0f2f5; padding:15px; border-radius:4px; text-align:center;">
                                    Bạn cần <a href="../login.php" style="font-weight:bold; color:#0056d2;">đăng nhập</a> để bình luận.
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="qa-list">
                        <?php while($bl = mysqli_fetch_assoc($result_bl)): ?>
                            <div class="qa-item">
                                <div class="user-avatar" style="background: #<?php echo substr(md5($bl['ten_user']),0,6); ?>">
                                    <?php echo htmlspecialchars($bl['avatar_text']); ?>
                                </div>
                                <div>
                                    <div class="qa-name"><?php echo htmlspecialchars($bl['ten_user']); ?></div>
                                    <div class="qa-text"><?php echo nl2br(htmlspecialchars($bl['noi_dung'])); ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Review Section -->
                <div class="review-section">
                    <h3 class="review-header">Đánh giá của học viên</h3>

                    <div class="rating-dashboard">
                        <div style="text-align:center;">
                            <div class="big-score"><?php echo $avg_rating; ?></div>
                            <div class="stars-gold">
                                <?php for($i=1;$i<=5;$i++) echo ($i<=round($avg_rating))?'<i class="fa-solid fa-star"></i>':'<i class="fa-regular fa-star"></i>'; ?>
                            </div>
                            <div style="color:#b4690e; font-weight:bold; font-size:14px; margin-top:5px;">Xếp hạng</div>
                        </div>

                        <div style="flex:1; max-width:500px; margin: 0 30px;">
                            <?php for($s=5;$s>=1;$s--):
                                $pct = ($total_reviews > 0) ? ($star_counts[$s]/$total_reviews*100) : 0; ?>
                                <div class="bar-item">
                                    <div style="width:20px;"><?php echo $s; ?> <i class="fa-solid fa-star" style="font-size:10px;"></i></div>
                                    <div class="progress-bg"><div class="progress-fill" style="width:<?php echo $pct; ?>%"></div></div>
                                    <div style="width:40px; text-align:right; color:#2d2f31;"><?php echo round($pct); ?>%</div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div>
                            <!-- LOGIC: NẾU ĐÃ ĐĂNG NHẬP THÌ HIỆN NÚT ĐÁNH GIÁ, CHƯA THÌ HIỆN NÚT ĐĂNG NHẬP -->
                            <?php if ($user_id > 0): ?>
                                <button type="button" class="btn-open-review" onclick="toggleReviewForm()">Đánh giá</button>
                            <?php else: ?>
                                <a href="../login.php" class="btn-open-review" style="background-color:#6c757d;">Đăng nhập để đánh giá</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- FORM REVIEW -->
                    <?php if ($user_id > 0): ?>
                        <div id="form-review-container" class="form-review-container">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <h4 style="margin-top:0; color:#00235a;">Gửi đánh giá của bạn</h4>

                                <div style="margin-bottom: 15px;">
                                    <label>1. Đánh giá của bạn về khóa học</label>
                                    <div class="star-rating-input">
                                        <i class="fa-regular fa-star star-icon" data-value="1"></i>
                                        <i class="fa-regular fa-star star-icon" data-value="2"></i>
                                        <i class="fa-regular fa-star star-icon" data-value="3"></i>
                                        <i class="fa-regular fa-star star-icon" data-value="4"></i>
                                        <i class="fa-regular fa-star star-icon" data-value="5"></i>
                                        <input type="hidden" name="rating_value" id="rating_value" value="5">
                                    </div>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label>2. Viết cảm nhận của bạn về khóa học</label>
                                    <textarea name="cam_nhan" class="form-control" rows="4" placeholder="Cảm nhận của bạn..." required></textarea>
                                </div>

                                <div style="margin-bottom: 15px;">
                                    <label>3. Thông tin cá nhân (Tự động điền)</label>
                                    <div style="margin-bottom: 10px;">
                                        <label style="font-size:13px; font-weight:normal;">Họ và tên:</label>
                                        <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" class="form-control" disabled style="background:#eee;">
                                        <input type="hidden" name="ho_ten" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>">
                                    </div>
                                </div>

                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <input type="file" name="review_img" id="review_img_input" style="display:none;" accept="image/*" onchange="previewImage(this)">

                                    <button type="button" class="btn-camera" onclick="document.getElementById('review_img_input').click()" title="Thêm ảnh">
                                        <i class="fa-solid fa-camera"></i>
                                    </button>

                                    <button type="submit" name="btn_submit_review" class="btn-submit-review">Gửi đánh giá</button>
                                    <span id="file-name-display" class="file-name-display"></span>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="review-list" style="margin-top:30px;">
                        <?php foreach ($reviews as $rv): ?>
                            <div class="review-item">
                                <div class="user-avatar" style="background: #<?php echo substr(md5($rv['ten_user']),0,6); ?>">
                                    <?php echo htmlspecialchars($rv['avatar_text']); ?>
                                </div>
                                <div class="review-content">
                                    <div style="font-weight:bold;"><?php echo htmlspecialchars($rv['ten_user']); ?></div>
                                    <div class="stars-gold" style="font-size:12px; margin: 3px 0;">
                                        <?php for($i=1;$i<=5;$i++) echo ($i<=$rv['so_sao'])?'<i class="fa-solid fa-star"></i>':'<i class="fa-regular fa-star" style="color:#ccc"></i>'; ?>
                                    </div>
                                    <div class="review-text"><?php echo nl2br(htmlspecialchars($rv['noi_dung'])); ?></div>

                                    <?php if(!empty($rv['hinh_anh'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($rv['hinh_anh']); ?>" class="review-image" alt="Ảnh đánh giá" onclick="window.open(this.src)">
                                    <?php endif; ?>

                                    <div class="review-date"><?php echo date('d/m/Y', strtotime($rv['ngay_tao'])); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    <?php endwhile; ?>
</div>

<footer>
    <div class="container">
        <div class="logo" style="color:#fff;">Udem<span>y</span></div>
        <div style="font-size:12px; margin-top:10px;">© 2024 Udemy Clone Inc.</div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const triggers = document.querySelectorAll('.accordion-trigger');
        triggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const item = trigger.parentElement;
                const content = trigger.nextElementSibling;
                item.classList.toggle('active');
                if (content.style.maxHeight) content.style.maxHeight = null;
                else content.style.maxHeight = content.scrollHeight + "px";
            });
        });

        const stars = document.querySelectorAll('.star-icon');
        const ratingInput = document.getElementById('rating_value');

        if(stars) {
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    stars.forEach(s => {
                        const sVal = s.getAttribute('data-value');
                        if(sVal <= value) {
                            s.classList.remove('fa-regular');
                            s.classList.add('fa-solid');
                        } else {
                            s.classList.remove('fa-solid');
                            s.classList.add('fa-regular');
                        }
                    });
                });
            });
        }
    });

    function toggleReviewForm() {
        var form = document.getElementById('form-review-container');
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            document.getElementById('file-name-display').innerText = "Đã chọn: " + input.files[0].name;
        }
    }
</script>

</body>
</html>