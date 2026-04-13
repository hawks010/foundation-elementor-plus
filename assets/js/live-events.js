(function () {
  function initLiveEvents(root) {
    if (!root || root.dataset.foundationLiveEventsReady === "true") {
      return;
    }

    var items = Array.prototype.slice.call(root.querySelectorAll("[data-live-events-item]"));
    var defaultOpen = root.getAttribute("data-default-open") === "yes";

    if (!items.length) {
      return;
    }

    root.dataset.foundationLiveEventsReady = "true";

    function closeItem(item) {
      var toggle = item.querySelector("[data-live-events-toggle]");
      var panel = item.querySelector("[data-live-events-panel]");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "false");
      panel.hidden = true;
    }

    function openItem(item) {
      var toggle = item.querySelector("[data-live-events-toggle]");
      var panel = item.querySelector("[data-live-events-panel]");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "true");
      panel.hidden = false;
    }

    items.forEach(function (item) {
      var toggle = item.querySelector("[data-live-events-toggle]");

      if (!toggle) {
        return;
      }

      toggle.addEventListener("click", function () {
        var isExpanded = toggle.getAttribute("aria-expanded") === "true";

        items.forEach(closeItem);

        if (!isExpanded) {
          openItem(item);
        }
      });
    });

    items.forEach(function (item, index) {
      if (!defaultOpen || index > 0) {
        closeItem(item);
      }
    });

    if (defaultOpen) {
      var firstUpcoming = root.querySelector('[data-live-events-item][data-event-group="upcoming"]');

      if (firstUpcoming) {
        openItem(firstUpcoming);
      }
    }
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-live-events]");
    Array.prototype.forEach.call(widgets, initLiveEvents);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-live-events.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-live-events.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
