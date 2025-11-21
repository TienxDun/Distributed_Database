<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HUFLIT Distributed Database - CRUD Interface</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <div class="loading-text">Đang xử lý...</div>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h1>🎓 HUFLIT Distributed Database</h1>
            <p>Full CRUD Interface - Hệ thống Cơ sở dữ liệu Phân tán</p>
            <div style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <label style="display: inline-flex; align-items: center; cursor: pointer; font-size: 0.95rem; color: var(--secondary);">
                    <input type="checkbox" id="toggleSiteColumn" checked onchange="toggleSiteColumnVisibility()" style="margin-right: 0.5rem; cursor: pointer; width: 18px; height: 18px;">
                    <span style="font-weight: 500;">🗺️ Hiển thị cột Site (phân mảnh dữ liệu)</span>
                </label>
                <button class="btn btn-settings" onclick="openSettingsModal()" style="font-size: 0.9rem; padding: 0.4rem 1rem;">
                    ⚙️ Cài đặt giao diện
                </button>
            </div>
        </div>

        <div class="tabs">
            <button class="tab-btn active" onclick="showTab('khoa')">Khoa</button>
            <button class="tab-btn" onclick="showTab('monhoc')">Môn Học</button>
            <button class="tab-btn" onclick="showTab('sinhvien')">Sinh Viên</button>
            <button class="tab-btn" onclick="showTab('ctdaotao')">CT Đào Tạo</button>
            <button class="tab-btn" onclick="showTab('dangky')">Đăng Ký</button>
            <button class="tab-btn" onclick="showTab('global')">Truy Vấn Toàn Cục</button>
        </div>

        <!-- Khoa Module -->
        <div id="khoa" class="tab-content active">
            <h2 class="module-title">Quản lý Khoa</h2>
            
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
            <h2 class="module-title">Quản lý Môn Học</h2>
            
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
            <h2 class="module-title">Quản lý Sinh Viên</h2>
            
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
            <h2 class="module-title">Chương Trình Đào Tạo</h2>
            
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
            <h2 class="module-title">Đăng Ký Học Phần</h2>
            
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
                    <button class="btn btn-primary" onclick="callGlobalQuery(1)">Truy Vấn</button>
                    <div id="global-result-1" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Các khóa học của một khoa</h3>
                    <div class="form-group">
                        <label for="global-query-2">Tên Khoa hoặc Mã Khoa:</label>
                        <input type="text" id="global-query-2" placeholder="Ví dụ: CNTT" onkeydown="if(event.key==='Enter') callGlobalQuery(2)">
                    </div>
                    <button class="btn btn-primary" onclick="callGlobalQuery(2)">Truy Vấn</button>
                    <div id="global-result-2" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Các môn học bắt buộc của sinh viên</h3>
                    <div class="form-group">
                        <label for="global-masv-3">Mã Sinh Viên:</label>
                        <input type="text" id="global-masv-3" placeholder="Ví dụ: 25DH000001" onkeydown="if(event.key==='Enter') callGlobalQuery(3)">
                    </div>
                    <button class="btn btn-primary" onclick="callGlobalQuery(3)">Truy Vấn</button>
                    <div id="global-result-3" class="result"></div>
                </div>

                <div class="query-card">
                    <h3>Danh sách sinh viên đủ điều kiện tốt nghiệp</h3>
                    <p class="info-text">Sinh viên đã hoàn thành tất cả môn trong CTDT và đạt điểm ≥5.</p>
                    <button class="btn btn-primary" onclick="callGlobalQuery(4)">Truy Vấn</button>
                    <div id="global-result-4" class="result"></div>
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
                            <input type="color" id="bgColor" value="#f8fafc" onchange="updateBackgroundColor()" 
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

    <script>
        const API_BASE = 'http://localhost:8080';
        let currentModule = '';
        let currentAction = ''; // 'create' or 'edit'
        let editingId = null;
        let showSiteColumn = true; // Global flag for Site column visibility
        let isLoading = false; // Global loading state

        // Show/hide loading overlay
        function showLoading(message = 'Đang xử lý...') {
            const overlay = document.getElementById('loadingOverlay');
            const text = overlay.querySelector('.loading-text');
            text.textContent = message;
            overlay.classList.add('show');
            isLoading = true;
        }

        function hideLoading() {
            const overlay = document.getElementById('loadingOverlay');
            overlay.classList.remove('show');
            isLoading = false;
        }

        // Set button loading state
        function setButtonLoading(button, loading) {
            if (loading) {
                button.classList.add('loading');
                button.disabled = true;
                button.dataset.originalText = button.textContent;
            } else {
                button.classList.remove('loading');
                button.disabled = false;
                if (button.dataset.originalText) {
                    button.textContent = button.dataset.originalText;
                }
            }
        }

        // Tab navigation
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            
            // Load data when switching tabs (except global)
            if (tabName !== 'global') {
                loadData(tabName);
            }
        }

        // Show alert message
        function showAlert(module, message, type = 'success') {
            const alertDiv = document.getElementById(`${module}-alert`);
            alertDiv.className = `alert alert-${type} show`;
            alertDiv.textContent = message;
            
            setTimeout(() => {
                alertDiv.classList.remove('show');
            }, 5000);
        }

        // Toggle Site column visibility
        function toggleSiteColumnVisibility() {
            showSiteColumn = document.getElementById('toggleSiteColumn').checked;
            
            // Reload current module data to apply changes
            const activeTab = document.querySelector('.tab-content.active');
            if (activeTab && activeTab.id !== 'global') {
                loadData(activeTab.id);
            }
        }

        // Create table with action buttons
        function createTableWithActions(data, module) {
            if (!Array.isArray(data) || data.length === 0) {
                return '<p>Không có dữ liệu</p>';
            }

            const headers = Object.keys(data[0]);
            let table = '<table><thead><tr>';
            headers.forEach(h => {
                // Skip Site column if toggle is off
                if (h === 'Site' && !showSiteColumn) return;
                // Add special class for Site header
                const headerClass = h === 'Site' ? ' class="site-header"' : '';
                table += `<th${headerClass}>${h}</th>`;
            });
            table += '<th>Thao tác</th></tr></thead><tbody>';
            
            data.forEach(row => {
                table += '<tr>';
                headers.forEach(h => {
                    // Skip Site column if toggle is off
                    if (h === 'Site' && !showSiteColumn) return;
                    
                    const value = row[h] !== null && row[h] !== undefined ? row[h] : '';
                    let cellClass = '';
                    
                    // Add class for Site column
                    if (h === 'Site') {
                        if (value === 'Site A') cellClass = ' class="site-a"';
                        else if (value === 'Site B') cellClass = ' class="site-b"';
                        else if (value === 'Site C') cellClass = ' class="site-c"';
                    }
                    
                    table += `<td${cellClass}>${value}</td>`;
                });
                
                // Action buttons
                table += '<td class="action-buttons">';
                
                if (module === 'khoa') {
                    table += `<button class="btn-edit" onclick='openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
                    table += `<button class="btn-delete" onclick='deleteRecord("${module}", "${row.MaKhoa}")'>🗑️ Xóa</button>`;
                } else if (module === 'monhoc') {
                    table += `<button class="btn-edit" onclick='openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
                    table += `<button class="btn-delete" onclick='deleteRecord("${module}", "${row.MaMH}")'>🗑️ Xóa</button>`;
                } else if (module === 'sinhvien') {
                    table += `<button class="btn-edit" onclick='openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
                    table += `<button class="btn-delete" onclick='deleteRecord("${module}", "${row.MaSV}")'>🗑️ Xóa</button>`;
                } else if (module === 'ctdaotao') {
                    table += `<button class="btn-delete" onclick='deleteCTDaoTao("${row.MaKhoa}", "${row.KhoaHoc}", "${row.MaMH}")'>🗑️ Xóa</button>`;
                } else if (module === 'dangky') {
                    table += `<button class="btn-edit" onclick='openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Cập nhật điểm</button>`;
                    table += `<button class="btn-delete" onclick='deleteDangKy("${row.MaSV}", "${row.MaMon}")'>🗑️ Xóa</button>`;
                }
                
                table += '</td></tr>';
            });
            
            table += '</tbody></table>';
            return table;
        }

        // Load data for a module
        async function loadData(module) {
            if (isLoading) return;
            
            const resultDiv = document.getElementById(`${module}-result`);
            resultDiv.innerHTML = '<div class="loading"></div> Đang tải dữ liệu...';
            resultDiv.className = 'result show';
            
            showLoading('Đang tải dữ liệu...');

            try {
                const response = await fetch(`${API_BASE}/${module}`);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                const count = Array.isArray(data) ? data.length : 0;
                const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📊 Tổng số: ${count} bản ghi</div>`;
                
                resultDiv.innerHTML = countText + createTableWithActions(data, module);
            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                resultDiv.className = 'result show error';
            } finally {
                hideLoading();
            }
        }

        // Load data by ID (for khoa, monhoc, sinhvien)
        async function loadDataById(module) {
            if (isLoading) return;
            
            const idInput = document.getElementById(`${module}-id`);
            const id = idInput ? idInput.value.trim() : '';
            
            if (!id) {
                showAlert(module, '⚠️ Vui lòng nhập ID để tìm kiếm', 'error');
                return;
            }

            const resultDiv = document.getElementById(`${module}-result`);
            resultDiv.innerHTML = '<div class="loading"></div> Đang tìm kiếm...';
            resultDiv.className = 'result show';
            
            showLoading('Đang tìm kiếm dữ liệu...');

            try {
                const response = await fetch(`${API_BASE}/${module}?id=${encodeURIComponent(id)}`);
                if (!response.ok) {
                    if (response.status === 404) {
                        throw new Error('Không tìm thấy dữ liệu');
                    }
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const data = await response.json();
                
                // Convert single object to array for table display
                const dataArray = Array.isArray(data) ? data : [data];
                const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📄 Kết quả tìm kiếm: ${dataArray.length} bản ghi</div>`;
                
                resultDiv.innerHTML = countText + createTableWithActions(dataArray, module);
            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                resultDiv.className = 'result show error';
            } finally {
                hideLoading();
            }
        }

        // Load CTDaoTao by filter (khoa and/or khoahoc)
        async function loadCTDaoTaoByFilter() {
            if (isLoading) return;
            
            const khoaInput = document.getElementById('ctdaotao-khoa');
            const khoahocInput = document.getElementById('ctdaotao-khoahoc');
            const khoa = khoaInput ? khoaInput.value.trim() : '';
            const khoahoc = khoahocInput ? khoahocInput.value.trim() : '';
            
            if (!khoa && !khoahoc) {
                showAlert('ctdaotao', '⚠️ Vui lòng nhập ít nhất Mã Khoa/Tên Khoa hoặc Khóa Học', 'error');
                return;
            }

            const resultDiv = document.getElementById('ctdaotao-result');
            resultDiv.innerHTML = '<div class="loading"></div> Đang tìm kiếm...';
            resultDiv.className = 'result show';
            
            showLoading('Đang tìm kiếm chương trình đào tạo...');

            try {
                const params = new URLSearchParams();
                if (khoa) params.append('khoa', khoa);
                if (khoahoc) params.append('khoahoc', khoahoc);
                
                const response = await fetch(`${API_BASE}/ctdaotao?${params.toString()}`);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                const count = Array.isArray(data) ? data.length : 0;
                const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📊 Kết quả: ${count} môn học</div>`;
                
                resultDiv.innerHTML = countText + createTableWithActions(data, 'ctdaotao');
            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                resultDiv.className = 'result show error';
            } finally {
                hideLoading();
            }
        }

        // Load DangKy by MaSV
        async function loadDangKyByMaSV() {
            if (isLoading) return;
            
            const masvInput = document.getElementById('dangky-masv');
            const masv = masvInput ? masvInput.value.trim() : '';
            
            if (!masv) {
                showAlert('dangky', '⚠️ Vui lòng nhập Mã Sinh Viên', 'error');
                return;
            }

            const resultDiv = document.getElementById('dangky-result');
            resultDiv.innerHTML = '<div class="loading"></div> Đang tải...';
            resultDiv.className = 'result show';
            
            showLoading('Đang tải thông tin đăng ký...');

            try {
                const response = await fetch(`${API_BASE}/dangky?masv=${encodeURIComponent(masv)}`);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                const count = Array.isArray(data) ? data.length : 0;
                const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📊 Sinh viên đã đăng ký: ${count} môn học</div>`;
                
                resultDiv.innerHTML = countText + createTableWithActions(data, 'dangky');
            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                resultDiv.className = 'result show error';
            } finally {
                hideLoading();
            }
        }

        // Open create modal
        function openCreateModal(module) {
            currentModule = module;
            currentAction = 'create';
            editingId = null;
            
            document.getElementById('modalTitle').textContent = getModalTitle(module, 'create');
            document.getElementById('formFields').innerHTML = getFormFields(module, {});
            hideModalAlert();
            document.getElementById('crudModal').classList.add('show');
        }

        // Open edit modal
        function openEditModal(module, data) {
            currentModule = module;
            currentAction = 'edit';
            
            if (module === 'khoa') editingId = data.MaKhoa;
            else if (module === 'monhoc') editingId = data.MaMH;
            else if (module === 'sinhvien') editingId = data.MaSV;
            else if (module === 'dangky') editingId = { masv: data.MaSV, mamon: data.MaMon };
            
            document.getElementById('modalTitle').textContent = getModalTitle(module, 'edit');
            document.getElementById('formFields').innerHTML = getFormFields(module, data);
            hideModalAlert();
            document.getElementById('crudModal').classList.add('show');
        }

        // Close modal
        function closeModal() {
            document.getElementById('crudModal').classList.remove('show');
            document.getElementById('crudForm').reset();
            hideModalAlert();
        }

        // Show alert in modal
        function showModalAlert(message, type = 'error') {
            const alertDiv = document.getElementById('modalAlert');
            alertDiv.textContent = message;
            alertDiv.className = 'alert alert-' + type;
            alertDiv.style.display = 'block';
            
            // Scroll to top of modal to see alert
            const modalContent = document.querySelector('.modal-content');
            if (modalContent) {
                modalContent.scrollTop = 0;
            }
        }

        // Hide modal alert
        function hideModalAlert() {
            const alertDiv = document.getElementById('modalAlert');
            alertDiv.style.display = 'none';
            alertDiv.className = 'alert';
        }

        // Get modal title
        function getModalTitle(module, action) {
            const titles = {
                khoa: { create: '➕ Thêm Khoa Mới', edit: '✏️ Sửa Thông Tin Khoa' },
                monhoc: { create: '➕ Thêm Môn Học Mới', edit: '✏️ Sửa Thông Tin Môn Học' },
                sinhvien: { create: '➕ Thêm Sinh Viên Mới', edit: '✏️ Sửa Thông Tin Sinh Viên' },
                ctdaotao: { create: '➕ Thêm Môn Vào CTĐT', edit: '' },
                dangky: { create: '➕ Đăng Ký Môn Học', edit: '✏️ Cập Nhật Điểm Thi' }
            };
            return titles[module][action];
        }

        // Get form fields for each module
        function getFormFields(module, data = {}) {
            let fields = '';
            
            if (module === 'khoa') {
                fields = `
                    <div class="form-group">
                        <label>Mã Khoa <span class="required">*</span></label>
                        <input type="text" id="field-MaKhoa" value="${data.MaKhoa || ''}" maxlength="10" ${currentAction === 'edit' ? 'readonly' : ''} required>
                        ${currentAction === 'edit' ? '<small style="color: #64748b;">🔒 Mã khoa không thể chỉnh sửa</small>' : '<small style="color: #64748b;">Ví dụ: CNTT, NN, LUAT</small>'}
                    </div>
                    <div class="form-group">
                        <label>Tên Khoa <span class="required">*</span></label>
                        <input type="text" id="field-TenKhoa" value="${data.TenKhoa || ''}" required>
                    </div>
                `;
            } else if (module === 'monhoc') {
                fields = `
                    <div class="form-group">
                        <label>Mã Môn Học <span class="required">*</span></label>
                        <input type="text" id="field-MaMH" value="${data.MaMH || ''}" maxlength="10" ${currentAction === 'edit' ? 'readonly' : ''} required>
                        ${currentAction === 'edit' ? '<small style="color: #64748b;">🔒 Mã môn học không thể chỉnh sửa</small>' : '<small style="color: #64748b;">Ví dụ: MH001, MH002</small>'}
                    </div>
                    <div class="form-group">
                        <label>Tên Môn Học <span class="required">*</span></label>
                        <input type="text" id="field-TenMH" value="${data.TenMH || ''}" required>
                    </div>
                `;
            } else if (module === 'sinhvien') {
                fields = `
                    <div class="form-group">
                        <label>Mã Sinh Viên <span class="required">*</span></label>
                        <input type="text" id="field-MaSV" value="${data.MaSV || ''}" maxlength="20" ${currentAction === 'edit' ? 'readonly' : ''} required>
                        ${currentAction === 'edit' ? '<small style="color: #64748b;">🔒 Mã sinh viên không thể chỉnh sửa</small>' : '<small style="color: #64748b;">Ví dụ: 25DH000001, 24DH000002</small>'}
                    </div>
                    <div class="form-group">
                        <label>Họ Tên <span class="required">*</span></label>
                        <input type="text" id="field-HoTen" value="${data.HoTen || ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Mã Khoa <span class="required">*</span></label>
                        <input type="text" id="field-MaKhoa" value="${data.MaKhoa || ''}" maxlength="10" required>
                        <small style="color: #64748b;">Ví dụ: CNTT, NN, LUAT</small>
                    </div>
                    <div class="form-group">
                        <label>Khóa Học <span class="required">*</span></label>
                        <input type="number" id="field-KhoaHoc" value="${data.KhoaHoc || ''}" min="2015" max="2030" required>
                        <small style="color: #64748b;">Năm nhập học (2015-2030)</small>
                    </div>
                `;
            } else if (module === 'ctdaotao') {
                fields = `
                    <div class="form-group">
                        <label>Mã Khoa <span class="required">*</span></label>
                        <input type="text" id="field-MaKhoa" value="${data.MaKhoa || ''}" maxlength="10" required>
                    </div>
                    <div class="form-group">
                        <label>Khóa Học <span class="required">*</span></label>
                        <input type="number" id="field-KhoaHoc" value="${data.KhoaHoc || ''}" min="2015" max="2030" required>
                    </div>
                    <div class="form-group">
                        <label>Mã Môn Học <span class="required">*</span></label>
                        <input type="text" id="field-MaMH" value="${data.MaMH || ''}" maxlength="10" required>
                    </div>
                `;
            } else if (module === 'dangky') {
                if (currentAction === 'create') {
                    fields = `
                        <div class="form-group">
                            <label>Mã Sinh Viên <span class="required">*</span></label>
                            <input type="text" id="field-MaSV" value="${data.MaSV || ''}" maxlength="20" required>
                            <small style="color: #64748b;">Ví dụ: 25DH000001, 24DH000002</small>
                        </div>
                        <div class="form-group">
                            <label>Mã Môn Học <span class="required">*</span></label>
                            <input type="text" id="field-MaMon" value="${data.MaMon || ''}" maxlength="10" required>
                            <small style="color: #64748b;">Ví dụ: MH001, MH002</small>
                        </div>
                        <div class="form-group">
                            <label>Điểm Thi</label>
                            <input type="number" id="field-DiemThi" value="${data.DiemThi || ''}" min="0" max="10" step="0.01">
                            <small style="color: #64748b;">Để trống nếu chưa có điểm (0-10)</small>
                        </div>
                    `;
                } else {
                    fields = `
                        <div class="form-group">
                            <label>Mã Sinh Viên</label>
                            <input type="text" value="${data.MaSV}" readonly>
                            <small style="color: #64748b;">🔒 Mã sinh viên không thể chỉnh sửa</small>
                        </div>
                        <div class="form-group">
                            <label>Mã Môn Học</label>
                            <input type="text" value="${data.MaMon}" readonly>
                            <small style="color: #64748b;">🔒 Mã môn học không thể chỉnh sửa</small>
                        </div>
                        <div class="form-group">
                            <label>Điểm Thi <span class="required">*</span></label>
                            <input type="number" id="field-DiemThi" value="${data.DiemThi || ''}" min="0" max="10" step="0.01" required>
                            <small style="color: #64748b;">Nhập điểm từ 0 đến 10</small>
                        </div>
                    `;
                }
            }
            
            return fields;
        }

        // Submit form
        async function submitForm() {
            if (isLoading) return;
            
            const formData = {};
            
            // Collect form data
            document.querySelectorAll('#formFields input').forEach(input => {
                if (input.id.startsWith('field-')) {
                    const fieldName = input.id.replace('field-', '');
                    const value = input.value.trim();
                    
                    // Only add non-empty values, or skip optional fields
                    if (value !== '') {
                        formData[fieldName] = value;
                    } else if (input.required) {
                        // Keep empty string for required fields (will be validated below)
                        formData[fieldName] = value;
                    }
                    // For optional fields with empty value, don't include in formData (send as undefined/null)
                }
            });

            // Validate required fields
            try {
                let hasValidationError = false;
                document.querySelectorAll('#formFields input[required]').forEach(input => {
                    if (input.id.startsWith('field-')) {
                        const fieldName = input.id.replace('field-', '');
                        if (!formData[fieldName] || formData[fieldName].trim() === '') {
                            showModalAlert(`⚠️ Vui lòng nhập ${fieldName}`, 'error');
                            hasValidationError = true;
                        }
                    }
                });
                
                if (hasValidationError) {
                    return;
                }
            } catch (validationError) {
                return;
            }

            // Hide any previous alerts
            hideModalAlert();
            
            const submitBtn = document.getElementById('submitBtn');
            setButtonLoading(submitBtn, true);
            showLoading(currentAction === 'create' ? 'Đang thêm dữ liệu...' : 'Đang cập nhật...');

            // Determine method and URL
            let method, url;
            
            if (currentAction === 'create') {
                method = 'POST';
                url = `${API_BASE}/${currentModule}`;
            } else {
                method = 'PUT';
                if (currentModule === 'dangky') {
                    url = `${API_BASE}/${currentModule}?masv=${editingId.masv}&mamon=${editingId.mamon}`;
                } else {
                    url = `${API_BASE}/${currentModule}?id=${editingId}`;
                }
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'Có lỗi xảy ra');
                }

                const result = await response.json();
                showAlert(currentModule, result.message || 'Thành công!', 'success');
                closeModal();
                loadData(currentModule);
            } catch (error) {
                showModalAlert(`❌ ${error.message}`, 'error');
            } finally {
                setButtonLoading(submitBtn, false);
                hideLoading();
            }
        }

        // Delete record
        async function deleteRecord(module, id) {
            if (isLoading) return;
            if (!confirm(`Bạn có chắc muốn xóa bản ghi này?`)) return;
            
            showLoading('Đang xóa dữ liệu...');

            try {
                const response = await fetch(`${API_BASE}/${module}?id=${id}`, {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'Có lỗi xảy ra');
                }

                const result = await response.json();
                showAlert(module, result.message || 'Xóa thành công!', 'success');
                loadData(module);
            } catch (error) {
                showAlert(module, `Lỗi: ${error.message}`, 'error');
                hideLoading();
            }
        }

        // Delete CTDaoTao
        async function deleteCTDaoTao(maKhoa, khoaHoc, maMH) {
            if (isLoading) return;
            if (!confirm(`Xóa môn ${maMH} khỏi CTĐT khoa ${maKhoa} khóa ${khoaHoc}?`)) return;
            
            showLoading('Đang xóa môn học khỏi CTĐT...');

            try {
                const response = await fetch(`${API_BASE}/ctdaotao?khoa=${maKhoa}&khoahoc=${khoaHoc}&monhoc=${maMH}`, {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'Có lỗi xảy ra');
                }

                const result = await response.json();
                showAlert('ctdaotao', result.message || 'Xóa thành công!', 'success');
                loadData('ctdaotao');
            } catch (error) {
                showAlert('ctdaotao', `Lỗi: ${error.message}`, 'error');
                hideLoading();
            }
        }

        // Delete DangKy
        async function deleteDangKy(maSV, maMon) {
            if (isLoading) return;
            if (!confirm(`Hủy đăng ký môn ${maMon} của sinh viên ${maSV}?`)) return;
            
            showLoading('Đang hủy đăng ký...');

            try {
                const response = await fetch(`${API_BASE}/dangky?masv=${maSV}&mamon=${maMon}`, {
                    method: 'DELETE'
                });

                if (!response.ok) {
                    const error = await response.json();
                    throw new Error(error.error || 'Có lỗi xảy ra');
                }

                const result = await response.json();
                showAlert('dangky', result.message || 'Xóa thành công!', 'success');
                loadData('dangky');
            } catch (error) {
                showAlert('dangky', `Lỗi: ${error.message}`, 'error');
                hideLoading();
            }
        }

        // Global queries
        async function callGlobalQuery(type) {
            if (isLoading) return;
            
            const resultDiv = document.getElementById(`global-result-${type}`);
            resultDiv.innerHTML = '<div class="loading"></div> Đang truy vấn...';
            resultDiv.className = 'result show';

            let params = new URLSearchParams();
            params.append('type', type);

            if (type === 1 || type === 3) {
                const masv = document.getElementById(`global-masv-${type}`).value.trim();
                if (!masv) {
                    alert('Vui lòng nhập Mã Sinh Viên');
                    resultDiv.innerHTML = '';
                    return;
                }
                params.append('masv', masv);
            } else if (type === 2) {
                const query = document.getElementById('global-query-2').value.trim();
                if (!query) {
                    alert('Vui lòng nhập Tên Khoa hoặc Mã Khoa');
                    resultDiv.innerHTML = '';
                    return;
                }
                params.append('query', query);
            }
            
            showLoading('Đang thực hiện truy vấn toàn cục...');

            try {
                const response = await fetch(`${API_BASE}/global?${params}`);
                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const data = await response.json();
                const count = Array.isArray(data) ? data.length : 0;
                const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📊 Kết quả: ${count} bản ghi</div>`;
                
                resultDiv.innerHTML = countText + createSimpleTable(data);
            } catch (error) {
                resultDiv.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                resultDiv.className = 'result show error';
            } finally {
                hideLoading();
            }
        }

        // Create simple table (no actions)
        function createSimpleTable(data) {
            if (!Array.isArray(data) || data.length === 0) {
                return '<p>Không có dữ liệu</p>';
            }

            const headers = Object.keys(data[0]);
            let table = '<table><thead><tr>';
            headers.forEach(h => {
                // Skip Site column if toggle is off
                if (h === 'Site' && !showSiteColumn) return;
                // Add special class for Site header
                const headerClass = h === 'Site' ? ' class="site-header"' : '';
                table += `<th${headerClass}>${h}</th>`;
            });
            table += '</tr></thead><tbody>';
            
            data.forEach(row => {
                table += '<tr>';
                headers.forEach(h => {
                    // Skip Site column if toggle is off
                    if (h === 'Site' && !showSiteColumn) return;
                    
                    const value = row[h] !== null && row[h] !== undefined ? row[h] : '';
                    let cellClass = '';
                    
                    // Add class for Site column
                    if (h === 'Site') {
                        if (value === 'Site A') cellClass = ' class="site-a"';
                        else if (value === 'Site B') cellClass = ' class="site-b"';
                        else if (value === 'Site C') cellClass = ' class="site-c"';
                    }
                    
                    table += `<td${cellClass}>${value}</td>`;
                });
                table += '</tr>';
            });
            
            table += '</tbody></table>';
            return table;
        }

        // Load initial data on page load
        window.addEventListener('DOMContentLoaded', () => {
            loadData('khoa');
            loadSettings();
        });

        // Close modal when clicking outside
        window.addEventListener('click', (event) => {
            const crudModal = document.getElementById('crudModal');
            const settingsModal = document.getElementById('settingsModal');
            if (event.target === crudModal) {
                closeModal();
            }
            if (event.target === settingsModal) {
                closeSettingsModal();
            }
        });

        // ===== SETTINGS FUNCTIONS =====

        function openSettingsModal() {
            document.getElementById('settingsModal').classList.add('show');
        }

        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.remove('show');
        }

        function loadSettings() {
            const settings = JSON.parse(localStorage.getItem('uiSettings')) || {};
            
            if (settings.bgColor) {
                document.getElementById('bgColor').value = settings.bgColor;
                document.getElementById('bgColorText').value = settings.bgColor;
                updateBackgroundColor(false);
            }
        }

        function saveSettings() {
            const settings = {
                bgColor: document.getElementById('bgColor').value
            };
            localStorage.setItem('uiSettings', JSON.stringify(settings));
        }

        function updateBackgroundColor(save = true) {
            const color = document.getElementById('bgColor').value;
            
            // Remove animation and gradient
            document.body.style.background = color;
            document.body.style.backgroundSize = 'auto';
            document.body.style.animation = 'none';
            
            // Update text input
            document.getElementById('bgColorText').value = color;
            
            if (save) saveSettings();
        }

        function updateBackgroundColorFromText() {
            const textInput = document.getElementById('bgColorText');
            const colorInput = document.getElementById('bgColor');
            const value = textInput.value.trim();
            
            // Validate hex color
            if (/^#[0-9A-F]{6}$/i.test(value)) {
                colorInput.value = value;
                updateBackgroundColor();
            } else {
                alert('⚠️ Vui lòng nhập mã màu hợp lệ (ví dụ: #ffffff)');
            }
        }

        function applyPresetColor(color) {
            document.getElementById('bgColor').value = color;
            document.getElementById('bgColorText').value = color;
            updateBackgroundColor();
        }

        function resetToDefault() {
            localStorage.removeItem('uiSettings');
            
            // Reset to default light gray
            const defaultColor = '#f8fafc';
            document.getElementById('bgColor').value = defaultColor;
            document.getElementById('bgColorText').value = defaultColor;
            updateBackgroundColor(false);
            
            alert('✅ Đã khôi phục màu nền mặc định!');
        }
    </script>
</body>
</html>
