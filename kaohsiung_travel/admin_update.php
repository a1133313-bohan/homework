<?php
// 啟動 Session，並檢查是不是管理員（防止壞人直接打網址進來偷改）
session_start();
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    die("您沒有權限執行此操作！");
}

// 1. 引入你的資料庫連線檔案（請依你專案實際的檔名修改，例如 db.php 或 connect.php）
include('db_connect.php'); 

// 2. 檢查有沒有收到表單傳過來的資料
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 接收欄位資料
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // 3. 執行 SQL UPDATE 語法（假設你的資料庫資料表名稱叫 spots，請依實際名稱修改）
    $sql = "UPDATE spots SET 
            name = '$name', 
            address = '$address', 
            image_url = '$image_url', 
            description = '$description' 
            WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        // 修改成功，跳出提示視窗，並自動跳轉回管理大廳（或 admin_explore.php）
        echo "<script>
                alert('景點資料已成功修改！');
                window.location.href = 'admin_explore.php';
              </script>";
    } else {
        // 萬一失敗，印出錯誤訊息方便除錯
        echo "修改失敗，錯誤原因：" . mysqli_error($conn);
    }
} else {
    echo "非法請求！";
}
?>