<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') { header("Location: login.php"); exit; }

// 實作後台直接予以一鍵清除不當言詞留言之邏輯
if (isset($_GET['delete_review_id'])) {
    $rid = intval($_GET['delete_review_id']);
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$rid]);
    echo "<script>alert('該違規不雅評論已被強制撤除！'); window.location.href='admin_reviews.php';</script>";
    exit;
}

// 查閱全站目前所有被建立的評論明細
$all_reviews = $pdo->query("
    SELECT r.*, u.username, a.name as attraction_name 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN attractions a ON r.attraction_id = a.id
    ORDER BY r.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>違規評論檢視控制</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .review-row { background:#fff; padding:20px; border-radius:12px; margin-bottom:15px; box-shadow:0 2px 5px rgba(0,0,0,0.02); display:flex; justify-content:space-between; align-items:start; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>💬 違規評論處理後台</h2>
        <p style="color:#666;">系統全域言論集中站，可在此快速揪出涉嫌違規洗板之評語並將其直接蒸發：</p>
        
        <div style="margin-top:20px;">
            <?php if(count($all_reviews) === 0): ?>
                <p style="color:#888; text-align:center; background:#fff; padding:30px; border-radius:12px;">全站目前風平浪靜，沒有任何評論紀錄。</p>
            <?php endif; ?>
            
            <?php foreach($all_reviews as $v): ?>
                <div class="review-row">
                    <div style="flex:1; padding-right:20px;">
                        <span style="font-size:12px; background:#edf2f7; padding:3px 8px; border-radius:4px; font-weight:bold; color:#4a5568;">
                            🎯 景點: <?php echo htmlspecialchars($v['attraction_name']); ?>
                        </span>
                        <div style="margin:8px 0; font-weight:bold; font-size:14px; color:#2c5e3b;">
                            👤 發表帳號: <?php echo htmlspecialchars($v['username']); ?> 
                            <span style="color:#f1c40f; margin-left:10px;"><?php echo str_repeat('★', $v['rating']); ?></span>
                        </div>
                        <p style="margin:5px 0; color:#4a5568; line-height:1.5; font-size:14px;"><?php echo nl2br(htmlspecialchars($v['comment'])); ?></p>
                        <small style="color:#a0aec0;">🕒 時間: <?php echo $v['created_at']; ?></small>
                    </div>
                    <a href="admin_reviews.php?delete_review_id=<?php echo $v['id']; ?>" 
                       onclick="return confirm('確定要完全強制撤除該用戶的本筆評論嗎？')" 
                       style="background:#e74c3c; color:#fff; border-none; padding:8px 15px; text-decoration:none; font-size:13px; border-radius:6px; font-weight:bold; white-space:nowrap;">
                        ❌ 撤除評論
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>