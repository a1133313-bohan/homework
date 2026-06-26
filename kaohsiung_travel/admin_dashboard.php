<?php
session_start();
// 雙重身分鎖：嚴格防止一般會員闖入後台控制端
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') { header("Location: login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>管理者後台 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <div style="background: #2c5e3b; padding: 40px; border-radius: 20px; color:#fff; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <h1>管理者控制後台來囉～ ⚙️</h1>
            <p>當前身分：系統最高管理員 (hank)。您可以進行私藏審查、處理不當評論及調閱熱門數據分析。</p>
            <hr style="border:none; border-top:1px solid rgba(255,255,255,0.2); margin:25px 0;">
            <div style="background:rgba(255,255,255,0.1); padding:20px; border-radius:12px; display:inline-block;">
                ⚡ 系統提醒：請定期前往「審核私藏提案」維護社群高品質旅遊景點庫。
            </div>
        </div>
    </div>
</body>
</html>