/**
 * Theme System Controller
 * Manages light/dark theme switching and persistence
 */

class ThemeController {
    constructor() {
        this.currentTheme = this.getStoredTheme() || this.getSystemTheme();
        this.init();
    }

    init() {
        this.applyTheme(this.currentTheme);
        this.setupEventListeners();
        this.updateToggleButton();
    }

    getSystemTheme() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    getStoredTheme() {
        return localStorage.getItem('preferred-theme');
    }

    storeTheme(theme) {
        localStorage.setItem('preferred-theme', theme);
    }

    applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        this.currentTheme = theme;
        this.storeTheme(theme);
    }

    toggleTheme() {
        const newTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        this.applyTheme(newTheme);
        this.updateToggleButton();
    }

    updateToggleButton() {
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                if (this.currentTheme === 'dark') {
                    icon.className = 'fas fa-sun';
                    toggleBtn.title = 'Switch to Light Mode';
                } else {
                    icon.className = 'fas fa-moon';
                    toggleBtn.title = 'Switch to Dark Mode';
                }
            }
        }
    }

    setupEventListeners() {
        // Theme toggle button
        const toggleBtn = document.getElementById('theme-toggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleTheme());
        }

        // System theme change detection
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!this.getStoredTheme()) {
                this.applyTheme(e.matches ? 'dark' : 'light');
                this.updateToggleButton();
            }
        });
    }
}

// Initialize theme controller when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.themeController = new ThemeController();
});

// Expose theme functions globally
window.toggleTheme = () => {
    if (window.themeController) {
        window.themeController.toggleTheme();
    }
};
