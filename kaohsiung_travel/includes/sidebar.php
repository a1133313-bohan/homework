<?php
// 取得當前執行的程式檔名，用來動態標記左側亮起狀態
$current_page = basename($_SERVER['PHP_SELF']);
$user_type = isset($_SESSION['type']) ? $_SESSION['type'] : 'user';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '未登入';
?>
<div class="sidebar-container">
    <a href="index.php" style="text-decoration: none; color: inherit;">
    <h2>高雄去哪玩</h2>
    </a>
    <ul class="menu-list">
        <?php if($user_type === 'admin'): ?>
            <li><a href="admin_dashboard.php" class="<?php echo ($current_page=='admin_dashboard.php')?'active':''; ?>">📊 後台大廳</a></li>
            <li><a href="admin_explore.php" class="<?php echo ($current_page=='admin_explore.php')?'active':''; ?>">🔍 景點管理修改</a></li>
            <li><a href="admin_approve.php" class="<?php echo ($current_page=='admin_approve.php')?'active':''; ?>">📥 審核私藏提案</a></li>
            <li><a href="admin_reviews.php" class="<?php echo ($current_page=='admin_reviews.php')?'active':''; ?>">💬 違規評論處理</a></li>
            <li><a href="admin_stats.php" class="<?php echo ($current_page=='admin_stats.php')?'active':''; ?>">📈 熱門景點圖表</a></li>
        <?php else: ?>
            <li>
                <a href="explore.php" class="<?php echo ($current_page=='explore.php' || $current_page=='category.php')?'active':''; ?>">景點探索</a>
                <?php if($current_page == 'explore.php' || $current_page == 'category.php'): ?>
                    <ul class="submenu">
                        <li><a href="category.php?cat=食">🍕 在地美食</a></li>
                        <li><a href="category.php?cat=衣">👗 潮流服飾</a></li>
                        <li><a href="category.php?cat=住">🏨 精選住宿</a></li>
                        <li><a href="category.php?cat=行">🚲 交通大眾</a></li>
                        <li><a href="category.php?cat=育樂">🎡 育樂玩耍</a></li>
                    </ul>
                <?php endif; ?>
            </li>
            <li><a href="favorites.php" class="<?php echo ($current_page=='favorites.php')?'active':''; ?>">收藏夾</a></li>
            <li><a href="recommend.php" class="<?php echo ($current_page=='recommend.php')?'active':''; ?>">私藏推薦</a></li>
            <li><a href="reviews.php" class="<?php echo ($current_page=='reviews.php')?'active':''; ?>">查看評論</a></li>
            <li><a href="itinerary.php" class="<?php echo ($current_page=='itinerary.php')?'active':''; ?>">行程規劃</a></li>
        <?php endif; ?>
    </ul>
    
    <!-- ======= 免責聲明區塊開始 ======= -->
    <div class="disclaimer-box" style="margin: 20px 15px; padding: 12px; background-color: #f8f9fa; border-left: 4px solid #2e6f40; border-radius: 4px;">
        <p style="margin: 0; font-size: 11px; color: #6c757d; line-height: 1.5; text-align: justify;">
        <strong style="color: #2e6f40; font-size: 12px; display: block; margin-bottom: 4px;">⚠️ 免責聲明</strong>
        本網站為學術教育、大學專題作業用，無任何商業營利行為。<strong>網頁內所使用之照片皆為AI生成之意象示意圖，並非實際街景照片</strong>；部分文字引自網路公開資源，著作權屬原作者所有，課程結束後本站將會下架，感謝。。
        </p>
    </div>
    <!-- ======= 免責聲明區塊結束 ======= -->
    <div style="position: absolute; bottom: 30px; left: 20px; right: 20px; background: rgba(0,0,0,0.05); padding: 15px; border-radius: 12px; text-align: center;">
        <span style="font-size: 14px; font-weight: bold; display: block; margin-bottom: 5px;">帳號: <?php echo htmlspecialchars($username); ?></span>
        <span style="font-size: 12px; color:#718096; display: block; margin-bottom: 10px;">身分: <?php echo ($user_type=='admin')?'管理員':'一般使用者'; ?></span>
        <a href="logout.php" style="color: #e74c3c; text-decoration: none; font-size: 14px; font-weight: bold;">登出系統</a>
    </div>
</div>