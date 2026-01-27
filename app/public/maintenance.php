<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - HUFLIT Distributed Database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'sidebar.php';
        renderSidebar('maintenance'); ?>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar-btn" id="toggleSidebarBtn">☰</button>
                    <h1 class="page-title">Quản trị Hệ thống</h1>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <div class="container">

                    <div class="alert alert-error" style="margin-bottom: 2rem; border-left: 4px solid var(--danger);">
                        <strong>⚠️ Khu vực Nguy hiểm:</strong> Các hành động tại đây sẽ ảnh hưởng trực tiếp đến dữ liệu
                        toàn hệ thống. Hãy cân nhắc trước khi thực hiện.
                    </div>

                    <!-- Actions Grid -->
                    <div
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

                        <!-- Reset DB -->
                        <div class="card" style="border-top: 4px solid var(--danger);">
                            <div class="card-body" style="text-align: center;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">🧹</div>
                                <h3
                                    style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--danger);">
                                    Reset Database</h3>
                                <p style="color: var(--slate-500); margin-bottom: 1.5rem;">
                                    Xóa toàn bộ dữ liệu hiện có trong tất cả các Site để làm sạch hệ thống.
                                </p>
                                <button class="btn btn-danger w-100" onclick="resetDatabase()">Xác nhận Xóa
                                    sạch</button>
                            </div>
                        </div>

                        <!-- Seed DB -->
                        <div class="card" style="border-top: 4px solid var(--success);">
                            <div class="card-body" style="text-align: center;">
                                <div style="font-size: 3rem; margin-bottom: 1rem;">🌱</div>
                                <h3
                                    style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--success);">
                                    Seed Sample Data</h3>
                                <p style="color: var(--slate-500); margin-bottom: 1.5rem;">
                                    Nạp lại bộ dữ liệu mẫu chuẩn (Khoa, Sinh viên, Môn học...).
                                </p>
                                <button class="btn btn-success w-100" onclick="seedDatabase()">Nạp dữ liệu mẫu</button>
                            </div>
                        </div>
                    </div>

                    <!-- Site Explorer -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">🗺️ Site Explorer (Công cụ Demo)</h2>
                            <div class="form-group" style="margin:0; width: 200px;">
                                <select id="exploreTable" class="form-control" onchange="exploreData()">
                                    <option value="Khoa">Bảng Khoa</option>
                                    <option value="SinhVien">Bảng Sinh Viên</option>
                                    <option value="MonHoc">Bảng Môn Học</option>
                                    <option value="CTDaoTao">Bảng CT Đào Tạo</option>
                                    <option value="DangKy">Bảng Đăng Ký</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <p style="color: var(--slate-500); margin-bottom: 1.5rem;">
                                So sánh dữ liệu thực tế được lưu trữ tại các phân mảnh (Sites) khác nhau.
                            </p>

                            <div
                                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                                <!-- Site A -->
                                <div class="site-card a" id="site-card-a">
                                    <div class="site-card-header">
                                        SITE A
                                    </div>
                                    <div id="site-a-table" style="font-size: 0.85rem; overflow-x: auto;"></div>
                                </div>

                                <!-- Site B -->
                                <div class="site-card b" id="site-card-b">
                                    <div class="site-card-header">
                                        SITE B
                                    </div>
                                    <div id="site-b-table" style="font-size: 0.85rem; overflow-x: auto;"></div>
                                </div>

                                <!-- Site C -->
                                <div class="site-card c" id="site-card-c">
                                    <div class="site-card-header">
                                        SITE C
                                    </div>
                                    <div id="site-c-table" style="font-size: 0.85rem; overflow-x: auto;"></div>
                                </div>
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
            <div class="loading-text" style="margin-top:1rem; font-weight:500; color:var(--slate-600);">Đang xử lý...</div>
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
    <script type="module" src="js/maintenance.js"></script>
</body>

</html>