<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị Database - HUFLIT</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/pages.css">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        .maintenance-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary-color);
        }

        .action-card h3 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .action-card p {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 1.5rem;
            min-height: 3em;
        }

        .explore-section {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 1.5rem;
        }

        .site-comparison {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .site-col {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            padding: 1rem;
            overflow-x: auto;
        }

        .site-col h4 {
            text-align: center;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .site-col table {
            font-size: 0.8rem;
        }

        .badge-site {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .badge-a {
            background: #3b82f6;
        }

        .badge-b {
            background: #ec4899;
        }

        .badge-c {
            background: #10b981;
        }
    </style>
</head>

<body>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Đang xử lý...</div>
        </div>
    </div>

    <?php include 'sidebar.php';
    renderSidebar('maintenance'); ?>

    <div class="main-content">
        <div class="topbar">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <span class="hamburger-icon">☰</span>
            </button>
            <div class="topbar-title">
                <h1>⚙️ Quản trị hệ thống</h1>
                <p>Công cụ quản lý dữ liệu và hỗ trợ Demo</p>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="maintenance-grid">
                <div class="action-card">
                    <h3>🧹 Reset Database</h3>
                    <p>Xóa toàn bộ dữ liệu hiện có trong tất cả các Site để làm sạch hệ thống.</p>
                    <button class="btn btn-danger w-full" onclick="resetDatabase()">Xác nhận xóa sạch</button>
                </div>

                <div class="action-card">
                    <h3>🌱 Seed Sample Data</h3>
                    <p>Nạp lại bộ dữ liệu mẫu chuẩn (bao gồm Khoa, Sinh viên, Môn học, CTĐT).</p>
                    <button class="btn btn-success w-full" onclick="seedDatabase()">Nạp dữ liệu mẫu</button>
                </div>
            </div>

            <div class="explore-section">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h3>🗺️ Site Explorer (Demo Tool)</h3>
                    <div class="form-group" style="margin: 0;">
                        <select id="exploreTable" onchange="exploreData()" style="padding: 0.5rem;">
                            <option value="Khoa">Bảng Khoa</option>
                            <option value="SinhVien">Bảng Sinh Viên</option>
                            <option value="MonHoc">Bảng Môn Học</option>
                            <option value="CTDaoTao">Bảng CT Đào Tạo</option>
                            <option value="DangKy">Bảng Đăng Ký</option>
                        </select>
                    </div>
                </div>
                <p style="font-size: 0.9rem; opacity: 0.7; margin-top: 0.5rem;">
                    Công cụ này giúp bạn so sánh trực tiếp dữ liệu thô tại các Site để chứng minh tính phân tán của hệ
                    thống.
                </p>

                <div class="site-comparison" id="exploreResult">
                    <div class="site-col">
                        <h4><span class="badge-site badge-a">SITE A</span></h4>
                        <div id="site-a-table">Chọn bảng để xem...</div>
                    </div>
                    <div class="site-col">
                        <h4><span class="badge-site badge-b">SITE B</span></h4>
                        <div id="site-b-table">Chọn bảng để xem...</div>
                    </div>
                    <div class="site-col">
                        <h4><span class="badge-site badge-c">SITE C</span></h4>
                        <div id="site-c-table">Chọn bảng để xem...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/sidebar.js"></script>
    <script type="module" src="js/maintenance.js"></script>
</body>

</html>