<?php
$host = '127.0.0.1';
$user = 'root';
$password = ''; // XAMPP 預設密碼通常為空
$dbname = 'email_system';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("資料庫連線失敗: " . $conn->connect_error);
}
?>