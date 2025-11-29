<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HUFLIT Distributed Database - Statistics Dashboard with analytics and performance metrics">
    <title>Statistics Dashboard - HUFLIT MongoDB</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <!-- Google Fonts for Vietnamese support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&family=Noto+Sans:wght@400;600;700&family=Be+Vietnam+Pro:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&family=Lato:wght@400;700&family=Nunito:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
    <link rel="stylesheet" href="css/responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script src="js/sidebar.js"></script>
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

    <?php include 'sidebar.php'; renderSidebar('stats'); ?>

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







</body>
</html>
