<?php
require_once 'includes/db.php';
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);
    
    if(!empty($user) && !empty($pass)){
        // 檢查是否帳號已被使用
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$user]);
        if($stmt->fetch()){
            $msg = "此帳號已被註冊！";
        } else {
            // 直接寫入資料庫(維持與原架構一致的直覺字串儲存方式)
            $stmt = $pdo->prepare("INSERT INTO users (username, password, type) VALUES (?, ?, 'user')");
            $stmt->execute([$user, $pass]);
            echo "<script>alert('註冊成功！請前去登入'); window.location.href='login.php';</script>";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>註冊新帳戶 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .register-body { display: flex; justify-content: center; align-items: center; height: 100vh; background: #2c3e50; }
        .reg-box { background: #fff; padding: 40px; border-radius: 16px; width: 340px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .reg-box h2 { text-align: center; margin-bottom: 25px; color: #2c5e3b; }
        .input-group { margin-bottom: 18px; }
        .input-group label { display: block; margin-bottom: 6px; font-size: 14px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
    </style>
</head>
<body class="register-body">
    <div class="reg-box">
        <h2>建立新帳戶</h2>
        <?php if(!empty($msg)): ?>
            <div style="color: red; text-align: center; margin-bottom: 15px; font-size: 14px;"><?php echo $msg; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="input-group">
                <label>自訂註冊帳號</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>設定密碼</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: #e67e22; border: none; color: #fff; border-radius: 8px; font-weight: bold; cursor: pointer;">確認註冊</button>
        </form>
        <p style="text-align: center; margin-top: 15px; font-size: 13px;"><a href="login.php" style="color: #666; text-decoration: none;">返回登入介面</a></p>
    </div>
</body>
</html>