<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - HUFLIT Distributed Database</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>
        <rect width='100' height='100' rx='25' fill='black'/>
        <text x='50%' y='68%' font-family='sans-serif' font-size='60' font-weight='800' fill='white' text-anchor='middle'>D</text>
        <path d='M80 20 L83 28 L91 31 L83 34 L80 42 L77 34 L69 31 L77 28 Z' fill='%23fef08a'/>
    </svg>">

    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/maintenance.css">
</head>

<body>

    <div class="app-container">
        <!-- Sidebar -->
        <?php
        include 'sidebar.php';
        include 'maintenance_components.php';
        renderSidebar('maintenance');
        ?>

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
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                        <?php
                        renderActionCard(
                            'Initialize Empty Database',
                            'Khởi tạo database với schema trống, không có dữ liệu mẫu.',
                            'Khởi tạo DB trống',
                            'btn-primary',
                            'initDatabase()',
                            'border-primary',
                            '🏗️'
                        );

                        renderActionCard(
                            'Reset Database',
                            'Xóa toàn bộ dữ liệu hiện có trong tất cả các Site để làm sạch hệ thống.',
                            'Xác nhận Xóa sạch',
                            'btn-danger',
                            'resetDatabase()',
                            'border-danger'
                        );

                        renderActionCard(
                            'Seed Sample Data',
                            'Nạp lại bộ dữ liệu mẫu chuẩn (Khoa, Sinh viên, Môn học...).',
                            'Nạp dữ liệu mẫu',
                            'btn-success',
                            'seedDatabase()',
                            'border-success'
                        );
                        ?>
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

                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                                <?php
                                // Sample data for initial load - in real app this would come from AJAX
                                $sampleDataA = [
                                    ['Makhoa' => 'CNTT', 'Tenkhoa' => 'Công nghệ thông tin'],
                                    ['Makhoa' => 'DLKS', 'Tenkhoa' => 'Du lịch khách sạn'],
                                    ['Makhoa' => 'KTTC', 'Tenkhoa' => 'Kế toán tài chính'],
                                    ['Makhoa' => 'LLCT', 'Tenkhoa' => 'Luật công ty']
                                ];

                                renderSiteCard('a', 'site a', 'a', $sampleDataA);

                                $sampleDataB = [
                                    ['Makhoa' => 'NN', 'Tenkhoa' => 'Ngôn ngữ'],
                                    ['Makhoa' => 'NVPD', 'Tenkhoa' => 'Ngôn ngữ và Văn hóa Phương Đông'],
                                    ['Makhoa' => 'QHQT', 'Tenkhoa' => 'Quan hệ quốc tế'],
                                    ['Makhoa' => 'QTKD', 'Tenkhoa' => 'Quản trị kinh doanh']
                                ];

                                renderSiteCard('b', 'site b', 'b', $sampleDataB);

                                $sampleDataC = [
                                    ['Makhoa' => 'SLCT', 'Tenkhoa' => 'Sư phạm Lịch sử'],
                                    ['Makhoa' => 'SUAT', 'Tenkhoa' => 'Sư phạm Anh'],
                                    ['Makhoa' => 'TLKS', 'Tenkhoa' => 'Thể dục thể thao']
                                ];

                                renderSiteCard('c', 'site c', 'c', $sampleDataC);
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <div class="loading-spinner" style="width:40px; height:40px; border:4px solid var(--slate-200); border-top-color:var(--primary-600); border-radius:50%; animation:spin 1s linear infinite;"></div>
            <div class="loading-text" style="margin-top:1rem; font-weight:500; color:var(--slate-600);">Đang xử lý...</div>
        </div>
    </div>

    <style>
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>

    <script src="js/sidebar.js"></script>
    <script type="module" src="js/maintenance.js"></script>
</body>

</html>