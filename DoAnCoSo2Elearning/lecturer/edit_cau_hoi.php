<?php
include '../database.php';

$bai_id = isset($_GET['bai_id']) ? (int)$_GET['bai_id'] : 0;
$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;

$cau_hoi_hien_tai = ['noi_dung' => '', 'giai_thich' => ''];
$dap_an_hien_tai = [];

// Nếu là chế độ sửa, lấy dữ liệu cũ
if ($edit_id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM cau_hoi WHERE id = $edit_id");
    $cau_hoi_hien_tai = mysqli_fetch_assoc($res);

    $res_da = mysqli_query($conn, "SELECT * FROM dap_an WHERE cau_hoi_id = $edit_id ORDER BY id ASC");
    while($r = mysqli_fetch_assoc($res_da)) {
        $dap_an_hien_tai[] = $r;
    }
}

// XỬ LÝ LƯU DỮ LIỆU
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $noi_dung = $_POST['noi_dung'];
    $giai_thich = $_POST['giai_thich'];
    $answers = $_POST['answers'] ?? []; // Mảng nội dung đáp án
    $correct = $_POST['correct'] ?? 0;  // Index của đáp án đúng (0, 1, 2...)

    if ($edit_id > 0) {
        // Cập nhật câu hỏi
        $sql = "UPDATE cau_hoi SET noi_dung='$noi_dung', giai_thich='$giai_thich' WHERE id=$edit_id";
        mysqli_query($conn, $sql);
        $qid = $edit_id;

        // Cách đơn giản nhất: Xóa hết đáp án cũ, thêm lại đáp án mới
        mysqli_query($conn, "DELETE FROM dap_an WHERE cau_hoi_id = $qid");
    } else {
        // Thêm câu hỏi mới
        $sql = "INSERT INTO cau_hoi (bai_kiem_tra_id, noi_dung, giai_thich) VALUES ($bai_id, '$noi_dung', '$giai_thich')";
        mysqli_query($conn, $sql);
        $qid = mysqli_insert_id($conn);
    }

    // Thêm các đáp án vào DB
    foreach ($answers as $index => $ans_text) {
        if (trim($ans_text) !== '') {
            $is_correct = ($index == $correct) ? 1 : 0;
            $sql_da = "INSERT INTO dap_an (cau_hoi_id, noi_dung, la_dap_an_dung) VALUES ($qid, '$ans_text', $is_correct)";
            mysqli_query($conn, $sql_da);
        }
    }

    header("Location: cau_hoi.php?id=$bai_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Soạn thảo câu hỏi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 5px; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }

        .answer-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; color: #fff; font-weight: bold; }
        .btn-save { background: #0056d2; }
        .btn-cancel { background: #6c757d; text-decoration: none; display: inline-block; }
        .btn-add-ans { background: #28a745; padding: 5px 10px; font-size: 12px; margin-bottom: 10px; }
        .btn-remove { background: #dc3545; padding: 5px 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2><?php echo $edit_id > 0 ? 'Sửa câu hỏi' : 'Thêm câu hỏi mới'; ?></h2>

    <form method="POST">
        <div class="form-group">
            <label>Nội dung câu hỏi:</label>
            <textarea name="noi_dung" rows="3" required><?php echo htmlspecialchars($cau_hoi_hien_tai['noi_dung']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Đáp án (Tích chọn đáp án đúng):</label>
            <div id="answer-list">
                <?php
                // Nếu có dữ liệu cũ thì loop ra, nếu không thì hiện sẵn 4 ô trống
                $list_to_show = (!empty($dap_an_hien_tai)) ? $dap_an_hien_tai : ['', '', '', ''];
                foreach ($list_to_show as $idx => $val):
                    $text = is_array($val) ? $val['noi_dung'] : $val;
                    $checked = (is_array($val) && $val['la_dap_an_dung'] == 1) ? 'checked' : '';
                    // Nếu là mảng rỗng ban đầu thì mặc định checked ô đầu tiên cho an toàn
                    if (empty($dap_an_hien_tai) && $idx == 0) $checked = 'checked';
                    ?>
                    <div class="answer-item">
                        <input type="radio" name="correct" value="<?php echo $idx; ?>" <?php echo $checked; ?>>
                        <input type="text" name="answers[<?php echo $idx; ?>]" value="<?php echo htmlspecialchars($text); ?>" placeholder="Nhập đáp án..." required>
                        <button type="button" class="btn btn-remove" onclick="removeAns(this)"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn btn-add-ans" onclick="addAnswer()"><i class="fa-solid fa-plus"></i> Thêm đáp án</button>
        </div>

        <div class="form-group">
            <label>Giải thích chi tiết (Hiện sau khi nộp bài):</label>
            <textarea name="giai_thich" rows="3"><?php echo htmlspecialchars($cau_hoi_hien_tai['giai_thich']); ?></textarea>
        </div>

        <div style="text-align: right;">
            <a href="cau_hoi.php?id=<?php echo $bai_id; ?>" class="btn btn-cancel">Hủy bỏ</a>
            <button type="submit" class="btn btn-save">Lưu câu hỏi</button>
        </div>
    </form>
</div>

<script>
    // Hàm thêm ô đáp án mới bằng Javascript
    function addAnswer() {
        const container = document.getElementById('answer-list');
        const count = container.children.length;

        const div = document.createElement('div');
        div.className = 'answer-item';
        div.innerHTML = `
            <input type="radio" name="correct" value="${count}">
            <input type="text" name="answers[${count}]" placeholder="Nhập đáp án..." required>
            <button type="button" class="btn btn-remove" onclick="removeAns(this)"><i class="fa-solid fa-xmark"></i></button>
        `;
        container.appendChild(div);

        // Cập nhật lại value cho radio button để đúng thứ tự
        updateIndices();
    }

    // Hàm xóa ô đáp án
    function removeAns(btn) {
        const list = document.getElementById('answer-list');
        if (list.children.length > 2) { // Giữ lại ít nhất 2 đáp án
            btn.parentElement.remove();
            updateIndices();
        } else {
            alert('Cần ít nhất 2 đáp án!');
        }
    }

    // Cập nhật lại index (0,1,2,3...) khi thêm/xóa để backend nhận đúng
    function updateIndices() {
        const list = document.getElementById('answer-list');
        Array.from(list.children).forEach((item, index) => {
            item.querySelector('input[type="radio"]').value = index;
            item.querySelector('input[type="text"]').name = `answers[${index}]`;
        });
    }
</script>

</body>
</html>