(function () {
  function setupHeader(root) {
    const overlay = root.querySelector('.imh-overlay');
    const menu = root.querySelector('.imh-menu');
    const menuToggles = root.querySelectorAll('.imh-menu-toggle');
    const searchToggles = root.querySelectorAll('.imh-search-toggle');
    const searchSheets = root.querySelectorAll('.imh-search-sheet');
    const accordionItems = root.querySelectorAll('[data-accordion-item]');
    const cardItems = root.querySelectorAll('[data-card-item]');

    if (!overlay || !menu) {
      return;
    }

    function clearBodyScrollLock() {
      document.body.style.overflow = '';
      document.body.classList.remove('foundation-mobile-header-open');
      document.documentElement.classList.remove('foundation-mobile-header-open');
    }

    function setSearchState(isOpen) {
      searchSheets.forEach((sheet) => {
        sheet.hidden = !isOpen;
      });
      searchToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    function setMenuState(isOpen) {
      menu.classList.toggle('is-open', isOpen);
      menu.hidden = !isOpen;
      menu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
      overlay.classList.toggle('is-visible', isOpen);
      overlay.hidden = !isOpen;
      if (isOpen) {
        document.body.style.overflow = 'hidden';
        document.body.classList.add('foundation-mobile-header-open');
        document.documentElement.classList.add('foundation-mobile-header-open');
      } else {
        clearBodyScrollLock();
      }

      menuToggles.forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggle.classList.toggle('is-open', isOpen);
      });

      if (!isOpen) {
        setSearchState(false);
      }
    }

    menuToggles.forEach((toggle) => {
      toggle.addEventListener('click', function () {
        const willOpen = this.getAttribute('aria-expanded') !== 'true';
        setMenuState(willOpen);
      });
    });

    searchToggles.forEach((toggle) => {
      toggle.addEventListener('click', function () {
        const willOpen = this.getAttribute('aria-expanded') !== 'true';
        setSearchState(willOpen);
        if (willOpen && !menu.classList.contains('is-open') && this.closest('.imh-menu')) {
          setMenuState(true);
        }
      });
    });

    overlay.addEventListener('click', function () {
      setMenuState(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        setMenuState(false);
      }
    });

    accordionItems.forEach((item) => {
      const toggle = item.querySelector('.imh-accordion-toggle');
      const panel = item.querySelector('.imh-accordion-panel');

      if (!toggle || !panel) {
        return;
      }

      toggle.addEventListener('click', function () {
        const isActive = item.classList.contains('is-active');

        accordionItems.forEach((otherItem) => {
          const otherToggle = otherItem.querySelector('.imh-accordion-toggle');
          const otherPanel = otherItem.querySelector('.imh-accordion-panel');
          otherItem.classList.remove('is-active');
          if (otherToggle) {
            otherToggle.setAttribute('aria-expanded', 'false');
          }
          if (otherPanel) {
            otherPanel.hidden = true;
          }
        });

        if (!isActive) {
          item.classList.add('is-active');
          toggle.setAttribute('aria-expanded', 'true');
          panel.hidden = false;
        }
      });
    });

    cardItems.forEach((item) => {
      const toggle = item.querySelector('.imh-card-toggle');
      const panel = item.querySelector('.imh-card-panel');

      if (!toggle || !panel) {
        return;
      }

      toggle.addEventListener('click', function () {
        const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
        item.classList.toggle('is-open', willOpen);
        toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        panel.hidden = !willOpen;
      });
    });

    root.querySelectorAll('.imh-inline-cta').forEach((wrap) => {
      const buttons = wrap.querySelectorAll('.imh-inline-cta-btn');
      const pill = wrap.querySelector('.imh-inline-cta-pill');

      if (!buttons.length || !pill) {
        return;
      }

      let activeButton = wrap.querySelector('.imh-inline-cta-btn.is-active') || buttons[0];
      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

      function movePill(target) {
        const wrapRect = wrap.getBoundingClientRect();
        const rect = target.getBoundingClientRect();
        const offset = rect.left - wrapRect.left;

        pill.style.width = rect.width + 'px';

        if (prefersReducedMotion) {
          pill.style.transition = 'none';
          pill.style.transform = 'translateX(' + offset + 'px)';
          window.setTimeout(function () {
            pill.style.transition = '';
          }, 0);
        } else {
          pill.style.transform = 'translateX(' + offset + 'px)';
        }

        buttons.forEach((button) => {
          button.classList.remove('is-active');
          button.setAttribute('aria-current', 'false');
        });

        target.classList.add('is-active');
        target.setAttribute('aria-current', 'true');
        activeButton = target;
      }

      buttons.forEach((button) => {
        button.addEventListener('mouseenter', function () {
          movePill(button);
        });

        button.addEventListener('focus', function () {
          movePill(button);
        });

        button.addEventListener('keydown', function (event) {
          if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
            return;
          }

          event.preventDefault();
          const currentIndex = Array.prototype.indexOf.call(buttons, button);
          const nextIndex = event.key === 'ArrowRight'
            ? (currentIndex + 1) % buttons.length
            : (currentIndex - 1 + buttons.length) % buttons.length;

          buttons[nextIndex].focus();
        });
      });

      window.setTimeout(function () {
        movePill(activeButton);
      }, 80);

      window.addEventListener('resize', function () {
        movePill(activeButton);
      });
    });

    clearBodyScrollLock();
  }

  function init() {
    document.querySelectorAll('[data-inkfire-header]').forEach(setupHeader);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
