$("#menu-toggle").click(function(e) {
  e.preventDefault();
  $("#wrapper").toggleClass("toggled");
});

const darkSwitch = document.getElementById('darkSwitch');
if (darkSwitch) {
    darkSwitch.addEventListener('change', () => {
        document.body.classList.toggle('dark-mode');
    });
}
