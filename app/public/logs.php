<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs - HUFLIT Distributed Database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>
        <rect width='100' height='100' rx='25' fill='black'/>
        <text x='50%' y='68%' font-family='sans-serif' font-size='60' font-weight='800' fill='white' text-anchor='middle'>D</text>
        <path d='M80 20 L83 28 L91 31 L83 34 L80 42 L77 34 L69 31 L77 28 Z' fill='%23fef08a'/>
    </svg>">

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php';
        renderSidebar('logs'); ?>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar-btn" id="toggleSidebarBtn">☰</button>
                    <h1 class="page-title">Audit Logs</h1>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <div class="container">

                    <!-- Filters Card -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">🔍 Bộ lọc tìm kiếm</h2>
                        </div>
                        <div class="card-body">
                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">

                                <div class="form-group">
                                    <label class="form-label">🗂️ Bảng</label>
                                    <select id="filterTable" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="Khoa">Khoa</option>
                                        <option value="MonHoc">MonHoc</option>
                                        <option value="SinhVien">SinhVien</option>
                                        <option value="CTDaoTao">CTDaoTao</option>
                                        <option value="DangKy">DangKy</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">⚡ Thao tác</label>
                                    <select id="filterOperation" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="INSERT">INSERT</option>
                                        <option value="UPDATE">UPDATE</option>
                                        <option value="DELETE">DELETE</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">🗺️ Site</label>
                                    <select id="filterSite" class="form-control">
                                        <option value="">Tất cả</option>
                                        <option value="Site_A">Site A</option>
                                        <option value="Site_B">Site B</option>
                                        <option value="Site_C">Site C</option>
                                        <option value="Global">Global</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">📅 Từ ngày</label>
                                    <input type="date" id="filterDateFrom" class="form-control">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">📅 Đến ngày</label>
                                    <input type="date" id="filterDateTo" class="form-control">
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem; display: flex; gap: 1rem; justify-content: flex-end;">
                                <button class="btn btn-secondary" onclick="resetFilters()">🔄 Đặt lại</button>
                                <button class="btn btn-primary" onclick="applyFilters()">Lọc dữ liệu</button>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Bar Placeholder -->
                    <div id="statsBar" style="margin-bottom: 1.5rem;"></div>

                    <!-- Logs Table -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Kết quả Logs</h2>
                        </div>
                        <div class="card-body" style="padding: 0;">
                            <div id="logsContent">
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
                            </div>
                            <div id="pagination" class="pagination-container">
                                <!-- Pagination will be injected here -->
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <div
                style="width:40px; height:40px; border:4px solid var(--slate-200); border-top-color:var(--primary-600); border-radius:50%; animation:spin 1s linear infinite;">
            </div>
        </div>
    </div>
    <style>
        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <script src="js/sidebar.js"></script>
    <script type="module" src="js/logs.js"></script>
</body>

</html>