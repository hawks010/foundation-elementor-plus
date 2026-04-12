<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Base_Widget extends Widget_Base {
	/**
	 * Merge shared Foundation stylesheet handles with any widget-specific handles.
	 *
	 * @param array<int, string> $handles Style handles.
	 * @return array<int, string>
	 */
	protected function get_foundation_style_depends( array $handles = array() ): array {
		return array_values(
			array_unique(
				array_filter(
					array_map(
						static function( $handle ) {
							return is_string( $handle ) ? trim( $handle ) : '';
						},
						$handles
					)
				)
			)
		);
	}

	/**
	 * Build a safe HTML attribute string for the widget root element.
	 *
	 * @param array<string, mixed> $settings   Widget settings.
	 * @param array<string, mixed> $attributes HTML attributes.
	 * @return string
	 */
	protected function get_widget_root_attributes( array $settings, array $attributes = array() ): string {
		unset( $settings );

		$parts = array();

		foreach ( $attributes as $name => $value ) {
			if ( ! is_string( $name ) || '' === $name || null === $value || false === $value ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$value = implode(
					' ',
					array_filter(
						array_map(
							static function( $item ) {
								return is_scalar( $item ) ? trim( (string) $item ) : '';
							},
							$value
						)
					)
				);
			}

			if ( '' === (string) $value ) {
				continue;
			}

			if ( true === $value ) {
				$parts[] = sanitize_key( $name );
				continue;
			}

			$parts[] = sprintf(
				'%1$s="%2$s"',
				sanitize_key( $name ),
				esc_attr( (string) $value )
			);
		}

		return implode( ' ', $parts );
	}
}
