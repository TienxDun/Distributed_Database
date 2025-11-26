<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - HUFLIT MongoDB</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Đang xử lý...</div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2>📋 Logs</h2>
            <p>Audit System</p>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">📊 Navigation</h3>
            <ul class="sidebar-nav">
                <li><a href="ui.php" class="sidebar-link">
                    <span class="sidebar-icon">🏠</span>
                    <span class="sidebar-text">Home</span>
                </a></li>
                <li><a href="stats.php" class="sidebar-link">
                    <span class="sidebar-icon">📊</span>
                    <span class="sidebar-text">Statistics</span>
                </a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">⚙️ Tools</h3>
            <ul class="sidebar-nav">
                <li><button class="sidebar-btn" onclick="toggleSiteStatusPanel()">
                    <span class="sidebar-icon">🔍</span>
                    <span class="sidebar-text">Site Status</span>
                </button></li>
                <li><button class="sidebar-btn" onclick="openSettingsModal()">
                    <span class="sidebar-icon">⚙️</span>
                    <span class="sidebar-text">Settings</span>
                </button></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">🗺️ Data Sites</h3>
            <div class="site-toggle-container">
                <label class="site-toggle-label" for="toggleSiteColumn">
                    <input type="checkbox" id="toggleSiteColumn" checked onchange="toggleSiteColumnVisibility()" class="site-toggle-checkbox">
                    <div class="site-toggle-slider">
                        <span class="site-toggle-icon">🗺️</span>
                    </div>
                    <span class="site-toggle-text">
                        <strong>Show Site Column</strong>
                        <small>Distributed data</small>
                    </span>
                </label>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span class="hamburger-icon">☰</span>
            </button>
            <div class="topbar-title">
                <h1>📋 Audit Logs</h1>
                <p>Lịch sử thay đổi dữ liệu - HUFLIT Distributed Database</p>
            </div>
            <div class="topbar-actions">
                <div class="quick-actions">
                    <button class="quick-action-btn" onclick="loadLogs()" title="Refresh">
                        <span>🔄</span>
                    </button>
                    <button class="quick-action-btn" onclick="resetFilters()" title="Reset Filters">
                        <span>🗑️</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
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
                    <button class="btn btn-primary" onclick="applyFilters()">Lọc dữ liệu</button>
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
    </div>

    <script type="module">
        // Import required modules
        import { toggleSiteStatusPanel } from './js/modules/modal.js';
        import { loadSettings, openSettingsModal, closeSettingsModal, updateBackgroundColor, updateBackgroundColorFromText, applyPresetColor, resetToDefault } from './js/modules/settings.js';

        // Module variables
        let currentPage = 1;
        let autoRefreshInterval = null;

        // Expose functions to global scope
        window.toggleSidebar = function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');

            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');

            // Save preference to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        };

        window.toggleSiteStatusPanel = toggleSiteStatusPanel;
        window.openSettingsModal = openSettingsModal;
        window.closeSettingsModal = closeSettingsModal;
        window.updateBackgroundColor = updateBackgroundColor;
        window.updateBackgroundColorFromText = updateBackgroundColorFromText;
        window.applyPresetColor = applyPresetColor;
        window.resetToDefault = resetToDefault;

        // Expose logs functions to global scope
        window.loadLogs = loadLogs;
        window.applyFilters = applyFilters;
        window.resetFilters = resetFilters;
        window.changePage = changePage;
        window.toggleAutoRefresh = toggleAutoRefresh;

        // Initialize on page load
        function initializePage() {
            loadSettings();

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

    <!-- Settings Modal -->
    <div id="settingsModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>⚙️ Cài đặt giao diện</h2>
                <button class="modal-close" onclick="closeSettingsModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="settings-section">
                    <h3 style="margin-bottom: 1.5rem; color: var(--text); font-size: 1.1rem;">🎨 Màu nền</h3>

                    <div class="form-group">
                        <label for="bgColor" style="font-weight: 600; font-size: 0.95rem;">Chọn màu nền:</label>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <input type="color" id="bgColor" value="#f8fafc" oninput="updateBackgroundColor()"
                                style="width: 80px; height: 80px; border: 3px solid var(--border); border-radius: 12px; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <div style="flex: 1;">
                                <input type="text" id="bgColorText" value="#f8fafc" onchange="updateBackgroundColorFromText()"
                                    placeholder="#RRGGBB"
                                    style="width: 100%; padding: 0.75rem; border: 2px solid var(--border); border-radius: 8px; font-family: monospace; font-size: 1rem; font-weight: 600;">
                                <small style="display: block; margin-top: 0.5rem; color: var(--secondary);">
                                    Ví dụ: #ffffff (trắng), #000000 (đen), #f0f0f0 (xám nhạt)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem; padding: 1rem; background: rgba(37, 99, 235, 0.05); border-radius: 8px; border-left: 4px solid var(--primary);">
                        <h4 style="margin-bottom: 0.75rem; color: var(--primary); font-size: 0.9rem;">💡 Gợi ý màu sắc</h4>
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem;">
                            <button onclick="applyPresetColor('#ffffff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #ffffff; aspect-ratio: 1;" title="Trắng"></button>
                            <button onclick="applyPresetColor('#f8fafc')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #f8fafc; aspect-ratio: 1;" title="Xám nhạt"></button>
                            <button onclick="applyPresetColor('#e0e7ff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #e0e7ff; aspect-ratio: 1;" title="Xanh nhạt"></button>
                            <button onclick="applyPresetColor('#fef3c7')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #fef3c7; aspect-ratio: 1;" title="Vàng nhạt"></button>
                            <button onclick="applyPresetColor('#dcfce7')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #dcfce7; aspect-ratio: 1;" title="Xanh lá nhạt"></button>
                            <button onclick="applyPresetColor('#fee2e2')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #fee2e2; aspect-ratio: 1;" title="Đỏ nhạt"></button>
                            <button onclick="applyPresetColor('#f3e8ff')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #f3e8ff; aspect-ratio: 1;" title="Tím nhạt"></button>
                            <button onclick="applyPresetColor('#cffafe')" style="padding: 0.5rem; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; background: #cffafe; aspect-ratio: 1;" title="Cyan nhạt"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" onclick="resetToDefault()">🔄 Khôi phục mặc định</button>
                <button class="btn btn-cancel" type="button" onclick="closeSettingsModal()">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Site Status Panel (Left Side) -->
    <div id="siteStatusPanel" class="site-status-panel">
        <div class="site-status-panel-header">
            <h3>🔍 Site Status</h3>
            <div class="site-status-panel-controls">
                <button onclick="refreshSiteStatus()" id="panel-refresh-btn" title="Làm mới">
                    <span id="panel-refresh-icon">🔄</span>
                </button>
                <button onclick="toggleSiteStatusPanel()" title="Đóng">&times;</button>
            </div>
        </div>
        <div class="site-status-panel-content">
            <div id="site-status-compact">
                <div class="loading-spinner" style="text-align: center; padding: 2rem;">
                    <div class="spinner" style="width: 30px; height: 30px;"></div>
                    <div style="margin-top: 0.5rem; color: #666; font-size: 0.9rem;">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
