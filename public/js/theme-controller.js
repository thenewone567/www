/* ==============================================
   ROBUST THEME SWITCHING JAVASCRIPT
   ============================================== */

// Handle browser extension communication errors
window.addEventListener('error', function (e) {
    if (e.message && e.message.includes('message port closed')) {
        // Suppress browser extension communication errors
        e.preventDefault();
        return false;
    }
});

// Suppress Chrome extension runtime errors
if (typeof chrome !== 'undefined' && chrome.runtime) {
    chrome.runtime.onMessage.addListener(() => {
        if (chrome.runtime.lastError) {
            // Silently handle extension communication errors
            return false;
        }
    });
}

class ThemeController {
    constructor() {
        this.currentTheme = 'light';
        this.init();
    }

    init() {
        // Load saved theme from localStorage
        this.loadSavedTheme();

        // Create theme toggle button
        this.createThemeToggle();

        // Apply initial theme
        this.applyTheme(this.currentTheme);

        // Listen for system theme changes
        this.listenForSystemThemeChanges();

        // Enable transitions after page load to prevent flash
        this.enableTransitionsAfterLoad();
    }

    enableTransitionsAfterLoad() {
        // Wait for page to fully load before enabling transitions
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.body.style.transition = 'all 0.3s ease';
                // Enable transitions for all elements
                const style = document.createElement('style');
                style.textContent = `
                    * {
                        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important;
                    }
                `;
                document.head.appendChild(style);
            }, 100); // Small delay to ensure everything is rendered
        });
    }

    loadSavedTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme && (savedTheme === 'light' || savedTheme === 'dark')) {
            this.currentTheme = savedTheme;
        } else {
            // Auto-detect system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.currentTheme = prefersDark ? 'dark' : 'light';
        }
    }

    createThemeToggle() {
        // Check if toggle already exists in the navbar
        const existingToggle = document.getElementById('theme-toggle');

        if (existingToggle) {
            // Use existing button from navbar - avoid duplicate event listeners
            if (!existingToggle.hasAttribute('data-theme-initialized')) {
                existingToggle.addEventListener('click', () => this.toggleTheme());
                existingToggle.setAttribute('data-theme-initialized', 'true');
                this.updateToggleIcon(existingToggle.querySelector('i'), this.currentTheme);
            }
            return;
        }

        // Fallback: create floating toggle if navbar button doesn't exist
        const toggle = document.createElement('button');
        toggle.id = 'theme-toggle';
        toggle.className = 'theme-toggle floating-theme-toggle';
        toggle.setAttribute('aria-label', 'Toggle theme');
        toggle.setAttribute('title', 'Toggle between light and dark theme');
        toggle.setAttribute('data-theme-initialized', 'true');

        const icon = document.createElement('i');
        // Set icon to reflect current theme: sun for light, moon for dark
        icon.className = this.currentTheme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
        toggle.appendChild(icon);

        toggle.addEventListener('click', () => this.toggleTheme());

        // Add to body
        document.body.appendChild(toggle);
    }

    updateToggleIcon(iconElement, theme) {
        if (iconElement) {
            // Set icon to reflect current theme: sun for light, moon for dark
            const newIconClass = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';

            // Only update if the class is different to avoid unnecessary DOM manipulation
            if (iconElement.className !== newIconClass) {
                iconElement.className = newIconClass;
            }
        }
    }

    applyTheme(theme) {
        // Remove existing theme classes
        document.documentElement.removeAttribute('data-theme');

        // Apply new theme
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }

        // Update toggle icon - find the main theme toggle button
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
                this.updateToggleIcon(icon, theme);
            }
        }

        // Update charts if they exist
        this.updateCharts(theme);

        // Save to localStorage
        localStorage.setItem('theme', theme);

        // Trigger custom event for other components
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme } }));
    }

    toggleTheme() {
        this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
        console.log('New theme:', this.currentTheme);
        this.applyTheme(this.currentTheme);
    }

    updateCharts(theme) {
        // Update Chart.js charts for theme
        if (typeof Chart !== 'undefined') {
            Chart.defaults.color = theme === 'dark' ? '#b0b0b0' : '#666666';
            Chart.defaults.borderColor = theme === 'dark' ? '#404040' : '#e5e5e5';
            Chart.defaults.backgroundColor = theme === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

            // Update existing charts
            Object.values(Chart.instances).forEach(chart => {
                if (chart && chart.update) {
                    chart.update();
                }
            });
        }
    }

    listenForSystemThemeChanges() {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
            // Only auto-switch if user hasn't manually set a preference
            if (!localStorage.getItem('theme-manual')) {
                this.currentTheme = e.matches ? 'dark' : 'light';
                this.applyTheme(this.currentTheme);
            }
        });
    }

    // Method to manually set theme (marks as user preference)
    setTheme(theme) {
        if (theme === 'light' || theme === 'dark') {
            this.currentTheme = theme;
            this.applyTheme(theme);
            localStorage.setItem('theme-manual', 'true');
        }
    }

    // Get current theme
    getTheme() {
        return this.currentTheme;
    }

    // Reset to system preference
    resetToSystemPreference() {
        localStorage.removeItem('theme-manual');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.currentTheme = prefersDark ? 'dark' : 'light';
        this.applyTheme(this.currentTheme);
    }
}

// Initialize theme controller when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    window.themeController = new ThemeController();
});

// Export for module use if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeController;
}

/* ==============================================
   ADDITIONAL UTILITY FUNCTIONS
   ============================================== */

// Function to get theme-appropriate colors for charts
function getThemeColors() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    return {
        background: isDark ? '#2d2d2d' : '#ffffff',
        text: isDark ? '#e0e0e0' : '#212529',
        grid: isDark ? '#404040' : '#e5e5e5',
        primary: isDark ? '#4dabf7' : '#007bff',
        success: isDark ? '#51cf66' : '#28a745',
        warning: isDark ? '#ffd43b' : '#ffc107',
        danger: isDark ? '#ff6b6b' : '#dc3545',
        info: isDark ? '#22b8cf' : '#17a2b8'
    };
}

// Function to create theme-aware chart options
function getChartOptions(baseOptions = {}) {
    const colors = getThemeColors();

    return {
        ...baseOptions,
        plugins: {
            ...baseOptions.plugins,
            legend: {
                ...baseOptions.plugins?.legend,
                labels: {
                    ...baseOptions.plugins?.legend?.labels,
                    color: colors.text
                }
            }
        },
        scales: {
            ...baseOptions.scales,
            x: {
                ...baseOptions.scales?.x,
                grid: {
                    ...baseOptions.scales?.x?.grid,
                    color: colors.grid
                },
                ticks: {
                    ...baseOptions.scales?.x?.ticks,
                    color: colors.text
                }
            },
            y: {
                ...baseOptions.scales?.y,
                grid: {
                    ...baseOptions.scales?.y?.grid,
                    color: colors.grid
                },
                ticks: {
                    ...baseOptions.scales?.y?.ticks,
                    color: colors.text
                }
            }
        }
    };
}

// Initialize the theme controller when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
    window.themeController = new ThemeController();
});
