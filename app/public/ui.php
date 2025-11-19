<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUFLIT Distributed Database API</title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #64748b;
            --success: #10b981;
            --danger: #ef4444;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --border: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .header h1 {
            color: var(--primary);
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: var(--secondary);
            font-size: 1.1rem;
        }

        .tabs {
            display: flex;
            margin-bottom: 2rem;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .tab-btn {
            flex: 1;
            padding: 1rem;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-right: 1px solid var(--border);
        }

        .tab-btn:last-child {
            border-right: none;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
        }

        .tab-btn:hover:not(.active) {
            background: var(--border);
        }

        .tab-content {
            display: none;
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 2rem;
        }

        .tab-content.active {
            display: block;
        }

        .module-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-title::before {
            content: '📚';
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: flex;
            gap: 1rem;
            align-items: end;
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn::before {
            font-size: 1.2rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary::before {
            content: '🔍';
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success::before {
            content: '➕';
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .result {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f1f5f9;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            font-family: 'Monaco', 'Menlo', monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
            display: none;
        }

        .result.show {
            display: block;
        }

        .result.error {
            border-left-color: var(--danger);
            background: #fef2f2;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .tabs {
                flex-direction: column;
            }

            .tab-btn {
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .form-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-group {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 HUFLIT Distributed Database</h1>
            <p>API Testing Interface - Hệ thống Cơ sở dữ liệu Phân tán</p>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('khoa')">Khoa</button>
            <button class="tab-btn" onclick="showTab('monhoc')">Môn Học</button>
            <button class="tab-btn" onclick="showTab('sinhvien')">Sinh Viên</button>
            <button class="tab-btn" onclick="showTab('ctdaotao')">CT Đào Tạo</button>
            <button class="tab-btn" onclick="showTab('dangky')">Đăng Ký</button>
        </div>

        <!-- Khoa Module -->
        <div id="khoa" class="tab-content active">
            <h2 class="module-title">Quản lý Khoa</h2>
            <div class="form-group">
                <label for="khoa-id">Mã Khoa:</label>
                <input type="text" id="khoa-id" placeholder="Ví dụ: CNTT, NN, LUAT">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('khoa', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-primary" onclick="callAPI('khoa', 'GET', null, 'khoa')">Lấy Theo ID</button>
            </div>
            <div id="khoa-result" class="result"></div>
        </div>

        <!-- MonHoc Module -->
        <div id="monhoc" class="tab-content">
            <h2 class="module-title">Quản lý Môn Học</h2>
            <div class="form-group">
                <label for="monhoc-id">Mã Môn Học:</label>
                <input type="text" id="monhoc-id" placeholder="Ví dụ: MH001, MH002">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('monhoc', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-primary" onclick="callAPI('monhoc', 'GET', null, 'monhoc')">Lấy Theo ID</button>
            </div>
            <div id="monhoc-result" class="result"></div>
        </div>

        <!-- SinhVien Module -->
        <div id="sinhvien" class="tab-content">
            <h2 class="module-title">Quản lý Sinh Viên</h2>
            <div class="form-group">
                <label for="sinhvien-id">Mã Sinh Viên:</label>
                <input type="text" id="sinhvien-id" placeholder="Ví dụ: SV001, SV002">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('sinhvien', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-primary" onclick="callAPI('sinhvien', 'GET', null, 'sinhvien')">Lấy Theo ID</button>
            </div>
            <div id="sinhvien-result" class="result"></div>
        </div>

        <!-- CTDaoTao Module -->
        <div id="ctdaotao" class="tab-content">
            <h2 class="module-title">Chương Trình Đào Tạo</h2>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('ctdaotao', 'GET')">Lấy Tất Cả</button>
            </div>
            <div id="ctdaotao-result" class="result"></div>
        </div>

        <!-- DangKy Module -->
        <div id="dangky" class="tab-content">
            <h2 class="module-title">Đăng Ký Học Phần</h2>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="dangky-masv">Mã Sinh Viên:</label>
                    <input type="text" id="dangky-masv" placeholder="Ví dụ: SV001">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="dangky-mamon">Mã Môn Học:</label>
                    <input type="text" id="dangky-mamon" placeholder="Ví dụ: MH001">
                </div>
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('dangky', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('dangky', 'GET', null, 'dangky', 'masv')">Xem Môn Học Đã Đăng Ký</button>
                <button class="btn btn-success" onclick="callAPI('dangky', 'GET', null, 'dangky', 'mamon')">Xem Sinh Viên Đã Đăng Ký</button>
            </div>
            <div id="dangky-result" class="result"></div>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8080';

        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        async function callAPI(endpoint, method, body = null, module = null, queryType = null) {
            const resultDiv = document.getElementById(`${module || endpoint}-result`);
            if (!resultDiv) return;

            // Show loading
            resultDiv.innerHTML = '<div class="loading"></div> Đang tải...';
            resultDiv.className = 'result show';

            let url = API_BASE + '/' + endpoint;
            const params = new URLSearchParams();

            if (module) {
                if (module === 'dangky') {
                    if (queryType === 'masv') {
                        const masv = document.getElementById('dangky-masv').value.trim();
                        if (masv) {
                            params.append('masv', masv);
                        }
                    } else if (queryType === 'mamon') {
                        const mamon = document.getElementById('dangky-mamon').value.trim();
                        if (mamon) {
                            params.append('mamon', mamon);
                        }
                    } else {
                        // Query cả hai nếu có
                        const masv = document.getElementById('dangky-masv').value.trim();
                        const mamon = document.getElementById('dangky-mamon').value.trim();
                        if (masv && mamon) {
                            params.append('masv', masv);
                            params.append('mamon', mamon);
                        }
                    }
                } else {
                    const idInput = document.getElementById(`${module}-id`);
                    if (idInput && idInput.value.trim()) {
                        params.append('id', idInput.value.trim());
                    }
                }
            }

            if (params.toString()) {
                url += '?' + params.toString();
            }

            const options = { method };
            if (body) {
                options.headers = { 'Content-Type': 'application/json' };
                options.body = JSON.stringify(body);
            }

            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                const data = await response.json();

                // Calculate count
                let countText = '';
                if (Array.isArray(data)) {
                    const count = data.length;
                    countText = `<div>📊 Tổng số: <strong>${count}</strong> ${count === 1 ? 'bản ghi' : 'bản ghi'}</div>`;
                } else if (data && typeof data === 'object') {
                    countText = `<div>📄 1 bản ghi</div>`;
                }

                // Format JSON nicely
                resultDiv.innerHTML = `${countText}<pre>${JSON.stringify(data, null, 2)}</pre>`;
                resultDiv.className = 'result show';

            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}<br><br>
                <strong>Khắc phục:</strong><br>
                • Kiểm tra container API đang chạy<br>
                • Kiểm tra kết nối mạng<br>
                • Kiểm tra endpoint và tham số<br>
                • Kiểm tra kết nối database`;
                resultDiv.className = 'result show error';
            }
        }

        // Auto-focus first input on tab change
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                setTimeout(() => {
                    const activeTab = document.querySelector('.tab-content.active');
                    const firstInput = activeTab.querySelector('input');
                    if (firstInput) firstInput.focus();
                }, 100);
            });
        });
    </script>
</body>
</html>