<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        // 先檢查此 email 是否已經存在於資料庫
        $stmt = $conn->prepare("SELECT id FROM emails WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('此 Email 已經建構在資料庫中！'); window.location.href='index.php';</script>";
        } else {
            $stmt->close();
            // 寫入資料庫
            $stmt = $conn->prepare("INSERT INTO emails (email) VALUES (?)");
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                echo "<script>alert('Email 成功存入資料庫！'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('寫入失敗'); window.location.href='index.php';</script>";
            }
        }
        $stmt->close();
    } else {
        echo "<script>alert('無效的 Email 格式！'); window.location.href='index.php';</script>";
    }
}
$conn->close();
?>