/**
 * DOM manipulation utilities
 */

// Global state
let isLoading = false;
let showSiteColumn = true;

/**
 * Show loading overlay
 * @param {string} message - Loading message to display
 */
export function showLoading(message = 'Đang xử lý...') {
    const overlay = document.getElementById('loadingOverlay');
    const text = overlay.querySelector('.loading-text');
    text.textContent = message;
    overlay.classList.add('show');
    isLoading = true;
}

/**
 * Hide loading overlay
 */
export function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.remove('show');
    isLoading = false;
}

/**
 * Check if currently loading
 * @returns {boolean} Loading state
 */
export function getLoadingState() {
    return isLoading;
}

/**
 * Set button loading state
 * @param {HTMLButtonElement} button - Button element
 * @param {boolean} loading - Loading state
 */
export function setButtonLoading(button, loading) {
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

/**
 * Show alert message in module
 * @param {string} module - Module name
 * @param {string} message - Alert message
 * @param {string} type - Alert type ('success', 'error', 'info')
 */
export function showAlert(module, message, type = 'success') {
    const alertDiv = document.getElementById(`${module}-alert`);
    alertDiv.className = `alert alert-${type} show`;
    alertDiv.textContent = message;

    setTimeout(() => {
        alertDiv.classList.remove('show');
    }, 5000);
}

/**
 * Get Site column visibility state
 * @returns {boolean} Site column visibility
 */
export function getSiteColumnVisibility() {
    return showSiteColumn;
}

/**
 * Set Site column visibility state
 * @param {boolean} visible - Visibility state
 */
export function setSiteColumnVisibility(visible) {
    showSiteColumn = visible;
}

/**
 * Create HTML table with action buttons from data
 * @param {Array} data - Array of data objects
 * @param {string} module - Module name
 * @returns {string} HTML table string
 */
export function createTableWithActions(data, module) {
    if (!Array.isArray(data) || data.length === 0) {
        return '<p>Không có dữ liệu</p>';
    }

    const headers = Object.keys(data[0]);
    let table = '<table><thead><tr>';

    // Helper to identify Site column case-insensitively
    const isSiteColumn = (h) => h.toLowerCase() === 'site';

    // Create headers
    headers.forEach(h => {
        // Skip Site column if toggle is off
        if (isSiteColumn(h) && !showSiteColumn) return;
        // Add special class for Site header
        const headerClass = isSiteColumn(h) ? ' class="site-header"' : '';
        // Display header (capitalize first letter if it's 'site')
        const displayHeader = isSiteColumn(h) ? 'Site' : h;
        table += `<th${headerClass}>${displayHeader}</th>`;
    });
    table += '<th>Thao tác</th></tr></thead><tbody>';

    // Create rows
    data.forEach(row => {
        table += '<tr>';
        headers.forEach(h => {
            // Skip Site column if toggle is off
            if (isSiteColumn(h) && !showSiteColumn) return;

            const value = row[h] !== null && row[h] !== undefined ? row[h] : '';
            let cellClass = '';

            // Add class for Site column
            if (isSiteColumn(h)) {
                if (value === 'Site A') cellClass = ' class="site-a"';
                else if (value === 'Site B') cellClass = ' class="site-b"';
                else if (value === 'Site C') cellClass = ' class="site-c"';
            }

            table += `<td${cellClass}>${value}</td>`;
        });

        // Action buttons
        table += '<td class="action-buttons">';
        table += getActionButtons(module, row);
        table += '</td></tr>';
    });

    table += '</tbody></table>';
    return table;
}

/**
 * Get action buttons HTML for a row
 * @param {string} module - Module name
 * @param {Object} row - Data row object
 * @returns {string} HTML buttons string
 */
function getActionButtons(module, row) {
    let buttons = '';

    if (module === 'khoa') {
        buttons += `<button class="btn-edit" onclick='window.openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
        buttons += `<button class="btn-delete" onclick='window.deleteRecord("${module}", "${row.MaKhoa}")'>🗑️ Xóa</button>`;
    } else if (module === 'monhoc') {
        buttons += `<button class="btn-edit" onclick='window.openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
        buttons += `<button class="btn-delete" onclick='window.deleteRecord("${module}", "${row.MaMH}")'>🗑️ Xóa</button>`;
    } else if (module === 'sinhvien') {
        buttons += `<button class="btn-edit" onclick='window.openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Sửa</button>`;
        buttons += `<button class="btn-delete" onclick='window.deleteRecord("${module}", "${row.MaSV}")'>🗑️ Xóa</button>`;
    } else if (module === 'ctdaotao') {
        buttons += `<button class="btn-delete" onclick='window.deleteCTDaoTao("${row.MaKhoa}", "${row.KhoaHoc}", "${row.MaMH}")'>🗑️ Xóa</button>`;
    } else if (module === 'dangky') {
        buttons += `<button class="btn-edit" onclick='window.openEditModal("${module}", ${JSON.stringify(row)})'>✏️ Cập nhật điểm</button>`;
        buttons += `<button class="btn-delete" onclick='window.deleteDangKy("${row.MaSV}", "${row.MaMon}")'>🗑️ Xóa</button>`;
    }

    return buttons;
}

/**
 * Render result in module result div
 * @param {string} module - Module name
 * @param {Array} data - Data array
 * @param {string} countLabel - Label for count display
 */
export function renderResult(module, data, countLabel = 'Tổng số') {
    const resultDiv = document.getElementById(`${module}-result`);
    const count = Array.isArray(data) ? data.length : 0;
    const countText = `<div style="background: #e0f2fe; color: #1e293b; padding: 0.5rem 1rem; border-radius: 6px; margin-bottom: 1rem; display: inline-block; font-weight: 600;">📊 ${countLabel}: ${count} bản ghi</div>`;

    resultDiv.innerHTML = countText + createTableWithActions(data, module);
    resultDiv.className = 'result show';
}

/**
 * Show error in result div
 * @param {string} module - Module name
 * @param {string} errorMessage - Error message
 */
export function showResultError(module, errorMessage) {
    const resultDiv = document.getElementById(`${module}-result`);
    resultDiv.innerHTML = `<strong>Lỗi:</strong> ${errorMessage}`;
    resultDiv.className = 'result show error';
}

/**
 * Show loading in result div
 * @param {string} module - Module name
 * @param {string} message - Loading message
 */
export function showResultLoading(module, message = 'Đang tải dữ liệu...') {
    const resultDiv = document.getElementById(`${module}-result`);
    resultDiv.innerHTML = `<div class="loading"></div> ${message}`;
    resultDiv.className = 'result show';
}
