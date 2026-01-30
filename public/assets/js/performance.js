/**
 * Performance Mode Toggle
 * Auto-detect low-end devices and provide performance mode option
 */

// Performance mode state
let performanceMode = localStorage.getItem('performanceMode') === 'true';

// Auto-detect low-end devices
function detectLowEndDevice() {
    // Check hardware concurrency (CPU cores)
    const cores = navigator.hardwareConcurrency || 2;

    // Check device memory (if available)
    const memory = navigator.deviceMemory || 4;

    // Low-end device if: <= 2 cores OR <= 2GB RAM
    return cores <= 2 || memory <= 2;
}

// Apply performance mode
function applyPerformanceMode(enabled) {
    performanceMode = enabled;

    if (enabled) {
        document.body.classList.add('performance-mode');
        console.log('⚡ Performance Mode: ENABLED - Animations reduced');
    } else {
        document.body.classList.remove('performance-mode');
        console.log('🎨 Performance Mode: DISABLED - Full animations');
    }

    // Save preference
    localStorage.setItem('performanceMode', enabled);
}

// Toggle performance mode
window.togglePerformanceMode = function () {
    applyPerformanceMode(!performanceMode);

    // Update toggle button if exists
    const toggleBtn = document.getElementById('performanceModeToggle');
    if (toggleBtn) {
        toggleBtn.textContent = performanceMode ? '⚡ Performance' : '🎨 Quality';
        toggleBtn.setAttribute('title', performanceMode ?
            'Switch to Quality Mode' : 'Switch to Performance Mode');
    }
};

// Initialize on page load
function initPerformanceMode() {
    // Auto-enable for low-end devices on first visit
    if (!localStorage.getItem('performanceModeSet')) {
        const isLowEnd = detectLowEndDevice();
        performanceMode = isLowEnd;
        localStorage.setItem('performanceModeSet', 'true');

        if (isLowEnd) {
            console.log('📱 Low-end device detected - Auto-enabling performance mode');
        }
    }

    // Apply saved or auto-detected mode
    applyPerformanceMode(performanceMode);
}

// Auto-initialize
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPerformanceMode);
} else {
    initPerformanceMode();
}

// Export for global access
window.initPerformanceMode = initPerformanceMode;
window.performanceMode = () => performanceMode;
