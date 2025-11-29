// Logs page JavaScript

// Module variables
let currentPage = 1;

// Expose functions to global scope
// Removed: window.updateAutoRefreshTime = updateAutoRefreshTime;

// Expose logs functions to global scope
window.loadLogs = loadLogs;
window.applyFilters = applyFilters;
window.resetFilters = resetFilters;
window.changePage = changePage;

// Initialize on page load
function initializePage() {
    // Removed: loadAutoRefreshSettings();

    // Load sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.querySelector('.sidebar').classList.add('collapsed');
        document.querySelector('.main-content').classList.add('sidebar-collapsed');
    }

    // Load logs
    loadLogs();
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePage);
} else {
    initializePage();
}

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
            <div class="stat-card-header">
                <div class="stat-icon blue">📊</div>
                <div class="stat-content">
                    <div class="stat-label">Tổng số logs</div>
                    <div class="stat-value blue">${total}</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon green">✅</div>
                <div class="stat-content">
                    <div class="stat-label">Insert</div>
                    <div class="stat-value green">${inserts}</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon orange">🔄</div>
                <div class="stat-content">
                    <div class="stat-label">Update</div>
                    <div class="stat-value orange">${updates}</div>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-icon red">❌</div>
                <div class="stat-content">
                    <div class="stat-label">Delete</div>
                    <div class="stat-value red">${deletes}</div>
                </div>
            </div>
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

// Load logs on page load
loadLogs();