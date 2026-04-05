<?php

namespace FoundationElementorPlus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Hero_Autoplay_Toggle {
	public function hooks() {
		add_shortcode( 'ink_hero_autoplay_toggle', array( $this, 'render_shortcode' ) );
	}

	public function render_shortcode( $atts = array() ) {
		unset( $atts );

		// Deprecated compatibility shim:
		// autoplay toggle markup now renders from the shared dark hero renderer.
		return '';
	}
}
