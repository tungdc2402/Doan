<?php
include '../database.php';

$bai_kiem_tra_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// --- PHẦN LOGIC PHP ---
// Lấy dữ liệu câu hỏi
$sql = "SELECT 
            ch.id as cau_hoi_id, ch.noi_dung as cau_hoi_text, ch.giai_thich,
            da.id as dap_an_id, da.noi_dung as dap_an_text, da.la_dap_an_dung
        FROM cau_hoi ch
        LEFT JOIN dap_an da ON ch.id = da.cau_hoi_id
        WHERE ch.bai_kiem_tra_id = $bai_kiem_tra_id
        ORDER BY ch.id ASC, da.id ASC";

$result = mysqli_query($conn, $sql);

$danh_sach_cau_hoi = [];
while ($row = mysqli_fetch_assoc($result)) {
    $qid = $row['cau_hoi_id'];
    if (!isset($danh_sach_cau_hoi[$qid])) {
        $danh_sach_cau_hoi[$qid] = [
            'id' => $qid,
            'noi_dung' => $row['cau_hoi_text'],
            'giai_thich' => $row['giai_thich'],
            'dap_an' => []
        ];
    }
    if ($row['dap_an_id']) {
        $danh_sach_cau_hoi[$qid]['dap_an'][] = [
            'id' => $row['dap_an_id'],
            'noi_dung' => $row['dap_an_text'],
            'la_dung' => $row['la_dap_an_dung']
        ];
    }
}
$danh_sach_cau_hoi = array_values($danh_sach_cau_hoi);
$tong_cau = count($danh_sach_cau_hoi);

// Xử lý nộp bài
$da_nop_bai = false;
$ket_qua_user = [];
$so_cau_dung = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nop_bai'])) {
    $da_nop_bai = true;
    $lua_chon = $_POST['answer'] ?? [];

    foreach ($danh_sach_cau_hoi as $index => $ch) {
        $qid = $ch['id'];
        $user_ans = $lua_chon[$qid] ?? null;
        $dap_an_dung_id = null;
        foreach ($ch['dap_an'] as $da) {
            if ($da['la_dung'] == 1) $dap_an_dung_id = $da['id'];
        }
        if ($user_ans == $dap_an_dung_id) $so_cau_dung++;

        $ket_qua_user[$qid] = [
            'chon' => $user_ans,
            'dung_id' => $dap_an_dung_id,
            'is_correct' => ($user_ans == $dap_an_dung_id)
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm bài kiểm tra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* CSS Global */
        * { box-sizing: border_box; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        body { margin: 0; background-color: #f7f9fa; color: #2d2f31; height: 100vh; overflow: hidden; }
        .wrapper { display: flex; height: 100%; }

        /* SIDEBAR */
        .sidebar { width: 300px; background: #fff; border-right: 1px solid #d1d7dc; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #d1d7dc; }
        .sidebar-content { padding: 20px; overflow-y: auto; flex: 1; }
        .question-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }

        /* --- STYLE TRẠNG THÁI CÂU HỎI (QUAN TRỌNG) --- */
        .q-item {
            height: 40px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; border-radius: 4px; font-weight: 500; transition: all 0.2s;

            /* Mặc định: Chưa làm (Trắng) */
            background-color: #fff;
            border: 1px solid #d1d7dc;
            color: #2d2f31;
        }

        /* 1. Đã trả lời (Xanh biển) */
        .q-item.answered {
            background-color: #007bff; /* Xanh biển */
            color: #fff;
            border-color: #007bff;
        }

        /* 2. Đánh dấu xem lại (Vàng) - Ghi đè màu xanh nếu vừa làm vừa đánh dấu */
        .q-item.flagged {
            background-color: #ffc107 !important; /* Vàng */
            color: #212529 !important;
            border-color: #ffc107 !important;
        }

        /* 3. Hiện tại (Trắng viền Xanh) - Ghi đè border */
        .q-item.active {
            border: 2px solid #0056d2 !important; /* Viền xanh đậm */
            /* Nền giữ nguyên theo trạng thái (nếu đã làm thì nền xanh, chưa làm nền trắng)
               Tuy nhiên theo yêu cầu "Hiện tại trắng viền xanh", ta sẽ set background white */
            background-color: #fff !important;
            color: #0056d2 !important;
            font-weight: bold;
        }

        /* Kết quả sau khi nộp (Xanh lá / Đỏ) */
        .q-item.res-correct { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
        .q-item.res-wrong { background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; }

        /* MAIN CONTENT */
        .main { flex: 1; display: flex; flex-direction: column; background: #fff; overflow-y: auto; }
        .main-content { padding: 40px; max-width: 800px; margin: 0 auto; width: 100%; }
        .question-text { font-size: 19px; font-weight: 700; margin-bottom: 24px; }

        .answer-group { display: flex; flex-direction: column; gap: 12px; }
        .ans-label { display: flex; align-items: center; padding: 12px 16px; border: 1px solid #2d2f31; border-radius: 4px; cursor: pointer; }
        .ans-label:hover { background-color: #f7f9fa; }
        .ans-label input { margin-right: 12px; width: 18px; height: 18px; accent-color: #2d2f31; }

        /* FOOTER */
        .footer-nav { padding: 16px 40px; border-top: 1px solid #d1d7dc; display: flex; justify-content: space-between; align-items: center; background: #fff; position: sticky; bottom: 0; }
        .btn { padding: 10px 16px; font-weight: 700; border: none; cursor: pointer; background: transparent; }
        .btn-link { color: #0056d2; }
        .btn-flag { border: 1px solid #d1d7dc; color: #2d2f31; border-radius: 4px; }
        .btn-flag.active-flag { background-color: #ffc107; border-color: #ffc107; color: #000; } /* Nút đổi màu khi active */
        .btn-primary { background-color: #2d2f31; color: white; border-radius: 4px; padding: 10px 24px; }

        /* Utilities */
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .timer { font-size: 20px; font-weight: bold; }

        /* Kết quả hiển thị */
        .result-explanation { margin-top: 20px; padding: 15px; background: #e6f2f5; border-left: 5px solid #0056d2; }
        .ans-correct-highlight { border: 2px solid #198754; background: #d1e7dd; }
        .ans-wrong-highlight { border: 2px solid #dc3545; background: #f8d7da; }
    </style>
</head>
<body>

<form method="POST" action="" id="quizForm" class="wrapper">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="font-weight:700; margin-bottom:5px;">Bài kiểm tra: Lập trình cơ bản</div>
            <?php if (!$da_nop_bai): ?>
                <div class="timer"><i class="fa-regular fa-clock"></i> <span id="timeDisplay">30:00</span></div>
            <?php else: ?>
                <div class="timer">Kết quả: <?php echo $so_cau_dung . '/' . $tong_cau; ?></div>
            <?php endif; ?>
        </div>

        <div class="sidebar-content">
            <h4 style="margin-top:0;">Danh sách câu hỏi</h4>
            <div class="question-grid">
                <?php foreach ($danh_sach_cau_hoi as $index => $ch):
                    $extra_class = '';
                    if ($da_nop_bai) {
                        $extra_class = $ket_qua_user[$ch['id']]['is_correct'] ? 'res-correct' : 'res-wrong';
                    }
                    ?>
                    <div class="q-item <?php echo $index === 0 ? 'active' : ''; ?> <?php echo $extra_class; ?>"
                         id="nav-<?php echo $index; ?>"
                         onclick="goToQuestion(<?php echo $index; ?>)">
                        <?php echo $index + 1; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Chú thích màu sắc -->
            <?php if (!$da_nop_bai): ?>
                <div style="margin-top: 20px; font-size: 12px; display: grid; gap: 8px;">
                    <div style="display:flex; align-items:center; gap:8px;"><div style="width:15px; height:15px; border:1px solid #ccc;"></div> Chưa làm</div>
                    <div style="display:flex; align-items:center; gap:8px;"><div style="width:15px; height:15px; border:2px solid #0056d2;"></div> Hiện tại</div>
                    <div style="display:flex; align-items:center; gap:8px;"><div style="width:15px; height:15px; background:#007bff;"></div> Đã trả lời</div>
                    <div style="display:flex; align-items:center; gap:8px;"><div style="width:15px; height:15px; background:#ffc107;"></div> Đánh dấu</div>
                </div>
            <?php endif; ?>
        </div>

        <div style="padding: 20px;">
            <?php if (!$da_nop_bai): ?>
                <button type="submit" name="nop_bai" class="btn-primary" style="width:100%;">Nộp bài</button>
            <?php else: ?>
                <a href="lam_bai.php?id=<?php echo $bai_kiem_tra_id; ?>" class="btn-primary" style="display:block; text-align:center; text-decoration:none;">Làm lại bài</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="main-content">
            <?php foreach ($danh_sach_cau_hoi as $index => $ch):
                $qid = $ch['id'];
                $user_chon = $da_nop_bai ? $ket_qua_user[$qid]['chon'] : null;
                $dung_id = $da_nop_bai ? $ket_qua_user[$qid]['dung_id'] : null;
                ?>
                <div class="tab-pane <?php echo $index === 0 ? 'active' : ''; ?>" id="question-<?php echo $index; ?>">
                    <div class="question-header">Câu hỏi <?php echo $index + 1; ?>/<?php echo $tong_cau; ?></div>
                    <div class="question-text"><?php echo htmlspecialchars($ch['noi_dung']); ?></div>

                    <div class="answer-group">
                        <?php foreach ($ch['dap_an'] as $da):
                            $bg = '';
                            $checked = ($user_chon == $da['id']) ? 'checked' : '';
                            if ($da_nop_bai) {
                                if ($da['id'] == $dung_id) $bg = 'ans-correct-highlight';
                                elseif ($da['id'] == $user_chon && $da['id'] != $dung_id) $bg = 'ans-wrong-highlight';
                            }
                            ?>
                            <label class="ans-label <?php echo $bg; ?>">
                                <input type="radio"
                                       name="answer[<?php echo $qid; ?>]"
                                       value="<?php echo $da['id']; ?>"
                                    <?php echo $checked; ?>
                                    <?php echo $da_nop_bai ? 'disabled' : ''; ?>
                                       onchange="markAnswered(<?php echo $index; ?>)">
                                <span><?php echo htmlspecialchars($da['noi_dung']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($da_nop_bai && $ch['giai_thich']): ?>
                        <div class="result-explanation">
                            <strong><i class="fa-solid fa-lightbulb"></i> Giải thích:</strong><br>
                            <?php echo nl2br(htmlspecialchars($ch['giai_thich'])); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="footer-nav">
            <?php if(!$da_nop_bai): ?>
                <button type="button" class="btn btn-flag" id="btn-flag" onclick="toggleFlag()">
                    <i class="fa-regular fa-flag"></i> Đánh dấu xem lại
                </button>
            <?php else: ?>
                <div></div> <!-- Spacer nếu đã nộp bài -->
            <?php endif; ?>

            <div>
                <button type="button" class="btn btn-link" onclick="changeQuestion(-1)"><i class="fa-solid fa-chevron-left"></i> Câu trước</button>
                <button type="button" class="btn btn-link" onclick="changeQuestion(1)">Câu tiếp theo <i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</form>

<script>
    let currentIdx = 0;
    const totalQuestions = <?php echo $tong_cau; ?>;
    const isSubmitted = <?php echo $da_nop_bai ? 'true' : 'false'; ?>;

    // Mảng lưu trạng thái cờ
    let flaggedQuestions = new Array(totalQuestions).fill(false);

    function changeQuestion(step) {
        let newIdx = currentIdx + step;
        if (newIdx >= 0 && newIdx < totalQuestions) {
            goToQuestion(newIdx);
        }
    }

    function goToQuestion(index) {
        // Bỏ active câu cũ
        document.getElementById('question-' + currentIdx).classList.remove('active');
        document.getElementById('nav-' + currentIdx).classList.remove('active');

        // Active câu mới
        currentIdx = index;
        document.getElementById('question-' + currentIdx).classList.add('active');
        document.getElementById('nav-' + currentIdx).classList.add('active');

        // Cập nhật trạng thái nút Flag ở footer cho đồng bộ với câu hỏi hiện tại
        updateFlagButtonState();
    }

    // Khi chọn đáp án -> Thêm class .answered (Xanh biển)
    function markAnswered(index) {
        if (!isSubmitted) {
            document.getElementById('nav-' + index).classList.add('answered');
        }
    }

    // Khi bấm "Đánh dấu xem lại" -> Thêm class .flagged (Vàng)
    function toggleFlag() {
        if (isSubmitted) return;

        flaggedQuestions[currentIdx] = !flaggedQuestions[currentIdx]; // Đảo trạng thái
        const navItem = document.getElementById('nav-' + currentIdx);

        if (flaggedQuestions[currentIdx]) {
            navItem.classList.add('flagged');
        } else {
            navItem.classList.remove('flagged');
        }
        updateFlagButtonState();
    }

    // Cập nhật màu nút Flag ở Footer
    function updateFlagButtonState() {
        const btnFlag = document.getElementById('btn-flag');
        if (btnFlag) {
            if (flaggedQuestions[currentIdx]) {
                btnFlag.classList.add('active-flag');
                btnFlag.innerHTML = '<i class="fa-solid fa-flag"></i> Đã đánh dấu';
            } else {
                btnFlag.classList.remove('active-flag');
                btnFlag.innerHTML = '<i class="fa-regular fa-flag"></i> Đánh dấu xem lại';
            }
        }
    }

    // Timer logic
    if (!isSubmitted) {
        let duration = 30 * 60;
        const display = document.getElementById('timeDisplay');
        setInterval(() => {
            let m = Math.floor(duration / 60);
            let s = duration % 60;
            display.textContent = (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
            if (--duration < 0) document.getElementById('quizForm').submit();
        }, 1000);
    }
</script>

</body>
</html>