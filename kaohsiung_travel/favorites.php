<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$my_id = $_SESSION['user_id'];

// 關聯撈取當前用戶所有按下收藏讚的景點資料
$stmt = $pdo->prepare("
    SELECT a.* FROM attractions a 
    JOIN favorites f ON a.id = f.attraction_id 
    WHERE f.user_id = ?
");
$stmt->execute([$my_id]);
$fav_items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>我的收藏夾 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>2. 我的收藏夾 ❤️</h2>
        <p style="color:#666;">此處收錄了所有您曾按下點讚心碎的專屬高雄景點：</p>
        
        <div class="cards-grid">
            <?php if(count($fav_items) === 0): ?>
                <p style="color: #888;">目前您的收藏夾空空如也，趕快去景點探索頁面點讚吧！</p>
            <?php endif; ?>
            
            <?php foreach($fav_items as $row): ?>
                <div class="attraction-card" id="fav-card-<?php echo $row['id']; ?>">
                    <div class="card-img-box">
                        <img src="images/attractions/<?php echo htmlspecialchars($row['image_url']); ?>" 
                             onerror="this.src='https://images.unsplash.com/photo-1596495578065-6e0763fa1141?auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="card-body">
                        <span style="background: #e67e22; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                            <?php echo htmlspecialchars($row['category']); ?>
                        </span>
                        <h3 class="card-title" style="margin-top: 5px;"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="card-meta">📍 <?php echo htmlspecialchars($row['address']); ?></div>
                        <div class="card-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                    </div>
                    <div class="card-actions">
                        <button class="action-btn liked" onclick="removeFavorite(<?php echo $row['id']; ?>)" style="width: 100%;">
                            ❌ 移出收藏夾
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function removeFavorite(id) {
            if(confirm('確定要將此景點從收藏夾移出嗎？')) {
                fetch('toggle_favorite.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'removed') {
                        document.getElementById('fav-card-' + id).remove();
                    }
                });
            }
        }
    </script>
</body>
</html>