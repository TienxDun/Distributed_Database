/**
 * Main application entry point
 * Imports all modules and exposes functions to global window scope
 */

// Import CRUD operations
import { loadData, deleteRecord, deleteCTDaoTao, deleteDangKy, showTab, toggleSiteColumnVisibility } from './modules/crud.js';

// Import modal functions
import { openCreateModal, openEditModal, closeModal, submitForm } from './modules/modal.js';

// Import settings functions
import { openSettingsModal, closeSettingsModal, loadSettings, updateBackgroundColor, updateBackgroundColorFromText, applyPresetColor, resetToDefault } from './modules/settings.js';

// Import view/search functions
import { loadDataById, loadCTDaoTaoByFilter, loadDangKyByMaSV } from './modules/view.js';

// Import global query functions
import { callGlobalQuery } from './modules/global-query.js';

/**
 * Initialize application on page load
 */
function initializeApp() {
    // Load settings from localStorage
    loadSettings();
    
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

window.callGlobalQuery = callGlobalQuery;

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    // DOM is already ready
    initializeApp();
}
