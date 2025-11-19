<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>API Tester</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .endpoint { margin: 10px 0; }
        button { margin: 5px; }
        #result { white-space: pre-wrap; background: #f4f4f4; padding: 10px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <h1>API Tester for Global Database</h1>
    <div class="endpoint">
        <h3>Khoa</h3>
        <button onclick="callAPI('/khoa', 'GET')">GET All</button>
        <button onclick="callAPI('/khoa?id=CNTT', 'GET')">GET One</button>
        <button onclick="callAPI('/khoa', 'POST', {MaKhoa: 'NEW', TenKhoa: 'New Khoa'})">POST</button>
    </div>
    <div class="endpoint">
        <h3>MonHoc</h3>
        <button onclick="callAPI('/monhoc', 'GET')">GET All</button>
        <button onclick="callAPI('/monhoc?id=MH001', 'GET')">GET One</button>
    </div>
    <div class="endpoint">
        <h3>SinhVien</h3>
        <button onclick="callAPI('/sinhvien', 'GET')">GET All</button>
        <button onclick="callAPI('/sinhvien?id=SV001', 'GET')">GET One</button>
    </div>
    <div class="endpoint">
        <h3>DangKy</h3>
        <button onclick="callAPI('/dangky', 'GET')">GET All</button>
        <button onclick="callAPI('/dangky?masv=SV001&mamon=MH001', 'GET')">GET One</button>
    </div>
    <div class="endpoint">
        <h3>CTDaoTao</h3>
        <button onclick="callAPI('/ctdaotao', 'GET')">GET All</button>
    </div>
    <h3>Result:</h3>
    <div id="result"></div>

    <script>
        const API_BASE = 'http://localhost:8080'; // API server

        async function callAPI(endpoint, method, body = null) {
            const url = API_BASE + endpoint;
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
                document.getElementById('result').textContent = JSON.stringify(data, null, 2);
            } catch (error) {
                document.getElementById('result').textContent = `Error: ${error.message}\n\nPossible issues:\n- API server is down (check if api_php container is running)\n- Network connection failed\n- Invalid endpoint or method\n- Database connection error\n- CORS issues if accessing from different host`;
            }
        }
    </script>
</body>
</html>