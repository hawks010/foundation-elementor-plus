(function () {
	const walls = document.querySelectorAll('[data-foundation-awards-hover]');
	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	if (!walls.length || reduceMotion || !hoverCapable) {
		return;
	}

	walls.forEach((card) => {
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
	});
})();
