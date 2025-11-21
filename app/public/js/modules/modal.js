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
}

/**
 * Close modal
 */
export function closeModal() {
    document.getElementById('crudModal').classList.remove('show');
    document.getElementById('crudForm').reset();
    hideModalAlert();
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
    
    // Validate required fields
    const requiredFields = [];
    document.querySelectorAll('#formFields input[required]').forEach(input => {
        if (input.id.startsWith('field-')) {
            const fieldName = input.id.replace('field-', '');
            if (!formData[fieldName] || formData[fieldName].trim() === '') {
                requiredFields.push(fieldName);
            }
        }
    });
    
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
