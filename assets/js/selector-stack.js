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

  function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
  }

  function unlockParents(root) {
    var parent = root.parentElement;

    while (parent && parent.tagName !== "BODY" && parent.tagName !== "HTML") {
      var style = window.getComputedStyle(parent);

      if (style.overflow !== "visible" || style.overflowX !== "visible" || style.overflowY !== "visible") {
        parent.style.setProperty("overflow", "visible", "important");
        parent.style.setProperty("overflow-x", "visible", "important");
        parent.style.setProperty("overflow-y", "visible", "important");
      }

      parent = parent.parentElement;
    }
  }

  function initSelector(root) {
    if (!root || root.dataset.inkfireSelectorReady === "true") {
      return;
    }

    var cards = Array.prototype.slice.call(root.querySelectorAll(".inkfire-selector__card"));
    var links = Array.prototype.slice.call(root.querySelectorAll(".inkfire-selector__nav a"));
    var layout = root.querySelector(".inkfire-selector__layout");
    var stage = root.querySelector(".inkfire-selector__stage");
    var nav = root.querySelector(".inkfire-selector__nav");
    var desktopBreakpoint = 1024;
    var ticking = false;
    var mobileActiveIndex = 0;
    var isEditorPreview = isElementorEditMode();

    if (!cards.length || !layout || !stage || !nav) {
      return;
    }

    root.dataset.inkfireSelectorReady = "true";

    function getMetrics() {
      var styles = window.getComputedStyle(root);
      var stickyTop = toNumber(styles.getPropertyValue("--inkfire-selector-sticky-top"), 120);
      var peek = toNumber(styles.getPropertyValue("--inkfire-selector-peek"), 132);
      var stackGap = toNumber(styles.getPropertyValue("--inkfire-selector-stack-gap"), 20);
      var layerStep = toNumber(styles.getPropertyValue("--inkfire-selector-layer-step"), 18);
      var exitSpace = toNumber(styles.getPropertyValue("--inkfire-selector-exit-space"), 260);
      var firstCard = cards[0];
      var cardHeight = firstCard ? firstCard.offsetHeight : toNumber(styles.getPropertyValue("--inkfire-selector-card-height"), 620);
      var segment = Math.max(cardHeight + stackGap, 1);
      var stageHeight = cardHeight + peek;
      var pinDistance = segment * Math.max(cards.length - 1, 0);

      return {
        stickyTop: stickyTop,
        peek: peek,
        stackGap: stackGap,
        layerStep: layerStep,
        exitSpace: exitSpace,
        cardHeight: cardHeight,
        segment: segment,
        stageHeight: stageHeight,
        pinDistance: pinDistance,
        totalHeight: stageHeight + pinDistance + exitSpace
      };
    }

    function setActiveLink(index) {
      var activeLink = null;

      links.forEach(function (link, linkIndex) {
        var isActive = linkIndex === index;
        link.classList.toggle("is-active", isActive);

        if (isActive) {
          activeLink = link;
        }
      });

      if (activeLink && window.innerWidth <= desktopBreakpoint && typeof activeLink.scrollIntoView === "function") {
        activeLink.scrollIntoView({
          behavior: "auto",
          block: "nearest",
          inline: "center"
        });
      }
    }

    function resetDesktopState() {
      layout.classList.remove("is-pinned");
      layout.classList.remove("is-finished");
      layout.style.left = "";
      layout.style.width = "";
      root.style.minHeight = "";
      root.style.removeProperty("--inkfire-selector-pin-distance");
      root.style.removeProperty("--inkfire-selector-nav-height");
      root.style.removeProperty("--inkfire-selector-nav-top-auto");
      stage.style.height = "";

      cards.forEach(function (card) {
        card.style.removeProperty("--inkfire-selector-card-y");
        card.style.removeProperty("--inkfire-selector-card-scale");
      });
    }

    function setMobileActiveCard(index) {
      mobileActiveIndex = clamp(index, 0, cards.length - 1);

      cards.forEach(function (card, cardIndex) {
        card.classList.toggle("is-active", cardIndex === mobileActiveIndex);
        card.classList.toggle("is-before", cardIndex < mobileActiveIndex);
      });

      var activeCard = cards[mobileActiveIndex];

      if (activeCard) {
        stage.style.height = activeCard.offsetHeight + "px";
      }

      setActiveLink(mobileActiveIndex);
    }

    function renderDesktop() {
      var metrics = getMetrics();
      var rootRect = root.getBoundingClientRect();
      var rootTop = window.scrollY + rootRect.top;
      var start = rootTop - metrics.stickyTop;
      var end = start + metrics.pinDistance;
      var progress = clamp(window.scrollY - start, 0, metrics.pinDistance);
      var activeIndex = Math.min(cards.length - 1, Math.floor(progress / metrics.segment));
      var navHeight = nav.offsetHeight;
      var centeredNavTop = metrics.stickyTop + Math.max((metrics.cardHeight - navHeight) / 2, 0);

      unlockParents(root);

      root.style.minHeight = metrics.totalHeight + "px";
      root.style.setProperty("--inkfire-selector-pin-distance", metrics.pinDistance + "px");
      root.style.setProperty("--inkfire-selector-nav-height", metrics.cardHeight + "px");
      root.style.setProperty("--inkfire-selector-nav-top-auto", centeredNavTop + "px");
      stage.style.height = metrics.stageHeight + "px";

      if (window.scrollY < start) {
        layout.classList.remove("is-pinned");
        layout.classList.remove("is-finished");
        layout.style.left = "";
        layout.style.width = "";
      } else if (window.scrollY <= end) {
        layout.classList.add("is-pinned");
        layout.classList.remove("is-finished");
        layout.style.left = rootRect.left + "px";
        layout.style.width = rootRect.width + "px";
      } else {
        layout.classList.remove("is-pinned");
        layout.classList.add("is-finished");
        layout.style.left = "";
        layout.style.width = "";
      }

      cards.forEach(function (card, index) {
        var baseY = index * metrics.segment;
        var y = Math.max(0, baseY - progress);
        var distanceFromActive = Math.max(0, activeIndex - index);
        var scale = 1 - Math.min(distanceFromActive, 4) * 0.03;

        card.style.setProperty("--inkfire-selector-card-y", y + "px");
        card.style.setProperty("--inkfire-selector-card-scale", scale.toFixed(3));
      });

      setActiveLink(activeIndex);
    }

    function renderMobile() {
      resetDesktopState();
      setMobileActiveCard(mobileActiveIndex);
    }

    function update() {
      if (isEditorPreview || window.innerWidth <= desktopBreakpoint) {
        renderMobile();
      } else {
        renderDesktop();
      }

      ticking = false;
    }

    function requestUpdate() {
      if (ticking) {
        return;
      }

      ticking = true;
      window.requestAnimationFrame(update);
    }

    links.forEach(function (link, index) {
      link.addEventListener("click", function (event) {
        var metrics = getMetrics();
        var rootRect = root.getBoundingClientRect();
        var rootTop = window.scrollY + rootRect.top;
        var start = rootTop - metrics.stickyTop;

        event.preventDefault();

        if (isEditorPreview || window.innerWidth <= desktopBreakpoint) {
          var targetCard = cards[index];

          if (!targetCard) {
            return;
          }

          setMobileActiveCard(index);

          return;
        }

        window.scrollTo({
          top: start + index * metrics.segment,
          behavior: "smooth"
        });
      });
    });

    if (!isEditorPreview) {
      window.addEventListener("scroll", requestUpdate, { passive: true });
    }
    window.addEventListener("resize", requestUpdate);
    window.addEventListener("load", requestUpdate);

    requestUpdate();

    if ("ResizeObserver" in window) {
      var observer = new ResizeObserver(function () {
        requestUpdate();
      });

      observer.observe(root);
      observer.observe(layout);
      observer.observe(stage);

      cards.forEach(function (card) {
        observer.observe(card);
      });
    }

    if (!isEditorPreview) {
      unlockParents(root);
    } else {
      window.setTimeout(requestUpdate, 120);
    }
  }

  function initAll(scope) {
    var root = scope || document;
    var selectors = root.querySelectorAll("[data-inkfire-selector]");
    Array.prototype.forEach.call(selectors, initSelector);
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
    window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-selector-stack.default", function ($scope) {
      initAll($scope[0] || $scope);
    });
  } else {
    window.addEventListener("elementor/frontend/init", function () {
      if (window.elementorFrontend && window.elementorFrontend.hooks) {
        window.elementorFrontend.hooks.addAction("frontend/element_ready/foundation-selector-stack.default", function ($scope) {
          initAll($scope[0] || $scope);
        });
      }
    });
  }
})();
