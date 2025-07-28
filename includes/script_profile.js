document.addEventListener("DOMContentLoaded", function () {
    const profileBtn = document.getElementById('profile-btn');
    const profileDropdown = document.getElementById('profile-dropdown');

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('visible');
        });

        document.addEventListener('click', function () {
            profileDropdown.classList.remove('visible');
        });

        profileDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }
});
