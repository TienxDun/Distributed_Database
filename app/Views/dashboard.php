<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUFLIT Distributed Database - Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'>
        <rect width='100' height='100' rx='25' fill='black'/>
        <text x='50%' y='68%' font-family='sans-serif' font-size='60' font-weight='800' fill='white' text-anchor='middle'>D</text>
        <path d='M80 20 L83 28 L91 31 L83 34 L80 42 L77 34 L69 31 L77 28 Z' fill='%23fef08a'/>
    </svg>">

    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/components.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="light-theme">

    <div class="app-container">
        <!-- Sidebar -->
        <?php include 'Components/sidebar.php';
        renderSidebar('ui'); ?>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            <!-- Top Header -->
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-sidebar-btn" id="toggleSidebarBtn">☰</button>
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="header-right">
                    <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme()" title="Chuyển sang Dark Mode">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </header>

            <!-- Content Body -->
            <div class="content-body">
                <div class="container">

                    <!-- Tabs Navigation -->
                    <div class="tabs">
                        <button class="tab-btn active" onclick="showTab('khoa')">Khoa</button>
                        <button class="tab-btn" onclick="showTab('monhoc')">Môn Học</button>
                        <button class="tab-btn" onclick="showTab('sinhvien')">Sinh Viên</button>
                        <button class="tab-btn" onclick="showTab('ctdaotao')">CT Đào Tạo</button>
                        <button class="tab-btn" onclick="showTab('dangky')">Đăng Ký</button>
                        <button class="tab-btn" onclick="showTab('global')">Truy Vấn Toàn Cục</button>
                    </div>

                    <!-- KHOA CONTENT -->
                    <div id="khoa" class="tab-content active">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Quản lý Khoa</h2>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm" onclick="refreshCurrentTab()">🔄
                                        Refresh</button>
                                    <button class="btn btn-primary btn-sm" onclick="openCreateModal('khoa')">+ Thêm
                                        Khoa</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="khoa-alert"></div>

                                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <input type="text" class="form-control" id="khoa-id"
                                                placeholder="Tìm kiếm theo Mã Khoa..."
                                                onkeydown="if(event.key==='Enter') loadDataById('khoa')">
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary" onclick="loadDataById('khoa')">Tìm kiếm</button>
                                </div>

                                <div id="khoa-result">
                                    <!-- Table will be rendered here by JS -->
                                    <div class="text-center text-muted p-4">Đang tải dữ liệu...</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MONHOC CONTENT -->
                    <div id="monhoc" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Quản lý Môn Học</h2>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm" onclick="refreshCurrentTab()">🔄
                                        Refresh</button>
                                    <button class="btn btn-primary btn-sm" onclick="openCreateModal('monhoc')">+ Thêm
                                        Môn Học</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="monhoc-alert"></div>
                                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div style="flex: 1;">
                                        <input type="text" class="form-control" id="monhoc-id"
                                            placeholder="Tìm kiếm theo Mã Môn Học..."
                                            onkeydown="if(event.key==='Enter') loadDataById('monhoc')">
                                    </div>
                                    <button class="btn btn-secondary" onclick="loadDataById('monhoc')">Tìm kiếm</button>
                                </div>
                                <div id="monhoc-result"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SINHVIEN CONTENT -->
                    <div id="sinhvien" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Quản lý Sinh Viên</h2>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm" onclick="refreshCurrentTab()">🔄
                                        Refresh</button>
                                    <button class="btn btn-primary btn-sm" onclick="openCreateModal('sinhvien')">+ Thêm
                                        Sinh Viên</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="sinhvien-alert"></div>
                                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div style="flex: 1;">
                                        <input type="text" class="form-control" id="sinhvien-id"
                                            placeholder="Tìm kiếm theo Mã Sinh Viên..."
                                            onkeydown="if(event.key==='Enter') loadDataById('sinhvien')">
                                    </div>
                                    <button class="btn btn-secondary" onclick="loadDataById('sinhvien')">Tìm
                                        kiếm</button>
                                </div>
                                <div id="sinhvien-result"></div>
                            </div>
                        </div>
                    </div>

                    <!-- CTDAOTAO CONTENT -->
                    <div id="ctdaotao" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Chương Trình Đào Tạo</h2>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm" onclick="refreshCurrentTab()">🔄
                                        Refresh</button>
                                    <button class="btn btn-primary btn-sm" onclick="openCreateModal('ctdaotao')">+ Thêm
                                        CTĐT</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="ctdaotao-alert"></div>
                                <div
                                    style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; margin-bottom: 1.5rem;">
                                    <input type="text" class="form-control" id="ctdaotao-khoa"
                                        placeholder="Mã Khoa hoặc Tên Khoa..."
                                        onkeydown="if(event.key==='Enter') loadCTDaoTaoByFilter()">
                                    <input type="number" class="form-control" id="ctdaotao-khoahoc"
                                        placeholder="Khóa học (VD: 2024)"
                                        onkeydown="if(event.key==='Enter') loadCTDaoTaoByFilter()">
                                    <button class="btn btn-secondary" onclick="loadCTDaoTaoByFilter()">Lọc</button>
                                </div>
                                <div id="ctdaotao-result"></div>
                            </div>
                        </div>
                    </div>

                    <!-- DANGKY CONTENT -->
                    <div id="dangky" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Đăng Ký Học Phần</h2>
                                <div class="btn-group">
                                    <button class="btn btn-secondary btn-sm" onclick="refreshCurrentTab()">🔄
                                        Refresh</button>
                                    <button class="btn btn-primary btn-sm" onclick="openCreateModal('dangky')">Đăng Ký
                                        Môn</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="dangky-alert"></div>
                                <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                                    <div style="flex: 1;">
                                        <input type="text" class="form-control" id="dangky-masv"
                                            placeholder="Nhập Mã Sinh Viên để xem các môn đã đăng ký..."
                                            onkeydown="if(event.key==='Enter') loadDangKyByMaSV()">
                                    </div>
                                    <button class="btn btn-secondary" onclick="loadDangKyByMaSV()">Xem Đăng Ký</button>
                                </div>
                                <div id="dangky-result"></div>
                            </div>
                        </div>
                    </div>

                    <!-- GLOBAL QUERY CONTENT -->
                    <div id="global" class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="card-title">Truy Vấn Toàn Cục</h2>
                            </div>
                            <div class="card-body">
                                <div
                                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">

                                    <!-- Query 1 -->
                                    <div
                                        style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">1. Môn học
                                            đã đạt (Điểm >= 5)</h3>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="global-masv-1"
                                                placeholder="Mã Sinh Viên...">
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="callGlobalQuery(1)">Truy
                                            vấn</button>
                                        <div id="global-result-1" class="mt-3"></div>
                                    </div>

                                    <!-- Query 2 -->
                                    <div
                                        style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">2. Khóa học
                                            của Khoa</h3>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="global-query-2"
                                                placeholder="Tên Khoa/Mã Khoa...">
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="callGlobalQuery(2)">Truy
                                            vấn</button>
                                        <div id="global-result-2" class="mt-3"></div>
                                    </div>

                                    <!-- Query 3 -->
                                    <div
                                        style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">3. Môn học
                                            bắt buộc</h3>
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="global-masv-3"
                                                placeholder="Mã Sinh Viên...">
                                        </div>
                                        <button class="btn btn-primary w-100" onclick="callGlobalQuery(3)">Truy
                                            vấn</button>
                                        <div id="global-result-3" class="mt-3"></div>
                                    </div>

                                    <!-- Query 4 -->
                                    <div
                                        style="border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                                        <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">4. Sinh viên
                                            đủ điều kiện tốt nghiệp</h3>
                                        <p style="color: var(--slate-500); font-size: 0.875rem; margin-bottom: 1rem;">
                                            Danh sách sinh viên hoàn thành CTĐT.</p>
                                        <button class="btn btn-success w-100" onclick="callGlobalQuery(4)">Kiểm tra toàn
                                            hệ thống</button>
                                        <div id="global-result-4" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- MAIN MODAL -->
    <div id="crudModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle" style="margin:0; font-size: 1.25rem;">Modal Title</h2>
                <button type="button" class="btn-icon-only" onclick="closeModal()"
                    style="border:none; background:none; font-size: 1.5rem;">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalAlert" class="alert alert-error" style="display: none;"></div>
                <form id="crudForm" onsubmit="event.preventDefault(); submitForm();">
                    <div id="formFields"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary" id="submitBtn" form="crudForm">Lưu thay đổi</button>
            </div>
        </div>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div style="text-align:center;">
            <div
                style="width:40px; height:40px; border:4px solid var(--slate-200); border-top-color:var(--primary-600); border-radius:50%; animation:spin 1s linear infinite;">
            </div>
            <div class="loading-text" style="margin-top:1rem; font-weight:500; color:var(--slate-600);">Đang xử lý...
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

    <script src="assets/js/performance.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/sidebar.js"></script>
    <script type="module" src="assets/js/app.js"></script>
</body>

</html>