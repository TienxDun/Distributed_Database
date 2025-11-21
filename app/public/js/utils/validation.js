/**
 * Form validation utilities
 */

/**
 * Validate required fields in a form
 * @param {Object} formData - Form data object
 * @param {Array} requiredFields - Array of required field names
 * @returns {Object} { valid: boolean, errors: Array<string> }
 */
export function validateRequiredFields(formData, requiredFields) {
    const errors = [];
    
    for (const fieldName of requiredFields) {
        if (!formData[fieldName] || formData[fieldName].trim() === '') {
            errors.push(`⚠️ Vui lòng nhập ${fieldName}`);
        }
    }
    
    return {
        valid: errors.length === 0,
        errors
    };
}

/**
 * Validate field value against constraints
 * @param {string} fieldName - Field name
 * @param {any} value - Field value
 * @param {Object} constraints - Field constraints (min, max, maxlength, pattern, etc.)
 * @returns {Object} { valid: boolean, error: string|null }
 */
export function validateField(fieldName, value, constraints = {}) {
    // Check required
    if (constraints.required && (!value || value.trim() === '')) {
        return {
            valid: false,
            error: `⚠️ ${constraints.label || fieldName} là bắt buộc`
        };
    }
    
    // If empty and not required, it's valid
    if (!value || value.trim() === '') {
        return { valid: true, error: null };
    }
    
    const strValue = String(value).trim();
    
    // Check maxlength for text
    if (constraints.maxlength && strValue.length > constraints.maxlength) {
        return {
            valid: false,
            error: `⚠️ ${constraints.label || fieldName} không được vượt quá ${constraints.maxlength} ký tự`
        };
    }
    
    // Check min/max for numbers
    if (constraints.type === 'number') {
        const numValue = parseFloat(value);
        
        if (isNaN(numValue)) {
            return {
                valid: false,
                error: `⚠️ ${constraints.label || fieldName} phải là số`
            };
        }
        
        if (constraints.min !== undefined && numValue < constraints.min) {
            return {
                valid: false,
                error: `⚠️ ${constraints.label || fieldName} phải >= ${constraints.min}`
            };
        }
        
        if (constraints.max !== undefined && numValue > constraints.max) {
            return {
                valid: false,
                error: `⚠️ ${constraints.label || fieldName} phải <= ${constraints.max}`
            };
        }
    }
    
    // Check pattern (regex)
    if (constraints.pattern) {
        const regex = new RegExp(constraints.pattern);
        if (!regex.test(strValue)) {
            return {
                valid: false,
                error: `⚠️ ${constraints.label || fieldName} không đúng định dạng`
            };
        }
    }
    
    return { valid: true, error: null };
}

/**
 * Validate hex color code
 * @param {string} color - Color string
 * @returns {boolean} Valid or not
 */
export function validateHexColor(color) {
    return /^#[0-9A-F]{6}$/i.test(color);
}

/**
 * Collect form data from input elements
 * @param {string} containerSelector - CSS selector for form container
 * @param {string} fieldPrefix - Prefix for input IDs (default: 'field-')
 * @returns {Object} Form data object
 */
export function collectFormData(containerSelector, fieldPrefix = 'field-') {
    const formData = {};
    const container = document.querySelector(containerSelector);
    
    if (!container) return formData;
    
    container.querySelectorAll('input, select, textarea').forEach(input => {
        if (input.id && input.id.startsWith(fieldPrefix)) {
            const fieldName = input.id.replace(fieldPrefix, '');
            const value = input.value.trim();
            
            // Only add non-empty values or required fields
            if (value !== '') {
                formData[fieldName] = value;
            } else if (input.required) {
                formData[fieldName] = value;
            }
        }
    });
    
    return formData;
}

/**
 * Get required fields from input elements
 * @param {string} containerSelector - CSS selector for form container
 * @param {string} fieldPrefix - Prefix for input IDs
 * @returns {Array<string>} Array of required field names
 */
export function getRequiredFields(containerSelector, fieldPrefix = 'field-') {
    const requiredFields = [];
    const container = document.querySelector(containerSelector);
    
    if (!container) return requiredFields;
    
    container.querySelectorAll('input[required], select[required], textarea[required]').forEach(input => {
        if (input.id && input.id.startsWith(fieldPrefix)) {
            const fieldName = input.id.replace(fieldPrefix, '');
            requiredFields.push(fieldName);
        }
    });
    
    return requiredFields;
}
