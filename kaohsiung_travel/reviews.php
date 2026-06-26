<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// 取得下拉式選單所有有被評論過的景點聚合
$all_atts = $pdo->query("SELECT id, name FROM attractions WHERE is_approved = 1 ORDER BY id ASC")->fetchAll();

$selected_id = isset($_GET['attraction_id']) ? intval($_GET['attraction_id']) : (count($all_atts) > 0 ? $all_atts[0]['id'] : 0);

// 抓取此特定景點的歷史用戶評論清單
$reviews = [];
if($selected_id > 0) {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.attraction_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$selected_id]);
    $reviews = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>查看評論 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .review-tile { background:#fff; padding:20px; border-radius:12px; margin-bottom:15px; box-shadow:0 2px 8px rgba(0,0,0,0.03); border:1px solid #edf2f7; }
        .star-box { color: #f1c40f; font-weight: bold; margin-bottom: 5px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>4. 查看景點評論 💬</h2>
        <p style="color:#666;">請從下拉選單選擇特定景點，以觀看旅友們留下的真實口碑：</p>
        
        <div style="background:#fff; padding:20px; border-radius:12px; margin-bottom:25px;">
            <form action="" method="GET">
                <label style="font-weight:bold; margin-right:10px;">切換觀看景點：</label>
                <select name="attraction_id" onchange="this.form.submit()" style="padding:10px 15px; border-radius:8px; width:300px;">
                    <?php foreach($all_atts as $opt): ?>
                        <option value="<?php echo $opt['id']; ?>" <?php echo ($opt['id'] == $selected_id)?'selected':''; ?>>
                            <?php echo htmlspecialchars($opt['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <div id="reviews-output-container">
            <?php if(count($reviews) === 0): ?>
                <p style="color:#888; text-align:center; padding:40px; background:#fff; border-radius:12px;">該景點目前尚無任何評論心得，歡迎您前去探索並留下第一筆回饋！</p>
            <?php endif; ?>
            
            <?php foreach($reviews as $v): ?>
                <div class="review-tile">
                    <div style="display:flex; justify-content:between; align-items:center; margin-bottom:10px;">
                        <span style="font-weight:bold; color:#2c5e3b;">👤 用戶: <?php echo htmlspecialchars($v['username']); ?></span>
                        <span style="font-size:12px; color:#a0aec0; margin-left:auto;">🕒 發表於: <?php echo $v['created_at']; ?></span>
                    </div>
                    <div class="star-box">
                        <?php echo str_repeat('⭐', $v['rating']); ?>
                    </div>
                    <div style="color:#4a5568; line-height:1.6; font-size:15px; margin-top:8px;">
                        <?php echo nl2br(htmlspecialchars($v['comment'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>