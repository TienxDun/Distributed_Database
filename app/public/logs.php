<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HUFLIT Distributed Database - Audit Logs system for tracking database changes and operations">
    <title>Audit Logs - HUFLIT MongoDB</title>
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
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Đang xử lý...</div>
        </div>
    </div>

    <?php include 'sidebar.php'; renderSidebar('logs'); ?>

    <!-- Main Content -->
    <div class="main-content glass">
        <div class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span class="hamburger-icon">☰</span>
            </button>
            <div class="topbar-title">
                <h1>📋 Audit Logs</h1>
                <p>Lịch sử thay đổi dữ liệu - HUFLIT Distributed Database</p>
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

            <div class="stats-grid" id="statsBar"></div>

            <div class="logs-container">
                <div id="logsContent"></div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
    <script type="module" src="js/logs.js"></script>
</body>
</html>
