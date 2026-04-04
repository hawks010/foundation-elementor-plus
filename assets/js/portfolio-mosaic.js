(function() {
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
	const hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)');
	const READY_ATTR = 'data-foundation-portfolio-mosaic-ready';

	const getMetaStackThreshold = (widget) => {
		const styles = window.getComputedStyle(widget);
		const rawValue = styles.getPropertyValue('--foundation-portfolio-meta-stack-threshold').trim();
		const parsed = Number.parseFloat(rawValue);

		return Number.isFinite(parsed) ? parsed : 360;
	};

	const getRenderedColumnCount = (widget) => {
		const grid = widget.querySelector('.foundation-portfolio-mosaic__grid');

		if (!grid) {
			return 1;
		}

		const columns = window.getComputedStyle(grid).gridTemplateColumns;

		if (!columns || columns === 'none') {
			return 1;
		}

		return columns.split(' ').filter(Boolean).length || 1;
	};

	const updateFeatureState = (widget) => {
		if (widget.classList.contains('foundation-portfolio-mosaic--layout-editorial')) {
			widget.classList.remove('foundation-portfolio-mosaic--feature-active');
			return;
		}

		const minColumns = Number.parseInt(widget.dataset.foundationPortfolioFeatureMinColumns || '4', 10);
		const isCompact = widget.classList.contains('foundation-portfolio-mosaic--compact');
		const columnCount = getRenderedColumnCount(widget);
		const shouldFeatureSpan = !isCompact && columnCount >= minColumns;

		widget.classList.toggle('foundation-portfolio-mosaic--feature-active', shouldFeatureSpan);
	};

	const updateMetaStacking = (widget) => {
		const threshold = getMetaStackThreshold(widget);

		widget.querySelectorAll('[data-foundation-portfolio-mosaic-card]').forEach((card) => {
			card.classList.toggle('foundation-portfolio-mosaic__card-shell--stacked-meta', card.clientWidth <= threshold);
		});
	};

	const getLoadMoreConfig = (widget) => {
		const initialLimit = Number.parseInt(widget.dataset.foundationPortfolioInitialLimit || '0', 10) || 0;
		const enabledByData = widget.dataset.foundationPortfolioLoadMore === 'yes';

		return {
			enabled: enabledByData || initialLimit > 0,
			initialLimit,
			step: Number.parseInt(widget.dataset.foundationPortfolioLoadMoreStep || '0', 10) || 0,
			label: widget.dataset.foundationPortfolioLoadMoreLabel || 'Load More Work',
		};
	};

	const ensureLoadMoreButton = (widget) => {
		const config = getLoadMoreConfig(widget);

		if (!config.enabled) {
			return;
		}

		let wrap = widget.querySelector('[data-foundation-portfolio-load-more-wrap]');
		let button = widget.querySelector('[data-foundation-portfolio-load-more-button]');

		if (!wrap) {
			wrap = document.createElement('div');
			wrap.className = 'foundation-portfolio-mosaic__load-more-wrap';
			wrap.setAttribute('data-foundation-portfolio-load-more-wrap', '');
			wrap.hidden = true;
			widget.querySelector('.foundation-portfolio-mosaic__wrap')?.appendChild(wrap);
		}

		if (!button) {
			button = document.createElement('button');
			button.className = 'foundation-portfolio-mosaic__load-more';
			button.type = 'button';
			button.setAttribute('data-foundation-portfolio-load-more-button', '');
			button.hidden = true;
			button.textContent = config.label;
			wrap.appendChild(button);
		}
	};

	const getActiveLimit = (widget) => {
		const config = getLoadMoreConfig(widget);

		if (!config.enabled) {
			return 0;
		}

		const currentLimit = Number.parseInt(widget.dataset.foundationPortfolioCurrentLimit || '', 10);
		return Number.isFinite(currentLimit) && currentLimit > 0 ? currentLimit : config.initialLimit;
	};

	const syncLoadMoreButton = (widget, visibleDynamicCount, totalDynamicCount) => {
		const wrap = widget.querySelector('[data-foundation-portfolio-load-more-wrap]');
		const button = widget.querySelector('[data-foundation-portfolio-load-more-button]');
		const config = getLoadMoreConfig(widget);

		if (!wrap || !button || !config.enabled) {
			return;
		}

		const hasMore = visibleDynamicCount < totalDynamicCount;
		wrap.hidden = !hasMore;
		button.hidden = !hasMore;
		button.setAttribute('aria-hidden', hasMore ? 'false' : 'true');
	};

	const applyFilter = (widget, filterSlug, resetLimit = false) => {
		const activeFilter = filterSlug || widget.dataset.foundationPortfolioActiveFilter || 'all';
		const cards = widget.querySelectorAll('[data-foundation-portfolio-mosaic-card]');
		const config = getLoadMoreConfig(widget);
		let visibleCount = 0;
		let matchedDynamicCount = 0;
		let revealedDynamicCount = 0;

		if (resetLimit && config.enabled) {
			widget.dataset.foundationPortfolioCurrentLimit = String(config.initialLimit);
		}

		const activeLimit = getActiveLimit(widget);
		widget.dataset.foundationPortfolioActiveFilter = activeFilter;

		cards.forEach((card) => {
			const isStatic = card.dataset.foundationPortfolioStaticCard === 'yes';
			const filters = (card.dataset.foundationPortfolioFilters || '').split(' ').filter(Boolean);
			const matchesFilter = activeFilter === 'all' || isStatic || filters.includes(activeFilter);
			let matches = matchesFilter;

			if (matchesFilter && !isStatic) {
				matchedDynamicCount += 1;

				if (config.enabled && activeLimit > 0 && revealedDynamicCount >= activeLimit) {
					matches = false;
				} else {
					revealedDynamicCount += 1;
				}
			}

			card.hidden = !matches;
			card.setAttribute('aria-hidden', matches ? 'false' : 'true');

			if (matches && !isStatic) {
				visibleCount += 1;
			}
		});

		syncLoadMoreButton(widget, visibleCount, matchedDynamicCount);
		widget.classList.toggle('foundation-portfolio-mosaic--filtered-view', activeFilter !== 'all' && visibleCount > 0);
		updateFeatureState(widget);
		updateMetaStacking(widget);
	};

	const bindFilters = (widget) => {
		const filterButtons = widget.querySelectorAll('[data-foundation-portfolio-filter]');

		if (!filterButtons.length) {
			return;
		}

		filterButtons.forEach((button) => {
			button.addEventListener('click', () => {
				const filterSlug = button.dataset.foundationPortfolioFilter || 'all';

					filterButtons.forEach((candidate) => {
						const isActive = candidate === button;
						candidate.classList.toggle('is-active', isActive);
						candidate.setAttribute('aria-selected', isActive ? 'true' : 'false');
					});

					applyFilter(widget, filterSlug, true);
				});
			});
		};

	const bindLoadMore = (widget) => {
		const button = widget.querySelector('[data-foundation-portfolio-load-more-button]');
		const config = getLoadMoreConfig(widget);

		if (!button || !config.enabled) {
			return;
		}

		button.addEventListener('click', () => {
			const currentLimit = getActiveLimit(widget);
			const nextLimit = currentLimit + Math.max(config.step, 1);
			widget.dataset.foundationPortfolioCurrentLimit = String(nextLimit);
			applyFilter(widget, widget.dataset.foundationPortfolioActiveFilter || 'all');
		});
	};

	const bindTilt = (card) => {
		card.addEventListener('mousemove', (event) => {
			const rect = card.getBoundingClientRect();
			const x = ((event.clientX - rect.left) / rect.width) * 100;
			const y = ((event.clientY - rect.top) / rect.height) * 100;
			const rotateY = ((x - 50) / 50) * 3;
			const rotateX = ((50 - y) / 50) * 3;

			card.style.setProperty('--foundation-portfolio-pointer-x', `${x}%`);
			card.style.setProperty('--foundation-portfolio-pointer-y', `${y}%`);
			card.style.setProperty('--foundation-portfolio-rotate-x', `${rotateX}deg`);
			card.style.setProperty('--foundation-portfolio-rotate-y', `${rotateY}deg`);
			card.classList.add('is-hovering');
		});

		card.addEventListener('mouseleave', () => {
			card.style.removeProperty('--foundation-portfolio-rotate-x');
			card.style.removeProperty('--foundation-portfolio-rotate-y');
			card.classList.remove('is-hovering');
		});

		card.addEventListener('blur', () => {
			card.style.removeProperty('--foundation-portfolio-rotate-x');
			card.style.removeProperty('--foundation-portfolio-rotate-y');
			card.classList.remove('is-hovering');
		}, true);
	};

	const initWidget = (widget) => {
		if (!widget || widget.getAttribute(READY_ATTR) === 'true') {
			return;
		}

		widget.setAttribute(READY_ATTR, 'true');
		ensureLoadMoreButton(widget);
		bindLoadMore(widget);
		bindFilters(widget);
		applyFilter(widget, 'all', true);
		updateFeatureState(widget);
		updateMetaStacking(widget);

		if (
			prefersReducedMotion.matches ||
			!hoverCapable.matches ||
			widget.dataset.foundationPortfolioTilt !== 'yes'
		) {
			return;
		}

		widget.querySelectorAll('[data-foundation-portfolio-mosaic-card]').forEach(bindTilt);

		if ('ResizeObserver' in window) {
			const observer = new ResizeObserver((entries) => {
				entries.forEach((entry) => {
					updateFeatureState(entry.target);
					updateMetaStacking(entry.target);
				});
			});

			observer.observe(widget);
			return;
		}

		window.addEventListener('resize', () => {
			updateFeatureState(widget);
			updateMetaStacking(widget);
		});
	};

	const initAll = (scope) => {
		const root = scope || document;
		const widgets = [];

		if (root.matches && root.matches('[data-foundation-portfolio-mosaic]')) {
			widgets.push(root);
		}

		if (root.querySelectorAll) {
			root.querySelectorAll('[data-foundation-portfolio-mosaic]').forEach((widget) => {
				widgets.push(widget);
			});
		}

		widgets.forEach(initWidget);
	};

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

	function bindElementorHooks() {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-portfolio-mosaic.default', function ($scope) {
			initAll($scope[0] || $scope);
		});
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		bindElementorHooks();
	} else {
		window.addEventListener('elementor/frontend/init', bindElementorHooks, { once: true });
	}
})();
