<?php
session_start();
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>景點探索 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .category-hub { display: flex; flex-wrap: wrap; gap: 20px; margin-top: 30px; }
        .hub-box {
            flex: 1; min-width: 180px; padding: 40px 20px; text-align: center;
            background: #fff; border-radius: 16px; text-decoration: none; color: #333;
            font-size: 20px; font-weight: bold; box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s; border: 1px solid rgba(0,0,0,0.05);
        }
        .hub-box:hover { background: #2c5e3b; color: #fff; transform: translateY(-5px); }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>1. 景點探索 🗺️</h2>
        <p style="color: #666;">請選擇你想探索的高雄旅遊主題分類：</p>
        
        <div class="category-hub">
            <a href="category.php?cat=食" class="hub-box">🍕 在地美食</a>
            <a href="category.php?cat=衣" class="hub-box">👗 潮流服飾</a>
            <a href="category.php?cat=住" class="hub-box">🏨 精選住宿</a>
            <a href="category.php?cat=行" class="hub-box">🚲 交通大眾</a>
            <a href="category.php?cat=育樂" class="hub-box">🎡 育樂玩耍</a>
            <br>
            <center><img src="./images/attractions/景點探索.jpg" alt="美麗高雄港灣主視覺" style="width:60%; height:130%; object-fit:cover;"></center>
        </div>
    </div>
</body>
</html>