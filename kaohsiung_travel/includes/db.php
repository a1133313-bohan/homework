<?php
// 本機開發環境連線定義
$host = 'sql302.infinityfree.com';
$db_name = 'if0_42266069_kaohsiungGo';
$db_user = 'if0_42266069';
$db_pass = 'pI6C2HwPQCxn'; // XAMPP 預設密碼為空字串

try {
    // 透過 PDO 實作高安全性的預防 SQL 注入連線
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("資料庫連線中斷，原因: " . $e->getMessage());
}
?>