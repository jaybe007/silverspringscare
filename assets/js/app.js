/**
 * Silver Springs Care - Main Application Script
 * WCAG 2.2 AA Accessibility & Interaction Behaviors
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mark JS as enabled for progressive enhancement
  document.documentElement.classList.add('js-enabled');

  initHeader();
  initMobileNav();
  initAccessibilityControls();
  initAccordions();
  initScrollReveals();
  initPrivacyBanner();
  initModals();
});

/**
 * Subtle Reveal-on-Scroll Observer
 */
function initScrollReveals() {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.reveal-on-scroll').forEach(el => el.classList.add('is-revealed'));
    return;
  }

  const reveals = document.querySelectorAll('.reveal-on-scroll');
  if (!reveals.length) return;

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach(el => observer.observe(el));
  } else {
    reveals.forEach(el => el.classList.add('is-revealed'));
  }
}

/**
 * Privacy & Consent Notice Banner
 */
function initPrivacyBanner() {
  const banner = document.getElementById('ssc-privacy-banner');
  if (!banner) return;

  const consent = localStorage.getItem('ssc_cookie_consent');
  if (!consent) {
    setTimeout(() => {
      banner.classList.add('is-visible');
    }, 800);
  }

  const acceptBtn = document.getElementById('accept-privacy-btn');
  if (acceptBtn) {
    acceptBtn.addEventListener('click', () => {
      localStorage.setItem('ssc_cookie_consent', 'accepted');
      banner.classList.remove('is-visible');
    });
  }
}

/**
 * Accessible Modal System with Focus Trap & Escape Key
 */
function initModals() {
  const openButtons = document.querySelectorAll('[data-open-modal]');
  const overlays = document.querySelectorAll('.ssc-modal-overlay');

  openButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.getAttribute('data-open-modal');
      const targetModal = document.getElementById(modalId);
      if (targetModal) {
        openModal(targetModal, btn);
      }
    });
  });

  overlays.forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        closeModal(overlay);
      }
    });

    const closeBtns = overlay.querySelectorAll('[data-close-modal], .ssc-modal-close');
    closeBtns.forEach(cBtn => {
      cBtn.addEventListener('click', () => closeModal(overlay));
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const openModalEl = document.querySelector('.ssc-modal-overlay.is-open');
      if (openModalEl) {
        closeModal(openModalEl);
      }
    }
  });

  function openModal(modalEl, triggerBtn) {
    modalEl.classList.add('is-open');
    modalEl.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    modalEl._triggerBtn = triggerBtn;

    const focusable = modalEl.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable.length) {
      setTimeout(() => focusable[0].focus(), 50);
    }
  }

  function closeModal(modalEl) {
    modalEl.classList.remove('is-open');
    modalEl.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    if (modalEl._triggerBtn) {
      modalEl._triggerBtn.focus();
    }
  }
}


/**
 * Sticky Header Scroll State
 */
function initHeader() {
  const header = document.querySelector('.site-header');
  if (!header) return;

  const handleScroll = () => {
    if (window.scrollY > 20) {
      header.classList.add('is-scrolled');
    } else {
      header.classList.remove('is-scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
}

/**
 * Accessible Mobile Navigation Drawer
 */
function initMobileNav() {
  const toggleBtn = document.querySelector('.mobile-nav-toggle');
  const drawer = document.querySelector('.mobile-drawer');
  const backdrop = document.querySelector('.mobile-drawer-backdrop');
  const closeBtn = document.querySelector('.mobile-drawer-close');
  if (!toggleBtn || !drawer) return;

  const toggleNav = (open) => {
    const isOpen = open !== undefined ? open : !drawer.classList.contains('is-open');
    toggleBtn.setAttribute('aria-expanded', String(isOpen));
    drawer.classList.toggle('is-open', isOpen);
    drawer.setAttribute('aria-hidden', String(!isOpen));
    if (backdrop) {
      backdrop.classList.toggle('is-open', isOpen);
      backdrop.setAttribute('aria-hidden', String(!isOpen));
    }
    document.body.classList.toggle('mobile-nav-open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
  };

  toggleBtn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    toggleNav();
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      toggleNav(false);
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', () => toggleNav(false));
  }

  // Close when pressing Escape
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
      toggleNav(false);
      toggleBtn.focus();
    }
  });

  // Close when clicking a nav link in mobile menu
  drawer.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => toggleNav(false));
  });
}

/**
 * Accessibility Controls: Contrast & Font Scaling
 */
function initAccessibilityControls() {
  const html = document.documentElement;

  // Restore saved preferences
  const savedContrast = localStorage.getItem('ssc_high_contrast');
  if (savedContrast === 'true') {
    html.setAttribute('data-high-contrast', 'true');
  }

  const savedFontSize = localStorage.getItem('ssc_font_size');
  if (savedFontSize) {
    html.setAttribute('data-font-size', savedFontSize);
  }

  // Toggle Contrast Button
  const contrastBtn = document.getElementById('toggle-contrast-btn');
  if (contrastBtn) {
    contrastBtn.addEventListener('click', () => {
      const isHigh = html.getAttribute('data-high-contrast') === 'true';
      if (isHigh) {
        html.removeAttribute('data-high-contrast');
        localStorage.removeItem('ssc_high_contrast');
        contrastBtn.setAttribute('aria-pressed', 'false');
      } else {
        html.setAttribute('data-high-contrast', 'true');
        localStorage.setItem('ssc_high_contrast', 'true');
        contrastBtn.setAttribute('aria-pressed', 'true');
      }
    });
  }

  // Font Size Buttons
  const fontDecBtn = document.getElementById('font-decrease-btn');
  const fontIncBtn = document.getElementById('font-increase-btn');
  const fontResetBtn = document.getElementById('font-reset-btn');

  const fontSizes = ['default', 'large', 'xlarge'];

  if (fontIncBtn) {
    fontIncBtn.addEventListener('click', () => {
      const current = html.getAttribute('data-font-size') || 'default';
      let next = 'large';
      if (current === 'large') next = 'xlarge';
      if (current === 'xlarge') next = 'xlarge';
      html.setAttribute('data-font-size', next);
      localStorage.setItem('ssc_font_size', next);
    });
  }

  if (fontDecBtn) {
    fontDecBtn.addEventListener('click', () => {
      const current = html.getAttribute('data-font-size') || 'default';
      let next = 'default';
      if (current === 'xlarge') next = 'large';
      html.setAttribute('data-font-size', next);
      localStorage.setItem('ssc_font_size', next);
    });
  }

  if (fontResetBtn) {
    fontResetBtn.addEventListener('click', () => {
      html.removeAttribute('data-font-size');
      localStorage.removeItem('ssc_font_size');
    });
  }
}

/**
 * Accessible FAQ & Service Accordions
 */
function initAccordions() {
  const accordionHeaders = document.querySelectorAll('.accordion-header');

  accordionHeaders.forEach(header => {
    header.addEventListener('click', () => {
      const item = header.closest('.accordion-item');
      const isExpanded = header.getAttribute('aria-expanded') === 'true';
      const content = item.querySelector('.accordion-content');

      // Toggle current
      header.setAttribute('aria-expanded', String(!isExpanded));
      item.classList.toggle('is-open', !isExpanded);
      if (content) {
        content.setAttribute('aria-hidden', String(isExpanded));
      }
    });
  });
}
