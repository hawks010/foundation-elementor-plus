(function () {
  function initBounceRail(root) {
    if (!root || root.dataset.foundationBounceReady === "true") {
      return;
    }

    var track = root.querySelector("[data-bounce-track]");
    var prev = root.querySelector("[data-bounce-prev]");
    var next = root.querySelector("[data-bounce-next]");
    var step = parseFloat(root.getAttribute("data-scroll-step")) || 302;

    if (!track) {
      return;
    }

    root.dataset.foundationBounceReady = "true";

    if (next) {
      next.addEventListener("click", function () {
        track.scrollBy({ left: step, behavior: "smooth" });
      });
    }

    if (prev) {
      prev.addEventListener("click", function () {
        track.scrollBy({ left: -step, behavior: "smooth" });
      });
    }
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-bounce-rail]");
    Array.prototype.forEach.call(widgets, initBounceRail);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-bounce-rail.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-bounce-rail.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
