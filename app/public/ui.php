<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUFLIT Distributed Database API</title>
    <link rel="stylesheet" href="styles.css">
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
            <button class="tab-btn" onclick="showTab('global')">Truy Vấn Toàn Cục</button>
        </div>

        <!-- Khoa Module -->
        <div id="khoa" class="tab-content active">
            <h2 class="module-title">Quản lý Khoa</h2>
            <div class="form-group">
                <label for="khoa-id">Mã Khoa:</label>
                <input type="text" id="khoa-id" placeholder="Ví dụ: CNTT, NN, LUAT" onkeydown="if(event.key==='Enter') callAPI('khoa', 'GET', null, 'khoa')">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('khoa', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('khoa', 'GET', null, 'khoa')">Xem theo ID</button>
            </div>
            <div id="khoa-result" class="result"></div>
        </div>

        <!-- MonHoc Module -->
        <div id="monhoc" class="tab-content">
            <h2 class="module-title">Quản lý Môn Học</h2>
            <div class="form-group">
                <label for="monhoc-id">Mã Môn Học:</label>
                <input type="text" id="monhoc-id" placeholder="Ví dụ: MH001, MH002" onkeydown="if(event.key==='Enter') callAPI('monhoc', 'GET', null, 'monhoc')">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('monhoc', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('monhoc', 'GET', null, 'monhoc')">Xem theo ID</button>
            </div>
            <div id="monhoc-result" class="result"></div>
        </div>

        <!-- SinhVien Module -->
        <div id="sinhvien" class="tab-content">
            <h2 class="module-title">Quản lý Sinh Viên</h2>
            <div class="form-group">
                <label for="sinhvien-id">Mã Sinh Viên:</label>
                <input type="text" id="sinhvien-id" placeholder="Ví dụ: SV001, SV002" onkeydown="if(event.key==='Enter') callAPI('sinhvien', 'GET', null, 'sinhvien')">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('sinhvien', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('sinhvien', 'GET', null, 'sinhvien')">Xem theo ID</button>
            </div>
            <div id="sinhvien-result" class="result"></div>
        </div>

        <!-- CTDaoTao Module -->
        <div id="ctdaotao" class="tab-content">
            <h2 class="module-title">Chương Trình Đào Tạo</h2>
            <div class="form-group">
                <label for="ctdaotao-makhoa">Mã Khoa:</label>
                <input type="text" id="ctdaotao-makhoa" placeholder="Ví dụ: CNTT, DLKS" onkeydown="if(event.key==='Enter') callAPI('ctdaotao', 'GET', null, 'ctdaotao', 'subjects')">
            </div>
            <div class="form-group">
                <label for="ctdaotao-khoahoc">Khóa Học:</label>
                <input type="number" id="ctdaotao-khoahoc" placeholder="Ví dụ: 2018, 2019" onkeydown="if(event.key==='Enter') callAPI('ctdaotao', 'GET', null, 'ctdaotao', 'subjects')">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('ctdaotao', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('ctdaotao', 'GET', null, 'ctdaotao', 'subjects')">Xem Môn Học</button>
            </div>
            <div id="ctdaotao-result" class="result"></div>
        </div>

        <!-- DangKy Module -->
        <div id="dangky" class="tab-content">
            <h2 class="module-title">Đăng Ký Học Phần</h2>
            <div class="form-group">
                <label for="dangky-masv">Mã Sinh Viên:</label>
                <input type="text" id="dangky-masv" placeholder="Ví dụ: SV001, SV002, SV003..." required onkeydown="if(event.key==='Enter') callAPI('dangky', 'GET', null, 'dangky', 'masv')">
            </div>
            <div class="btn-group">
                <button class="btn btn-primary" onclick="callAPI('dangky', 'GET')">Lấy Tất Cả</button>
                <button class="btn btn-success" onclick="callAPI('dangky', 'GET', null, 'dangky', 'masv')">Xem Môn Học Đã Đăng Ký</button>
            </div>
            <div id="dangky-result" class="result"></div>
        </div>

        <!-- Global Queries Module -->
        <div id="global" class="tab-content">
            <h2 class="module-title">Truy Vấn Toàn Cục</h2>
            <div class="query-grid">
                <!-- Form 1 -->
                <div class="query-card">
                    <h3>Các môn học sinh viên đã học và đạt từ điểm 5 trở lên</h3>
                    <div class="form-group">
                        <label for="global-masv-1">Mã Sinh Viên:</label>
                        <input type="text" id="global-masv-1" placeholder="Ví dụ: SV001" onkeydown="if(event.key==='Enter') callAPI('global', 'GET', null, 'global', '1')">
                    </div>
                    <button class="btn btn-primary" onclick="callAPI('global', 'GET', null, 'global', '1')">Truy Vấn</button>
                    <div id="global-result-1" class="result"></div>
                </div>

                <!-- Form 2 -->
                <div class="query-card">
                    <h3>Các khóa học của một khoa</h3>
                    <div class="form-group">
                        <label for="global-query-2">Tên Khoa hoặc Mã Khoa:</label>
                        <input type="text" id="global-query-2" placeholder="Ví dụ: Công nghệ thông tin hoặc CNTT" onkeydown="if(event.key==='Enter') callAPI('global', 'GET', null, 'global', '2')">
                    </div>
                    <button class="btn btn-primary" onclick="callAPI('global', 'GET', null, 'global', '2')">Truy Vấn</button>
                    <div id="global-result-2" class="result"></div>
                </div>

                <!-- Form 3 -->
                <div class="query-card">
                    <h3>Các môn học bắt buộc của sinh viên</h3>
                    <div class="form-group">
                        <label for="global-masv-3">Mã Sinh Viên:</label>
                        <input type="text" id="global-masv-3" placeholder="Ví dụ: SV001" onkeydown="if(event.key==='Enter') callAPI('global', 'GET', null, 'global', '3')">
                    </div>
                    <button class="btn btn-primary" onclick="callAPI('global', 'GET', null, 'global', '3')">Truy Vấn</button>
                    <div id="global-result-3" class="result"></div>
                </div>

                <!-- Form 4 -->
                <div class="query-card">
                    <h3>Danh sách sinh viên đủ điều kiện tốt nghiệp</h3>
                    <p class="info-text">Sinh viên đã hoàn thành tất cả môn trong CTDT và đạt điểm ≥5.</p>
                    <button class="btn btn-primary" onclick="callAPI('global', 'GET', null, 'global', '4')">Truy Vấn</button>
                    <div id="global-result-4" class="result"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8080';

        function createTable(data) {
            if (!Array.isArray(data)) {
                if (typeof data === 'object' && data !== null && !Array.isArray(data)) {
                    // Single object
                    let table = '<table><thead><tr><th>Thuộc tính</th><th>Giá trị</th></tr></thead><tbody>';
                    for (let key in data) {
                        table += `<tr><td>${key}</td><td>${data[key]}</td></tr>`;
                    }
                    table += '</tbody></table>';
                    return table;
                } else {
                    return '<p>Không có dữ liệu hoặc định dạng không hỗ trợ</p>';
                }
            }
            if (data.length === 0) {
                return '<p>Không có dữ liệu</p>';
            }
            // Array of objects
            const headers = Object.keys(data[0]);
            let table = '<table><thead><tr>';
            headers.forEach(h => table += `<th>${h}</th>`);
            table += '</tr></thead><tbody>';
            data.forEach(row => {
                table += '<tr>';
                headers.forEach(h => table += `<td>${row[h] !== null && row[h] !== undefined ? row[h] : ''}</td>`);
                table += '</tr>';
            });
            table += '</tbody></table>';
            return table;
        }

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
            let resultDivId = `${module || endpoint}-result`;
            if (module === 'global') {
                resultDivId = `global-result-${queryType}`;
            }
            const resultDiv = document.getElementById(resultDivId);
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
                } else if (module === 'ctdaotao') {
                    if (queryType === 'subjects') {
                        const makhoa = document.getElementById('ctdaotao-makhoa').value.trim();
                        const khoahoc = document.getElementById('ctdaotao-khoahoc').value.trim();
                        if (makhoa) params.append('makhoa', makhoa);
                        if (khoahoc) params.append('khoahoc', khoahoc);
                        if (!makhoa && !khoahoc) {
                            alert('Vui lòng nhập ít nhất Mã Khoa hoặc Khóa Học để xem môn học.');
                            resultDiv.innerHTML = '';
                            resultDiv.className = 'result';
                            return;
                        }
                    }
                } else if (module === 'global') {
                    if (queryType === '1') {
                        const masv = document.getElementById('global-masv-1').value.trim();
                        if (masv) {
                            params.append('type', '1');
                            params.append('masv', masv);
                        } else {
                            alert('Vui lòng nhập Mã Sinh Viên');
                            resultDiv.innerHTML = '';
                            resultDiv.className = 'result';
                            return;
                        }
                    } else if (queryType === '2') {
                        const query = document.getElementById('global-query-2').value.trim();
                        if (query) {
                            params.append('type', '2');
                            params.append('query', query);
                        } else {
                            alert('Vui lòng nhập Tên Khoa hoặc Mã Khoa');
                            resultDiv.innerHTML = '';
                            resultDiv.className = 'result';
                            return;
                        }
                    } else if (queryType === '3') {
                        const masv = document.getElementById('global-masv-3').value.trim();
                        if (masv) {
                            params.append('type', '3');
                            params.append('masv', masv);
                        } else {
                            alert('Vui lòng nhập Mã Sinh Viên');
                            resultDiv.innerHTML = '';
                            resultDiv.className = 'result';
                            return;
                        }
                    } else if (queryType === '4') {
                        params.append('type', '4');
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
                    countText = `<div style="background: #e0f2fe; color: var(--text); padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600; font-size: 0.9rem;">📊 Tổng số: <strong>${count}</strong> ${count === 1 ? 'bản ghi' : 'bản ghi'}</div>`;
                } else if (data && typeof data === 'object') {
                    countText = `<div style="background: #e0f2fe; color: var(--text); padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600; font-size: 0.9rem;">📄 1 bản ghi</div>`;
                }

                // Format as table
                resultDiv.innerHTML = `${countText}${createTable(data)}`;
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