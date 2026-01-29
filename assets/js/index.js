

// Scroll To Top Functionality
document.addEventListener('DOMContentLoaded', () => {
  const scrollTopBtn = document.getElementById('scrollTopBtn');

  if (scrollTopBtn) {
    // Show button after scrolling down 300px
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
      } else {
        scrollTopBtn.classList.remove('show');
      }
    });

    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
});

// =============================================== NAVBAR HIDE/SHOW ON SCROLL ===============================================
// Hide navbar when scrolling down, show when scrolling up
const header = document.querySelector("header");

if (header) {
  let lastScrollTop = 0;

  window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    // If at the top, always show navbar
    if (currentScroll <= 0) {
      header.classList.remove("navbar-hidden");
      lastScrollTop = currentScroll;
      return;
    }

    // Scrolling down - hide navbar
    if (currentScroll > lastScrollTop && currentScroll > 100) {
      header.classList.add("navbar-hidden");
    }
    // Scrolling up - show navbar
    else if (currentScroll < lastScrollTop) {
      header.classList.remove("navbar-hidden");
    }

    lastScrollTop = currentScroll;
  }, { passive: true });
}

// =============================================== NAVBAR ===============================================
// variables
const body = document.querySelector("body");
const sideBtn = document.querySelector(".sideBtn");
const sideBg = document.querySelector(".sideNav");

// Toggle side navbar (open/close with burger button)
if (sideBtn && sideBg) {
  sideBtn.addEventListener("click", () => {
    const isActive = sideBg.classList.contains("active");

    if (isActive) {
      // Close menu
      body.classList.remove("no-scrolling");
      sideBg.classList.remove("active");
      sideBtn.classList.remove("active");
    } else {
      // Open menu
      body.classList.add("no-scrolling");
      sideBg.classList.add("active");
      sideBtn.classList.add("active");
    }
  });
}


// AOS
AOS.init({
  duration: 1000,
  easing: 'ease-in-sine',
  delay: 0,
  offset: 0, // Changed from 100 to 0 so elements already visible on screen animate immediately
  once: true,
  mirror: true,
  disable: function () {
    var maxWidth = 800;
    return window.innerWidth < maxWidth;
  }
});

