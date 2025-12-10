<?php
include '../database.php';
$active_menu = 'bai_kiem_tra';
$page_title = 'Quản lý Bài kiểm tra';
include 'lecturer_layout.php';
// Xử lý XÓA bài kiểm tra
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Xóa đáp án -> Xóa câu hỏi -> Xóa bài kiểm tra (Để tránh rác Data)
    mysqli_query($conn, "DELETE FROM dap_an WHERE cau_hoi_id IN (SELECT id FROM cau_hoi WHERE bai_kiem_tra_id = $id)");
    mysqli_query($conn, "DELETE FROM cau_hoi WHERE bai_kiem_tra_id = $id");
    mysqli_query($conn, "DELETE FROM bai_kiem_tra WHERE id = $id");
    header("Location: bai_kiem_tra.php");
    exit;
}

// Xử lý THÊM / SỬA bài kiểm tra
$tieu_de = ""; $mo_ta = ""; $ngay_mo = ""; $han_nop = ""; $id_edit = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $mo_ta = $_POST['mo_ta'];
    $ngay_mo = $_POST['ngay_mo'];
    $han_nop = $_POST['han_nop'];
    $id_edit = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($id_edit > 0) {
        $sql = "UPDATE bai_kiem_tra SET tieu_de='$tieu_de', mo_ta='$mo_ta', ngay_mo='$ngay_mo', han_nop='$han_nop' WHERE id=$id_edit";
    } else {
        $sql = "INSERT INTO bai_kiem_tra (tieu_de, mo_ta, ngay_mo, han_nop, trang_thai) VALUES ('$tieu_de', '$mo_ta', '$ngay_mo', '$han_nop', 0)";
    }
    mysqli_query($conn, $sql);
    header("Location: bai_kiem_tra.php");
    exit;
}

// Lấy danh sách
$result = mysqli_query($conn, "SELECT * FROM bai_kiem_tra ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bài kiểm tra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { border-bottom: 2px solid #0056d2; padding-bottom: 10px; color: #0056d2; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 12px; border: none; cursor: pointer; border-radius: 4px; color: #fff; text-decoration: none; font-size: 13px; display: inline-block; }
        .btn-add { background: #28a745; margin-bottom: 15px; }
        .btn-edit { background: #ffc107; color: #000; }
        .btn-del { background: #dc3545; }
        .btn-questions { background: #0056d2; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; width: 500px; margin: 100px auto; padding: 20px; border-radius: 8px; position: relative; }
        .close { position: absolute; top: 10px; right: 15px; cursor: pointer; font-size: 20px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Quản lý Bài kiểm tra</h2>
    <button class="btn btn-add" onclick="openModal()"><i class="fa-solid fa-plus"></i> Tạo bài kiểm tra mới</button>

    <table>
        <tr>
            <th>ID</th>
            <th>Tiêu đề</th>
            <th>Ngày mở</th>
            <th>Hạn nộp</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($row['tieu_de']); ?></strong><br>
                    <small style="color:#666;"><?php echo htmlspecialchars(substr($row['mo_ta'], 0, 50)); ?>...</small>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['ngay_mo'])); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($row['han_nop'])); ?></td>
                <td>
                    <!-- Nút soạn câu hỏi -->
                    <a href="cau_hoi.php?id=<?php echo $row['id']; ?>" class="btn btn-questions" title="Soạn câu hỏi">
                        <i class="fa-solid fa-list-check"></i> Câu hỏi
                    </a>
                    <!-- Nút sửa thông tin -->
                    <button class="btn btn-edit" onclick='openModal(<?php echo json_encode($row); ?>)'>
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <!-- Nút xóa -->
                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-del" onclick="return confirm('Bạn chắc chắn muốn xóa bài này và toàn bộ câu hỏi bên trong?')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<!-- Modal Form Thêm/Sửa -->
<div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3 id="modalTitle">Thêm bài kiểm tra</h3>
        <form method="POST" action="">
            <input type="hidden" name="id" id="quizId">
            <div class="form-group">
                <label>Tiêu đề:</label>
                <input type="text" name="tieu_de" id="tieu_de" required>
            </div>
            <div class="form-group">
                <label>Mô tả:</label>
                <textarea name="mo_ta" id="mo_ta" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Ngày mở:</label>
                <input type="datetime-local" name="ngay_mo" id="ngay_mo" required>
            </div>
            <div class="form-group">
                <label>Hạn nộp:</label>
                <input type="datetime-local" name="han_nop" id="han_nop" required>
            </div>
            <button type="submit" class="btn btn-questions" style="width:100%;">Lưu thông tin</button>
        </form>
    </div>
</div>

<script>
    function openModal(data = null) {
        document.getElementById('quizModal').style.display = 'block';
        if (data) {
            document.getElementById('modalTitle').innerText = 'Cập nhật bài kiểm tra';
            document.getElementById('quizId').value = data.id;
            document.getElementById('tieu_de').value = data.tieu_de;
            document.getElementById('mo_ta').value = data.mo_ta;
            document.getElementById('ngay_mo').value = data.ngay_mo.replace(' ', 'T');
            document.getElementById('han_nop').value = data.han_nop.replace(' ', 'T');
        } else {
            document.getElementById('modalTitle').innerText = 'Thêm bài kiểm tra';
            document.getElementById('quizId').value = '';
            document.getElementById('tieu_de').value = '';
            document.getElementById('mo_ta').value = '';
            document.getElementById('ngay_mo').value = '';
            document.getElementById('han_nop').value = '';
        }
    }
    function closeModal() {
        document.getElementById('quizModal').style.display = 'none';
    }
</script>

</body>
</html>