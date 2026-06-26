<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'admin') { header("Location: login.php"); exit; }

// 修改表單提交處理（這裡本來就寫好了，非常棒！）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_attraction') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    
    $stmt = $pdo->prepare("UPDATE attractions SET name=?, category=?, address=?, description=? WHERE id=?");
    $stmt->execute([$name, $category, $address, $description, $id]);
    echo "<script>alert('該景點欄位已成功全面修改！'); window.location.href='admin_explore.php';</script>";
    exit;
}

// 撈出全站所有已被審查通過之現役卡片景點
$all_items = $pdo->query("SELECT * FROM attractions WHERE is_approved = 1 ORDER BY category, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>後台景點修改管理</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .edit-badge-btn { position: absolute; top: 10px; right: 10px; background: #e67e22; color: #fff; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; z-index: 10; }
        
        /* 修正：優化彈出視窗樣式，預設為隱藏 display: none */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding: 20px 0; }
        .attraction-card { position: relative; border: 1px solid #dee2e6; border-radius: 10px; overflow: hidden; background: #fff; }
        .card-img-box img { width: 100%; height: 180px; object-fit: cover; }
        .card-body { padding: 15px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper" style="margin-left: 260px; padding: 20px;">
        <h2>🔍 景點管理修改分頁 (管理端)</h2>
        <p style="color:#666;">點擊任意景點卡片右上角的「修改」按鈕，即可針對所有文字欄位進行覆寫變更：</p>
        
        <div class="cards-grid">
            <?php foreach($all_items as $row): ?>
                <div class="attraction-card">
                    <button class="edit-badge-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)">✏️ 修改欄位</button>
                    
                    <div class="card-img-box">
                        <img src="images/attractions/<?php echo htmlspecialchars($row['image_url']); ?>" onerror="this.src='https://images.unsplash.com/photo-1596495578065-6e0763fa1141?auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="card-body">
                        <span style="background:#2c5e3b; color:#fff; font-size:11px; padding:2px 6px; border-radius:4px; font-weight:bold;"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h3 class="card-title" style="margin-top:5px;"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p style="font-size:12px; color:#718096; margin:4px 0;">📍 <?php echo htmlspecialchars($row['address']); ?></p>
                        <div class="card-desc" style="font-size:13px; color:#4a5568; line-height:1.5;"><?php echo htmlspecialchars($row['description']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="editModal" class="modal-overlay">
        <div class="modal-content" style="width: 500px;">
            <h3 style="margin-top:0; color:#e67e22;">編輯景點全欄位變更</h3>
            <form action="admin_explore.php" method="POST">
                <input type="hidden" name="action" value="edit_attraction">
                <input type="hidden" name="id" id="edit_id">
                
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-weight:bold; margin-bottom:4px;">景點名稱</label>
                    <input type="text" name="name" id="edit_name" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box;" required>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-weight:bold; margin-bottom:4px;">主題分類</label>
                    <select name="category" id="edit_cat" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box;">
                        <option value="食">食</option>
                        <option value="衣">衣</option>
                        <option value="住">住</option>
                        <option value="行">行</option>
                        <option value="育樂">育樂</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-weight:bold; margin-bottom:4px;">景點地址</label>
                    <input type="text" name="address" id="edit_addr" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box;" required>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; font-weight:bold; margin-bottom:4px;">簡介詳述</label>
                    <textarea name="description" id="edit_desc" rows="5" style="width:100%; padding:8px; border-radius:6px; border:1px solid #ccc; box-sizing:border-box;" required></textarea>
                </div>
                
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="closeEditModal()" style="padding:8px 15px; border:1px solid #ccc; background:#fff; border-radius:6px; cursor:pointer;">取消</button>
                    <button type="submit" style="padding:8px 15px; background:#e67e22; color:#fff; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">儲存變更</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 【修正 2】：重寫並修復彈出視窗控制機制，精確帶入欄位值
        function openEditModal(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_cat').value = data.category;
            document.getElementById('edit_addr').value = data.address;
            document.getElementById('edit_desc').value = data.description;
            
            // 修正原本找不到物件與 parentElement 的寫法錯字，精確展開視窗
            document.getElementById('editModal').style.display = 'flex';
        }
        
        function closeEditModal() { 
            document.getElementById('editModal').style.display = 'none'; 
        }
    </script>
</body>
</html>