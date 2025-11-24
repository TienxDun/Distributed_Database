<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - HUFLIT MongoDB</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Audit Logs</h1>
            <p>Lịch sử thay đổi dữ liệu - HUFLIT Distributed Database</p>
        </div>

        <div class="stats-nav" style="margin-bottom: 2rem;">
            <a href="ui.php">← Về trang chính</a>
            <a href="stats.php">📈 Statistics</a>
        </div>

        <div class="logs-filters">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>🗂️ Bảng:</label>
                    <select id="filterTable">
                        <option value="">Tất cả</option>
                        <option value="Khoa">Khoa</option>
                        <option value="MonHoc">MonHoc</option>
                        <option value="SinhVien">SinhVien</option>
                        <option value="CTDaoTao">CTDaoTao</option>
                        <option value="DangKy">DangKy</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>⚡ Thao tác:</label>
                    <select id="filterOperation">
                        <option value="">Tất cả</option>
                        <option value="INSERT">INSERT</option>
                        <option value="UPDATE">UPDATE</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>🗺️ Site:</label>
                    <select id="filterSite">
                        <option value="">Tất cả</option>
                        <option value="Site_A">Site A</option>
                        <option value="Site_B">Site B</option>
                        <option value="Site_C">Site C</option>
                        <option value="Global">Global</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>📅 Từ ngày:</label>
                    <input type="date" id="filterDateFrom">
                </div>

                <div class="filter-group">
                    <label>📅 Đến ngày:</label>
                    <input type="date" id="filterDateTo">
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn btn-primary" onclick="applyFilters()">🔍 Lọc dữ liệu</button>
                <button class="btn btn-secondary" onclick="resetFilters()">🔄 Đặt lại</button>
            </div>
        </div>

        <div class="auto-refresh">
            <input type="checkbox" id="autoRefresh" onchange="toggleAutoRefresh()">
            <label for="autoRefresh">🔄 Tự động làm mới mỗi 30 giây</label>
        </div>

        <div class="stats-grid" id="statsBar"></div>

        <div class="logs-container">
            <div id="logsContent"></div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <script>
        // Load background color from localStorage
        const savedBgColor = localStorage.getItem('bgColor');
        if (savedBgColor) {
            document.body.style.backgroundColor = savedBgColor;
        }

        let currentPage = 1;
        let autoRefreshInterval = null;

        async function loadLogs() {
            const params = new URLSearchParams({
                page: currentPage,
                limit: 20
            });

            const table = document.getElementById('filterTable').value;
            const operation = document.getElementById('filterOperation').value;
            const site = document.getElementById('filterSite').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;

            if (table) params.append('table', table);
            if (operation) params.append('operation', operation);
            if (site) params.append('site', site);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);

            try {
                document.getElementById('logsContent').innerHTML = '<div class="loading">Đang tải dữ liệu</div>';
                
                const response = await fetch(`http://localhost:8080/logs?${params}`);
                const result = await response.json();

                if (result.success) {
                    displayLogs(result.data);
                    updatePagination(result.page);
                    updateStats(result.data);
                } else {
                    document.getElementById('logsContent').innerHTML = `<div class="no-data">❌ Lỗi: ${result.error}</div>`;
                }
            } catch (error) {
                document.getElementById('logsContent').innerHTML = `<div class="no-data">❌ Không thể kết nối đến server</div>`;
            }
        }

        function displayLogs(logs) {
            if (logs.length === 0) {
                document.getElementById('logsContent').innerHTML = `
                    <div class="no-data">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3>Không có dữ liệu</h3>
                        <p>Chưa có log nào phù hợp với bộ lọc</p>
                    </div>
                `;
                return;
            }

            let html = `
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>⏰ Thời gian</th>
                            <th>🗂️ Bảng</th>
                            <th>⚡ Thao tác</th>
                            <th>🗺️ Site</th>
                            <th>📝 Dữ liệu mới</th>
                            <th>📄 Dữ liệu cũ</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            logs.forEach(log => {
                const operationClass = log.operation.toLowerCase();
                html += `
                    <tr>
                        <td><strong>${log.timestamp}</strong></td>
                        <td><span class="badge badge-table">${log.table}</span></td>
                        <td><span class="badge badge-${operationClass}">${log.operation}</span></td>
                        <td><span class="badge badge-site">${log.site}</span></td>
                        <td><div class="data-preview">${JSON.stringify(log.data, null, 2)}</div></td>
                        <td>${log.old_data ? `<div class="data-preview">${JSON.stringify(log.old_data, null, 2)}</div>` : '<em style="color: #94a3b8;">Không có</em>'}</td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
            `;

            document.getElementById('logsContent').innerHTML = html;
        }

        function updatePagination(page) {
            const html = `
                <button onclick="changePage(${page - 1})" ${page <= 1 ? 'disabled' : ''}>← Trang trước</button>
                <span>Trang ${page}</span>
                <button onclick="changePage(${page + 1})">Trang sau →</button>
            `;
            document.getElementById('pagination').innerHTML = html;
        }

        function updateStats(logs) {
            const total = logs.length;
            const inserts = logs.filter(l => l.operation === 'INSERT').length;
            const updates = logs.filter(l => l.operation === 'UPDATE').length;
            const deletes = logs.filter(l => l.operation === 'DELETE').length;

            document.getElementById('statsBar').innerHTML = `
                <div class="stat-card">
                    <h3>${total}</h3>
                    <p>📊 Tổng số logs</p>
                </div>
                <div class="stat-card">
                    <h3>${inserts}</h3>
                    <p>✅ INSERT</p>
                </div>
                <div class="stat-card">
                    <h3>${updates}</h3>
                    <p>🔄 UPDATE</p>
                </div>
                <div class="stat-card">
                    <h3>${deletes}</h3>
                    <p>❌ DELETE</p>
                </div>
            `;
        }

        function changePage(page) {
            if (page < 1) return;
            currentPage = page;
            loadLogs();
        }

        function applyFilters() {
            currentPage = 1;
            loadLogs();
        }

        function resetFilters() {
            document.getElementById('filterTable').value = '';
            document.getElementById('filterOperation').value = '';
            document.getElementById('filterSite').value = '';
            document.getElementById('filterDateFrom').value = '';
            document.getElementById('filterDateTo').value = '';
            currentPage = 1;
            loadLogs();
        }

        function toggleAutoRefresh() {
            const checkbox = document.getElementById('autoRefresh');
            if (checkbox.checked) {
                autoRefreshInterval = setInterval(loadLogs, 30000);
            } else {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                }
            }
        }

        // Load logs on page load
        loadLogs();
    </script>
</body>
</html>
