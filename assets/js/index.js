
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

// =============================================== NAVBAR GLASSMORPHISM SCROLL EFFECT ===============================================
// Add scroll effect to navbar for enhanced glass effect
const header = document.querySelector("header");

if (header) {
  window.addEventListener("scroll", () => {
    if (window.scrollY > 50) {
      header.classList.add("scrolled");
    } else {
      header.classList.remove("scrolled");
    }
  });
}

// =============================================== NAVBAR ===============================================
// variables
const body = document.querySelector("body");
const sideBtn = document.querySelector(".sideBtn");
const sideBg = document.querySelector(".sideNav");
const x = document.querySelector(".closeNav");

// Create overlay for mobile menu
let overlay = document.querySelector(".overlay");
if (!overlay && sideBg) {
  overlay = document.createElement("div");
  overlay.className = "overlay";
  document.body.appendChild(overlay);
}

// open side navbar
if (sideBtn && sideBg) {
  sideBtn.addEventListener("click", () => {
    body.classList.add("no-scrolling");
    sideBg.classList.add("active");
    if (overlay) overlay.classList.add("active");
  });
}

// close side navbar
const closeSideNav = () => {
  body.classList.remove("no-scrolling");
  if (sideBg) sideBg.classList.remove("active");
  if (overlay) overlay.classList.remove("active");
};

if (x) {
  x.addEventListener("click", closeSideNav);
}

// Close sidebar when clicking overlay
if (overlay) {
  overlay.addEventListener("click", closeSideNav);
}


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