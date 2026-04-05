<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hero_Autoplay_Toggle {
	/**
	 * Prevent duplicate inline styles when the shortcode is used multiple times.
	 *
	 * @var bool
	 */
	private static $styles_printed = false;

	public function hooks() {
		add_shortcode( 'ink_hero_autoplay_toggle', array( $this, 'render_shortcode' ) );
	}

	public function render_shortcode( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'label' => __( 'Animation', 'foundation-elementor-plus' ),
				'mode'  => __( 'AUTO', 'foundation-elementor-plus' ),
			),
			$atts,
			'ink_hero_autoplay_toggle'
		);

		ob_start();

		if ( ! self::$styles_printed ) {
			self::$styles_printed = true;
			?>
			<style>
				.foundation-inkfire-autoplay-toggle {
					position: fixed;
					left: 50%;
					transform: translateX(-50%);
					bottom: max(24px, env(safe-area-inset-bottom, 0px) + 24px);
					z-index: 1005;
					display: inline-flex;
					align-items: center;
					gap: 8px;
					min-height: 44px;
					padding: 8px 14px;
					max-width: calc(100vw - 32px);
					border: 1px solid rgba(255,255,255,0.18);
					border-radius: 999px;
					background: rgba(21, 22, 34, 0.72);
					box-shadow: 0 20px 40px rgba(0,0,0,0.22), inset 0 1px 0 rgba(255,255,255,0.08);
					backdrop-filter: blur(14px);
					-webkit-backdrop-filter: blur(14px);
					color: #fff5f1;
					font: 600 0.76rem/1 "Montserrat", system-ui, sans-serif;
					letter-spacing: 0.05em;
					text-transform: uppercase;
					cursor: pointer;
					transition: transform 180ms ease, background-color 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
				}

				.foundation-inkfire-autoplay-toggle:hover {
					transform: translateX(-50%) translateY(-1px);
					background: rgba(21, 22, 34, 0.82);
					border-color: rgba(255,255,255,0.28);
				}

				.foundation-inkfire-autoplay-toggle:focus-visible {
					outline: 2px solid #FBCCBF;
					outline-offset: 4px;
				}

				.foundation-inkfire-autoplay-toggle__dot {
					width: 8px;
					height: 8px;
					border-radius: 999px;
					background: #32B190;
					box-shadow: 0 0 0 5px rgba(50,177,144,0.18);
					flex: 0 0 auto;
					transition: background-color 180ms ease, box-shadow 180ms ease;
				}

				.foundation-inkfire-autoplay-toggle__prefix {
					opacity: 0.78;
				}

				.foundation-inkfire-autoplay-toggle__label {
					font-weight: 700;
				}

				.foundation-inkfire-autoplay-toggle:not(.is-on) .foundation-inkfire-autoplay-toggle__dot {
					background: #F18E5C;
					box-shadow: 0 0 0 5px rgba(241,142,92,0.18);
				}

				@media (max-width: 768px) {
					.foundation-inkfire-autoplay-toggle {
						bottom: max(16px, env(safe-area-inset-bottom, 0px) + 16px);
						padding: 8px 12px;
						font-size: 0.72rem;
						letter-spacing: 0.035em;
						max-width: calc(100vw - 24px);
					}
				}

				@media (prefers-reduced-motion: reduce) {
					.foundation-inkfire-autoplay-toggle {
						transition: none;
					}
				}
			</style>
			<?php
		}
		?>
		<button
			id="foundation-inkfire-autoplay-toggle"
			class="foundation-inkfire-autoplay-toggle is-on"
			type="button"
			data-foundation-inkfire-autoplay-toggle
			aria-pressed="true"
			aria-label="<?php echo esc_attr__( 'Toggle hero animation autoplay mode', 'foundation-elementor-plus' ); ?>"
		>
			<span class="foundation-inkfire-autoplay-toggle__dot" aria-hidden="true"></span>
			<span class="foundation-inkfire-autoplay-toggle__prefix"><?php echo esc_html( $atts['label'] ); ?></span>
			<span class="foundation-inkfire-autoplay-toggle__label" data-foundation-inkfire-toggle-label><?php echo esc_html( $atts['mode'] ); ?></span>
		</button>
		<?php

		return trim( ob_get_clean() );
	}
}
