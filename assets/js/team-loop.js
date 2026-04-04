(function () {
  function initTeamLoop(root) {
    if (!root || root.dataset.foundationTeamLoopReady === "true") {
      return;
    }

    var departmentSelect = root.querySelector('[data-team-loop-filter="department"]');
    var groupSelect = root.querySelector('[data-team-loop-filter="group"]');
    var items = Array.prototype.slice.call(root.querySelectorAll("[data-team-loop-item]"));
    var emptyState = root.querySelector("[data-team-loop-empty]");
    var defaultDepartment = root.getAttribute("data-default-department") || "all";
    var defaultGroup = normalizeGroup(root.getAttribute("data-default-group") || "all");

    root.dataset.foundationTeamLoopReady = "true";

    function getCurrentDepartment() {
      return departmentSelect ? departmentSelect.value : defaultDepartment;
    }

    function getCurrentGroup() {
      return normalizeGroup(groupSelect ? groupSelect.value : defaultGroup);
    }

    function normalizeGroup(group) {
      if (group === "management") {
        return "founders";
      }

      if (group === "staff" || group === "founders" || group === "all") {
        return group;
      }

      return "all";
    }

    function closeItem(item) {
      var toggle = item.querySelector("[data-team-loop-toggle]");
      var panel = item.querySelector("[data-team-loop-panel]");
      var text = item.querySelector(".foundation-team-loop__toggle-text");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "false");
      panel.hidden = true;

      if (text) {
        text.textContent = toggle.getAttribute("data-closed-label") || text.textContent;
      }
    }

    function openItem(item) {
      var toggle = item.querySelector("[data-team-loop-toggle]");
      var panel = item.querySelector("[data-team-loop-panel]");
      var text = item.querySelector(".foundation-team-loop__toggle-text");

      if (!toggle || !panel) {
        return;
      }

      toggle.setAttribute("aria-expanded", "true");
      panel.hidden = false;

      if (text) {
        text.textContent = toggle.getAttribute("data-open-label") || text.textContent;
      }
    }

    function applyFilters() {
      var department = getCurrentDepartment();
      var group = getCurrentGroup();
      var visibleCount = 0;

      items.forEach(function (item) {
        var departments = (item.getAttribute("data-team-loop-departments") || "").split(/\s+/).filter(Boolean);
        var itemGroup = normalizeGroup(item.getAttribute("data-team-loop-group") || "staff");
        var matchesDepartment = department === "all" || departments.indexOf(department) !== -1;
        var matchesGroup = group === "all" || itemGroup === group;
        var shouldShow = matchesDepartment && matchesGroup;

        item.hidden = !shouldShow;
        item.setAttribute("aria-hidden", shouldShow ? "false" : "true");

        if (shouldShow) {
          item.style.removeProperty("display");
        } else {
          item.style.setProperty("display", "none", "important");
        }

        if (!shouldShow) {
          closeItem(item);
        } else {
          visibleCount += 1;
        }
      });

      if (emptyState) {
        emptyState.hidden = visibleCount !== 0;
      }
    }

    items.forEach(function (item) {
      var toggle = item.querySelector("[data-team-loop-toggle]");

      if (!toggle) {
        return;
      }

      toggle.addEventListener("click", function () {
        var expanded = toggle.getAttribute("aria-expanded") === "true";

        if (expanded) {
          closeItem(item);
        } else {
          openItem(item);
        }
      });
    });

    if (departmentSelect) {
      departmentSelect.addEventListener("change", applyFilters);
    }

    if (groupSelect) {
      groupSelect.addEventListener("change", applyFilters);
    }

    applyFilters();
  }

  function initAll(scope) {
    var root = scope || document;
    var widgets = root.querySelectorAll("[data-foundation-team-loop]");
    Array.prototype.forEach.call(widgets, initTeamLoop);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-team-loop.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-team-loop.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
