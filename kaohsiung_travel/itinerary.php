<?php
session_start();
require_once 'includes/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// 取得全站所有合法景點，做為行程規劃下拉式選單的核心資料庫依據
$all_spots = $pdo->query("SELECT id, name, category FROM attractions WHERE is_approved = 1 ORDER BY category, id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>行程規劃 - 高雄去哪玩</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .days-nav-bar { display: flex; gap: 15px; margin-bottom: 25px; }
        .day-tab-btn {
            padding: 12px 25px; border: none; background: #fff; border-radius: 8px;
            font-weight: bold; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.05); transition: background 0.2s;
        }
        .day-tab-btn.active { background: #2c5e3b; color: #fff; }
        
        .itinerary-workspace { display: flex; gap: 30px; align-items: start; }
        .builder-left { flex: 7; background: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .summary-right { flex: 3; background: rgba(255,255,255,0.8); backdrop-filter:blur(5px); padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border:1px solid rgba(255,255,255,0.5); }
        
        .day-block-section { margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px dashed #e2e8f0; }
        .day-block-section h3 { margin-top: 0; color: #2c5e3b; }
        .spot-row-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        
        /* 拖拉區塊樣式宣告 */
        .draggable-summary-spot {
            background: #fff; padding: 12px; border-radius: 8px; margin-bottom: 8px;
            border: 1px solid #cbd5e0; cursor: move; font-weight: 500; font-size: 14px;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02); transition: background 0.2s;
        }
        .draggable-summary-spot:hover { background: #f7fafc; }
        .draggable-summary-spot.dragging { opacity: 0.4; background: #edf2f7; }
        .drop-zone-bucket { min-height: 50px; background: rgba(0,0,0,0.02); border-radius: 8px; padding: 5px; }
    </style>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-wrapper">
        <h2>5. 旅遊行程規劃 📅</h2>
        <p style="color:#666;">可自由切換天數規劃，填入想去的目的地，並在右側利用拖拉手感重新變更排序：</p>
        
        <div class="days-nav-bar">
            <button class="day-tab-btn active" onclick="switchDayDuration(1, this)">一天 (One Day)</button>
            <button class="day-tab-btn" onclick="switchDayDuration(3, this)">三天 (Three Days)</button>
            <button class="day-tab-btn" onclick="switchDayDuration(5, this)">五天 (Five Days)</button>
        </div>

        <div class="itinerary-workspace">
            <div class="builder-left">
                <form id="itineraryForm" onsubmit="event.preventDefault(); renderSummary();">
                    <div id="dynamic-days-injection-point">
                        </div>
                    <div style="margin-top: 20px;">
                        <button type="submit" style="padding: 12px 30px; border:none; background:#2c5e3b; color:#fff; font-weight:bold; border-radius:8px; cursor:pointer;">生成右側規劃表</button>
                        <button type="button" onclick="resetItinerary()" style="padding: 12px 30px; border:1px solid #ccc; background:#fff; border-radius:8px; cursor:pointer;">重置表格</button>
                    </div>
                </form>
            </div>
            
            <div class="summary-right">
                <h3 style="margin-top:0; color:#2d3748; border-bottom:2px solid #2c5e3b; padding-bottom:8px;">行程表結果 (Summary)</h3>
                <div id="summary-drag-pool">
                    </div>
            </div>
        </div>
    </div>

    <script>
        // 將 PHP 的大清單編譯成前端 JavaScript 物件陣列
        const availableSpots = <?php echo json_encode($all_spots); ?>;
        let currentSelectedDays = 1;

        // 生成左側指定天數的 10 格下拉選單矩陣
        function generateFormMatrix(daysCount) {
            const container = document.getElementById('dynamic-days-injection-point');
            container.innerHTML = '';
            
            for (let d = 1; d <= daysCount; d++) {
                let section = document.createElement('div');
                section.className = 'day-block-section';
                section.innerHTML = `<h3>第 ${d} 天行程表</h3>`;
                
                // 每日精準配給 10 個行程格位
                for (let slot = 1; slot <= 10; slot++) {
                    let spotOptionsHTML = `<option value="">-- 點擊選取安排景點 (${slot}) --</option>`;
                    availableSpots.forEach(spot => {
                        spotOptionsHTML += `<option value="${spot.name}">[${spot.category}] ${spot.name}</option>`;
                    });
                    
                    section.innerHTML += `
                        <div class="spot-row-item">
                            <span style="font-size:13px; color:#718096; width:20px;">${slot}</span>
                            <select class="itinerary-select-node" data-day="${d}" style="padding:6px; border-radius:6px; flex:1;">
                                ${spotOptionsHTML}
                            </select>
                        </div>
                    `;
                }
                container.appendChild(section);
            }
        }

        function switchDayDuration(days, btn) {
            document.querySelectorAll('.day-tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentSelectedDays = days;
            generateFormMatrix(days);
            document.getElementById('summary-drag-pool').innerHTML = ''; // 清空右側
        }

        // 彙整左側所選項目，渲染至右側的 HTML5 原生拖拉清單中
        function renderSummary() {
            const pool = document.getElementById('summary-drag-pool');
            pool.innerHTML = '';
            
            for (let d = 1; d <= currentSelectedDays; d++) {
                let dayContainer = document.createElement('div');
                dayContainer.style.marginBottom = '20px';
                dayContainer.innerHTML = `<h4 style="margin:5px 0; color:#e67e22;">📍 第 ${d} 天</h4>`;
                
                let bucket = document.createElement('div');
                bucket.className = 'drop-zone-bucket';
                bucket.dataset.targetDay = d;
                
                // 抓取左邊對應這天有選景點的所有選單
                const selects = document.querySelectorAll(`.itinerary-select-node[data-day="${d}"]`);
                let addedAny = false;
                
                selects.forEach(sel => {
                    if (sel.value) {
                        addedAny = true;
                        let item = document.createElement('div');
                        item.className = 'draggable-summary-spot';
                        item.setAttribute('draggable', 'true');
                        item.innerHTML = `<span>${sel.value}</span> <span style="font-size:11px; color:#a0aec0;">↕ 拖曳</span>`;
                        bucket.appendChild(item);
                    }
                });
                
                if(!addedAny) {
                    bucket.innerHTML = `<p style="font-size:12px; color:#a0aec0; margin:5px; text-align:center;">此日尚無編排景點</p>`;
                }
                
                dayContainer.appendChild(bucket);
                pool.appendChild(dayContainer);
            }
            initDragAndDropLogic();
        }

        // HTML5 原生無套件無瑕疵 Drag & Drop 控制核心
        function initDragAndDropLogic() {
            const draggables = document.querySelectorAll('.draggable-summary-spot');
            const buckets = document.querySelectorAll('.drop-zone-bucket');

            draggables.forEach(draggable => {
                draggable.addEventListener('dragstart', () => { draggable.classList.add('dragging'); });
                draggable.addEventListener('dragend', () => { draggable.classList.remove('dragging'); });
            });

            buckets.forEach(bucket => {
                bucket.addEventListener('dragover', e => {
                    e.preventDefault(); // 允許放置
                    const afterElement = getDragAfterElement(bucket, e.clientY);
                    const draggingItem = document.querySelector('.dragging');
                    
                    // 如果原本提示文字還在，先移除
                    const tip = bucket.querySelector('p');
                    if(tip) tip.remove();
                    
                    if (afterElement == null) {
                        bucket.appendChild(draggingItem);
                    } else {
                        bucket.insertBefore(draggingItem, afterElement);
                    }
                });
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('.draggable-summary-spot:not(.dragging)')];
            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function resetItinerary() {
            if(confirm('確定要清空所有填寫的表格嗎？')) {
                generateFormMatrix(currentSelectedDays);
                document.getElementById('summary-drag-pool').innerHTML = '';
            }
        }

        // 初始化自動執行 1 天規格建構
        generateFormMatrix(1);
    </script>
</body>
</html>