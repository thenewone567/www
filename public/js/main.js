$("#menu-toggle").click(function(e) {
  e.preventDefault();
  $("#wrapper").toggleClass("toggled");
});

// Application is now in permanent dark mode
$(document).ready(function() {
    console.log('Application loaded in permanent dark mode');
    
    // Update the theme button to show current state (dark mode active)
    const themeIcon = $('#themeIcon');
    const themeText = $('#themeText');
    const themeButton = $('#themeToggle');
    
    if (themeButton.length) {
        themeIcon.removeClass('fa-moon').addClass('fa-sun');
        themeText.text('Light');
        themeButton.attr('title', 'Dark Mode Active');
        console.log('Theme button updated for permanent dark mode');
    }
    
    console.log('Dark mode application initialized');
});
