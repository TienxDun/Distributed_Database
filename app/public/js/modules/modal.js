/**
 * Modal management module
 */

import { FIELDS_CONFIG, MODAL_TITLES, PRIMARY_KEYS } from '../config.js';
import { setButtonLoading, getLoadingState } from '../utils/dom.js';
import { collectFormData, validateRequiredFields } from '../utils/validation.js';
import { createRecord, updateRecord, loadData } from './crud.js';
import { fetchOptionsForField } from '../utils/api.js';

// Module state
let currentModule = '';
let currentAction = ''; // 'create' or 'edit'
let editingId = null;

/**
 * Handle Escape key press to close modal
 * @param {KeyboardEvent} event - Keyboard event
 */
function handleEscapeKey(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
}

/**
 * Add Escape key listener when modal opens
 */
function addEscapeKeyListener() {
    document.addEventListener('keydown', handleEscapeKey);
}

/**
 * Remove Escape key listener when modal closes
 */
function removeEscapeKeyListener() {
    document.removeEventListener('keydown', handleEscapeKey);
}

/**
 * Open create modal
 * @param {string} module - Module name
 */
export async function openCreateModal(module) {
    currentModule = module;
    currentAction = 'create';
    editingId = null;
    
    document.getElementById('modalTitle').textContent = MODAL_TITLES[module].create;
    document.getElementById('formFields').innerHTML = buildFormFields(module, {});
    hideModalAlert();
    document.getElementById('crudModal').classList.add('show');
    
    // Load options for select fields
    await populateSelectOptions();
    
    // Add Escape key listener
    addEscapeKeyListener();
}

/**
 * Open edit modal
 * @param {string} module - Module name
 * @param {Object} data - Record data
 */
export async function openEditModal(module, data) {
    currentModule = module;
    currentAction = 'edit';
    
    // Set editing ID based on primary key
    const primaryKey = PRIMARY_KEYS[module];
    if (Array.isArray(primaryKey)) {
        // Composite key (e.g., dangky)
        if (module === 'dangky') {
            editingId = { masv: data.MaSV, mamon: data.MaMon };
        }
    } else {
        editingId = data[primaryKey];
    }
    
    document.getElementById('modalTitle').textContent = MODAL_TITLES[module].edit;
    document.getElementById('formFields').innerHTML = buildFormFields(module, data);
    hideModalAlert();
    document.getElementById('crudModal').classList.add('show');
    
    // Load options for select fields
    await populateSelectOptions();
    
    // Add Escape key listener
    addEscapeKeyListener();
}

/**
 * Close modal
 */
export function closeModal() {
    document.getElementById('crudModal').classList.remove('show');
    document.getElementById('crudForm').reset();
    hideModalAlert();
    
    // Remove Escape key listener
    removeEscapeKeyListener();
}

/**
 * Show alert in modal
 * @param {string} message - Alert message
 * @param {string} type - Alert type ('success', 'error', 'info')
 */
export function showModalAlert(message, type = 'error') {
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

/**
 * Hide modal alert
 */
export function hideModalAlert() {
    const alertDiv = document.getElementById('modalAlert');
    alertDiv.style.display = 'none';
    alertDiv.className = 'alert';
}

/**
 * Populate select dropdowns with options from API
 * @returns {Promise<void>}
 */
async function populateSelectOptions() {
    const selectElements = document.querySelectorAll('select[data-options-from]');
    
    if (selectElements.length === 0) {
        return; // No select fields to populate
    }
    
    console.log(`[populateSelectOptions] Found ${selectElements.length} select fields to populate`);
    
    // Populate all selects in parallel
    const promises = Array.from(selectElements).map(async (selectEl) => {
        const endpoint = selectEl.dataset.optionsFrom;
        const valueField = selectEl.dataset.optionValue;
        const labelFields = JSON.parse(selectEl.dataset.optionLabel);
        const selectedValue = selectEl.dataset.selectedValue || '';
        
        try {
            // Fetch options from API
            const options = await fetchOptionsForField(endpoint, valueField, labelFields);
            
            // Clear loading option
            selectEl.innerHTML = '';
            selectEl.classList.remove('select-loading');
            
            // Add empty option
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = `-- Chọn ${selectEl.previousElementSibling.textContent.replace(' *', '')} --`;
            selectEl.appendChild(emptyOption);
            
            // Add options
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.textContent = opt.label;
                if (opt.value === selectedValue) {
                    option.selected = true;
                }
                selectEl.appendChild(option);
            });
            
            console.log(`[populateSelectOptions] Populated ${options.length} options for ${endpoint}`);
        } catch (error) {
            console.error(`[populateSelectOptions] Error loading options from ${endpoint}:`, error);
            selectEl.innerHTML = '<option value="">❌ Lỗi tải dữ liệu</option>';
            selectEl.classList.remove('select-loading');
            selectEl.classList.add('select-error');
        }
    });
    
    await Promise.all(promises);
    console.log('[populateSelectOptions] All selects populated');
}

/**
 * Build form fields HTML
 * @param {string} module - Module name
 * @param {Object} data - Form data (for edit mode)
 * @returns {string} HTML string
 */
function buildFormFields(module, data = {}) {
    let fieldsConfig = FIELDS_CONFIG[module];
    
    // Handle special case for dangky (different fields for create/edit)
    if (module === 'dangky') {
        fieldsConfig = FIELDS_CONFIG[module][currentAction];
    }
    
    let html = '';
    
    for (const field of fieldsConfig) {
        const value = data[field.name] || '';
        const isReadonly = (field.readonly === true) || 
                          (field.readonly === 'edit' && currentAction === 'edit') || 
                          (field.readonly === 'create' && currentAction === 'create');
        
        html += '<div class="form-group">';
        html += `<label>${field.label}`;
        if (field.required) {
            html += ' <span class="required">*</span>';
        }
        html += '</label>';
        
        // Check if field is a select dropdown
        if (field.type === 'select') {
            // Build select element
            html += `<select id="field-${field.name}" class="select-loading"`;
            if (field.required) html += ' required';
            if (isReadonly) html += ' disabled';
            html += ' data-options-from="' + field.optionsFrom + '"';
            html += ' data-option-value="' + field.optionValue + '"';
            html += ' data-option-label=\'' + JSON.stringify(field.optionLabel) + '\'';
            html += ' data-selected-value="' + value + '"';
            html += '>';
            html += `<option value="">⏳ ${field.placeholder || 'Đang tải...'}</option>`;
            html += '</select>';
            
            // Add helper text
            if (isReadonly && field.lockMessage) {
                html += `<small style="color: #64748b;">${field.lockMessage}</small>`;
            } else if (currentAction === 'edit' && field.readonly === 'edit') {
                html += `<small style="color: #64748b;">🔒 ${field.label} không thể chỉnh sửa</small>`;
            } else if (field.placeholder) {
                html += `<small style="color: #64748b;">${field.placeholder}</small>`;
            }
        } else {
            // Build regular input element
            const attrs = [];
            attrs.push(`type="${field.type || 'text'}"`);
            attrs.push(`id="field-${field.name}"`);
            attrs.push(`value="${value}"`);
            
            if (field.maxlength) attrs.push(`maxlength="${field.maxlength}"`);
            if (field.min !== undefined) attrs.push(`min="${field.min}"`);
            if (field.max !== undefined) attrs.push(`max="${field.max}"`);
            if (field.step) attrs.push(`step="${field.step}"`);
            if (field.required) attrs.push('required');
            if (isReadonly) attrs.push('readonly');
            
            html += `<input ${attrs.join(' ')}>`;
            
            // Add helper text
            if (isReadonly && field.lockMessage) {
                html += `<small style="color: #64748b;">${field.lockMessage}</small>`;
            } else if (currentAction === 'edit' && field.readonly === 'edit') {
                html += `<small style="color: #64748b;">🔒 ${field.label} không thể chỉnh sửa</small>`;
            } else if (field.placeholder) {
                html += `<small style="color: #64748b;">${field.placeholder}</small>`;
            }
        }
        
        html += '</div>';
    }
    
    return html;
}

/**
 * Submit form (create or update)
 */
export async function submitForm() {
    if (getLoadingState()) return;
    
    // Collect form data
    const formData = collectFormData('#formFields', 'field-');
    
    // Special handling for dangky: prioritize MaSV_input over MaSV dropdown
    if (currentModule === 'dangky' && currentAction === 'create') {
        if (formData['MaSV_input'] && formData['MaSV_input'].trim() !== '') {
            formData['MaSV'] = formData['MaSV_input'].trim();
            delete formData['MaSV_input']; // Remove the helper field
        }
        // If MaSV_input is empty but MaSV dropdown has value, use dropdown value
        else if (formData['MaSV'] && formData['MaSV'].trim() !== '') {
            // Keep the dropdown value
        }
        // If both are empty, validation will catch it
    }
    
    // Validate required fields
    const requiredFields = [];
    document.querySelectorAll('#formFields input[required]').forEach(input => {
        if (input.id.startsWith('field-')) {
            const fieldName = input.id.replace('field-', '');
            
            // Special validation for dangky MaSV fields
            if (currentModule === 'dangky' && currentAction === 'create' && fieldName === 'MaSV') {
                // Skip validation for MaSV dropdown since we have MaSV_input as alternative
                return;
            }
            
            if (!formData[fieldName] || formData[fieldName].trim() === '') {
                requiredFields.push(fieldName);
            }
        }
    });
    
    // Special validation for dangky: ensure at least one MaSV field is filled
    if (currentModule === 'dangky' && currentAction === 'create') {
        const hasMaSV = formData['MaSV'] && formData['MaSV'].trim() !== '';
        const hasMaSVInput = formData['MaSV_input'] && formData['MaSV_input'].trim() !== '';
        
        if (!hasMaSV && !hasMaSVInput) {
            showModalAlert('⚠️ Vui lòng chọn sinh viên từ danh sách hoặc nhập mã sinh viên', 'error');
            return;
        }
    }
    
    if (requiredFields.length > 0) {
        showModalAlert(`⚠️ Vui lòng nhập ${requiredFields.join(', ')}`, 'error');
        return;
    }
    
    // Hide any previous alerts
    hideModalAlert();
    
    const submitBtn = document.getElementById('submitBtn');
    setButtonLoading(submitBtn, true);
    
    try {
        let result;
        
        if (currentAction === 'create') {
            result = await createRecord(currentModule, formData);
        } else {
            result = await updateRecord(currentModule, editingId, formData);
        }
        
        // Import showAlert dynamically to avoid circular dependency
        const { showAlert } = await import('../utils/dom.js');
        showAlert(currentModule, result.message || 'Thành công!', 'success');
        
        closeModal();
        loadData(currentModule);
    } catch (error) {
        showModalAlert(`❌ ${error.message}`, 'error');
    } finally {
        setButtonLoading(submitBtn, false);
    }
}

/**
 * Site Status Functions
 */

/**
 * Toggle site status panel
 */
export function toggleSiteStatusPanel() {
    const panel = document.getElementById('siteStatusPanel');
    panel.classList.toggle('open');
    
    // Load status if panel is opening and not loaded yet
    if (panel.classList.contains('open')) {
        const content = document.getElementById('site-status-compact');
        if (content.children.length === 1 && content.children[0].classList.contains('loading-spinner')) {
            loadSiteStatus();
        }
    }
}

/**
 * Load and display site status
 */
export async function loadSiteStatus() {
    const content = document.getElementById('site-status-compact');
    const refreshBtn = document.getElementById('panel-refresh-btn');
    const refreshIcon = document.getElementById('panel-refresh-icon');

    try {
        // Show loading state
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshIcon.textContent = '⏳';
        }

        const { API_BASE } = await import('../config.js');
        const response = await fetch(`${API_BASE}/health`);

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const text = await response.text();
        const data = JSON.parse(text);

        if (data.error) {
            throw new Error(data.error);
        }

        content.innerHTML = buildCompactSiteStatusHTML(data);

    } catch (error) {
        console.error('Site status error:', error);
        content.innerHTML = `
            <div style="text-align: center; padding: 2rem; color: #ef4444; font-size: 0.8rem;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">❌</div>
                <div style="font-weight: 600; margin-bottom: 0.5rem;">Lỗi tải dữ liệu</div>
                <div style="color: #64748b; margin-bottom: 1rem;">${error.message}</div>
                <button class="btn btn-primary" onclick="refreshSiteStatus()" style="padding: 0.25rem 0.75rem; font-size: 0.8rem;">
                    🔄 Thử lại
                </button>
            </div>
        `;
    } finally {
        // Reset refresh button
        if (refreshBtn) {
            refreshBtn.disabled = false;
            refreshIcon.textContent = '🔄';
        }
    }
}

/**
 * Refresh site status
 */
export async function refreshSiteStatus() {
    await loadSiteStatus();
}

/**
 * Build compact HTML for site status panel
 */
function buildCompactSiteStatusHTML(data) {
    const statusConfig = {
        healthy: { icon: '✅', color: 'green', text: 'Hoạt động tốt' },
        degraded: { icon: '⚠️', color: 'orange', text: 'Hoạt động hạn chế' },
        critical: { icon: '❌', color: 'red', text: 'Nguy hiểm' }
    };
    
    const overall = statusConfig[data.overall_status] || statusConfig.critical;
    
    let html = `
        <div class="site-status-compact-overall ${data.overall_status}">
            ${overall.icon} ${overall.text.toUpperCase()}<br>
            <small>${data.healthy_sites}/${data.total_sites} sites</small>
        </div>
        
        <div class="site-status-compact-grid">
    `;
    
    for (const [siteKey, site] of Object.entries(data.sites)) {
        const statusClass = site.status;
        const responseTime = site.response_time > 0 ? `${site.response_time}ms` : 'N/A';
        const statusText = site.status === 'healthy' ? '✅ OK' : site.status === 'unhealthy' ? '❌ Down' : '❓ Unknown';
        
        html += `
            <div class="site-status-compact-card ${statusClass}">
                <div class="site-status-compact-name">${site.name.replace('Site ', 'S').replace('Global DB', 'Global')}</div>
                <div class="site-status-compact-metrics">
                    <div class="site-status-compact-status">
                        <span class="site-status-compact-indicator ${statusClass}"></span>
                        ${statusText.split(' ')[0]}
                    </div>
                    <div class="site-status-compact-response">${responseTime}</div>
                </div>
            </div>
        `;
    }
    
    html += `
        </div>
        
        <div style="text-align: center; margin-top: 1rem; color: #64748b; font-size: 0.7rem;">
            Cập nhật: ${new Date().toLocaleTimeString('vi-VN')}
        </div>
    `;
    
    return html;
}

/**
 * Build HTML for site status display
 */
function buildSiteStatusHTML(data) {
    const statusConfig = {
        healthy: { icon: '✅', color: 'green', text: 'Hoạt động tốt' },
        degraded: { icon: '⚠️', color: 'orange', text: 'Hoạt động hạn chế' },
        critical: { icon: '❌', color: 'red', text: 'Nguy hiểm' }
    };
    
    const overall = statusConfig[data.overall_status] || statusConfig.critical;
    
    let html = `
        <div class="overall-status ${data.overall_status}">
            <div class="overall-status-title">${overall.icon} ${overall.text.toUpperCase()}</div>
            <div class="overall-status-subtitle">
                ${data.healthy_sites}/${data.total_sites} sites hoạt động
            </div>
        </div>
        
        <div class="site-status-grid">
    `;
    
    for (const [siteKey, site] of Object.entries(data.sites)) {
        const statusClass = site.status;
        const indicatorClass = site.status;
        const responseTime = site.response_time > 0 ? `${site.response_time}ms` : 'N/A';
        
        html += `
            <div class="site-status-card ${statusClass}">
                <div class="site-status-header">
                    <div class="site-status-name">${site.name}</div>
                    <div class="site-status-indicator ${indicatorClass}"></div>
                </div>
                
                <div class="site-status-metric">
                    <span class="site-status-metric-label">Trạng thái:</span>
                    <span class="site-status-metric-value">${site.status === 'healthy' ? '✅ Hoạt động' : site.status === 'unhealthy' ? '❌ Không khả dụng' : '❓ Không xác định'}</span>
                </div>
                
                <div class="site-status-metric">
                    <span class="site-status-metric-label">Thời gian phản hồi:</span>
                    <span class="site-status-metric-value">${responseTime}</span>
                </div>
                
                <div class="site-status-details">
                    ${site.details}
                </div>
            </div>
        `;
    }
    
    html += `
        </div>
        
        <div style="text-align: center; margin-top: 2rem; color: #64748b; font-size: 0.9rem;">
            💡 Mẹo: Thời gian phản hồi dưới 100ms là tốt, 100-500ms là chấp nhận được, trên 500ms cần kiểm tra.
        </div>
    `;
    
    return html;
}
