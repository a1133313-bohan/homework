<?php
session_start();
// 保護控制：不允許未經登入的訪客直接進入系統
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'user') { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-wrapper">
        <div style="background: rgba(255,255,255,0.7); padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
            <h1 style="color: #2c5e3b; margin-bottom: 10px;">帶你暢玩高雄 🌴</h1>
            <p style="color: #718096; font-size: 16px;">整合在地探索、私藏景點投稿、社群評論與自由拖拉排程規劃的一站式導覽平台</p>
            <hr style="border: none; border-top: 1px solid #edf2f7; margin: 30px 0;">
            
            <div style="width: 100%; height: 400px; border-radius: 15px; overflow: hidden; background: #ccc;">
                <img src="./images/attractions/大廳頁面.png" alt="美麗高雄港灣主視覺" style="width:100%; height:120%; object-fit:cover;">
            </div>
            
            <p style="margin-top: 25px; font-weight: 500; color: #4a5568;">點擊左側選單的各個功能，開啟你的專屬深度高雄之旅吧！</p>
        </div>
    </div>
</body>
</html>