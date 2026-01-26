/**
 * Maintenance and Demo Tools Logic
 */
import { apiGet, apiPost } from './utils/api.js';
import { showLoading, hideLoading, showAlert } from './utils/dom.js';

/**
 * Reset all data in the database
 */
async function resetDatabase() {
    if (!confirm('CẢNH BÁO: Hành động này sẽ xóa TOÀN BỘ dữ liệu trong database. Bạn có chắc chắn muốn tiếp tục?')) {
        return;
    }

    showLoading('Đang xóa sạch dữ liệu...');
    try {
        const result = await apiPost('/maintenance?action=reset');
        alert(result.message);
        exploreData(); // Refresh explorer
    } catch (error) {
        alert('Lỗi: ' + error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Seed database with sample data
 */
async function seedDatabase() {
    showLoading('Đang nạp dữ liệu mẫu...');
    try {
        const result = await apiPost('/maintenance?action=seed');
        alert(result.message);
        exploreData(); // Refresh explorer
    } catch (error) {
        alert('Lỗi: ' + error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Explore raw data from different sites
 */
async function exploreData() {
    const table = document.getElementById('exploreTable').value;
    const resultContainer = document.getElementById('exploreResult');

    try {
        const data = await apiGet(`/maintenance?action=explore&table=${table}`);

        renderSiteTable('site-a', data.site_a);
        renderSiteTable('site-b', data.site_b);
        renderSiteTable('site-c', data.site_c);

    } catch (error) {
        console.error('Explore failed:', error);
    }
}

/**
 * Helper to render a small table for a specific site
 */
function renderSiteTable(containerId, rows) {
    const container = document.getElementById(`${containerId}-table`);

    if (!rows || rows.length === 0) {
        container.innerHTML = '<p style="text-align:center; opacity:0.5; font-style:italic;">Trống</p>';
        return;
    }

    const headers = Object.keys(rows[0]);
    let html = '<table><thead><tr>';
    headers.forEach(h => html += `<th>${h}</th>`);
    html += '</tr></thead><tbody>';

    rows.forEach(row => {
        html += '<tr>';
        headers.forEach(h => html += `<td>${row[h]}</td>`);
        html += '</tr>';
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

// Expose functions to global window scope
window.resetDatabase = resetDatabase;
window.seedDatabase = seedDatabase;
window.exploreData = exploreData;

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    exploreData();
});
