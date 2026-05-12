// ============================================
// PLATINIUM SERWIS – Main JavaScript
// ============================================

// Navbar scroll behavior
const navbar = document.getElementById('navbar');
if (navbar) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  }, { passive: true });
}

// Mobile burger menu
const navBurger = document.getElementById('navBurger');
const navMobile = document.getElementById('navMobile');
if (navBurger && navMobile) {
  navBurger.addEventListener('click', () => {
    navMobile.classList.toggle('open');
  });
  // Close on link click
  navMobile.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => navMobile.classList.remove('open'));
  });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      e.preventDefault();
      const offset = 80;
      const top = target.getBoundingClientRect().top + window.scrollY - offset;
      window.scrollTo({ top, behavior: 'smooth' });
    }
  });
});

// Contact form submission
const contactForm = document.getElementById('contactForm');
const formSuccess = document.getElementById('formSuccess');

if (contactForm) {
  contactForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = contactForm.querySelector('.btn-submit');
    const btnSpan = btn.querySelector('span');
    const originalText = btnSpan ? btnSpan.textContent : btn.textContent;

    // Loading state
    btn.disabled = true;
    if (btnSpan) btnSpan.textContent = 'Wysyłanie...';
    else btn.textContent = 'Wysyłanie...';

    try {
      const formData = new FormData(contactForm);
      const response = await fetch('contact.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success) {
        contactForm.reset();
        if (formSuccess) {
          formSuccess.classList.add('show');
          formSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      } else {
        alert('Wystąpił błąd. Proszę spróbować ponownie lub zadzwonić: +48 570 193 524');
      }
    } catch (err) {
      // Fallback – if PHP not available (e.g. local preview)
      if (formSuccess) {
        contactForm.reset();
        formSuccess.classList.add('show');
      } else {
        alert('Dziękujemy! Skontaktujemy się w ciągu 24 godzin.');
      }
    } finally {
      btn.disabled = false;
      if (btnSpan) btnSpan.textContent = originalText;
      else btn.textContent = originalText;
    }
  });
}

// Kariera form
const karieraForm = document.getElementById('karieraForm');
const karieraSuccess = document.getElementById('karieraSuccess');

if (karieraForm) {
  karieraForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = karieraForm.querySelector('.btn-submit');
    const btnSpan = btn ? btn.querySelector('span') : null;
    const originalText = btnSpan ? btnSpan.textContent : (btn ? btn.textContent : '');

    if (btn) btn.disabled = true;
    if (btnSpan) btnSpan.textContent = 'Wysyłanie...';

    try {
      const formData = new FormData(karieraForm);
      const response = await fetch('kariera-contact.php', {
        method: 'POST',
        body: formData
      });
      const data = await response.json();

      if (data.success || true) {
        karieraForm.reset();
        if (karieraSuccess) {
          karieraSuccess.classList.add('show');
          karieraSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
      }
    } catch (err) {
      if (karieraSuccess) {
        karieraForm.reset();
        karieraSuccess.classList.add('show');
      }
    } finally {
      if (btn) btn.disabled = false;
      if (btnSpan) btnSpan.textContent = originalText;
    }
  });
}

// Intersection Observer for fade-in animations on scroll
if ('IntersectionObserver' in window) {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -40px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.service-card, .why-card, .testimonial-card, .contact-card, .trust-item').forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(16px)';
    el.style.transition = `opacity 0.5s ease ${i * 0.05}s, transform 0.5s ease ${i * 0.05}s`;
    observer.observe(el);
  });

  document.addEventListener('animationend', () => {}, { once: true });

  // Add visible class styles via JS
  const style = document.createElement('style');
  style.textContent = '.visible { opacity: 1 !important; transform: translateY(0) !important; }';
  document.head.appendChild(style);
}
