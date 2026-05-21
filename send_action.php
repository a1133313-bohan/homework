<?php
// 引入 PHPMailer 的核心類別
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if ($email && !empty($subject) && !empty($content)) {
        
        $mail = new PHPMailer(true);

        try {
            // --- 伺服器發信設定 ---
            $mail->isSMTP();                                      // 設定使用 SMTP 模式
            $mail->Host       = 'smtp.gmail.com';                 // Gmail SMTP 伺服器
            $mail->SMTPAuth   = true;                             // 開啟認證
            
            // ⚠️ 這裡請替換成你的 Gmail 帳號與剛剛申請的 16 位數應用程式密碼
            $mail->Username   = 'A1133313@mail.nuk.edu.tw'; 
            $mail->Password   = '';           // 請自行輸入  16 位數密碼
            
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // 加密方式
            $mail->Port       = 587;                              // TLS 常用連接埠
            $mail->CharSet    = 'UTF-8';                          // 防止中文亂碼

            // --- 收發件人設定 ---
            $mail->setFrom('A1133313@mail.nuk.edu.tw');
            $mail->addAddress($email);                            // 接收端（網頁帶入的 Email）

            // --- 郵件內容 ---
            $mail->isHTML(true);                                 // 純文字格式
            $mail->Subject = $subject;
            $mail->Body    = $content;

            // 執行發送
            $mail->send();
            
            echo json_encode(['status' => 'success', 'message' => '真實信件已成功發送！']);
        } catch (Exception $e) {
            // 如果發信失敗，把原因傳回前端（方便 Debug）
            echo json_encode(['status' => 'error', 'message' => '發信失敗，錯誤原因: ' . $mail->ErrorInfo]);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => '欄位缺失。']);
    }
}
?>