(function () {
  function isElementorEditMode() {
    return !!(
      window.elementorFrontend &&
      typeof window.elementorFrontend.isEditMode === "function" &&
      window.elementorFrontend.isEditMode()
    );
  }

  function toNumber(value, fallback) {
    var parsed = parseFloat(value);
    return Number.isFinite(parsed) ? parsed : fallback;
  }

  function initYHero(root) {
    if (!root || root.dataset.foundationYHeroReady === "true") {
      return;
    }

    var stack = root.querySelector(".foundation-y-hero__stack");
    var stage = root.querySelector(".foundation-y-hero__left");
    var up = root.querySelector("[data-yhero-prev]");
    var down = root.querySelector("[data-yhero-next]");
    var cards = stack ? Array.prototype.slice.call(stack.children) : [];
    var index = 0;
    var pendingFrame = 0;
    var isEditorPreview = isElementorEditMode();

    if (!stack || !cards.length) {
      return;
    }

    root.dataset.foundationYHeroReady = "true";

    function isMobile() {
      return window.innerWidth <= toNumber(root.getAttribute("data-mobile-breakpoint"), 920);
    }

    function getStep() {
      var firstCard = cards[0];
      var styles;
      var gap;

      if (!firstCard) {
        return 0;
      }

      styles = window.getComputedStyle(stack);
      gap = toNumber(styles.rowGap || styles.gap, 0);

      return firstCard.offsetHeight + gap;
    }

    function updateButtonState(button, disabled) {
      if (!button) {
        return;
      }

      button.disabled = disabled;
    }

    function updateSlider() {
      var maxIndex;
      var step;
      var stacked = isMobile();

      root.classList.toggle("foundation-y-hero--stacked", stacked);

      if (stacked) {
        stack.style.transform = "none";
        updateButtonState(up, true);
        updateButtonState(down, true);
        return;
      }

      maxIndex = Math.max(0, cards.length - 1);
      index = Math.max(0, Math.min(index, maxIndex));
      step = getStep();

      stack.style.transform = "translateY(-" + (index * step) + "px)";

      updateButtonState(up, index === 0);
      updateButtonState(down, index === maxIndex);
    }

    function scheduleUpdate() {
      if (pendingFrame) {
        window.cancelAnimationFrame(pendingFrame);
      }

      pendingFrame = window.requestAnimationFrame(function () {
        pendingFrame = 0;
        updateSlider();
      });
    }

    if (up) {
      up.addEventListener("click", function () {
        if (isMobile()) {
          return;
        }

        index = Math.max(0, index - 1);
        updateSlider();
      });
    }

    if (down) {
      down.addEventListener("click", function () {
        if (isMobile()) {
          return;
        }

        index = Math.min(cards.length - 1, index + 1);
        updateSlider();
      });
    }

    window.addEventListener("resize", scheduleUpdate);
    window.addEventListener("load", scheduleUpdate);

    if ("ResizeObserver" in window) {
      var observer = new ResizeObserver(function () {
        scheduleUpdate();
      });

      observer.observe(root);
      observer.observe(stack);

      cards.forEach(function (card) {
        observer.observe(card);
      });
    }

    updateSlider();
    scheduleUpdate();

    if (isEditorPreview) {
      window.setTimeout(scheduleUpdate, 120);
    }
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-y-hero]");
    Array.prototype.forEach.call(widgets, initYHero);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initAll(document);
    });
  } else {
    initAll(document);
  }

  window.addEventListener("load", function () {
    initAll(document);
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-y-hero.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-y-hero.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
