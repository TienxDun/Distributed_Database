/**
 * Global query module
 */

import { apiGet, buildQueryString } from '../utils/api.js';
import { showLoading, hideLoading, getLoadingState, getSiteColumnVisibility } from '../utils/dom.js';

/**
 * Call global query
 * @param {number} type - Query type (1-4)
 */
export async function callGlobalQuery(type) {
    if (getLoadingState()) return;
    
    const resultDiv = document.getElementById(`global-result-${type}`);
    resultDiv.innerHTML = '<div class="loading"></div> Đang truy vấn...';
    resultDiv.className = 'result show';

    const params = { type };

    // Get parameters based on query type
    if (type === 1 || type === 3) {
        const masv = document.getElementById(`global-masv-${type}`).value.trim();
        if (!masv) {
            alert('Vui lòng nhập Mã Sinh Viên');
            resultDiv.innerHTML = '';
            return;
        }
        params.masv = masv;
    } else if (type === 2) {
        const query = document.getElementById('global-query-2').value.trim();
        if (!query) {
            alert('Vui lòng nhập Tên Khoa hoặc Mã Khoa');
            resultDiv.innerHTML = '';
            return;
        }
        params.query = query;
    }
    
    showLoading('Đang thực hiện truy vấn toàn cục...');

    try {
        const queryString = buildQueryString(params);
        const data = await apiGet(`/global${queryString}`);
        
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

/**
 * Create simple table without action buttons
 * @param {Array} data - Data array
 * @returns {string} HTML table string
 */
function createSimpleTable(data) {
    if (!Array.isArray(data) || data.length === 0) {
        return '<p>Không có dữ liệu</p>';
    }

    const showSiteColumn = getSiteColumnVisibility();
    const headers = Object.keys(data[0]);
    let table = '<table><thead><tr>';
    
    // Create headers
    headers.forEach(h => {
        // Skip Site column if toggle is off
        if (h === 'Site' && !showSiteColumn) return;
        // Add special class for Site header
        const headerClass = h === 'Site' ? ' class="site-header"' : '';
        table += `<th${headerClass}>${h}</th>`;
    });
    table += '</tr></thead><tbody>';
    
    // Create rows
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
