<?php
include '../database.php';
$id_bai = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin bài thi
$bai_thi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bai_kiem_tra WHERE id = $id_bai"));

if (!$bai_thi) {
    echo "<p style='color:red;'>Bài kiểm tra không tồn tại!</p>";
    exit;
}

// ---------------------------------------------------------
// 1. XỬ LÝ IMPORT TỪ EXCEL (FILE CSV)
// ---------------------------------------------------------
if (isset($_POST['btn_import']) && isset($_FILES['file_csv'])) {
    $filename = $_FILES['file_csv']['tmp_name'];

    if ($_FILES['file_csv']['size'] > 0) {
        $file = fopen($filename, "r");

        // Bỏ qua dòng tiêu đề đầu tiên
        fgetcsv($file);

        // Đọc từng dòng
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            // Cấu trúc file CSV:
            // Cột 0: Nội dung câu hỏi
            // Cột 1: Giải thích
            // Cột 2: Đáp án 1
            // Cột 3: Đáp án 2
            // Cột 4: Đáp án 3
            // Cột 5: Đáp án 4
            // Cột 6: Số thứ tự đáp án đúng (1, 2, 3 hoặc 4)

            // Kiểm tra dữ liệu cơ bản (phải có câu hỏi và ít nhất 2 đáp án)
            if (!empty($column[0]) && !empty($column[2]) && !empty($column[3])) {
                $noi_dung = mysqli_real_escape_string($conn, $column[0]);
                $giai_thich = mysqli_real_escape_string($conn, $column[1] ?? '');

                // 1. Thêm câu hỏi
                $sql_q = "INSERT INTO cau_hoi (bai_kiem_tra_id, noi_dung, giai_thich) VALUES ($id_bai, '$noi_dung', '$giai_thich')";
                if (mysqli_query($conn, $sql_q)) {
                    $cau_hoi_id = mysqli_insert_id($conn);
                    $dap_an_dung_index = isset($column[6]) ? (int)$column[6] : 0;

                    // 2. Thêm 4 đáp án (Cột 2 đến 5)
                    for ($k = 1; $k <= 4; $k++) {
                        // Index trong mảng CSV là $k + 1 (vì cột 0,1 là câu hỏi/giải thích)
                        $col_idx = $k + 1;

                        if (isset($column[$col_idx]) && trim($column[$col_idx]) !== '') {
                            $noi_dung_da = mysqli_real_escape_string($conn, $column[$col_idx]);
                            $is_correct = ($k == $dap_an_dung_index) ? 1 : 0;

                            $sql_a = "INSERT INTO dap_an (cau_hoi_id, noi_dung, la_dap_an_dung) VALUES ($cau_hoi_id, '$noi_dung_da', $is_correct)";
                            mysqli_query($conn, $sql_a);
                        }
                    }
                }
            }
        }
        fclose($file);
        echo "<script>alert('Nhập dữ liệu thành công!'); window.location.href='cau_hoi.php?id=$id_bai';</script>";
    }
}

// ---------------------------------------------------------
// 2. XỬ LÝ XÓA CÂU HỎI
// ---------------------------------------------------------
if (isset($_GET['delete_q'])) {
    $qid = (int)$_GET['delete_q'];
    mysqli_query($conn, "DELETE FROM dap_an WHERE cau_hoi_id = $qid");
    mysqli_query($conn, "DELETE FROM cau_hoi WHERE id = $qid");
    header("Location: cau_hoi.php?id=$id_bai");
    exit;
}

// Lấy danh sách câu hỏi
$sql = "SELECT * FROM cau_hoi WHERE bai_kiem_tra_id = $id_bai ORDER BY id ASC";
$ds_cau_hoi = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý câu hỏi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }

        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }

        .q-item { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 4px; }
        .q-header { font-weight: bold; color: #0056d2; margin-bottom: 5px; display: flex; justify-content: space-between; }

        .btn { padding: 6px 12px; border: none; cursor: pointer; border-radius: 4px; color: #fff; text-decoration: none; font-size: 13px; display: inline-block;}
        .btn-edit { background: #ffc107; color: #000; }
        .btn-del { background: #dc3545; }
        .btn-add { background: #28a745; font-size: 14px; padding: 8px 15px; }
        .btn-import { background: #17a2b8; font-size: 14px; padding: 8px 15px; margin-right: 5px; }
        .btn-back { background: #6c757d; }

        /* Modal Import */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 400px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <div>
            <a href="bai_kiem_tra.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
            <h2 style="margin: 10px 0;">Câu hỏi: <?php echo htmlspecialchars($bai_thi['tieu_de']); ?></h2>
        </div>
        <div>
            <!-- Nút mở Modal Import -->
            <button onclick="document.getElementById('importModal').style.display='block'" class="btn btn-import">
                <i class="fa-solid fa-file-excel"></i> Nhập Excel
            </button>
            <a href="admin_edit_cau_hoi.php?bai_id=<?php echo $id_bai; ?>" class="btn btn-add">
                <i class="fa-solid fa-plus"></i> Thêm mới
            </a>
        </div>
    </div>

    <!-- DANH SÁCH CÂU HỎI -->
    <?php
    $i = 1;
    if (mysqli_num_rows($ds_cau_hoi) > 0):
        while ($row = mysqli_fetch_assoc($ds_cau_hoi)):
            $qid = $row['id'];
            $res_da = mysqli_query($conn, "SELECT * FROM dap_an WHERE cau_hoi_id = $qid");
            ?>
            <div class="q-item">
                <div class="q-header">
                    <span>Câu <?php echo $i++; ?>: <?php echo htmlspecialchars($row['noi_dung']); ?></span>
                    <div>
                        <a href="admin_edit_cau_hoi.php?bai_id=<?php echo $id_bai; ?>&edit_id=<?php echo $row['id']; ?>" class="btn btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="?id=<?php echo $id_bai; ?>&delete_q=<?php echo $row['id']; ?>" class="btn btn-del" onclick="return confirm('Xóa câu hỏi này?')"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
                <div style="font-size: 13px; color: #555; margin-left: 20px;">
                    <?php while($da = mysqli_fetch_assoc($res_da)): ?>
                        <div style="<?php echo $da['la_dap_an_dung'] ? 'color:green; font-weight:bold;' : ''; ?>">
                            - <?php echo htmlspecialchars($da['noi_dung']); ?>
                            <?php echo $da['la_dap_an_dung'] ? '<i class="fa-solid fa-check"></i>' : ''; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endwhile;
    else:
        echo "<p style='text-align:center; color:#999;'>Chưa có câu hỏi nào.</p>";
    endif;
    ?>
</div>

<!-- MODAL IMPORT EXCEL (CSV) -->
<div id="importModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('importModal').style.display='none'">&times;</span>
        <h3>Nhập câu hỏi từ Excel</h3>

        <div style="background: #e9ecef; padding: 10px; margin-bottom: 15px; font-size: 12px; border-radius: 4px;">
            <strong>Lưu ý:</strong> File phải là định dạng <b>.CSV (UTF-8)</b>.<br>
            Thứ tự cột: <br>
            1. Nội dung câu hỏi <br>
            2. Giải thích <br>
            3. Đáp án 1 | 4. Đáp án 2 | 5. Đáp án 3 | 6. Đáp án 4 <br>
            7. Số thứ tự đáp án đúng (1, 2, 3 hoặc 4)
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Chọn file CSV:</label>
                <input type="file" name="file_csv" accept=".csv" required style="border: 1px solid #ddd; width: 100%; padding: 5px;">
            </div>
            <button type="submit" name="btn_import" class="btn btn-add" style="width: 100%;">Tải lên</button>
        </form>
    </div>
</div>

<script>
    // Đóng modal khi click ra ngoài
    window.onclick = function(event) {
        var modal = document.getElementById('importModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</body>
</html>