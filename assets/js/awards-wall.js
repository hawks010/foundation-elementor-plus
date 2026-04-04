(function () {
	const READY_ATTR = 'data-foundation-awards-ready';

	function initCard(card) {
		const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		const hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

		if (!card || card.getAttribute(READY_ATTR) === 'true' || reduceMotion || !hoverCapable) {
			return;
		}

		card.setAttribute(READY_ATTR, 'true');

		card.addEventListener('mousemove', (event) => {
			const rect = card.getBoundingClientRect();
			const x = ((event.clientX - rect.left) / rect.width) * 100;
			const y = ((event.clientY - rect.top) / rect.height) * 100;

			card.style.setProperty('--foundation-awards-x', x + '%');
			card.style.setProperty('--foundation-awards-y', y + '%');
			card.classList.add('is-hovering');
		});

		card.addEventListener('mouseleave', () => {
			card.classList.remove('is-hovering');
			card.style.removeProperty('--foundation-awards-x');
			card.style.removeProperty('--foundation-awards-y');
		});

		card.addEventListener(
			'blur',
			() => {
				card.classList.remove('is-hovering');
				card.style.removeProperty('--foundation-awards-x');
				card.style.removeProperty('--foundation-awards-y');
			},
			true
		);
	}

	function initAll(scope) {
		const root = scope || document;
		const cards = [];

		if (root.matches && root.matches('[data-foundation-awards-hover]')) {
			cards.push(root);
		}

		if (root.querySelectorAll) {
			root.querySelectorAll('[data-foundation-awards-hover]').forEach((card) => {
				cards.push(card);
			});
		}

		cards.forEach(initCard);
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

	function bindElementorHooks() {
		if (!window.elementorFrontend || !window.elementorFrontend.hooks) {
			return;
		}

		window.elementorFrontend.hooks.addAction('frontend/element_ready/foundation-awards-recognition-wall.default', function ($scope) {
			initAll($scope[0] || $scope);
		});
	}

	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		bindElementorHooks();
	} else {
		window.addEventListener('elementor/frontend/init', bindElementorHooks, { once: true });
	}
})();
