(function () {
  'use strict';

  const READY_ATTR = 'data-foundation-inkfire-splash-ready';
  const AUTOPLAY_STORAGE_KEY = 'foundation_inkfire_autoplay_mode_v1';
  const urlModeOverride = new URLSearchParams(window.location.search).get('foundation_inkfire_mode');
  const touchDeviceQuery = window.matchMedia('(hover: none) and (pointer: coarse)');
  const isTouchDevice = touchDeviceQuery.matches || navigator.maxTouchPoints > 0;
  const defaultPalette = ['#08352F', '#0E6055', '#138170', '#07A079', '#32B190', '#CE3D27', '#D85814', '#E27200', '#EC853D', '#F18E5C'];
  const globalPalette = Array.isArray(window.FOUNDATION_INKFIRE_SPLASH_CONFIG?.palette)
    ? window.FOUNDATION_INKFIRE_SPLASH_CONFIG.palette
    : defaultPalette;
  const shouldDisableFluid =
    window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
    !hasWebGL() ||
    typeof window.foundationInkfireGenerateCanvas !== 'function';

  let currentMode = isTouchDevice ? 'auto' : 'hover';
  if (urlModeOverride === 'hover' || urlModeOverride === 'auto') {
    currentMode = urlModeOverride;
  } else {
    try {
      const storedMode = localStorage.getItem(AUTOPLAY_STORAGE_KEY);
      if (storedMode === 'hover' || storedMode === 'auto') currentMode = storedMode;
    } catch (error) {}
  }

  if (isTouchDevice && currentMode === 'hover') {
    currentMode = 'auto';
  }

  const sectionStates = [];
  let visibilityObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            const state = sectionStates.find((item) => item.section === entry.target);
            if (!state) return;

            state.isVisible = entry.isIntersecting;

            if (!entry.isIntersecting) {
              window.clearTimeout(state.bootTimeoutId);
              stopAutoplayLoop(state);
              if (currentMode !== 'hover' || !state.section.matches(':hover')) {
                setPaused(state, true);
              }
              return;
            }

            if (currentMode === 'auto') {
              primeVisibleSection(state);
            } else if (state.section.matches(':hover')) {
              setPaused(state, false);
            } else {
              setPaused(state, true);
            }
          });
        },
        {
          threshold: 0.12
        }
      )
    : null;

  function getSplashSections(scope) {
    const root = scope || document;
    const sections = [];

    if (root.matches && root.matches('[data-foundation-inkfire-splash]')) {
      sections.push(root);
    }

    if (root.querySelectorAll) {
      root.querySelectorAll('[data-foundation-inkfire-splash]').forEach((section) => {
        sections.push(section);
      });
    }

    return sections.filter((section) => section && section.getAttribute(READY_ATTR) !== 'true');
  }

  function registerSection(section) {
    if (!section || section.getAttribute(READY_ATTR) === 'true') {
      return;
    }

    section.setAttribute(READY_ATTR, 'true');

    if (shouldDisableFluid) {
      disableFluidForSection(section);
      return;
    }

    const state = createSectionState(section);
    if (!state) {
      return;
    }

    sectionStates.push(state);

    if (visibilityObserver) {
      visibilityObserver.observe(state.section);
    } else {
      state.isVisible = true;
    }

    if (isSectionInViewport(state.section)) {
      state.isVisible = true;
    }

    if (currentMode === 'auto') {
      if (state.isVisible) {
        primeVisibleSection(state);
      } else {
        setPaused(state, true);
      }
    } else if (state.isVisible && state.section.matches(':hover')) {
      restoreFluidDissipation(state);
      setPaused(state, false);
      scheduleHoverSettle(state);
    } else {
      setPaused(state, true);
    }
  }

  function registerSections(scope) {
    getSplashSections(scope).forEach(registerSection);
  }

  registerSections(document);

  function disableFluidForSection(section) {
    const canvas = section.querySelector('.foundation-inkfire-splash__canvas');
    const mask = section.querySelector('.foundation-inkfire-splash__mask');

    if (canvas && canvas.parentNode) canvas.parentNode.removeChild(canvas);
    if (mask && mask.parentNode) mask.parentNode.removeChild(mask);
  }

  function hasWebGL() {
    try {
      const testCanvas = document.createElement('canvas');
      return !!(testCanvas.getContext('webgl') || testCanvas.getContext('experimental-webgl'));
    } catch (error) {
      return false;
    }
  }

  function parsePalette(section) {
    const attributePalette = typeof section.dataset.palette === 'string'
      ? section.dataset.palette
          .split(',')
          .map((color) => color.trim())
          .filter(Boolean)
      : [];

    return attributePalette.length ? attributePalette : globalPalette;
  }

  function hexToFluidColor(hex, forcedIntensity) {
    if (typeof hex !== 'string') return null;

    const normalizedHex = hex.trim().replace('#', '');
    if (!/^[0-9a-fA-F]{6}$/.test(normalizedHex)) return null;

    const numericColor = parseInt(normalizedHex, 16);
    const red = (numericColor >> 16) & 255;
    const green = (numericColor >> 8) & 255;
    const blue = numericColor & 255;
    const maxChannel = Math.max(red, green, blue);
    const minChannel = Math.min(red, green, blue);
    const brightness = (red * 0.299 + green * 0.587 + blue * 0.114) / 255;
    let intensity = typeof forcedIntensity === 'number' ? forcedIntensity : 0.082;

    if (typeof forcedIntensity !== 'number') {
      if (brightness < 0.22) intensity = 0.098;
      if (brightness > 0.86) intensity = 0.05;
    }
    if (maxChannel - minChannel < 18) intensity *= 0.9;

    return {
      r: (red / 255) * intensity,
      g: (green / 255) * intensity,
      b: (blue / 255) * intensity
    };
  }

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function pickAmbientColor(state, progress) {
    const palette = state.ambientPalette;
    if (!palette.length) {
      return { r: 0.06, g: 0.025, b: 0.01 };
    }

    const normalizedProgress = ((progress % 1) + 1) % 1;
    const colorIndex = Math.floor(normalizedProgress * palette.length) % palette.length;
    return palette[colorIndex];
  }

  function createSectionState(section) {
    const canvas = section.querySelector('.foundation-inkfire-splash__canvas');
    if (!canvas) return null;

    const palette = parsePalette(section);
    const paletteKey = typeof section.dataset.paletteKey === 'string' ? section.dataset.paletteKey : '';
    const isHomepageHero = section.classList.contains('foundation-inkfire-splash--preset-home');
    const isMobileViewport = window.matchMedia('(max-width: 768px)').matches;
    const isTabletViewport = window.matchMedia('(max-width: 1024px)').matches;
    const isLargeViewport = window.innerWidth >= 1500;
    const isDenseScreen = window.devicePixelRatio > 1.5;
    const isOrangePalette = paletteKey === 'inkfire_orange';
    const isGradientPalette = paletteKey === 'inkfire_gradient';
    const isWarmPalette = isOrangePalette || isGradientPalette;
    const forcedPaletteIntensity = isTouchDevice
      ? (isHomepageHero ? 0.086 : (isWarmPalette ? 0.082 : 0.078))
      : (isHomepageHero ? 0.094 : (isWarmPalette ? 0.088 : 0.082));
    const fluidPalette = palette
      .map((hex) => hexToFluidColor(hex, forcedPaletteIntensity))
      .filter(Boolean);

    const simResolution = isMobileViewport ? 40 : (isHomepageHero ? 88 : (isLargeViewport ? 92 : 84));
    const dyeResolution = isMobileViewport
      ? 384
      : (isHomepageHero
        ? (isLargeViewport ? 896 : (isDenseScreen ? 832 : 768))
        : (isTabletViewport ? 640 : (isLargeViewport ? 832 : 768)));
    const bloomIterations = isTouchDevice ? 3 : (isHomepageHero ? 4 : 3);
    const bloomResolution = isTouchDevice ? 160 : (isHomepageHero ? 224 : (isWarmPalette ? 192 : 176));
    const bloomIntensity = isTouchDevice ? 0.11 : (isHomepageHero ? 0.18 : (isWarmPalette ? 0.16 : 0.14));
    const bloomThreshold = isTouchDevice ? 0.74 : (isHomepageHero ? 0.71 : (isWarmPalette ? 0.7 : 0.72));
    const bloomSoftKnee = isTouchDevice ? 0.24 : (isHomepageHero ? 0.28 : (isWarmPalette ? 0.3 : 0.26));
    const maxPixelRatio = isTouchDevice ? 1 : (isHomepageHero ? 1.12 : (isWarmPalette ? 1.1 : 1.05));
    const densityDissipation = isTouchDevice ? 0.94 : 0.9;
    const velocityDissipation = isTouchDevice ? 0.34 : 0.3;

    window.foundationInkfireGenerateCanvas(canvas, {
      SIM_RESOLUTION: simResolution,
      DYE_RESOLUTION: dyeResolution,
      MAX_PIXEL_RATIO: maxPixelRatio,
      DENSITY_DISSIPATION: densityDissipation,
      VELOCITY_DISSIPATION: velocityDissipation,
      PRESSURE_ITERATIONS: 8,
      BLOOM: true,
      BLOOM_ITERATIONS: bloomIterations,
      BLOOM_RESOLUTION: bloomResolution,
      BLOOM_INTENSITY: bloomIntensity,
      BLOOM_THRESHOLD: bloomThreshold,
      BLOOM_SOFT_KNEE: bloomSoftKnee,
      ALLOW_TOUCH_INPUT: !isTouchDevice,
      PAUSED: false,
      BRAND_PALETTE: palette
    });

    const state = {
      section,
      canvas,
      ambientPalette: fluidPalette,
      animationFrameId: null,
      hoverFrameId: null,
      hoverPoint: null,
      lastHoverX: 0.5,
      lastHoverY: 0.5,
      pointerX: 0.5,
      pointerY: 0.5,
      velocityX: 0,
      velocityY: 0,
      targetX: Math.random(),
      targetY: Math.random(),
      colorCycle: 0,
      isVisible: !visibilityObserver,
      bootTimeoutId: null,
      idleClearTimeoutId: null,
      idlePauseTimeoutId: null,
      scrollSuspendUntil: 0,
      baseDensityDissipation: densityDissipation,
      baseVelocityDissipation: velocityDissipation,
      idleDensityDissipation: 2.2,
      idleVelocityDissipation: 1.15,
      autoAcceleration: isTouchDevice ? 0.00024 : 0.0004,
      autoFriction: isTouchDevice ? 0.952 : 0.96,
      autoForceMultiplier: isTouchDevice ? 3900 : 5600,
      autoColorCycleStep: isTouchDevice ? 0.0042 : 0.006,
      autoRadius: isTouchDevice ? 0.21 : 0.26,
      autoMinForceThreshold: isTouchDevice ? 4.2 : 4,
      autoStartVelocityX: isTouchDevice ? 0.0084 : 0.012,
      autoStartVelocityY: isTouchDevice ? 0.0068 : 0.01
    };

    section.addEventListener('mouseenter', (event) => {
      if (isHoverSuspended(state)) return;
      section.classList.add('foundation-inkfire-splash--active');
      setPaused(state, false);
      restoreFluidDissipation(state);
      triggerHoverBurst(state, event);
      scheduleHoverSettle(state);
    });

    section.addEventListener('mousemove', (event) => {
      if (currentMode !== 'hover' || !state.isVisible || isHoverSuspended(state)) return;
      section.classList.add('foundation-inkfire-splash--active');
      setPaused(state, false);
      restoreFluidDissipation(state);
      queueHoverSplat(state, event);
      scheduleHoverSettle(state);
    });

    section.addEventListener('mouseleave', () => {
      if (currentMode === 'hover') {
        scheduleHoverSettle(state);
      }
    });

    return state;
  }

  function setPaused(state, isPaused) {
    if (
      state.canvas.__foundationInkfireFluid &&
      typeof state.canvas.__foundationInkfireFluid.pause === 'function'
    ) {
      state.canvas.__foundationInkfireFluid.pause(!!isPaused);
    }
  }

  function endInteraction(state) {
    if (
      state.canvas.__foundationInkfireFluid &&
      typeof state.canvas.__foundationInkfireFluid.endInteraction === 'function'
    ) {
      state.canvas.__foundationInkfireFluid.endInteraction();
    }
  }

  function setFluidDissipation(state, density, velocity) {
    if (
      state.canvas.__foundationInkfireFluid &&
      typeof state.canvas.__foundationInkfireFluid.setDissipation === 'function'
    ) {
      state.canvas.__foundationInkfireFluid.setDissipation(density, velocity);
    }
  }

  function restoreFluidDissipation(state) {
    setFluidDissipation(
      state,
      state.baseDensityDissipation,
      state.baseVelocityDissipation
    );
  }

  function clearFluid(state) {
    if (
      state.canvas.__foundationInkfireFluid &&
      typeof state.canvas.__foundationInkfireFluid.clear === 'function'
    ) {
      state.canvas.__foundationInkfireFluid.clear();
    }
  }

  function clearHoverSettleTimers(state) {
    window.clearTimeout(state.idleClearTimeoutId);
    window.clearTimeout(state.idlePauseTimeoutId);
  }

  function scheduleHoverSettle(state) {
    if (currentMode !== 'hover') return;

    clearHoverSettleTimers(state);

    state.idleClearTimeoutId = window.setTimeout(() => {
      if (currentMode !== 'hover') return;
      endInteraction(state);
      setFluidDissipation(
        state,
        state.idleDensityDissipation,
        state.idleVelocityDissipation
      );
    }, 140);

    state.idlePauseTimeoutId = window.setTimeout(() => {
      if (currentMode !== 'hover') return;
      endInteraction(state);
      state.section.classList.remove('foundation-inkfire-splash--active');
      clearFluid(state);
      restoreFluidDissipation(state);
      setPaused(state, true);
    }, 1100);
  }

  function isHoverSuspended(state) {
    return state.scrollSuspendUntil > performance.now();
  }

  function suspendHoverForScroll(state, duration) {
    if (currentMode !== 'hover') return;

    state.scrollSuspendUntil = performance.now() + duration;
    clearHoverSettleTimers(state);
    window.cancelAnimationFrame(state.hoverFrameId);
    state.hoverFrameId = null;
    state.hoverPoint = null;
    endInteraction(state);
    restoreFluidDissipation(state);
    state.section.classList.remove('foundation-inkfire-splash--active');
    setPaused(state, true);
  }

  function triggerHoverBurst(state, hoverEvent) {
    if (
      !state.canvas.__foundationInkfireFluid ||
      typeof state.canvas.__foundationInkfireFluid.triggerSplat !== 'function'
    ) {
      return;
    }

    const burstColor = pickAmbientColor(state, state.colorCycle + 0.15);
    const anchor = hoverEvent ? getPointFromEventForSection(state.section, hoverEvent) : null;
    const centerX = anchor ? anchor.x : 0.5;
    const centerY = anchor ? anchor.y : 0.5;

    if (anchor) {
      state.lastHoverX = centerX;
      state.lastHoverY = centerY;
      state.pointerX = centerX;
      state.pointerY = centerY;
      state.targetX = centerX;
      state.targetY = centerY;
      state.velocityX = 0;
      state.velocityY = 0;
    }

    [
      { x: centerX - 0.02, y: centerY + 0.02, dx: 0, dy: -260, radius: 0.24 },
      { x: centerX + 0.03, y: centerY - 0.02, dx: 150, dy: 80, radius: 0.18 },
      { x: centerX - 0.05, y: centerY - 0.04, dx: -150, dy: 90, radius: 0.18 }
    ].forEach((burst) => {
      state.canvas.__foundationInkfireFluid.triggerSplat(
        burst.x,
        burst.y,
        burst.dx,
        burst.dy,
        burstColor.r,
        burstColor.g,
        burstColor.b,
        burst.radius
      );
    });
  }

  function getPointFromEventForSection(section, event) {
    const rect = section.getBoundingClientRect();
    if (!rect.width || !rect.height) {
      return null;
    }

    const normalizedX = clamp((event.clientX - rect.left) / rect.width, 0.02, 0.98);
    const normalizedY = clamp((event.clientY - rect.top) / rect.height, 0.02, 0.98);

    return {
      x: normalizedX,
      y: 1 - normalizedY
    };
  }

  function queueHoverSplat(state, event) {
    if (
      !state.canvas.__foundationInkfireFluid ||
      typeof state.canvas.__foundationInkfireFluid.triggerSplat !== 'function'
    ) {
      return;
    }

    const point = getPointFromEventForSection(state.section, event);
    if (!point) {
      return;
    }

    state.hoverPoint = point;

    if (state.hoverFrameId) {
      return;
    }

    state.hoverFrameId = window.requestAnimationFrame(() => {
      state.hoverFrameId = null;

      const activePoint = state.hoverPoint;
      state.hoverPoint = null;
      if (!activePoint) return;

      const deltaX = activePoint.x - state.lastHoverX;
      const deltaY = activePoint.y - state.lastHoverY;
      const distance = Math.sqrt((deltaX * deltaX) + (deltaY * deltaY));
      const forceX = clamp(deltaX * 1200, -160, 160);
      const forceY = clamp(deltaY * 1200, -160, 160);
      const radius = clamp(0.14 + distance * 1.4, 0.12, 0.24);
      const fluidColor = pickAmbientColor(state, state.colorCycle + 0.09);

      state.lastHoverX = activePoint.x;
      state.lastHoverY = activePoint.y;
      state.pointerX = activePoint.x;
      state.pointerY = activePoint.y;
      state.targetX = activePoint.x;
      state.targetY = activePoint.y;
      state.velocityX = deltaX;
      state.velocityY = deltaY;
      state.colorCycle += 0.005;

      state.canvas.__foundationInkfireFluid.triggerSplat(
        activePoint.x,
        activePoint.y,
        forceX || 8,
        forceY || -8,
        fluidColor.r,
        fluidColor.g,
        fluidColor.b,
        radius
      );
    });
  }

  function seedAutoplay(state) {
    if (
      !state.canvas.__foundationInkfireFluid ||
      typeof state.canvas.__foundationInkfireFluid.triggerSplat !== 'function'
    ) {
      return;
    }

    [
      { x: 0.47, y: 0.55, dx: 0, dy: -160, radius: 0.16, offset: 0.00 },
      { x: 0.52, y: 0.49, dx: 120, dy: 30, radius: 0.13, offset: 0.18 },
      { x: 0.43, y: 0.52, dx: -110, dy: 45, radius: 0.13, offset: 0.34 },
      { x: 0.56, y: 0.58, dx: 90, dy: -35, radius: 0.12, offset: 0.52 }
    ].forEach((seed) => {
      const color = pickAmbientColor(state, state.colorCycle + seed.offset);
      state.canvas.__foundationInkfireFluid.triggerSplat(
        seed.x,
        seed.y,
        seed.dx,
        seed.dy,
        color.r,
        color.g,
        color.b,
        seed.radius
      );
    });
  }

  function runAutoplayLoop(state) {
    if (currentMode !== 'auto' || document.hidden || !state.isVisible) return;

    if (
      state.canvas.__foundationInkfireFluid &&
      typeof state.canvas.__foundationInkfireFluid.triggerSplat === 'function'
    ) {
      const distanceToTarget = Math.sqrt(
        (state.targetX - state.pointerX) ** 2 + (state.targetY - state.pointerY) ** 2
      );

      if (distanceToTarget < 0.15) {
        state.targetX = 0.28 + Math.random() * 0.44;
        state.targetY = 0.24 + Math.random() * 0.48;
      }

      state.velocityX += (state.targetX - state.pointerX) * state.autoAcceleration;
      state.velocityY += (state.targetY - state.pointerY) * state.autoAcceleration;
      state.velocityX *= state.autoFriction;
      state.velocityY *= state.autoFriction;
      state.pointerX += state.velocityX;
      state.pointerY += state.velocityY;

      const forceX = state.velocityX * state.autoForceMultiplier;
      const forceY = state.velocityY * state.autoForceMultiplier;

      state.colorCycle += state.autoColorCycleStep;
      const fluidColor = pickAmbientColor(state, state.colorCycle);

      if (Math.abs(forceX) + Math.abs(forceY) > state.autoMinForceThreshold) {
        state.canvas.__foundationInkfireFluid.triggerSplat(
          state.pointerX,
          state.pointerY,
          forceX,
          forceY,
          fluidColor.r,
          fluidColor.g,
          fluidColor.b,
          state.autoRadius
        );
      }
    }

    state.animationFrameId = requestAnimationFrame(() => runAutoplayLoop(state));
  }

  function startAutoplayLoop(state) {
    cancelAnimationFrame(state.animationFrameId);
    if (!state.isVisible) return;
    state.pointerX = 0.5;
    state.pointerY = 0.5;
    state.velocityX = state.autoStartVelocityX;
    state.velocityY = state.autoStartVelocityY;
    seedAutoplay(state);
    triggerHoverBurst(state);
    runAutoplayLoop(state);
  }

  function stopAutoplayLoop(state) {
    cancelAnimationFrame(state.animationFrameId);
  }

  function isSectionInViewport(section) {
    const rect = section.getBoundingClientRect();
    const viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;
    return rect.bottom > 0 && rect.top < viewportHeight * 0.92;
  }

  function primeVisibleSection(state) {
    if (!state.isVisible) return;

    state.section.classList.add('foundation-inkfire-splash--active');
    setPaused(state, currentMode === 'auto' ? false : true);

    window.clearTimeout(state.bootTimeoutId);

    if (currentMode === 'auto') {
      startAutoplayLoop(state);
      return;
    }

    state.bootTimeoutId = window.setTimeout(() => {
      if (currentMode === 'hover' && !state.section.matches(':hover')) {
        state.section.classList.remove('foundation-inkfire-splash--active');
        setPaused(state, true);
      }
    }, 1600);
  }

  function updateMode(mode) {
    currentMode = mode;

    try {
      localStorage.setItem(AUTOPLAY_STORAGE_KEY, mode);
    } catch (error) {}

    updateAutoplayToggleUi(mode);

    sectionStates.forEach((state) => {
      clearHoverSettleTimers(state);

      if (mode === 'auto') {
        state.section.classList.add('foundation-inkfire-splash--active');
        restoreFluidDissipation(state);
        if (state.isVisible) {
          setPaused(state, false);
          startAutoplayLoop(state);
        }
      } else {
        state.section.classList.remove('foundation-inkfire-splash--active');
        stopAutoplayLoop(state);
        restoreFluidDissipation(state);
        if (!state.section.matches(':hover')) {
          setPaused(state, true);
        }
      }
    });
  }

  function getAutoplayToggleButtons() {
    return Array.from(
      document.querySelectorAll('[data-foundation-inkfire-autoplay-toggle], #foundation-inkfire-autoplay-toggle')
    );
  }

  function updateAutoplayToggleUi(mode) {
    const isAutoMode = mode === 'auto';
    getAutoplayToggleButtons().forEach((button) => {
      button.setAttribute('aria-pressed', String(isAutoMode));
      button.classList.toggle('is-on', isAutoMode);

      const label = button.querySelector('[data-foundation-inkfire-toggle-label]');
      if (label) label.textContent = isAutoMode ? 'AUTO' : 'HOVER';
    });
  }

  function toggleMode() {
    updateMode(currentMode === 'auto' ? 'hover' : 'auto');
  }

  document.addEventListener('click', (event) => {
    const toggleButton = event.target.closest(
      '[data-foundation-inkfire-autoplay-toggle], #foundation-inkfire-autoplay-toggle'
    );

    if (!toggleButton) return;

    event.preventDefault();
    toggleMode();
  });

  document.addEventListener('foundation-inkfire-toggle-autoplay', () => {
    toggleMode();
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
      sectionStates.forEach((state) => {
        clearHoverSettleTimers(state);
        stopAutoplayLoop(state);
        setPaused(state, true);
      });
      return;
    }

    if (currentMode === 'auto') {
      sectionStates.forEach((state) => {
        if (state.isVisible) {
          restoreFluidDissipation(state);
          setPaused(state, false);
          startAutoplayLoop(state);
        }
      });
    } else {
      sectionStates.forEach((state) => {
        if (state.isVisible && state.section.matches(':hover')) {
          restoreFluidDissipation(state);
          setPaused(state, false);
          scheduleHoverSettle(state);
        }
      });
    }
  });

  window.addEventListener(
    'wheel',
    () => {
      sectionStates.forEach((state) => suspendHoverForScroll(state, 260));
    },
    { passive: true }
  );

  window.addEventListener(
    'scroll',
    () => {
      sectionStates.forEach((state) => suspendHoverForScroll(state, 220));
    },
    { passive: true }
  );

  function bindElementorHooks() {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
      return;
    }

    const hookHandler = ($scope) => {
      registerSections($scope && $scope[0] ? $scope[0] : $scope);
    };

    window.elementorFrontend.hooks.addAction(
      'frontend/element_ready/foundation-dark-animated-hero.default',
      hookHandler
    );
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', hookHandler);
  }

  if (window.elementorFrontend && window.elementorFrontend.hooks) {
    bindElementorHooks();
  } else {
    window.addEventListener('elementor/frontend/init', bindElementorHooks, { once: true });
  }

  if ('MutationObserver' in window && document.documentElement) {
    const sectionObserver = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (!(node instanceof Element)) {
            return;
          }

          registerSections(node);
        });
      });
    });

    sectionObserver.observe(document.documentElement, {
      childList: true,
      subtree: true
    });
  }

  updateMode(currentMode);
  sectionStates.forEach((state) => {
    if (isSectionInViewport(state.section)) {
      state.isVisible = true;
      primeVisibleSection(state);
    }
  });
  window.foundationInkfireHeroControls = Object.assign({}, window.foundationInkfireHeroControls, {
    getMode: function () {
      return currentMode;
    },
    setMode: function (mode) {
      if (mode === 'hover' || mode === 'auto') {
        updateMode(mode);
      }
    },
    toggleMode: toggleMode,
    updateToggleUi: updateAutoplayToggleUi
  });
})();
