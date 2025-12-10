<?php
// send_mail.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        // Cấu hình Server
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dangcongtung2426@gmail.com';
        $mail->Password = 'vgxw ircl baoy nggm';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        // Người gửi - Người nhận
        $mail->setFrom('dangcongtung2426@gmail.com', 'Elearning');
        $mail->addAddress($email);

        // Nội dung
        $mail->isHTML(true);
        $mail->Subject = 'Mã xác thực đổi mật khẩu';
        $mail->Body = "Mã OTP của bạn là: <b style='font-size:20px;'>$otp</b>. Mã này sẽ hết hạn sau 15 phút.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>