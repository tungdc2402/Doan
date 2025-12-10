<?php
session_start();
include '../database.php';

// 1. XỬ LÝ THÊM VÀO GIỎ
if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = (int)$_GET['id'];
    // Nếu chưa có giỏ thì tạo mới
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    // Thêm id khóa học vào giỏ (dùng ID làm key để không bị trùng)
    $_SESSION['cart'][$id] = 1;
}

// 2. XỬ LÝ XÓA KHÓA HỌC
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id = (int)$_GET['id'];
    unset($_SESSION['cart'][$id]);
}

// 3. LẤY DANH SÁCH KHÓA HỌC TRONG GIỎ ĐỂ HIỂN THỊ
$cart_items = [];
$total_price = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Lấy danh sách ID từ session
    $ids = implode(',', array_keys($_SESSION['cart']));

    if (!empty($ids)) {
        $sql = "SELECT * FROM khoa_hoc WHERE id IN ($ids)";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $cart_items[] = $row;
            // Tính giá sau giảm
            $gia_thuc = $row['gia'] * (100 - $row['giam_gia']) / 100;
            $total_price += $gia_thuc;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: left; }
        .total { text-align: right; font-size: 20px; font-weight: bold; margin-top: 20px; color: #d9534f; }
        .btn-pay { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px; font-weight: bold; }
        .btn-remove { color: red; text-decoration: none; font-size: 14px; }
        .btn-back { color: #555; text-decoration: none; display: inline-block; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <a href="home.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Tiếp tục xem khóa học</a>
    <h2>Giỏ hàng của bạn</h2>

    <?php if (empty($cart_items)): ?>
        <p style="text-align:center; padding: 30px;">Giỏ hàng đang trống.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Khóa học</th>
                <th>Giá gốc</th>
                <th>Giảm giá</th>
                <th>Thành tiền</th>
                <th></th>
            </tr>
            <?php foreach ($cart_items as $item):
                $gia_giam = $item['gia'] * (100 - $item['giam_gia']) / 100;
                ?>
                <tr>
                    <td>
                        <div style="font-weight:bold;"><?php echo $item['ten_khoa_hoc']; ?></div>
                        <small><?php echo $item['giang_vien']; ?></small>
                    </td>
                    <td style="text-decoration: line-through; color:#999;"><?php echo number_format($item['gia']); ?> đ</td>
                    <td>-<?php echo $item['giam_gia']; ?>%</td>
                    <td style="color:#d9534f; font-weight:bold;"><?php echo number_format($gia_giam); ?> đ</td>
                    <td>
                        <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="btn-remove"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="total">
            Tổng thanh toán: <?php echo number_format($total_price); ?> đ
        </div>

        <div style="text-align: right; margin-top: 20px;">
            <!-- Kiểm tra đăng nhập trước khi thanh toán -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="process_payment.php" method="POST">
                    <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">
                    <button type="submit" name="btn_pay" class="btn-pay">Tiến hành Thanh toán</button>
                </form>
            <?php else: ?>
                <p style="color:red;">Vui lòng <a href="../login.php">Đăng nhập</a> để thanh toán.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>