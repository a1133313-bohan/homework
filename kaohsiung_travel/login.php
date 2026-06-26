<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>登入 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* 專門為登入打造的森林戶外感意象樣式 */
        .login-body {
            display: flex; justify-content: center; align-items: center; height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('https://images.unsplash.com/photo-1470240731273-7821a6eeb6bd?auto=format&fit=crop&w=1200&q=80');
            background-size: cover; background-position: center; margin: 0;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(15px);
            padding: 40px; border-radius: 24px; border: 1px solid rgba(255,255,255,0.2);
            width: 360px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); color: #fff;
        }
        .login-box h2 { text-align: center; margin-bottom: 30px; letter-spacing: 2px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-size: 14px; }
        .input-group input {
            width: 100%; padding: 12px; border-radius: 10px; border: none;
            background: rgba(255,255,255,0.9); color: #333; box-sizing: border-box;
        }
        .submit-btn {
            width: 100%; padding: 14px; border: none; border-radius: 10px;
            background: #2c5e3b; color: #fff; font-weight: bold; font-size: 16px;
            cursor: pointer; transition: background 0.3s; margin-top: 10px;
        }
        .submit-btn:hover { background: #1e4228; }
    </style>
</head>
<body class="login-body">
    <div class="login-box">
        <h2>高雄去哪玩 🧭</h2>
        <form action="logincheck.php" method="POST">
            <div class="input-group">
                <label>使用者帳號 (Username)</label>
                <input type="text" name="username" placeholder="請輸入帳號" required>
            </div>
            <div class="input-group">
                <label>密碼 (Password)</label>
                <input type="password" name="password" placeholder="請輸入密碼" required>
            </div>
            <button type="submit" class="submit-btn">驗證並登入系統</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 14px;">
            新夥伴？ <a href="register.php" style="color: #f39c12; text-decoration: none; font-weight: bold;">立即註冊帳戶</a>
        </p>
    </div>
</body>
</html>