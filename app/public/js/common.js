/**
 * Common script for all pages
 * Provides shared functionality for sidebar, settings, and site status
 */

// Import required modules
import { toggleSiteStatusPanel } from './modules/modal.js';
import { loadSettings, openSettingsModal, closeSettingsModal, updateBackgroundColor, updateBackgroundColorFromText, applyPresetColor, resetToDefault } from './modules/settings.js';

// Module variables
let currentPage = 1;
let autoRefreshInterval = null;
let charts = {};

// Expose functions to global scope
window.toggleSidebar = function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');

    // Save preference to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
};

window.toggleSiteStatusPanel = toggleSiteStatusPanel;
window.openSettingsModal = openSettingsModal;
window.closeSettingsModal = closeSettingsModal;
window.updateBackgroundColor = updateBackgroundColor;
window.updateBackgroundColorFromText = updateBackgroundColorFromText;
window.applyPresetColor = applyPresetColor;
window.resetToDefault = resetToDefault;

// Initialize on page load
function initializeCommon() {
    loadSettings();

    // Load sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.querySelector('.sidebar').classList.add('collapsed');
        document.querySelector('.main-content').classList.add('sidebar-collapsed');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCommon);
} else {
    initializeCommon();
}