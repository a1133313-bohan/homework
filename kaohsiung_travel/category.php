<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$cat = isset($_GET['cat']) ? $_GET['cat'] : '食';
$my_id = $_SESSION['user_id'];

// 處理接收前端送出的全新評論表單
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    $att_id = $_POST['attraction_id'];
    $rating = $_POST['rating'];
    $comment = trim($_POST['comment']);
    
    if(!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, attraction_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$my_id, $att_id, $rating, $comment]);
        echo "<script>alert('感謝您的星級評論！'); window.location.href='category.php?cat=".urlencode($cat)."';</script>";
        exit;
    }
}

// 撈取經管理員核准通過的當前特定主題景點紀錄
$stmt = $pdo->prepare("SELECT * FROM attractions WHERE category = ? AND is_approved = 1");
$stmt->execute([$cat]);
$items = $stmt->fetchAll();

// 找出當前用戶所有曾讚過的景點清單陣列，用以渲染愛心高亮樣式
$fav_stmt = $pdo->prepare("SELECT attraction_id FROM favorites WHERE user_id = ?");
$fav_stmt->execute([$my_id]);
$my_favs = $fav_stmt->fetchAll(PDO::FETCH_COLUMN, 0);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>探索高雄 - <?php echo htmlspecialchars($cat); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>景點探索 - 精選【<?php echo htmlspecialchars($cat); ?>】清單</h2>
        
        <div class="cards-grid">
            <?php if(count($items) === 0): ?>
                <p style="color: #666;">目前本分類尚無已審核通過的景點。</p>
            <?php endif; ?>
            
            <?php foreach($items as $row): 
                $is_liked = in_array($row['id'], $my_favs);
            ?>
                <div class="attraction-card">
                    <div class="card-img-box">
                        <img src="images/attractions/<?php echo htmlspecialchars($row['image_url']); ?>" 
                             onerror=<img src="images/attractions/<?php echo $row['image_url']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>" style="height: 200px; object-fit: cover;">
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <div class="card-meta">📍 <?php echo htmlspecialchars($row['address']); ?></div>
                        <div class="card-desc"><?php echo htmlspecialchars($row['description']); ?></div>
                        
                        <div style="margin-top: 10px;">
                            <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($row['name'] . ' ' . $row['address']); ?>" 
                               target="_blank" style="color: #2c5e3b; text-decoration: none; font-size: 13px; font-weight: bold;">🧭 導航至此地 Go!</a>
                        </div>
                    </div>
                    <div class="card-actions">
                        <button class="action-btn <?php echo $is_liked ? 'liked' : ''; ?>" onclick="toggleLike(this, <?php echo $row['id']; ?>)">
                            <?php echo $is_liked ? '❤️ 已收藏' : '🤍 點讚收藏'; ?>
                        </button>
                        <button class="action-btn" onclick="openReviewModal(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                            💬 填寫評論
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="reviewModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="modalTitle" style="margin-top: 0; color: #2c5e3b;">留下景點評論</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="add_review">
                <input type="hidden" name="attraction_id" id="modalAttractionId">
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">給予星級評分：</label>
                    <select name="rating" style="width: 100%; padding: 8px; border-radius: 6px;">
                        <option value="5">⭐⭐⭐⭐⭐ 5 星完美極推</option>
                        <option value="4">⭐⭐⭐⭐ 4 星值得一去</option>
                        <option value="3">⭐⭐⭐ 3 星體驗普普通通</option>
                        <option value="2">⭐⭐ 2 星有待加強改善</option>
                        <option value="1">⭐ 1 星極度不推薦</option>
                    </select>
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">具體心得評論：</label>
                    <textarea name="comment" rows="4" style="width: 100%; padding: 8px; border-radius: 6px; box-sizing: border-box;" placeholder="請輸入您對此景點的真實觀感..." required></textarea>
                </div>
                
                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeReviewModal()" style="padding: 8px 15px; border-radius: 6px; border: 1px solid #ccc; background:#fff; cursor:pointer;">取消</button>
                    <button type="submit" style="padding: 8px 15px; border-radius: 6px; border: none; background:#2c5e3b; color:#fff; cursor:pointer; font-weight:bold;">提交心得</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 利用 Fetch API 達成網頁免重新整理非同步點讚切換
        function toggleLike(btn, attractionId) {
            fetch('toggle_favorite.php?id=' + attractionId)
            .then(res => res.json())
            .then(data => {
                if(data.status === 'added') {
                    btn.classList.add('liked');
                    btn.innerText = '❤️ 已收藏';
                } else if(data.status === 'removed') {
                    btn.classList.remove('liked');
                    btn.innerText = '🤍 點讚收藏';
                }
            });
        }

        function openReviewModal(id, name) {
            document.getElementById('modalAttractionId').value = id;
            document.getElementById('modalTitle').innerText = '評論景點：' + name;
            document.getElementById('reviewModal').style.display = 'flex';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
    </script>
</body>
</html>