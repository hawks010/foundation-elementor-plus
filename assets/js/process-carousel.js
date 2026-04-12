(function () {
  function initProcessCarousel(root) {
    if (!root || root.dataset.foundationProcessReady === "true") {
      return;
    }

    var track = root.querySelector("[data-process-track]");
    var steps = Array.prototype.slice.call(root.querySelectorAll("[data-process-step]"));
    var cards = Array.prototype.slice.call(root.querySelectorAll("[data-process-card]"));
    var next = root.querySelector("[data-process-next]");
    var prev = root.querySelector("[data-process-prev]");
    var index = 0;

    if (!track || !cards.length || !steps.length) {
      return;
    }

    root.dataset.foundationProcessReady = "true";

    function getStepSize() {
      var firstCard = cards[0];
      var styles = window.getComputedStyle(track);
      var gap = parseFloat(styles.columnGap || styles.gap || 0) || 0;

      return firstCard.offsetWidth + gap;
    }

    function update() {
      var translate = index * getStepSize();

      track.style.transform = "translateX(-" + translate + "px)";
      if (prev) {
        prev.disabled = index === 0;
      }
      if (next) {
        next.disabled = index === cards.length - 1;
      }

      steps.forEach(function (step, stepIndex) {
        var active = stepIndex === index;
        step.classList.toggle("is-active", active);
        step.setAttribute("aria-selected", active ? "true" : "false");
        step.setAttribute("tabindex", active ? "0" : "-1");
      });

      cards.forEach(function (card, cardIndex) {
        var active = cardIndex === index;
        card.classList.toggle("is-active", active);
        card.setAttribute("aria-hidden", active ? "false" : "true");
      });
    }

    steps.forEach(function (step) {
      step.addEventListener("click", function () {
        index = parseInt(step.getAttribute("data-process-step"), 10) || 0;
        index = Math.max(0, Math.min(index, cards.length - 1));
        update();
      });
    });

    if (next) {
      next.addEventListener("click", function () {
        index = Math.min(index + 1, cards.length - 1);
        update();
      });
    }

    if (prev) {
      prev.addEventListener("click", function () {
        index = Math.max(index - 1, 0);
        update();
      });
    }

    window.addEventListener("resize", update);
    window.addEventListener("load", update);
    root.addEventListener("keydown", function (event) {
      if (event.key === "ArrowRight") {
        event.preventDefault();
        index = Math.min(index + 1, cards.length - 1);
        update();
      }

      if (event.key === "ArrowLeft") {
        event.preventDefault();
        index = Math.max(index - 1, 0);
        update();
      }
    });

    update();
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-process-carousel]");
    Array.prototype.forEach.call(widgets, initProcessCarousel);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-process-carousel.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-process-carousel.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
