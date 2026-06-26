<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') { header("Location: login.php"); exit; }

// 核准審核案件或駁回案件之核心處置
if (isset($_GET['decision']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['decision'] === 'approve') {
        // 通過審核：改為 1
        $stmt = $pdo->prepare("UPDATE attractions SET is_approved = 1 WHERE id = ?");
        $stmt->execute([$id]);
        echo "<script>alert('該提案已順利通過核准並即刻上架！'); window.location.href='admin_approve.php';</script>";
    } else if ($_GET['decision'] === 'reject') {
        // 拒絕駁回：直接從資料庫拔除
        $stmt = $pdo->prepare("DELETE FROM attractions WHERE id = ?");
        $stmt->execute([$id]);
        echo "<script>alert('已成功駁回並剔除該不符規定的提案案件。'); window.location.href='admin_approve.php';</script>";
    }
    exit;
}

// 撈出當前所有待審查 (is_approved = 0) 的使用者推薦案件
$pending_items = $pdo->query("SELECT * FROM attractions WHERE is_approved = 0 ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>審核使用者提案</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>📥 審核私藏提案後台</h2>
        <p style="color:#666;">以下為熱心用戶上傳投稿的私藏口袋名單，請審查其文字及相片是否得體：</p>
        
        <div class="cards-grid">
            <?php if(count($pending_items) === 0): ?>
                <p style="color:#888; background:#fff; padding:30px; border-radius:12px; width:100%;">🎉 太棒了！目前沒有任何尚未審理的景點案件。</p>
            <?php endif; ?>
            
            <?php foreach($pending_items as $row): ?>
                <div class="attraction-card">
                    <div class="card-img-box">
                        <img src="images/attractions/<?php echo htmlspecialchars($row['image_url']); ?>" onerror="this.src='https://images.unsplash.com/photo-1596495578065-6e0763fa1141?auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="card-body">
                        <span style="background:#e67e22; color:#fff; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:bold;">待審【<?php echo htmlspecialchars($row['category']); ?>】</span>
                        <h3 class="card-title" style="margin-top:5px;"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p style="font-size:12px; color:#718096;">📍 <?php echo htmlspecialchars($row['address']); ?></p>
                        <div class="card-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                    </div>
                    <div class="card-actions">
                        <a href="admin_approve.php?decision=approve&id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('確定核准通過此景點上架嗎？')" class="action-btn" style="color:green; text-decoration:none; line-height:40px;">🟢 核准通過</a>
                        <a href="admin_approve.php?decision=reject&id=<?php echo $row['id']; ?>" 
                           onclick="return confirm('確定要回絕刪除此不符規定的提案嗎？')" class="action-btn" style="color:red; text-decoration:none; line-height:40px;">🔴 駁回回絕</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>