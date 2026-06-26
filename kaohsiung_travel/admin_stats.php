<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') { header("Location: login.php"); exit; }

// 依據點讚收藏人氣熱度，高明排序揪出最受矚目的熱門景點前五名
$chart_data = $pdo->query("
    SELECT a.name, COUNT(f.id) as total_likes 
    FROM attractions a
    LEFT JOIN favorites f ON a.id = f.attraction_id
    WHERE a.is_approved = 1
    GROUP BY a.id 
    ORDER BY total_likes DESC, a.id ASC 
    LIMIT 5
")->fetchAll();

// 找出前五名裡面最大的數字當作圖表百分比換算基準分母
$max_val = 1;
if(count($chart_data) > 0 && $chart_data[0]['total_likes'] > 0) {
    $max_val = $chart_data[0]['total_likes'];
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>後台數據中心 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .chart-box-wrapper { background:#fff; padding:35px; border-radius:16px; box-shadow:0 4px 15px rgba(0,0,0,0.05); margin-top:25px; }
        .chart-row { margin-bottom: 25px; }
        .chart-label { font-weight: bold; font-size: 14px; margin-bottom: 6px; color:#4a5568; }
        .bar-outer-track { background: #edf2f7; border-radius: 10px; width: 100%; height: 28px; overflow: hidden; position: relative; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
        /* 長條圖動態核心 */
        .bar-inner-fill { background: linear-gradient(90deg, #2c5e3b, #e67e22); height: 100%; border-radius: 10px; transition: width 1s ease-out; display: flex; align-items: center; justify-content: flex-end; padding-right: 12px; box-sizing: border-box; }
        .bar-counter-text { color: #fff; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>📈 後台數據中心 (統計分析)</h2>
        <p style="color:#666;">系統自動根據全站會員按下點讚收藏 (Favorites) 的總計次數，精準演算出熱門景點 Top 5：</p>
        
        <div class="chart-box-wrapper">
            <h3 style="margin-top:0; color:#2d3748; text-align:center; margin-bottom:30px;">📊 最受歡迎的高雄旅遊景點統計 (依收藏數)</h3>
            
            <?php 
            $rank = 1;
            foreach($chart_data as $row): 
                // 將實際按讚次數等比放大換算為畫面的寬度 CSS 百分比值
                $calculated_pct = ($row['total_likes'] / $max_val) * 100;
            ?>
                <div class="chart-row">
                    <div class="chart-label">
                        <span style="color:#e67e22; margin-right:5px;">NO.<?php echo $rank++; ?></span> 
                        <?php echo htmlspecialchars($row['name']); ?>
                    </div>
                    <div class="bar-outer-track">
                        <div class="bar-inner-fill" style="width: <?php echo $calculated_pct; ?>%;">
                            <span class="bar-counter-text"><?php echo $row['total_likes']; ?> 收藏</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>