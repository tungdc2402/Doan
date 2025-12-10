<?php
// Bắt buộc start session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once '../database.php';

$current_page = basename($_SERVER['PHP_SELF']);


$cart_count = 0;
$cart_list_header = [];
$total_header = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_count = count($_SESSION['cart']);

    // Lấy danh sách ID khóa học
    $ids = implode(',', array_keys($_SESSION['cart']));

    if (!empty($ids) && isset($conn)) {
        $sql_header = "SELECT * FROM khoa_hoc WHERE id IN ($ids)";
        $res_header = mysqli_query($conn, $sql_header);

        while ($row = mysqli_fetch_assoc($res_header)) {
            $cart_list_header[] = $row;
            $total_header += $row['gia'] * (100 - $row['giam_gia']) / 100;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPro - Nền tảng học trực tuyến hàng đầu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/home.css">

    <style>
        /* CSS cho User (Giữ nguyên cũ) */
        .auth-btns { display: flex; gap: 10px; margin-left: 15px; }
        .btn-login, .btn-register { text-decoration: none; padding: 8px 15px; border-radius: 20px; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .btn-login { border: 1px solid #333; color: #333; }
        .btn-login:hover { background: #f0f0f0; }
        .btn-register { background: #333; color: #fff; border: 1px solid #333; }
        .btn-register:hover { background: #555; border-color: #555; }
        .user-info { display: flex; align-items: center; gap: 15px; margin-left: 15px; font-size: 14px; font-weight: 600; }
        .user-link { color: #333; text-decoration: none; display: flex; align-items: center; gap: 5px; }
        .user-link:hover { color: #007bff; }

        /* --- CSS MỚI CHO GIỎ HÀNG DROPDOWN --- */
        .cart-icon { position: relative; cursor: pointer; padding: 10px; }
        .cart-count {
            position: absolute; top: 0; right: 0;
            background: #d9534f; color: white;
            border-radius: 50%; font-size: 12px;
            width: 18px; height: 18px;
            display: flex; align-items: center; justify-content: center;
        }

        /* Menu giỏ hàng xổ xuống */
        .cart-dropdown {
            display: none; /* Mặc định ẩn */
            position: absolute;
            top: 100%; right: 0;
            width: 320px;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-radius: 8px;
            z-index: 1000;
            padding: 10px;
        }

        /* Hover vào icon thì hiện dropdown */
        .cart-icon:hover .cart-dropdown { display: block; }

        .cart-header-item {
            display: flex; gap: 10px; padding: 10px;
            border-bottom: 1px solid #eee;
            transition: 0.2s;
        }
        .cart-header-item:hover { background-color: #f9f9f9; }
        .cart-header-item img { width: 50px; height: 35px; object-fit: cover; border-radius: 4px; }
        .cart-item-info { flex: 1; overflow: hidden; }
        .cart-item-title {
            font-size: 13px; font-weight: bold; color: #333;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .cart-item-price { font-size: 12px; color: #d9534f; }

        .cart-footer { padding: 10px; text-align: center; }
        .btn-view-cart {
            display: block; width: 100%;
            background: #2d2f31; color: white;
            text-decoration: none; padding: 10px;
            border-radius: 4px; font-size: 14px; font-weight: bold;
        }
        .btn-view-cart:hover { background: #000; }

        /* Mũi tên nhỏ chỉ lên trên */
        .cart-dropdown::before {
            content: ""; position: absolute; top: -8px; right: 15px;
            border-width: 0 8px 8px 8px;
            border-style: solid;
            border-color: transparent transparent #fff transparent;
        }
        .navbar ul li a.active {
            color: #0056b3;
            font-weight: 600;
        }

    </style>
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <div class="logo">
            <a href="home.php">
                <h1>HọcVìNgàyMai<span>.VN</span></h1>
                <p>Học Cùng Tùng Sói - Negav</p>
            </a>
        </div>

        <nav class="navbar">
            <ul>
                <li><a href="home.php"
                       class="<?= ($current_page == 'home.php') ? 'active' : '' ?>"
                    >Trang chủ</a></li>

                <li><a href="danh_sach_bai.php"
                       class="<?= ($current_page == 'danh_sach_bai.php') ? 'active' : '' ?>"
                    >Kiểm tra trắc nghiệm</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <div class="search-box2">
                <input type="text" placeholder="Tìm khóa học...">
                <button><i class="fas fa-search"></i></button>
            </div>

            <!-- GIỎ HÀNG (CẬP NHẬT MỚI) -->
            <div class="cart-icon">
                <a href="cart.php" style="color: inherit; text-decoration: none;">
                    <i class="fas fa-shopping-cart"></i>
                    <!-- Hiển thị số lượng động -->
                    <?php if($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Dropdown danh sách -->
                <div class="cart-dropdown">
                    <?php if ($cart_count > 0): ?>
                        <div style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cart_list_header as $item):
                                $gia_ban = $item['gia'] * (100 - $item['giam_gia']) / 100;
                                ?>
                                <a href="../chi_tiet_khoa_hoc.php?id=<?php echo $item['id']; ?>" style="text-decoration:none;">
                                    <div class="cart-header-item">
                                        <img src="<?php echo htmlspecialchars($item['hinh_anh']); ?>" alt="img">
                                        <div class="cart-item-info">
                                            <div class="cart-item-title"><?php echo htmlspecialchars($item['ten_khoa_hoc']); ?></div>
                                            <div class="cart-item-price"><?php echo number_format($gia_ban); ?> đ</div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <div class="cart-footer">
                            <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-weight:bold; font-size:14px;">
                                <span>Tổng cộng:</span>
                                <span style="color:#d9534f;"><?php echo number_format($total_header); ?> đ</span>
                            </div>
                            <a href="../cart.php" class="btn-view-cart">Xem giỏ hàng & Thanh toán</a>
                        </div>
                    <?php else: ?>
                        <div style="padding: 20px; text-align: center; color: #666; font-size: 14px;">
                            <i class="fas fa-shopping-basket" style="font-size: 30px; margin-bottom: 10px; display:block;"></i>
                            Giỏ hàng của bạn đang trống
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ============================================= -->

            <!-- LOGIC PHP: HIỂN THỊ USER / ĐĂNG NHẬP (GIỮ NGUYÊN) -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-info">
                    <a href="../profile.php" class="user-link" title="Trang cá nhân">
                        <i class="fas fa-user-circle" style="font-size: 20px;"></i>
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </a>
                    <a href="my_courses.php" class="user-link" title="Khóa học của tôi" style="margin-top: 5px;">
                        <i class="fas fa-book-open" style="font-size: 20px;"></i>
                        Khóa học của tôi
                    </a>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                        <a href="admin/admin_bai_kiem_tra.php" class="user-link" title="Trang quản trị" style="color: #d9534f;">
                            <i class="fas fa-cog"></i>
                        </a>
                    <?php endif; ?>
                    <a href="../logout.php" class="user-link" title="Đăng xuất" style="color: #666;">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            <?php else: ?>
                <div class="auth-btns">
                    <a href="../login.php" class="btn-login">Đăng nhập</a>
                    <a href="../register.php" class="btn-register">Đăng ký</a>
                </div>
            <?php endif; ?>

            <div class="mobile-menu-btn"><i class="fas fa-bars"></i></div>
        </div>
    </div>
</header>