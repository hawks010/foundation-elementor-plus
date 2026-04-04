(function () {

  function parseItems(root) {
    var dataNode = root.querySelector('.foundation-rubiks-gallery__data');
    if (!dataNode) {
      return [];
    }

    try {
      var items = JSON.parse(dataNode.textContent || '[]');
      return Array.isArray(items) ? items : [];
    } catch (error) {
      return [];
    }
  }

  function cloneItem(item) {
    return JSON.parse(JSON.stringify(item));
  }

  function ensureSeed(items) {
    var seed = items.slice();

    while (seed.length < 4 && items.length) {
      seed = seed.concat(items.map(cloneItem));
    }

    return seed.slice(0, 4);
  }

  function getPositions() {
    return {
      TL: { top: '0', left: '0' },
      TR: { top: '0', left: 'calc(50% + (var(--foundation-rubiks-gap) / 2))' },
      BL: { top: 'calc(50% + (var(--foundation-rubiks-gap) / 2))', left: '0' },
      BR: { top: 'calc(50% + (var(--foundation-rubiks-gap) / 2))', left: 'calc(50% + (var(--foundation-rubiks-gap) / 2))' },
      OUT_RIGHT: { top: null, left: '120%' },
      OUT_LEFT: { top: null, left: '-60%' },
      OUT_DOWN: { top: '120%', left: null },
      OUT_UP: { top: '-60%', left: null }
    };
  }

  function renderMedia(item) {
    if (item.media_type === 'video') {
      var posterAttr = item.video_poster ? ' poster="' + item.video_poster + '"' : '';
      return '<div class="foundation-rubiks-gallery__surface"><video class="foundation-rubiks-gallery__video" src="' + item.video_url + '"' + posterAttr + ' autoplay muted loop playsinline preload="metadata"></video></div>';
    }

    if (item.media_type === 'lottie') {
      return '<div class="foundation-rubiks-gallery__surface"><div class="foundation-rubiks-gallery__lottie" data-foundation-rubiks-lottie data-lottie-url="' + item.lottie_url + '" aria-hidden="true"></div></div>';
    }

    var imageUrl = item.image_render_url || item.image_url;
    return '<div class="foundation-rubiks-gallery__surface"><img class="foundation-rubiks-gallery__image" src="' + imageUrl + '" alt="" loading="eager" decoding="async"></div>';
  }

  function createCard(item) {
    var card = document.createElement('div');
    card.className = 'foundation-rubiks-gallery__card';
    card.tabIndex = 0;
    card.setAttribute('aria-label', item.alt || 'Portfolio media item');
    card.innerHTML = renderMedia(item);
    initLottie(card);
    return card;
  }

  function initLottie(scope) {
    if (!window.lottie) {
      return;
    }

    var nodes = scope.querySelectorAll('[data-foundation-rubiks-lottie]');
    Array.prototype.forEach.call(nodes, function (node) {
      if (node.dataset.lottieReady === 'true') {
        return;
      }

      var animationUrl = node.getAttribute('data-lottie-url');
      if (!animationUrl) {
        return;
      }

      node.dataset.lottieReady = 'true';

      window.lottie.loadAnimation({
        container: node,
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: animationUrl,
        rendererSettings: {
          preserveAspectRatio: 'xMidYMid slice'
        }
      });
    });
  }

  function setPosition(card, position) {
    if (!card) {
      return;
    }

    if (position.top !== null) {
      card.style.top = position.top;
    }

    if (position.left !== null) {
      card.style.left = position.left;
    }
  }

  function initGallery(root) {
    if (!root || root.dataset.foundationRubiksReady === 'true') {
      return;
    }

    var stage = root.querySelector('.foundation-rubiks-gallery__stage');
    var cards = Array.prototype.slice.call(stage.querySelectorAll('.foundation-rubiks-gallery__card'));
    var items = parseItems(root);
    var prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var isSmallViewport = window.matchMedia && window.matchMedia('(max-width: 767px)').matches;
    var pauseOnHover = root.getAttribute('data-pause-on-hover') !== 'false';
    var interval = parseInt(root.getAttribute('data-interval') || '1200', 10);
    var moveDuration = Math.max(520, Math.min(Math.round(interval * 0.58), 920));
    var effectiveInterval = Math.max(interval, moveDuration + 280);
    var positions = getPositions();
    var slots = cards.slice(0, 4);
    var imgIndex = slots.length % Math.max(items.length, 1);
    var isPaused = false;
    var timer = null;
    var sequenceStep = 0;
    var inViewport = false;
    var hasStarted = false;
    var observer = null;

    if (!stage || items.length < 1 || slots.length < 4) {
      return;
    }

    root.dataset.foundationRubiksReady = 'true';
    root.style.setProperty('--foundation-rubiks-move-duration', moveDuration + 'ms');
    initLottie(root);

    function getNextItem() {
      var item = items[imgIndex];
      imgIndex = (imgIndex + 1) % items.length;
      return item;
    }

    function spawnCard(position) {
      var card = createCard(getNextItem());
      stage.appendChild(card);
      setPosition(card, position);
      return card;
    }

    function doDualHorizontalSlide() {
      var exitTR = slots[1];
      var moveTL = slots[0];
      var exitBL = slots[2];
      var moveBR = slots[3];

      setPosition(exitTR, positions.OUT_RIGHT);
      setPosition(moveTL, positions.TR);
      setPosition(exitBL, positions.OUT_LEFT);
      setPosition(moveBR, positions.BL);

      var enterTL = spawnCard(positions.OUT_LEFT);
      enterTL.style.top = '0';
      enterTL.offsetHeight;
      setPosition(enterTL, positions.TL);

      var enterBR = spawnCard(positions.OUT_RIGHT);
      enterBR.style.top = 'calc(50% + (var(--foundation-rubiks-gap) / 2))';
      enterBR.offsetHeight;
      setPosition(enterBR, positions.BR);

      window.setTimeout(function () {
        if (exitTR && exitTR.parentNode) {
          exitTR.parentNode.removeChild(exitTR);
        }
        if (exitBL && exitBL.parentNode) {
          exitBL.parentNode.removeChild(exitBL);
        }
      }, moveDuration + 40);

      slots[1] = moveTL;
      slots[0] = enterTL;
      slots[2] = moveBR;
      slots[3] = enterBR;
    }

    function doRightColumnDrop() {
      var exitBR = slots[3];
      var moveTR = slots[1];

      setPosition(exitBR, positions.OUT_DOWN);
      setPosition(moveTR, positions.BR);

      var enterTR = spawnCard(positions.OUT_UP);
      enterTR.style.left = 'calc(50% + (var(--foundation-rubiks-gap) / 2))';
      enterTR.offsetHeight;
      setPosition(enterTR, positions.TR);

      window.setTimeout(function () {
        if (exitBR && exitBR.parentNode) {
          exitBR.parentNode.removeChild(exitBR);
        }
      }, moveDuration + 40);

      slots[3] = moveTR;
      slots[1] = enterTR;
    }

    function tick() {
      if (isPaused || prefersReducedMotion || isSmallViewport || !inViewport || document.hidden) {
        timer = window.setTimeout(tick, 500);
        return;
      }

      if (sequenceStep === 0 || sequenceStep === 1) {
        doDualHorizontalSlide();
        sequenceStep += 1;
      } else {
        doRightColumnDrop();
        sequenceStep = 0;
      }

      timer = window.setTimeout(tick, effectiveInterval);
    }

    function setActive() {
      inViewport = true;
      if (!hasStarted && !prefersReducedMotion && !isSmallViewport) {
        hasStarted = true;
        timer = window.setTimeout(tick, Math.min(180, Math.max(80, Math.round(effectiveInterval * 0.12))));
      }
    }

    function setInactive() {
      inViewport = false;
    }

    if (pauseOnHover) {
      root.addEventListener('mouseenter', function () {
        isPaused = true;
      });
      root.addEventListener('mouseleave', function () {
        isPaused = false;
      });
      root.addEventListener('focusin', function () {
        isPaused = true;
      });
      root.addEventListener('focusout', function () {
        isPaused = false;
      });
    }

    setPosition(slots[0], positions.TL);
    setPosition(slots[1], positions.TR);
    setPosition(slots[2], positions.BL);
    setPosition(slots[3], positions.BR);

    if ('IntersectionObserver' in window) {
      observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            setActive();
          } else {
            setInactive();
          }
        });
      }, {
        threshold: 0.3,
        rootMargin: '120px 0px'
      });

      observer.observe(root);
    } else if (!prefersReducedMotion && !isSmallViewport) {
      setActive();
    }

    document.addEventListener('visibilitychange', function () {
      if (!document.hidden && inViewport && !timer) {
        timer = window.setTimeout(tick, Math.min(180, Math.max(80, Math.round(effectiveInterval * 0.12))));
      }
    });

    if (isSmallViewport) {
      root.setAttribute('data-foundation-rubiks-static', 'true');
    }
  }

  function initAll(scope) {
    var root = scope || document;
    var galleries = root.querySelectorAll('[data-foundation-rubiks-gallery]');
    Array.prototype.forEach.call(galleries, initGallery);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      initAll(document);
    });
  } else {
    initAll(document);
  }

  window.addEventListener('load', function () {
    initAll(document);
  });

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-rubiks-gallery.default', function ($scope) {
      initAll($scope[0] || $scope);
    });
  }
})();
