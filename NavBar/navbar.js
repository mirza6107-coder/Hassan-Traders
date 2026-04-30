document.addEventListener("DOMContentLoaded", function () {
 // Premium Navbar Scroll Effect
const navbar = document.querySelector('.premium-navbar');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 80) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}
});
