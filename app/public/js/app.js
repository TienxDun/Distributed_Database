/**
 * Main application entry point
 * Imports all modules and exposes functions to global window scope
 */

// Import CRUD operations
import { loadData, deleteRecord, deleteCTDaoTao, deleteDangKy, showTab, toggleSiteColumnVisibility } from './modules/crud.js';

// Import modal functions
import { openCreateModal, openEditModal, closeModal, submitForm } from './modules/modal.js';

// Import settings functions
import { openSettingsModal, closeSettingsModal, loadSettings, updateBackgroundColor, updateBackgroundColorFromText, applyPresetColor, resetToDefault, toggleAutoRefresh, updateAutoRefreshTime, getAutoRefreshStatus } from './modules/settings.js';

// Import view/search functions
import { loadDataById, loadCTDaoTaoByFilter, loadDangKyByMaSV } from './modules/view.js';

// Import global query functions
import { callGlobalQuery } from './modules/global-query.js';

/**
 * Toggle sidebar visibility
 */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');
    
    // Save preference to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
}

/**
 * Refresh current active tab
 */
function refreshCurrentTab() {
    const activeTab = document.querySelector('.tab-btn.active');
    if (activeTab) {
        const tabId = activeTab.onclick.toString().match(/'([^']+)'/)[1];
        loadData(tabId);
    }
}

/**
 * Clear all result sections
 */
function clearAllResults() {
    const results = document.querySelectorAll('.result');
    results.forEach(result => {
        result.innerHTML = '';
    });
    
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.innerHTML = '';
        alert.style.display = 'none';
    });
}

/**
 * Initialize application on page load
 */
function initializeApp() {
    // Load settings from localStorage
    loadSettings();
    
    // Load sidebar state
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.querySelector('.sidebar').classList.add('collapsed');
        document.querySelector('.main-content').classList.add('sidebar-collapsed');
    }
    
    // Load default tab data
    loadData('khoa');
    
    // Setup event listeners
    setupEventListeners();
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        const crudModal = document.getElementById('crudModal');
        const settingsModal = document.getElementById('settingsModal');
        
        if (event.target === crudModal) {
            closeModal();
        }
        if (event.target === settingsModal) {
            closeSettingsModal();
        }
    });
}

/**
 * Expose functions to global window scope for HTML onclick handlers
 */
window.showTab = showTab;
window.toggleSiteColumnVisibility = toggleSiteColumnVisibility;

window.loadData = loadData;
window.loadDataById = loadDataById;
window.loadCTDaoTaoByFilter = loadCTDaoTaoByFilter;
window.loadDangKyByMaSV = loadDangKyByMaSV;

window.openCreateModal = openCreateModal;
window.openEditModal = openEditModal;
window.closeModal = closeModal;
window.submitForm = submitForm;

window.deleteRecord = deleteRecord;
window.deleteCTDaoTao = deleteCTDaoTao;
window.deleteDangKy = deleteDangKy;

window.openSettingsModal = openSettingsModal;
window.closeSettingsModal = closeSettingsModal;
window.updateBackgroundColor = updateBackgroundColor;
window.updateBackgroundColorFromText = updateBackgroundColorFromText;
window.applyPresetColor = applyPresetColor;
window.resetToDefault = resetToDefault;
window.toggleAutoRefresh = toggleAutoRefresh;
window.updateAutoRefreshTime = updateAutoRefreshTime;

window.toggleSidebar = toggleSidebar;
window.refreshCurrentTab = refreshCurrentTab;
window.clearAllResults = clearAllResults;

window.callGlobalQuery = callGlobalQuery;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    // DOM is already ready
    initializeApp();
}
