/**
 * Modal management module
 */

import { FIELDS_CONFIG, MODAL_TITLES, PRIMARY_KEYS } from '../config.js';
import { setButtonLoading, getLoadingState } from '../utils/dom.js';
import { collectFormData, validateRequiredFields } from '../utils/validation.js';
import { createRecord, updateRecord, loadData } from './crud.js';

// Module state
let currentModule = '';
let currentAction = ''; // 'create' or 'edit'
let editingId = null;

/**
 * Open create modal
 * @param {string} module - Module name
 */
export function openCreateModal(module) {
    currentModule = module;
    currentAction = 'create';
    editingId = null;
    
    document.getElementById('modalTitle').textContent = MODAL_TITLES[module].create;
    document.getElementById('formFields').innerHTML = buildFormFields(module, {});
    hideModalAlert();
    document.getElementById('crudModal').classList.add('show');
}

/**
 * Open edit modal
 * @param {string} module - Module name
 * @param {Object} data - Record data
 */
export function openEditModal(module, data) {
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
        
        // Build input attributes
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
