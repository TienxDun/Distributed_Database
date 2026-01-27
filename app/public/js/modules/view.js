/**
 * View/Search module for viewing data by filters
 */

import { apiGet, buildQueryString } from '../utils/api.js';
import { showLoading, hideLoading, getLoadingState, showAlert, renderResult, showResultError, showResultLoading } from '../utils/dom.js';

/**
 * Load data by ID for khoa, monhoc, sinhvien
 * @param {string} module - Module name
 */
export async function loadDataById(module) {
    console.log('🔍 Button clicked: Load Data By ID -', module);
    if (getLoadingState()) return;
    
    const idInput = document.getElementById(`${module}-id`);
    const id = idInput ? idInput.value.trim() : '';
    
    if (!id) {
        showAlert(module, '⚠️ Vui lòng nhập ID để tìm kiếm', 'error');
        return;
    }

    showResultLoading(module, 'Đang tìm kiếm...');
    showLoading('Đang tìm kiếm dữ liệu...');

    try {
        const data = await apiGet(`/${module}?id=${encodeURIComponent(id)}`);
        
        // Convert single object to array for table display
        const dataArray = Array.isArray(data) ? data : [data];
        renderResult(module, dataArray, 'Kết quả tìm kiếm');
    } catch (error) {
        showResultError(module, error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Load CTDaoTao by filter (khoa and/or khoahoc)
 */
export async function loadCTDaoTaoByFilter() {
    console.log('🔍 Button clicked: Load CTDaoTao By Filter');
    if (getLoadingState()) return;
    
    const khoaInput = document.getElementById('ctdaotao-khoa');
    const khoahocInput = document.getElementById('ctdaotao-khoahoc');
    const khoa = khoaInput ? khoaInput.value.trim() : '';
    const khoahoc = khoahocInput ? khoahocInput.value.trim() : '';
    
    if (!khoa && !khoahoc) {
        showAlert('ctdaotao', '⚠️ Vui lòng nhập ít nhất Mã Khoa/Tên Khoa hoặc Khóa Học', 'error');
        return;
    }

    showResultLoading('ctdaotao', 'Đang tìm kiếm...');
    showLoading('Đang tìm kiếm chương trình đào tạo...');

    try {
        const queryString = buildQueryString({
            khoa: khoa,
            khoahoc: khoahoc
        });
        
        const data = await apiGet(`/ctdaotao${queryString}`);
        const count = Array.isArray(data) ? data.length : 0;
        renderResult('ctdaotao', data, `Kết quả: ${count} môn học`);
    } catch (error) {
        showResultError('ctdaotao', error.message);
    } finally {
        hideLoading();
    }
}

/**
 * Load DangKy by MaSV
 */
export async function loadDangKyByMaSV() {
    console.log('Button clicked: loadDangKyByMaSV');
    if (getLoadingState()) return;
    
    const masvInput = document.getElementById('dangky-masv');
    const masv = masvInput ? masvInput.value.trim() : '';
    
    if (!masv) {
        showAlert('dangky', '⚠️ Vui lòng nhập Mã Sinh Viên', 'error');
        return;
    }

    showResultLoading('dangky', 'Đang tải...');
    showLoading('Đang tải thông tin đăng ký...');

    try {
        const data = await apiGet(`/dangky?masv=${encodeURIComponent(masv)}`);
        const count = Array.isArray(data) ? data.length : 0;
        renderResult('dangky', data, `Sinh viên đã đăng ký: ${count} môn học`);
    } catch (error) {
        showResultError('dangky', error.message);
    } finally {
        hideLoading();
    }
}
