/**
 * CRUD operations module
 */

import { apiGet, apiPost, apiPut, apiDelete, buildQueryString } from '../utils/api.js';
import { showLoading, hideLoading, getLoadingState, showAlert, renderResult, showResultError, showResultLoading } from '../utils/dom.js';
import { PRIMARY_KEYS } from '../config.js';

/**
 * Load all data for a module
 * @param {string} module - Module name
 */
export async function loadData(module) {
    if (getLoadingState()) return;
    
    showResultLoading(module, 'Đang tải dữ liệu...');
    showLoading('Đang tải dữ liệu...');

    try {
        const data = await apiGet(`/${module}`);
        renderResult(module, data, 'Tổng số');
    } catch (error) {
        showResultError(module, error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Delete a record
 * @param {string} module - Module name
 * @param {string} id - Record ID
 */
export async function deleteRecord(module, id) {
    if (getLoadingState()) return;
    if (!confirm(`Bạn có chắc muốn xóa bản ghi này?`)) return;
    
    showLoading('Đang xóa dữ liệu...');

    try {
        const result = await apiDelete(`/${module}?id=${id}`);
        showAlert(module, result.message || 'Xóa thành công!', 'success');
        await loadData(module);
    } catch (error) {
        showAlert(module, `Lỗi: ${error.message}`, 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete CTDaoTao record
 * @param {string} maKhoa - Mã khoa
 * @param {string|number} khoaHoc - Khóa học
 * @param {string} maMH - Mã môn học
 */
export async function deleteCTDaoTao(maKhoa, khoaHoc, maMH) {
    if (getLoadingState()) return;
    if (!confirm(`Xóa môn ${maMH} khỏi CTĐT khoa ${maKhoa} khóa ${khoaHoc}?`)) return;
    
    showLoading('Đang xóa môn học khỏi CTĐT...');

    try {
        const queryString = buildQueryString({
            khoa: maKhoa,
            khoahoc: khoaHoc,
            monhoc: maMH
        });
        const result = await apiDelete(`/ctdaotao${queryString}`);
        showAlert('ctdaotao', result.message || 'Xóa thành công!', 'success');
        await loadData('ctdaotao');
    } catch (error) {
        showAlert('ctdaotao', `Lỗi: ${error.message}`, 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Delete DangKy record
 * @param {string} maSV - Mã sinh viên
 * @param {string} maMon - Mã môn
 */
export async function deleteDangKy(maSV, maMon) {
    if (getLoadingState()) return;
    if (!confirm(`Hủy đăng ký môn ${maMon} của sinh viên ${maSV}?`)) return;
    
    showLoading('Đang hủy đăng ký...');

    try {
        const queryString = buildQueryString({
            masv: maSV,
            mamon: maMon
        });
        const result = await apiDelete(`/dangky${queryString}`);
        showAlert('dangky', result.message || 'Xóa thành công!', 'success');
        await loadData('dangky');
    } catch (error) {
        showAlert('dangky', `Lỗi: ${error.message}`, 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Create a new record
 * @param {string} module - Module name
 * @param {Object} formData - Form data
 * @returns {Promise<Object>} Response data
 */
export async function createRecord(module, formData) {
    showLoading('Đang thêm dữ liệu...');
    
    try {
        const result = await apiPost(`/${module}`, formData);
        return result;
    } finally {
        hideLoading();
    }
}

/**
 * Update an existing record
 * @param {string} module - Module name
 * @param {string|Object} id - Record ID (can be string or object for composite keys)
 * @param {Object} formData - Form data
 * @returns {Promise<Object>} Response data
 */
export async function updateRecord(module, id, formData) {
    showLoading('Đang cập nhật...');
    
    try {
        let queryString;
        
        if (module === 'dangky' && typeof id === 'object') {
            queryString = buildQueryString({
                masv: id.masv,
                mamon: id.mamon
            });
        } else {
            queryString = `?id=${encodeURIComponent(id)}`;
        }
        
        const result = await apiPut(`/${module}${queryString}`, formData);
        return result;
    } finally {
        hideLoading();
    }
}

/**
 * Show current active tab
 * @param {string} tabName - Tab name
 */
export function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
    
    // Load data when switching tabs (except global)
    if (tabName !== 'global') {
        loadData(tabName);
    }
}

/**
 * Toggle Site column visibility
 */
export function toggleSiteColumnVisibility() {
    const checked = document.getElementById('toggleSiteColumn').checked;
    
    // Import and use setSiteColumnVisibility from dom.js
    import('../utils/dom.js').then(domModule => {
        domModule.setSiteColumnVisibility(checked);
        
        // Reload current module data to apply changes
        const activeTab = document.querySelector('.tab-content.active');
        if (activeTab && activeTab.id !== 'global') {
            loadData(activeTab.id);
        }
    });
}
