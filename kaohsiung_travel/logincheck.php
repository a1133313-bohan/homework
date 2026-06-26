<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 進行帳密精確比對驗證
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        // 配置全站階段 Session 變數值
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['type'] = $user['type'];

        // 刷新寫入當前登入的時間紀錄戳記
        $update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $update->execute([$user['id']]);

        // 依據角色權限導向至不同的大廳首頁
        if ($user['type'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        echo "<script>alert('認證失敗：帳號或密碼填寫錯誤！'); window.location.href='login.php';</script>";
        exit;
    }
}
?>