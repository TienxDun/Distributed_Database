/**
 * Shared Sidebar JavaScript
 */

// Toggle sidebar visibility
window.toggleSidebar = function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');

    // Save preference to localStorage
    const isCollapsed = sidebar.classList.contains('collapsed');
    localStorage.setItem('sidebarCollapsed', isCollapsed);
};

// Theme management
function initializeTheme() {
    let savedTheme = localStorage.getItem('selectedTheme') || 'green';
    // Fallback if dark theme was selected (removed)
    if (savedTheme === 'dark') savedTheme = 'green';
    applyTheme(savedTheme);
    
    // Set checked radio button
    const radio = document.getElementById(`theme-${savedTheme}`);
    if (radio) radio.checked = true;
}

function applyTheme(theme) {
    document.body.className = document.body.className.replace(/theme-\w+/g, '').trim();
    document.body.classList.add(`theme-${theme}`);
    localStorage.setItem('selectedTheme', theme);
}

function handleThemeChange(event) {
    if (event.target.name === 'theme') {
        applyTheme(event.target.value);
    }
}

// Initialize sidebar state on page load
function initializeSidebar() {
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (sidebarCollapsed) {
        document.querySelector('.sidebar').classList.add('collapsed');
        document.querySelector('.main-content').classList.add('sidebar-collapsed');
    }
    
    initializeTheme();
    
    // Add theme change listener
    document.addEventListener('change', handleThemeChange);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeSidebar);
} else {
    initializeSidebar();
}