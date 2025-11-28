<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="HUFLIT Distributed Database - Full CRUD Interface for managing distributed database system with SQL Server and MongoDB">
    <title>HUFLIT Distributed Database - CRUD Interface</title>
    <link rel="icon" type="image/x-icon" href="css/favicon.ico">
    <!-- Google Fonts for Vietnamese support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&family=Open+Sans:wght@400;600;700&family=Noto+Sans:wght@400;600;700&family=Be+Vietnam+Pro:wght@400;500;600;700&family=Montserrat:wght@400;500;600;700&family=Lato:wght@400;700&family=Nunito:wght@400;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/layout.css">
    <link rel="stylesheet" href="css/components.css">
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
            <h2>HUFLIT</h2>
            <p>Distributed DB</p>
        </div>

        <div class="sidebar-section">
            <h3 class="sidebar-section-title">📊 Navigation</h3>
            <ul class="sidebar-nav">
                <li><a href="logs.php" class="sidebar-link">
                    <span class="sidebar-icon">📋</span>
                    <span class="sidebar-text">Audit Logs</span>
                </a></li>
                <li><a href="stats.php" class="sidebar-link">
                    <span class="sidebar-icon">📊</span>
                    <span class="sidebar-text">Statistics</span>
                </a></li>
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
                <h1>🎓 HUFLIT Distributed Database</h1>
                <p>Full CRUD Interface - Hệ thống Cơ sở dữ liệu Phân tán</p>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="tabs-container">
                <div class="tabs">
                    <button class="tab-btn active" onclick="showTab('khoa')">Khoa</button>
                    <button class="tab-btn" onclick="showTab('monhoc')">Môn Học</button>
                    <button class="tab-btn" onclick="showTab('sinhvien')">Sinh Viên</button>
                    <button class="tab-btn" onclick="showTab('ctdaotao')">CT Đào Tạo</button>
                    <button class="tab-btn" onclick="showTab('dangky')">Đăng Ký</button>
                    <button class="tab-btn" onclick="showTab('global')">Truy Vấn Toàn Cục</button>
                </div>
            </div>

        <!-- Khoa Module -->
        <div id="khoa" class="tab-content active">
            <h2 class="module-title">
                Quản lý Khoa
                <button class="btn-refresh" onclick="refreshCurrentTab()" title="Làm mới dữ liệu">
                    🔄
                </button>
            </h2>
            
            <div id="khoa-alert" class="alert"></div>
            
            <div class="form-group">
                <label for="khoa-id">Mã Khoa:</label>
                <input type="text" id="khoa-id" placeholder="Ví dụ: CNTT, NN, LUAT" onkeydown="if(event.key==='Enter') loadDataById('khoa')">
            </div>
            
            <div class="btn-group">
                <button class="btn btn-add" onclick="openCreateModal('khoa')">Thêm Khoa Mới</button>
                <button class="btn btn-primary" onclick="loadData('khoa')">Tải Danh Sách</button>
                <button class="btn btn-success" onclick="loadDataById('khoa')">Xem theo ID</button>
            </div>
            
            <div id="khoa-result" class="result"></div>
        </div>

        <!-- MonHoc Module -->
        <div id="monhoc" class="tab-content">
            <h2 class="module-title">
                Quản lý Môn Học
                <button class="btn-refresh" onclick="refreshCurrentTab()" title="Làm mới dữ liệu">
                    🔄
                </button>
            </h2>
            
            <div id="monhoc-alert" class="alert"></div>
            
            <div class="form-group">
                <label for="monhoc-id">Mã Môn Học:</label>
                <input type="text" id="monhoc-id" placeholder="Ví dụ: MH001, MH002" onkeydown="if(event.key==='Enter') loadDataById('monhoc')">
            </div>
            
            <div class="btn-group">
                <button class="btn btn-add" onclick="openCreateModal('monhoc')">Thêm Môn Học Mới</button>
                <button class="btn btn-primary" onclick="loadData('monhoc')">Tải Danh Sách</button>
                <button class="btn btn-success" onclick="loadDataById('monhoc')">Xem theo ID</button>
            </div>
            
            <div id="monhoc-result" class="result"></div>
        </div>

        <!-- SinhVien Module -->
        <div id="sinhvien" class="tab-content">
            <h2 class="module-title">
                Quản lý Sinh Viên
                <button class="btn-refresh" onclick="refreshCurrentTab()" title="Làm mới dữ liệu">
                    🔄
                </button>
            </h2>
            
            <div id="sinhvien-alert" class="alert"></div>
            
            <div class="form-group">
                <label for="sinhvien-id">Mã Sinh Viên:</label>
                <input type="text" id="sinhvien-id" placeholder="Ví dụ: 25DH000001, 24DH000002" onkeydown="if(event.key==='Enter') loadDataById('sinhvien')">
            </div>
            
            <div class="btn-group">
                <button class="btn btn-add" onclick="openCreateModal('sinhvien')">Thêm Sinh Viên Mới</button>
                <button class="btn btn-primary" onclick="loadData('sinhvien')">Tải Danh Sách</button>
                <button class="btn btn-success" onclick="loadDataById('sinhvien')">Xem theo ID</button>
            </div>
            
            <div id="sinhvien-result" class="result"></div>
        </div>

        <!-- CTDaoTao Module -->
        <div id="ctdaotao" class="tab-content">
            <h2 class="module-title">
                Chương Trình Đào Tạo
                <button class="btn-refresh" onclick="refreshCurrentTab()" title="Làm mới dữ liệu">
                    🔄
                </button>
            </h2>
            
            <div id="ctdaotao-alert" class="alert"></div>
            
            <div class="form-group">
                <label for="ctdaotao-khoa">Mã Khoa hoặc Tên Khoa:</label>
                <input type="text" id="ctdaotao-khoa" placeholder="Ví dụ: CNTT hoặc Công nghệ thông tin" onkeydown="if(event.key==='Enter') loadCTDaoTaoByFilter()">
            </div>
            <div class="form-group">
                <label for="ctdaotao-khoahoc">Khóa Học:</label>
                <input type="number" id="ctdaotao-khoahoc" placeholder="Ví dụ: 2018, 2019" onkeydown="if(event.key==='Enter') loadCTDaoTaoByFilter()">
            </div>
            
            <div class="btn-group">
                <button class="btn btn-add" onclick="openCreateModal('ctdaotao')">Thêm Môn Vào CTĐT</button>
                <button class="btn btn-primary" onclick="loadData('ctdaotao')">Tải Danh Sách</button>
                <button class="btn btn-success" onclick="loadCTDaoTaoByFilter()">Xem Môn Học</button>
            </div>
            
            <div id="ctdaotao-result" class="result"></div>
        </div>

        <!-- DangKy Module -->
        <div id="dangky" class="tab-content">
            <h2 class="module-title">
                Đăng Ký Học Phần
                <button class="btn-refresh" onclick="refreshCurrentTab()" title="Làm mới dữ liệu">
                    🔄
                </button>
            </h2>
            
            <div id="dangky-alert" class="alert"></div>
            
            <div class="form-group">
                <label for="dangky-masv">Mã Sinh Viên:</label>
                <input type="text" id="dangky-masv" placeholder="Ví dụ: 25DH000001, 24DH000002" onkeydown="if(event.key==='Enter') loadDangKyByMaSV()">
            </div>
            
            <div class="btn-group">
                <button class="btn btn-add" onclick="openCreateModal('dangky')">Đăng Ký Môn Học</button>
                <button class="btn btn-primary" onclick="loadData('dangky')">Tải Danh Sách</button>
                <button class="btn btn-success" onclick="loadDangKyByMaSV()">Xem Môn Học Đã Đăng Ký</button>
            </div>
            
            <div id="dangky-result" class="result"></div>
        </div>

        <!-- Global Queries Module -->
        <div id="global" class="tab-content">
            <h2 class="module-title">Truy Vấn Toàn Cục</h2>
            <div class="query-grid">
                <div class="query-card">
                    <h3>Các môn học sinh viên đã học và đạt từ điểm 5 trở lên</h3>
                    <div class="form-group">
                        <label for="global-masv-1">Mã Sinh Viên:</label>
                        <input type="text" id="global-masv-1" placeholder="Ví dụ: 25DH000001" onkeydown="if(event.key==='Enter') callGlobalQuery(1)">
                    </div>
                    <button class="btn btn-success" onclick="callGlobalQuery(1)">Truy Vấn</button>
                    <div id="global-result-1" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Các khóa học của một khoa</h3>
                    <div class="form-group">
                        <label for="global-query-2">Tên Khoa hoặc Mã Khoa:</label>
                        <input type="text" id="global-query-2" placeholder="Ví dụ: CNTT" onkeydown="if(event.key==='Enter') callGlobalQuery(2)">
                    </div>
                    <button class="btn btn-success" onclick="callGlobalQuery(2)">Truy Vấn</button>
                    <div id="global-result-2" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Các môn học bắt buộc của sinh viên</h3>
                    <div class="form-group">
                        <label for="global-masv-3">Mã Sinh Viên:</label>
                        <input type="text" id="global-masv-3" placeholder="Ví dụ: 25DH000001" onkeydown="if(event.key==='Enter') callGlobalQuery(3)">
                    </div>
                    <button class="btn btn-success" onclick="callGlobalQuery(3)">Truy Vấn</button>
                    <div id="global-result-3" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Danh sách sinh viên đủ điều kiện tốt nghiệp</h3>
                    <p class="info-text">Sinh viên đã hoàn thành tất cả môn trong CTDT và đạt điểm ≥5.</p>
                    <button class="btn btn-success" onclick="callGlobalQuery(4)">Truy Vấn</button>
                    <div id="global-result-4" class="result"></div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Generic Modal for Create/Edit -->
    <div id="crudModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Modal Title</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="modalAlert" class="alert" style="display: none; margin-bottom: 1rem;"></div>
                <form id="crudForm" onsubmit="event.preventDefault(); submitForm();">
                    <div id="formFields"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" type="button" onclick="closeModal()">Hủy</button>
                <button class="btn btn-success" type="submit" id="submitBtn" form="crudForm">Lưu</button>
            </div>
        </div>
    </div>

    <script type="module" src="js/app.js"></script>
</body>
</html>
