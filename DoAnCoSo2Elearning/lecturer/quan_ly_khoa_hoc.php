<?php
include '../database.php';
$active_menu = 'khoa_hoc';
$page_title = 'Quản lý Khóa học';

// --- XỬ LÝ FORM (THÊM & SỬA & XÓA) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';

    // Lấy dữ liệu từ form
    $ten_khoa_hoc = $_POST['ten_khoa_hoc'] ?? '';
    $mo_ta = $_POST['mo_ta'] ?? '';
    $gia = $_POST['gia'] ?? 0;
    $giam_gia = $_POST['giam_gia'] ?? 0; // % giảm giá
    $danh_muc = $_POST['danh_muc'] ?? 'Lập trình';
    $hinh_anh = $_POST['hinh_anh'] ?? ''; // Link ảnh URL

    // Giả định ID giảng viên đang đăng nhập là 1 (hoặc lấy từ session)
    $giang_vien_id = 1;
    $ten_giang_vien = "Đặng Công Tùng"; // Lấy từ session user thì tốt hơn

    if ($action == 'add') {
        $sql = "INSERT INTO khoa_hoc (ten_khoa_hoc, mo_ta, gia, giam_gia, danh_muc, hinh_anh, giang_vien_id, ngay_tao) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdissi", $ten_khoa_hoc, $mo_ta, $gia, $giam_gia, $danh_muc, $hinh_anh, $giang_vien_id);
        $stmt->execute();
    }
    elseif ($action == 'edit') {
        $id = $_POST['id'];
        $sql = "UPDATE khoa_hoc SET ten_khoa_hoc=?, mo_ta=?, gia=?, giam_gia=?, danh_muc=?, hinh_anh=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdissi", $ten_khoa_hoc, $mo_ta, $gia, $giam_gia, $danh_muc, $hinh_anh, $id);
        $stmt->execute();
    }

    // Refresh để tránh gửi lại form
    header("Location: quan_ly_khoa_hoc.php");
    exit;
}

// Xử lý XÓA
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM khoa_hoc WHERE id=$id");
    header("Location: quan_ly_khoa_hoc.php");
    exit;
}

include 'lecturer_layout.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin:0;">Danh sách khóa học</h3>
        <button onclick="openModal('add')" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Tạo khóa học mới
        </button>
    </div>

    <table>
        <thead>
        <tr>
            <th width="50">ID</th>
            <th width="120">Hình ảnh</th>
            <th>Thông tin khóa học</th>
            <th>Giá & Khuyến mãi</th>
            <th>Thống kê</th>
            <th width="150">Hành động</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Lấy danh sách khóa học
        $res = mysqli_query($conn, "SELECT * FROM khoa_hoc ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)):
            // Tính giá sau giảm
            $gia_goc = $row['gia'];
            $giam = $row['giam_gia'];
            $gia_ban = $gia_goc * (1 - $giam/100);
            ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td>
                    <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>"
                         alt="Course Img"
                         style="width: 100px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                </td>
                <td>
                    <strong style="font-size: 16px; color: #2d2f31;"><?php echo htmlspecialchars($row['ten_khoa_hoc']); ?></strong><br>
                    <span class="badge" style="background:#e0e0e0; font-size:11px; padding:2px 6px; border-radius:4px; margin-top:5px; display:inline-block;">
                        <?php echo htmlspecialchars($row['danh_muc']); ?>
                    </span>
                    <br>
                    <small style="color: #666; display: block; margin-top: 5px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo htmlspecialchars($row['mo_ta']); ?>
                    </small>
                </td>
                <td>
                    <div style="font-weight: bold; color: #a435f0; font-size: 16px;">
                        <?php echo number_format($gia_ban, 0, ',', '.'); ?> đ
                    </div>
                    <?php if($giam > 0): ?>
                        <div style="font-size: 13px; color: #6a6f73; text-decoration: line-through;">
                            <?php echo number_format($gia_goc, 0, ',', '.'); ?> đ
                        </div>
                        <div style="font-size: 12px; color: #dc3545;">Giảm <?php echo $giam; ?>%</div>
                    <?php endif; ?>
                </td>
                <td style="font-size: 14px;">
                    <div><i class="fa-solid fa-user"></i> <?php echo $row['so_luong_hoc_vien']; ?> học viên</div>
                    <div style="margin-top:5px; color:#e59819;">
                        <i class="fa-solid fa-star"></i> <?php echo $row['danh_gia_tb']; ?>/5
                    </div>
                </td>
                <td>
                    <button onclick='openModal("edit", <?php echo json_encode($row); ?>)' class="btn btn-warning" style="color:#000;">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn chắc chắn muốn xóa khóa học này?')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                    <a href="noi_dung_khoa_hoc.php?id=<?php echo $row['id']; ?>" class="btn btn-success" style="margin-top: 5px; display:block; text-align:center;">
                        <i class="fa-solid fa-list"></i> Bài học
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div id="courseModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 1000;">
    <div style="background:#fff; width:600px; margin: 50px auto; padding:25px; border-radius:8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
        <h3 id="modalTitle" style="margin-top:0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Thêm khóa học mới</h3>

        <form method="POST">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="id" id="courseId">

            <div class="form-group">
                <label>Tên khóa học:</label>
                <input type="text" name="ten_khoa_hoc" id="ten_khoa_hoc" class="form-control" required placeholder="VD: Lập trình C++ Cơ bản">
            </div>

            <div class="row" style="display:flex; gap: 15px;">
                <div class="col" style="flex:1;">
                    <label>Danh mục:</label>
                    <select name="danh_muc" id="danh_muc" class="form-control">
                        <option value="Lập trình">Lập trình</option>
                        <option value="Kinh doanh">Kinh doanh</option>
                        <option value="Marketing">Marketing</option>
                        <option value="Thiết kế">Thiết kế</option>
                        <option value="Ngoại ngữ">Ngoại ngữ</option>
                    </select>
                </div>
                <div class="col" style="flex:1;">
                    <label>Link Hình ảnh (URL):</label>
                    <input type="text" name="hinh_anh" id="hinh_anh" class="form-control" placeholder="https://...">
                </div>
            </div>

            <div class="row" style="display:flex; gap: 15px; margin-top: 15px;">
                <div class="col" style="flex:1;">
                    <label>Giá gốc (VNĐ):</label>
                    <input type="number" name="gia" id="gia" class="form-control" value="0">
                </div>
                <div class="col" style="flex:1;">
                    <label>Giảm giá (%):</label>
                    <input type="number" name="giam_gia" id="giam_gia" class="form-control" value="0" min="0" max="100">
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Mô tả chi tiết:</label>
                <textarea name="mo_ta" id="mo_ta" rows="5" class="form-control" placeholder="Nhập giới thiệu về khóa học..."></textarea>
                <small style="color: #666;">* Nhập mô tả đầy đủ để hiển thị đẹp trên trang chi tiết.</small>
            </div>

            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                <button type="button" onclick="closeModal()" class="btn btn-secondary" style="background:#6c757d; margin-right: 10px;">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-control { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    .form-group { margin-bottom: 15px; }
    /* Responsive table */
    table img { max-width: 100%; }
</style>

<script>
    function openModal(mode, data = null) {
        document.getElementById('courseModal').style.display = 'block';
        if (mode === 'edit' && data) {
            document.getElementById('modalTitle').innerText = 'Cập nhật khóa học';
            document.getElementById('action').value = 'edit';
            document.getElementById('courseId').value = data.id;

            document.getElementById('ten_khoa_hoc').value = data.ten_khoa_hoc;
            document.getElementById('danh_muc').value = data.danh_muc;
            document.getElementById('hinh_anh').value = data.hinh_anh;
            document.getElementById('gia').value = data.gia;
            document.getElementById('giam_gia').value = data.giam_gia;
            document.getElementById('mo_ta').value = data.mo_ta;
        } else {
            document.getElementById('modalTitle').innerText = 'Thêm khóa học mới';
            document.getElementById('action').value = 'add';
            document.getElementById('courseId').value = '';

            // Reset form
            document.getElementById('ten_khoa_hoc').value = '';
            document.getElementById('gia').value = '0';
            document.getElementById('giam_gia').value = '0';
            document.getElementById('mo_ta').value = '';
            document.getElementById('hinh_anh').value = '';
        }
    }

    function closeModal() {
        document.getElementById('courseModal').style.display = 'none';
    }

    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
        let modal = document.getElementById('courseModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>

</div>
</body>
</html>