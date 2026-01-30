/**
 * Theme Toggle Functionality
 * Xử lý chuyển đổi giữa Dark Theme và Light Theme
 */

// Lấy theme hiện tại từ localStorage hoặc mặc định là dark
const getTheme = () => {
    return localStorage.getItem('theme') || 'dark';
};

// Lưu theme vào localStorage
const saveTheme = (theme) => {
    localStorage.setItem('theme', theme);
};

// Áp dụng theme vào body
const applyTheme = (theme) => {
    if (theme === 'light') {
        document.body.classList.add('light-theme');
    } else {
        document.body.classList.remove('light-theme');
    }
    
    // Cập nhật icon của nút toggle
    updateThemeToggleIcon(theme);
};

// Cập nhật icon của nút toggle
const updateThemeToggleIcon = (theme) => {
    const themeToggle = document.getElementById('themeToggle');
    if (!themeToggle) return;
    
    const icon = themeToggle.querySelector('i');
    if (theme === 'light') {
        icon.className = 'fas fa-moon';
        themeToggle.setAttribute('title', 'Chuyển sang Dark Mode');
    } else {
        icon.className = 'fas fa-sun';
        themeToggle.setAttribute('title', 'Chuyển sang Light Mode');
    }
};

// Chuyển đổi theme
const toggleTheme = () => {
    const currentTheme = getTheme();
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    saveTheme(newTheme);
    applyTheme(newTheme);
    
    console.log(`🎨 Theme switched to: ${newTheme}`);
};

// Khởi tạo theme khi trang load
const initializeTheme = () => {
    const savedTheme = getTheme();
    applyTheme(savedTheme);
    console.log(`🎨 Theme initialized: ${savedTheme}`);
};

// Export functions to global scope
window.toggleTheme = toggleTheme;
window.initializeTheme = initializeTheme;

// Auto-initialize khi DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTheme);
} else {
    initializeTheme();
}
