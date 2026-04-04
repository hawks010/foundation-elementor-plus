(function () {
  function initLiveRoles(root) {
    if (!root || root.dataset.foundationLiveRolesReady === "true") {
      return;
    }

    var filters = Array.prototype.slice.call(root.querySelectorAll("[data-live-roles-filter]"));
    var roles = Array.prototype.slice.call(root.querySelectorAll("[data-live-roles-item]"));
    var emptyState = root.querySelector("[data-live-roles-empty]");
    var defaultFilter = root.getAttribute("data-default-filter") || "all";
    var defaultOpen = root.getAttribute("data-default-open") === "yes";
    var currentFilter = defaultFilter;

    if (!roles.length) {
      return;
    }

    root.dataset.foundationLiveRolesReady = "true";

    function getVisibleRoles() {
      return roles.filter(function (role) {
        return !role.hidden;
      });
    }

    function closeRole(role) {
      var toggle = role.querySelector("[data-live-roles-toggle]");
      var panel = role.querySelector("[data-live-roles-panel]");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "false");
      panel.hidden = true;
    }

    function openRole(role) {
      var toggle = role.querySelector("[data-live-roles-toggle]");
      var panel = role.querySelector("[data-live-roles-panel]");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "true");
      panel.hidden = false;
    }

    function applyFilter(filter) {
      currentFilter = filter;

      filters.forEach(function (button) {
        var active = button.getAttribute("data-live-roles-filter") === filter;
        button.classList.toggle("is-active", active);
        button.setAttribute("aria-pressed", active ? "true" : "false");
      });

      roles.forEach(function (role) {
        var status = role.getAttribute("data-role-status") || "open";
        var shouldShow = filter === "all" || status === filter;

        role.hidden = !shouldShow;

        if (!shouldShow) {
          closeRole(role);
        }
      });

      if (emptyState) {
        emptyState.hidden = getVisibleRoles().length !== 0;
      }

      if (defaultOpen) {
        var visibleRoles = getVisibleRoles();
        var hasExpanded = visibleRoles.some(function (role) {
          var toggle = role.querySelector("[data-live-roles-toggle]");
          return toggle && toggle.getAttribute("aria-expanded") === "true";
        });

        if (!hasExpanded && visibleRoles[0]) {
          openRole(visibleRoles[0]);
        }
      }
    }

    roles.forEach(function (role) {
      var toggle = role.querySelector("[data-live-roles-toggle]");
      var panel = role.querySelector("[data-live-roles-panel]");

      if (!toggle || !panel) {
        return;
      }

      toggle.addEventListener("click", function () {
        var isExpanded = toggle.getAttribute("aria-expanded") === "true";

        roles.forEach(closeRole);

        if (!isExpanded) {
          openRole(role);
        }
      });
    });

    filters.forEach(function (button) {
      button.addEventListener("click", function () {
        var filter = button.getAttribute("data-live-roles-filter") || "all";
        applyFilter(filter);
      });
    });

    if (defaultOpen) {
      roles.forEach(function (role, index) {
        if (index > 0) {
          closeRole(role);
        }
      });
    }

    applyFilter(defaultFilter);
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-live-roles]");
    Array.prototype.forEach.call(widgets, initLiveRoles);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-live-roles.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-live-roles.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
