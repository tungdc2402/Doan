<?php
include '../database.php';
$active_menu = 'hoc_vien';
$page_title = 'Quản lý Học viên';

// --- CẤU HÌNH GIẢNG VIÊN ---
// Vì database của bạn lưu tên giảng viên trực tiếp vào bảng khoa_hoc
// Bạn có thể lấy tên này từ Session khi đăng nhập.
// Tạm thời tôi để cứng là "Đặng Công Tùng" như trong ảnh bạn gửi.
$ten_giang_vien_hien_tai = "Đặng Công Tùng";

// --- XỬ LÝ LỌC ---
$course_filter = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Câu điều kiện lọc cơ bản: Chỉ lấy các khóa học của giảng viên này
// Lưu ý: So sánh chuỗi cần thêm dấu nháy đơn '$ten...'
$where_clause = "WHERE kh.giang_vien = '$ten_giang_vien_hien_tai'";

// Nếu có chọn lọc theo khóa học cụ thể
if ($course_filter > 0) {
    $where_clause .= " AND kh.id = $course_filter";
}

// --- TRUY VẤN DỮ LIỆU ---
// JOIN 3 bảng: khoa_hoc_da_mua -> users -> khoa_hoc
$sql_students = "
    SELECT 
        khdm.id as ma_don,
        khdm.ngay_mua,
        khdm.tien_do,
        u.ho_ten,
        u.email,
        u.so_dien_thoai,
        kh.ten_khoa_hoc,
        kh.id as khoa_hoc_id
    FROM khoa_hoc_da_mua khdm
    JOIN users u ON khdm.user_id = u.id
    JOIN khoa_hoc kh ON khdm.khoa_hoc_id = kh.id
    $where_clause
    ORDER BY khdm.ngay_mua DESC
";

$res_students = mysqli_query($conn, $sql_students);

// Kiểm tra lỗi SQL nếu có (Giúp debug dễ hơn)
if (!$res_students) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}

// Lấy danh sách khóa học của giảng viên để nạp vào Dropdown lọc
$sql_courses = "SELECT id, ten_khoa_hoc FROM khoa_hoc WHERE giang_vien = '$ten_giang_vien_hien_tai'";
$res_courses = mysqli_query($conn, $sql_courses);

include 'lecturer_layout.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="margin: 0;">Danh sách học viên</h3>
            <small style="color: #666;">Quản lý tiến độ học tập của sinh viên</small>
        </div>

        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
            <select name="course_id" onchange="this.form.submit()" style="padding: 8px 12px; border-radius: 4px; border: 1px solid #ddd; outline: none; cursor: pointer;">
                <option value="0">--- Tất cả khóa học ---</option>
                <?php while($c = mysqli_fetch_assoc($res_courses)): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($course_filter == $c['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['ten_khoa_hoc']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <?php if (mysqli_num_rows($res_students) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #495057;">STT</th>
                    <th style="padding: 12px; text-align: left; color: #495057;">Học viên</th>
                    <th style="padding: 12px; text-align: left; color: #495057;">Khóa học</th>
                    <th style="padding: 12px; text-align: left; color: #495057;">Ngày đăng ký</th>
                    <th style="padding: 12px; text-align: left; color: #495057;">Tiến độ</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $stt = 1;
                while($row = mysqli_fetch_assoc($res_students)):
                    // Tính toán trạng thái dựa trên tiến độ (vì bảng của bạn không có cột trạng thái)
                    $tien_do = (int)$row['tien_do'];
                    $is_completed = ($tien_do >= 100);
                    $progress_color = $is_completed ? '#28a745' : '#a435f0'; // Xanh lá nếu xong, Tím nếu đang học
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;"><?php echo $stt++; ?></td>
                        <td style="padding: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 35px; height: 35px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #495057;">
                                    <?php echo strtoupper(substr($row['ho_ten'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: #212529;"><?php echo htmlspecialchars($row['ho_ten']); ?></div>
                                    <div style="font-size: 13px; color: #868e96;"><?php echo htmlspecialchars($row['email']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px;">
                        <span style="font-weight: 500; color: #0056d2;">
                            <?php echo htmlspecialchars($row['ten_khoa_hoc']); ?>
                        </span>
                        </td>
                        <td style="padding: 12px; color: #495057;">
                            <?php echo date('d/m/Y', strtotime($row['ngay_mua'])); ?>
                        </td>
                        <td style="padding: 12px; width: 200px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; height: 6px; background: #e9ecef; border-radius: 10px; overflow: hidden;">
                                    <div style="width: <?php echo $tien_do; ?>%; height: 100%; background: <?php echo $progress_color; ?>;"></div>
                                </div>
                                <span style="font-size: 13px; font-weight: bold; color: <?php echo $progress_color; ?>;">
                                <?php echo $tien_do; ?>%
                            </span>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 50px 20px; color: #6c757d;">
                <i class="fa-solid fa-graduation-cap" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p>Chưa có học viên nào đăng ký khóa học này.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</div> </body>
</html>