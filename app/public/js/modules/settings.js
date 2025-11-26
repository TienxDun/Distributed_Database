/**
 * Settings module for UI customization
 */

import { validateHexColor } from '../utils/validation.js';

const DEFAULT_BG_COLOR = '#f8fafc';

// Auto-refresh variables
let autoRefreshInterval = null;
let autoRefreshEnabled = false;
let autoRefreshTime = 30000; // 30 seconds default

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

    // Load auto-refresh settings
    if (settings.autoRefreshEnabled !== undefined) {
        autoRefreshEnabled = settings.autoRefreshEnabled;
        document.getElementById('autoRefreshEnabled').checked = autoRefreshEnabled;
    }

    if (settings.autoRefreshTime) {
        autoRefreshTime = settings.autoRefreshTime;
        document.getElementById('autoRefreshTime').value = autoRefreshTime / 1000; // Convert to seconds
    }

    // Apply auto-refresh settings
    updateAutoRefresh();
}

/**
 * Save settings to localStorage
 */
export function saveSettings() {
    const settings = {
        bgColor: document.getElementById('bgColor').value,
        autoRefreshEnabled: autoRefreshEnabled,
        autoRefreshTime: autoRefreshTime
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
    
    // Save to localStorage for cross-page sync
    localStorage.setItem('bgColor', color);
    
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
    localStorage.removeItem('bgColor');

    // Reset to default light gray
    document.getElementById('bgColor').value = DEFAULT_BG_COLOR;
    document.getElementById('bgColorText').value = DEFAULT_BG_COLOR;
    updateBackgroundColor(false);

    // Reset auto-refresh
    autoRefreshEnabled = false;
    autoRefreshTime = 30000;
    updateAutoRefresh();

    alert('✅ Đã khôi phục cài đặt mặc định!');
}

/**
 * Toggle auto-refresh
 */
export function toggleAutoRefresh() {
    autoRefreshEnabled = document.getElementById('autoRefreshEnabled').checked;
    updateAutoRefresh();
    saveSettings();
}

/**
 * Update auto-refresh time
 */
export function updateAutoRefreshTime() {
    const timeInput = document.getElementById('autoRefreshTime');
    const seconds = parseInt(timeInput.value) || 30;
    autoRefreshTime = seconds * 1000; // Convert to milliseconds
    updateAutoRefresh();
    saveSettings();
}

/**
 * Update auto-refresh functionality
 */
function updateAutoRefresh() {
    // Clear existing interval
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        autoRefreshInterval = null;
    }

    // Set up new interval if enabled
    if (autoRefreshEnabled) {
        autoRefreshInterval = setInterval(() => {
            const activeTab = document.querySelector('.tab-content.active');
            if (activeTab && activeTab.id !== 'global') {
                console.log(`[Auto-refresh] Refreshing ${activeTab.id} data`);
                // Import and call loadData
                import('../modules/crud.js').then(crudModule => {
                    crudModule.loadData(activeTab.id, true); // Skip loading overlay
                });
            }
        }, autoRefreshTime);

        console.log(`[Auto-refresh] Enabled with ${autoRefreshTime/1000}s interval`);
    } else {
        console.log('[Auto-refresh] Disabled');
    }
}

/**
 * Get auto-refresh status
 */
export function getAutoRefreshStatus() {
    return {
        enabled: autoRefreshEnabled,
        interval: autoRefreshTime
    };
}
