<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HUFLIT Distributed Database - Audit Logs system for tracking database changes and operations">
    <title>Audit Logs - HUFLIT MongoDB</title>
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
    <link rel="stylesheet" href="css/responsive.css">
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

    <script type="module" src="js/logs.js"></script>
</body>
</html>

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
