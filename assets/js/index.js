
// Scroll To Top Functionality
const scrollTopBtn = document.getElementById('scrollTopBtn');

if (scrollTopBtn) {
    // Get the first section to determine when to show the button
    const firstSection = document.querySelector('.workshopsHero');

    window.addEventListener('scroll', () => {
        if (firstSection) {
            const firstSectionHeight = firstSection.offsetHeight;

            if (window.pageYOffset > firstSectionHeight) {
                scrollTopBtn.classList.add('show');
            } else {
                scrollTopBtn.classList.remove('show');
            }
        }
    });

    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

// ===============================================navBar===============================================
// variables
const body = document.querySelector("body");
const sideBtn = document.querySelector(".sideBtn");
const sideBg = document.querySelector(".sideNav");
const x = document.querySelector(".closeNav");

// open side navbar
sideBtn.addEventListener("click", () => {
  body.classList.add("no-scrolling");
  sideBg.classList.add("active");
});

// close side navbar
x.addEventListener("click", () => {
  body.classList.remove("no-scrolling");
  sideBg.classList.remove("active");
});

sideBtn.addEventListener("click", () => {
  console.log("clicked");

});

// AOS
AOS.init({
    duration: 1000,
    easing: 'ease-in-sine',
    delay: 100,
    offset: 100,
    once: true,
    mirror: true,
    disable: function () {
        var maxWidth = 800;
        return window.innerWidth < maxWidth;
    }
});