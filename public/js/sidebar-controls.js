/* ==============================================
   SIDEBAR WIDTH CONTROL JAVASCRIPT
   ============================================== */

// Sidebar width control functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Create sidebar control buttons if they don't exist
    function createSidebarControls() {
        // Check if controls already exist
        if (document.querySelector('.sidebar-width-controls')) return;
        
        // Create controls container
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'sidebar-width-controls';
        
        // Create width control buttons
        const buttons = [
            { class: 'narrow', icon: '←', title: 'Narrow Sidebar' },
            { class: 'normal', icon: '↔', title: 'Normal Sidebar' },
            { class: 'wide', icon: '→', title: 'Wide Sidebar' },
            { class: 'collapse', icon: '⚏', title: 'Collapse Sidebar' }
        ];
        
        buttons.forEach(btn => {
            const button = document.createElement('div');
            button.className = 'sidebar-width-btn';
            button.innerHTML = btn.icon;
            button.title = btn.title;
            button.onclick = () => setSidebarWidth(btn.class);
            controlsContainer.appendChild(button);
        });
        
        // Create sidebar toggle for mobile
        const sidebarToggle = document.createElement('div');
        sidebarToggle.className = 'sidebar-toggle';
        sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
        sidebarToggle.onclick = toggleSidebar;
        
        // Add to page
        document.body.appendChild(controlsContainer);
        document.body.appendChild(sidebarToggle);
        
        // Set default active state
        updateActiveButton('normal');
    }
    
    // Set sidebar width
    function setSidebarWidth(width) {
        const body = document.body;
        
        // Remove all width classes
        body.classList.remove('sidebar-narrow', 'sidebar-wide', 'sidebar-collapsed');
        
        // Add new width class
        if (width !== 'normal') {
            body.classList.add(`sidebar-${width}`);
        }
        
        // Update active button
        updateActiveButton(width);
        
        // Save preference
        localStorage.setItem('sidebar-width', width);
    }
    
    // Update active button state
    function updateActiveButton(activeWidth) {
        document.querySelectorAll('.sidebar-width-btn').forEach((btn, index) => {
            btn.classList.remove('active');
            const widths = ['narrow', 'normal', 'wide', 'collapse'];
            if (widths[index] === activeWidth) {
                btn.classList.add('active');
            }
        });
    }
    
    // Toggle sidebar for mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar-wrapper');
        const overlay = document.querySelector('.sidebar-overlay') || createOverlay();
        
        if (sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        } else {
            sidebar.classList.add('show');
            overlay.classList.add('show');
        }
    }
    
    // Create overlay for mobile
    function createOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.onclick = toggleSidebar;
        document.body.appendChild(overlay);
        return overlay;
    }
    
    // Load saved sidebar width
    function loadSavedWidth() {
        const savedWidth = localStorage.getItem('sidebar-width') || 'normal';
        setSidebarWidth(savedWidth);
    }
    
    // Initialize
    createSidebarControls();
    loadSavedWidth();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Close mobile sidebar on desktop
            const sidebar = document.getElementById('sidebar-wrapper');
            const overlay = document.querySelector('.sidebar-overlay');
            if (sidebar) sidebar.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }
    });
});

// Export functions for manual control
window.SidebarControls = {
    setWidth: function(width) {
        setSidebarWidth(width);
    },
    toggle: function() {
        toggleSidebar();
    },
    collapse: function() {
        setSidebarWidth('collapse');
    },
    expand: function() {
        setSidebarWidth('normal');
    }
};
