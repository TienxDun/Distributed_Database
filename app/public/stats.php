<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HUFLIT Distributed Database - Statistics Dashboard with analytics and performance metrics">
    <title>Statistics Dashboard - HUFLIT MongoDB</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script type="module" src="js/stats.js"></script>
    <style>
        /* Toggle Button Styles */
        .toggle-btn {
            position: relative;
            width: 50px;
            height: 26px;
            border-radius: 13px;
            background: #ccc;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            outline: none;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .toggle-btn.active {
            background: var(--primary, #2563eb);
        }

        .toggle-btn .toggle-slider {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .toggle-btn.active .toggle-slider {
            transform: translateX(24px);
        }

        .toggle-btn:hover {
            box-shadow: 0 0 8px rgba(37, 99, 235, 0.3);
        }

        .toggle-btn.active:hover {
            box-shadow: 0 0 8px rgba(37, 99, 235, 0.5);
        }
    </style>
    <style>
        /* Sidebar Button Styles */
        .sidebar-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.75rem 1rem;
            background: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text, #1f2937);
        }

        .sidebar-btn:hover {
            background: rgba(37, 99, 235, 0.1);
            transform: translateX(4px);
        }

        .sidebar-btn.active {
            background: rgba(37, 99, 235, 0.15);
            border-left: 3px solid var(--primary, #2563eb);
        }

        .sidebar-btn.active .sidebar-status {
            color: var(--primary, #2563eb);
            font-weight: 600;
        }

        .sidebar-status {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--secondary, #6b7280);
            background: rgba(255, 255, 255, 0.8);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            min-width: 32px;
            text-align: center;
        }
    </style>
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
            <h2>📊 Stats</h2>
            <p>Analytics Dashboard</p>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">📊 Navigation</h3>
            <ul class="sidebar-nav">
                <li><a href="ui.php" class="sidebar-link">
                    <span class="sidebar-icon">🏠</span>
                    <span class="sidebar-text">Home</span>
                </a></li>
                <li><a href="logs.php" class="sidebar-link">
                    <span class="sidebar-icon">📋</span>
                    <span class="sidebar-text">Audit Logs</span>
                </a></li>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">⚙️ Tools</h3>
            <ul class="sidebar-nav">
                <li><button class="sidebar-btn" id="autoRefreshBtn" onclick="toggleAutoRefresh()">
                    <span class="sidebar-icon">🔄</span>
                    <span class="sidebar-text">Auto Refresh</span>
                    <span class="sidebar-status" id="autoRefreshStatus">ON</span>
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
                        <div class="site-toggle-main-text">Show Site Column</div>
                        <div class="site-toggle-sub-text">Distributed data</div>
                    </span>
                </label>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content glass">
        <div class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span class="hamburger-icon">☰</span>
            </button>
            <div class="topbar-title">
                <h1>📊 Statistics Dashboard</h1>
                <p>Thống kê và phân tích - HUFLIT Distributed Database</p>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="stats-overview" id="statsOverview">
                <div class="loading-container">
                    <div class="spinner"></div>
                    <div class="loading-text">Đang tải thống kê...</div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h3>📊 Thao tác theo loại</h3>
                    <div class="chart-wrapper">
                        <canvas id="operationsChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>📋 Thao tác theo bảng</h3>
                    <div class="chart-wrapper">
                        <canvas id="tablesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h3>🗺️ Thao tác theo Site</h3>
                    <div class="chart-wrapper">
                        <canvas id="sitesChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <h3>🔗 Truy vấn theo Endpoint</h3>
                    <div class="chart-wrapper">
                        <canvas id="endpointsChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <h3>⚡ Thời gian phản hồi trung bình (ms)</h3>
                    <div class="chart-wrapper">
                        <canvas id="responseTimeChart" style="width: 100%; height: 100%;"></canvas>
                        <div id="responseTimeMessage" style="text-align: center; padding: 20px; color: #666; display: none;">
                            Đang tải dữ liệu...
                        </div>
                    </div>
                </div>
            </div>

            <div class="last-updated" id="lastUpdated"></div>
        </div>
    </div>



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

</body>
</html>
