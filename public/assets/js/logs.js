import { API_BASE } from './config.js';

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

        const response = await fetch(`${API_BASE}/logs?${params}`);
        const result = await response.json();

        if (result.success) {
            renderLogsData(result.data, result.page);
            updatePagination(result.page, result.totalPages || 1);
            updateStats(result.data);
        } else {
            document.getElementById('logsContent').innerHTML = `<div class="error-state">❌ Lỗi: ${result.error}</div>`;
        }
    } catch (error) {
        document.getElementById('logsContent').innerHTML = `<div class="error-state">❌ Không thể kết nối đến server</div>`;
    }
}

// Utility function to create pagination HTML - can be reused across pages
function createPaginationHTML(currentPage, totalPages) {
    // Don't show pagination if there's only 1 page or no pages
    if (totalPages <= 1) {
        return `<div class="pagination-info">Trang ${currentPage} / ${totalPages}</div>`;
    }

    let html = '<div class="pagination">';

    // Previous button
    if (currentPage > 1) {
        html += `<button class="pagination-btn" onclick="changePage(${currentPage - 1})" title="Trang trước">
                    <i class="fas fa-chevron-left"></i>
                 </button>`;
    } else {
        html += `<button class="pagination-btn" disabled title="Trang đầu">
                    <i class="fas fa-chevron-left"></i>
                 </button>`;
    }

    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);

    // First page + dots if needed
    if (startPage > 1) {
        html += `<button class="pagination-btn" onclick="changePage(1)">1</button>`;
        if (startPage > 2) {
            html += '<span class="pagination-dots">...</span>';
        }
    }

    // Page range
    for (let i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            html += `<button class="pagination-btn active" onclick="changePage(${i})">${i}</button>`;
        } else {
            html += `<button class="pagination-btn" onclick="changePage(${i})">${i}</button>`;
        }
    }

    // Last page + dots if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += '<span class="pagination-dots">...</span>';
        }
        html += `<button class="pagination-btn" onclick="changePage(${totalPages})">${totalPages}</button>`;
    }

    // Next button
    if (currentPage < totalPages) {
        html += `<button class="pagination-btn" onclick="changePage(${currentPage + 1})" title="Trang sau">
                    <i class="fas fa-chevron-right"></i>
                 </button>`;
    } else {
        html += `<button class="pagination-btn" disabled title="Trang cuối">
                    <i class="fas fa-chevron-right"></i>
                 </button>`;
    }

    html += '</div>';

    // Page info
    html += `<div class="pagination-info">
                Trang ${currentPage} / ${totalPages}
             </div>`;

    return html;
}

function updatePagination(page, totalPages = 1) {
    // Use totalPages from API response
    const html = createPaginationHTML(page, totalPages);
    document.getElementById('pagination').innerHTML = html;
}

// Empty state functions
function renderEmptyState() {
    const html = `
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="empty-state-title">Chưa có dữ liệu logs</h3>
            <p class="empty-state-description">
                Vui lòng chọn các bộ lọc bên trên để xem nhật ký hoạt động của hệ thống phân tán.
            </p>
            <div class="empty-state-actions">
                <button class="empty-state-action primary" onclick="document.querySelector('.tab-btn').click()">
                    <i class="fas fa-filter"></i>
                    Áp dụng bộ lọc
                </button>
                <button class="empty-state-action secondary" onclick="loadAllLogs()">
                    <i class="fas fa-list"></i>
                    Xem tất cả
                </button>
            </div>
        </div>
    `;
    document.getElementById('logsContent').innerHTML = html;
    document.getElementById('pagination').innerHTML = '';
}

function renderLogsData(logs, page = 1) {
    let html = '';

    if (logs.length === 0) {
        renderEmptyState();
        return;
    }

    // Table header
    html += `
        <div class="table-responsive">
            <table class="table logs-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Site</th>
                        <th>Operation</th>
                        <th>Table</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
    `;

    // Table rows
    logs.forEach(log => {
        const operationClass = `badge-${log.operation.toLowerCase()}`;
        const siteClass = `site-${log.site.toLowerCase().replace('_', '-')}`;

        html += `
            <tr>
                <td>${new Date(log.timestamp).toLocaleString('vi-VN')}</td>
                <td><span class="site-badge ${siteClass}">${log.site}</span></td>
                <td><span class="badge badge-table ${operationClass}">${log.operation}</span></td>
                <td>${log.table_name}</td>
                <td>
                    <div class="data-preview">${log.data_preview || 'N/A'}</div>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    document.getElementById('logsContent').innerHTML = html;
    updatePagination(page);
}

function updateStats(logs) {
    const total = logs.length;
    const inserts = logs.filter(l => l.operation === 'INSERT').length;
    const updates = logs.filter(l => l.operation === 'UPDATE').length;
    const deletes = logs.filter(l => l.operation === 'DELETE').length;

    document.getElementById('statsBar').innerHTML = `
    <div class="stats-grid cols-4"> <!-- Use cols-4 for Logs page (4 cards) -->
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
    </div>
    `;
}

function changePage(page) {
    if (page < 1) return;
    currentPage = page;
    loadLogs();
}

function applyFilters() {
    console.log('Button clicked: applyFilters');
    currentPage = 1;
    loadLogs();
}

function resetFilters() {
    console.log('Button clicked: resetFilters');
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

// Utility function to load all logs without filters
function loadAllLogs() {
    // Reset all filters
    document.getElementById('filterTable').value = '';
    document.getElementById('filterOperation').value = '';
    document.getElementById('filterSite').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';

    // Reset to page 1
    currentPage = 1;

    // Load logs without filters
    loadLogs();
}

// Expose loadAllLogs to global scope
window.loadAllLogs = loadAllLogs;