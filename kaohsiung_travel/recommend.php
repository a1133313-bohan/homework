<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $address = trim($_POST['address']);
    $description = trim($_POST['description']);
    
    $final_filename = 'default.jpg'; // 預設檔案名
    
    // 實作使用者實體照片檔案自動化上傳邏輯
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['photo']['tmp_name'];
        $original_name = $_FILES['photo']['name'];
        $ext = pathinfo($original_name, PATHINFO_EXTENSION);
        
        // 重新分配防重複的亂數檔名架構
        $final_filename = 'upload_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $target_dir = 'images/attractions/' . $final_filename;
        
        // 移動檔案至我們規定的實體資產目錄中
        move_uploaded_file($file_tmp, $target_dir);
    }
    
    // 插入資料庫並保持待審核狀態(is_approved = 0)
    $stmt = $pdo->prepare("INSERT INTO attractions (name, category, description, address, image_url, is_approved) VALUES (?, ?, ?, ?, ?, 0)");
    $stmt->execute([$name, $category, $description, $address, $final_filename]);
    
    echo "<script>alert('私藏景點投稿成功！請靜待管理員審核通過。'); window.location.href='recommend.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>私藏推薦 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-container { display: flex; gap: 40px; background: #fff; padding: 30px; border-radius: 16px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
        .form-left { flex: 1; }
        .form-right { width: 320px; background: #f7fafc; border-radius: 12px; padding: 20px; box-sizing: border-box; }
        .field-group { margin-bottom: 15px; }
        .field-group label { display: block; margin-bottom: 6px; font-weight: bold; font-size: 14px; }
        .field-group input, .field-group select, .field-group textarea {
            width: 100%; padding: 10px; border: 1px solid #cbd5e0; border-radius: 8px; box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>3. 私藏推薦 ✨</h2>
        <p style="color: #666;">發現了高雄在地不為人知的厲害角落？歡迎投稿！經後台審核通過將公諸於世。</p>
        
        <div class="form-container">
            <div class="form-left">
                <form action="recommend.php" method="POST" enctype="multipart/form-data">
                    <div class="field-group">
                        <label>上傳景點照片 (Upload Photo)</label>
                        <input type="file" name="photo" accept="image/*" id="photoInput" onchange="previewImage(this)" required>
                    </div>
                    <div class="field-group">
                        <label>景點名稱 (Attraction Name)</label>
                        <input type="text" name="name" placeholder="例如：西子灣落日祕境" id="formName" oninput="syncPreview()" required>
                    </div>
                    <div class="field-group">
                        <label>主題分類 (Category)</label>
                        <select name="category" required>
                            <option value="食">🍕 在地美食</option>
                            <option value="衣">👗 潮流服飾</option>
                            <option value="住">🏨 精選住宿</option>
                            <option value="行">🚲 交通大眾</option>
                            <option value="育樂" selected>🎡 育樂玩耍</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label>景點地址 (Address)</label>
                        <input type="text" name="address" placeholder="例如：高雄市鼓山區蓮海路70號" id="formAddress" oninput="syncPreview()" required>
                    </div>
                    <div class="field-group">
                        <label>景點簡介 (Introduction)</label>
                        <textarea name="description" rows="4" placeholder="請詳細描述這個地方為什麼迷人..." id="formDesc" oninput="syncPreview()" required></textarea>
                    </div>
                    
                    <button type="submit" style="padding: 12px 25px; border:none; background:#2c5e3b; color:#fff; font-weight:bold; border-radius:8px; cursor:pointer;">遞交提案</button>
                    <button type="reset" style="padding: 12px 25px; border:1px solid #ccc; background:#fff; border-radius:8px; cursor:pointer;">重設</button>
                </form>
            </div>
            
            <div class="form-right">
                <h4 style="margin-top:0; text-align:center; color:#4a5568;">即時前端預覽</h4>
                <div class="attraction-card" style="width:100%; box-shadow:none; border:1px solid #e2e8f0;">
                    <div class="card-img-box" style="height:160px;">
                        <img id="previewImg" src="https://images.unsplash.com/photo-1596495578065-6e0763fa1141?auto=format&fit=crop&w=400&q=80">
                    </div>
                    <div class="card-body" style="padding: 15px;">
                        <h4 class="card-title" id="previewTitle" style="font-size:16px; margin-bottom:5px;">未命名私藏景點</h4>
                        <div class="card-meta" id="previewAddress" style="font-size:12px; margin:5px 0;">📍 尚未填寫地址</div>
                        <div class="card-desc" id="previewDesc" style="font-size:12px; height:60px;">等待填寫簡介說明...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function syncPreview() {
            var name = document.getElementById('formName').value;
            var addr = document.getElementById('formAddress').value;
            var desc = document.getElementById('formDesc').value;
            
            document.getElementById('previewTitle').innerText = name ? name : '未命名私藏景點';
            document.getElementById('previewAddress').innerText = addr ? '📍 ' + addr : '📍 尚未填寫地址';
            document.getElementById('previewDesc').innerText = desc ? desc : '等待填寫簡介說明...';
        }
    </script>
</body>
</html>