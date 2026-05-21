<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>垃圾郵件寄送系統 - 科技主控台</title>
    <style>
        /* 明亮科技風核心視覺 */
        body { 
            font-family: 'Segoe UI', 'Microsoft JhengHei', sans-serif; 
            max-width: 650px; 
            margin: 40px auto; 
            padding: 20px; 
            background-color: #f4f7fc; /* 乾淨的科技淺藍灰背景 */
            color: #333d47; 
        }
        
        /* 科技藍主標題 */
        h2 {
            text-align: center;
            color: #0066cc;
            text-shadow: 0 2px 4px rgba(0, 102, 204, 0.1);
            font-size: 26px;
            letter-spacing: 1px;
            margin-bottom: 30px;
            font-weight: 700;
        }

        /* 區塊包裝：明亮流線型面板 */
        .section { 
            background: #ffffff; 
            margin-bottom: 25px; 
            padding: 25px; 
            border-radius: 10px; 
            border-top: 4px solid #00d2ff; /* 頂部高亮科技藍線條 */
            box-shadow: 0 4px 20px rgba(160, 175, 195, 0.15); /* 輕盈的陰影 */
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .section:hover {
            transform: translateY(-2px); /* 滑過時有微浮空感 */
            box-shadow: 0 6px 25px rgba(160, 175, 195, 0.25);
        }

        h3 { 
            color: #0056b3; 
            margin-top: 0; 
            font-size: 18px;
            border-bottom: 1px solid #eef2f5;
            padding-bottom: 10px;
        }

        label { 
            display: block; 
            margin-top: 15px; 
            font-weight: bold; 
            color: #5a6b7c; 
            font-size: 14px;
        }

        /* 輸入框：現代化極簡設計 */
        input[type="text"], input[type="email"], input[type="number"], textarea, select { 
            width: 100%; 
            padding: 10px 12px; 
            margin-top: 6px; 
            box-sizing: border-box; 
            background-color: #fcfdfe;
            border: 1px solid #cdd7e2; 
            border-radius: 6px; 
            color: #333;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        /* 輸入框聚焦時亮藍邊框 */
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
            background-color: #fff;
        }

        /* 科技感動能漸層按鈕 */
        button { 
            background: linear-gradient(135deg, #007bff 0%, #00d2ff 100%); 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            margin-top: 20px; 
            cursor: pointer; 
            border-radius: 6px; 
            font-size: 16px; 
            font-weight: bold;
            width: 100%;
            letter-spacing: 1px;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            transition: all 0.2s ease;
        }

        button:hover { 
            filter: brightness(1.05);
            box-shadow: 0 6px 15px rgba(0, 123, 255, 0.4);
        }

        button:active {
            transform: scale(0.99);
        }

        /* 圖片即時預覽視窗樣式 */
        .preview-container {
            margin-top: 10px;
            padding: 10px;
            border: 1px dashed #007bff;
            border-radius: 6px;
            background-color: #f8faff;
            display: none;
            text-align: center;
        }
        .preview-container img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* 進度條外框：簡約清爽 */
        #progress-container { 
            display: none; 
            margin-top: 25px; 
            padding: 18px; 
            background: #f8fafc; 
            border-radius: 8px; 
            border: 1px solid #e2e8f0;
        }

        #progress-status {
            font-size: 14px;
            font-weight: bold;
            color: #2b6cb0; 
        }

        .progress-bar { 
            width: 100%; 
            background-color: #edf2f7; 
            border-radius: 20px; 
            overflow: hidden; 
            margin-top: 10px; 
            border: 1px solid #e2e8f0;
        }

        /* 進度條填充：藍綠色動態漸層 */
        .progress-fill { 
            height: 22px; 
            width: 0%; 
            background: linear-gradient(90deg, #00c6ff 0%, #0072ff 100%); 
            text-align: center; 
            color: white; 
            line-height: 22px; 
            font-weight: bold; 
            font-size: 12px;
            transition: width 0.3s ease; 
        }
    </style>
</head>
<body>

    <h2>封鎖型 - 垃圾郵件寄送系統</h2>

    <div class="section">
        <h3>A. 建構資料庫 (新增 Email)</h3>
        <form action="add_email.php" method="POST">
            <label for="email">請輸入 Email 位址:</label>
            <input type="email" id="email" name="email" required placeholder="example@domain.com">
            <button type="submit">加入資料庫</button>
        </form>
    </div>

    <div class="section">
        <h3>B. 寄信設定介面</h3>
        <form id="emailForm">
            <label for="subject">郵件主旨:</label>
            <input type="text" id="subject" name="subject" required placeholder="請輸入主旨">

            <label for="img_url">插入郵件照片 (請輸入圖片網址):</label>
            <input type="text" id="img_url" placeholder="請貼上網際網路圖片網址 (如 https://.../photo.jpg)" oninput="updateImagePreview()">
            <div id="image-preview-box" class="preview-container">
                <div style="font-size: 12px; color: #007bff; margin-bottom: 5px; font-weight: bold;">📷 郵件動態照片預覽：</div>
                <img id="preview-img" src="" alt="預覽圖">
            </div>

            <label for="content">郵件內容:</label>
            <textarea id="content" name="content" rows="5" required placeholder="請輸入信件內容..."></textarea>

            <label for="send_mode">寄送模式:</label>
            <select id="send_mode" name="send_mode" onchange="toggleRandomInput()">
                <option value="all">全部寄送</option>
                <option value="random">隨機寄送幾筆</option>
            </select>

            <div id="random_count_div" style="display: none;">
                <label for="random_count">隨機抽取筆數:</label>
                <input type="number" id="random_count" name="random_count" min="1" value="5">
            </div>

            <label for="interval">寄送間隔時間 (秒):</label>
            <input type="number" id="interval" name="interval" min="0" value="2" required>

            <button type="button" onclick="startSendingProcess()">開始群發郵件</button>
        </form>

        <div id="progress-container">
            <span id="progress-status">準備中...</span>
            <div class="progress-bar">
                <div id="progress-fill" class="progress-fill">0%</div>
            </div>
        </div>
    </div>

    <script>
        // 控制隨機筆數輸入框的顯示與隱藏
        function toggleRandomInput() {
            const mode = document.getElementById('send_mode').value;
            document.getElementById('random_count_div').style.display = (mode === 'random') ? 'block' : 'none';
        }

        // 📸 即時監聽並更新照片預覽
        function updateImagePreview() {
            const url = document.getElementById('img_url').value.trim();
            const previewBox = document.getElementById('image-preview-box');
            const previewImg = document.getElementById('preview-img');

            if (url) {
                previewImg.src = url;
                previewBox.style.display = 'block';
            } else {
                previewBox.style.display = 'none';
                previewImg.src = '';
            }
        }

        // 核心非同步群發邏輯
        async function startSendingProcess() {
            const subject = document.getElementById('subject').value;
            let content = document.getElementById('content').value;
            const imgUrl = document.getElementById('img_url').value.trim();
            const mode = document.getElementById('send_mode').value;
            const randomCount = document.getElementById('random_count').value;
            const interval = parseInt(document.getElementById('interval').value) * 1000; // 轉為毫秒

            if (!subject || !content) {
                alert('請填寫郵件主旨與內容！');
                return;
            }

            // 📸 如果使用者有填寫圖片網址，動態將 <img> 標籤組合進郵件內容中（前置或後置皆可）
            if (imgUrl) {
                // 將圖片放在內文上方，並加上簡單的 HTML 置中排版
                content = `<div style="text-align:center; margin-bottom:15px;"><img src="${imgUrl}" style="max-width:100%; border-radius:8px;" /></div>` + content;
            }

            // 顯示進度條
            document.getElementById('progress-container').style.display = 'block';
            document.getElementById('progress-status').innerText = '正在向撈取資料庫名單...';

            let fetchUrl = `get_targets.php?mode=${mode}`;
            if (mode === 'random') fetchUrl += `&limit=${randomCount}`;

            try {
                let response = await fetch(fetchUrl);
                let emailList = await response.json();

                if (emailList.length === 0) {
                    document.getElementById('progress-status').innerText = '失敗：資料庫中沒有符合的名單！';
                    return;
                }

                let total = emailList.length;
                let current = 0;

                for (let i = 0; i < total; i++) {
                    let targetEmail = emailList[i];
                    document.getElementById('progress-status').innerText = `正在寄送給 (${i + 1}/${total}): ${targetEmail}`;

                    let formData = new FormData();
                    formData.append('email', targetEmail);
                    formData.append('subject', subject);
                    formData.append('content', content); // 此處的 content 已被包裝為含圖片的 HTML

                    // 打發信 API
                    await fetch('send_action.php', { method: 'POST', body: formData });

                    current++;
                    let percent = Math.round((current / total) * 100);
                    document.getElementById('progress-fill').style.width = percent + '%';
                    document.getElementById('progress-fill').innerText = percent + '%';

                    if (i < total - 1 && interval > 0) {
                        document.getElementById('progress-status').innerText = `冷卻中... 預計 ${interval/1000} 秒後發送下一筆`;
                        await new Promise(resolve => setTimeout(resolve, interval));
                    }
                }

                document.getElementById('progress-status').innerText = '🎉 任務完成！所有郵件已寄送完畢。';

            } catch (err) {
                console.error(err);
                document.getElementById('progress-status').innerText = '發生非預期錯誤，請檢查主機狀態。';
            }
        }
    </script>
</body>
</html>