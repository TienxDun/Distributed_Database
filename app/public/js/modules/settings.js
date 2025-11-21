/**
 * Settings module for UI customization
 */

import { validateHexColor } from '../utils/validation.js';

const DEFAULT_BG_COLOR = '#f8fafc';

/**
 * Open settings modal
 */
export function openSettingsModal() {
    document.getElementById('settingsModal').classList.add('show');
}

/**
 * Close settings modal
 */
export function closeSettingsModal() {
    document.getElementById('settingsModal').classList.remove('show');
}

/**
 * Load settings from localStorage
 */
export function loadSettings() {
    const settings = JSON.parse(localStorage.getItem('uiSettings')) || {};
    
    if (settings.bgColor) {
        document.getElementById('bgColor').value = settings.bgColor;
        document.getElementById('bgColorText').value = settings.bgColor;
        updateBackgroundColor(false);
    }
}

/**
 * Save settings to localStorage
 */
export function saveSettings() {
    const settings = {
        bgColor: document.getElementById('bgColor').value
    };
    localStorage.setItem('uiSettings', JSON.stringify(settings));
}

/**
 * Update background color
 * @param {boolean} save - Whether to save settings
 */
export function updateBackgroundColor(save = true) {
    const color = document.getElementById('bgColor').value;
    
    // Remove animation and gradient
    document.body.style.background = color;
    document.body.style.backgroundSize = 'auto';
    document.body.style.animation = 'none';
    
    // Update text input
    document.getElementById('bgColorText').value = color;
    
    if (save) saveSettings();
}

/**
 * Update background color from text input
 */
export function updateBackgroundColorFromText() {
    const textInput = document.getElementById('bgColorText');
    const colorInput = document.getElementById('bgColor');
    const value = textInput.value.trim();
    
    // Validate hex color
    if (validateHexColor(value)) {
        colorInput.value = value;
        updateBackgroundColor();
    } else {
        alert('⚠️ Vui lòng nhập mã màu hợp lệ (ví dụ: #ffffff)');
    }
}

/**
 * Apply preset color
 * @param {string} color - Hex color code
 */
export function applyPresetColor(color) {
    document.getElementById('bgColor').value = color;
    document.getElementById('bgColorText').value = color;
    updateBackgroundColor();
}

/**
 * Reset to default settings
 */
export function resetToDefault() {
    localStorage.removeItem('uiSettings');
    
    // Reset to default light gray
    document.getElementById('bgColor').value = DEFAULT_BG_COLOR;
    document.getElementById('bgColorText').value = DEFAULT_BG_COLOR;
    updateBackgroundColor(false);
    
    alert('✅ Đã khôi phục màu nền mặc định!');
}
