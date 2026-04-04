(function () {
  const ROOT_SELECTOR = '[data-inkfire-header]';
  const ROOT_READY_ATTR = 'data-inkfire-header-ready';

  function clearBodyScrollLock() {
    document.body.style.overflow = '';
    document.body.classList.remove('foundation-mobile-header-open');
    document.documentElement.classList.remove('foundation-mobile-header-open');
  }

  function setupInlineCta(wrap) {
    if (!wrap || wrap.dataset.inlineCtaReady === 'true') {
      return;
    }

    const buttons = wrap.querySelectorAll('.imh-inline-cta-btn');
    const pill = wrap.querySelector('.imh-inline-cta-pill');

    if (!buttons.length || !pill) {
      return;
    }

    wrap.dataset.inlineCtaReady = 'true';

    let activeButton = wrap.querySelector('.imh-inline-cta-btn.is-active') || buttons[0];
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function movePill(target) {
      if (!target || !document.body.contains(target)) {
        return;
      }

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
  }

  function setupHeader(root) {
    if (!root || root.getAttribute(ROOT_READY_ATTR) === 'true') {
      return;
    }

    const overlay = root.querySelector('.imh-overlay');
    const menu = root.querySelector('.imh-menu');

    if (!overlay || !menu) {
      return;
    }

    root.setAttribute(ROOT_READY_ATTR, 'true');

    function getMenu() {
      return root.querySelector('.imh-menu');
    }

    function getOverlay() {
      return root.querySelector('.imh-overlay');
    }

    function getMenuToggles() {
      return root.querySelectorAll('.imh-menu-toggle');
    }

    function getSearchToggles() {
      return root.querySelectorAll('.imh-search-toggle');
    }

    function getSearchSheets() {
      return root.querySelectorAll('.imh-search-sheet');
    }

    function getAccordionItems() {
      return root.querySelectorAll('[data-accordion-item]');
    }

    function setSearchState(isOpen) {
      getSearchSheets().forEach((sheet) => {
        sheet.hidden = !isOpen;
      });

      getSearchToggles().forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    function setMenuState(isOpen) {
      const currentMenu = getMenu();
      const currentOverlay = getOverlay();

      if (!currentMenu || !currentOverlay) {
        return;
      }

      currentMenu.classList.toggle('is-open', isOpen);
      currentMenu.hidden = !isOpen;
      currentMenu.setAttribute('aria-hidden', isOpen ? 'false' : 'true');

      currentOverlay.classList.toggle('is-visible', isOpen);
      currentOverlay.hidden = !isOpen;

      if (isOpen) {
        document.body.style.overflow = 'hidden';
        document.body.classList.add('foundation-mobile-header-open');
        document.documentElement.classList.add('foundation-mobile-header-open');
      } else {
        clearBodyScrollLock();
      }

      getMenuToggles().forEach((toggle) => {
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        toggle.classList.toggle('is-open', isOpen);
      });

      if (!isOpen) {
        setSearchState(false);
      }
    }

    root.addEventListener('click', function (event) {
      const target = event.target;
      if (!(target instanceof Element)) {
        return;
      }

      const menuToggle = target.closest('.imh-menu-toggle');
      if (menuToggle && root.contains(menuToggle)) {
        event.preventDefault();
        const willOpen = menuToggle.getAttribute('aria-expanded') !== 'true';
        setMenuState(willOpen);
        return;
      }

      const searchToggle = target.closest('.imh-search-toggle');
      if (searchToggle && root.contains(searchToggle)) {
        event.preventDefault();
        const willOpen = searchToggle.getAttribute('aria-expanded') !== 'true';
        setSearchState(willOpen);

        const currentMenu = getMenu();
        if (
          willOpen &&
          currentMenu &&
          !currentMenu.classList.contains('is-open') &&
          searchToggle.closest('.imh-menu')
        ) {
          setMenuState(true);
        }

        return;
      }

      const overlayTarget = target.closest('.imh-overlay');
      if (overlayTarget && root.contains(overlayTarget)) {
        setMenuState(false);
        return;
      }

      const accordionToggle = target.closest('.imh-accordion-toggle');
      if (accordionToggle && root.contains(accordionToggle)) {
        const item = accordionToggle.closest('[data-accordion-item]');
        const panel = item ? item.querySelector('.imh-accordion-panel') : null;

        if (!item || !panel) {
          return;
        }

        const isActive = item.classList.contains('is-active');

        getAccordionItems().forEach((otherItem) => {
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
          accordionToggle.setAttribute('aria-expanded', 'true');
          panel.hidden = false;
        }

        return;
      }

      const cardToggle = target.closest('.imh-card-toggle');
      if (cardToggle && root.contains(cardToggle)) {
        const item = cardToggle.closest('[data-card-item]');
        const panel = item ? item.querySelector('.imh-card-panel') : null;

        if (!item || !panel) {
          return;
        }

        const willOpen = cardToggle.getAttribute('aria-expanded') !== 'true';
        item.classList.toggle('is-open', willOpen);
        cardToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        panel.hidden = !willOpen;
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        setMenuState(false);
      }
    });

    root.querySelectorAll('.imh-inline-cta').forEach(setupInlineCta);

    clearBodyScrollLock();
  }

  function initWithin(node) {
    if (!node) {
      return;
    }

    if (node instanceof Element && node.matches(ROOT_SELECTOR)) {
      setupHeader(node);
    }

    if (!(node instanceof Element) && node !== document) {
      return;
    }

    node.querySelectorAll(ROOT_SELECTOR).forEach(setupHeader);
  }

  function bindElementorHooks() {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
      return;
    }

    const hookHandler = function (scope) {
      if (!scope) {
        initWithin(document);
        return;
      }

      const element = scope[0] || (typeof scope.get === 'function' ? scope.get(0) : scope);
      initWithin(element);
    };

    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', hookHandler);
    window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-mobile-header.default', hookHandler);
  }

  function init() {
    initWithin(document);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }

  window.addEventListener('load', init);

  if ('MutationObserver' in window) {
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          initWithin(node);
        });
      });
    });

    observer.observe(document.documentElement, {
      childList: true,
      subtree: true,
    });
  }

  if (window.elementorFrontend) {
    bindElementorHooks();
  } else {
    window.addEventListener('elementor/frontend/init', bindElementorHooks, { once: true });
  }
})();
