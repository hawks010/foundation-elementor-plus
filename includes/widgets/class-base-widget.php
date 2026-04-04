<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Base_Widget extends Widget_Base {

	/**
	 * Merge the shared Foundation core stylesheet with widget styles.
	 *
	 * @param array<int, string> $handles Widget style handles.
	 * @return array<int, string>
	 */
	protected function get_foundation_style_depends( array $handles = array() ) {
		array_unshift( $handles, 'foundation-elementor-plus-core' );

		return array_values( array_unique( array_filter( $handles ) ) );
	}
	/**
	 * Register shared advanced accessibility controls.
	 *
	 * @return void
	 */
	protected function register_accessibility_controls() {
		$this->start_controls_section(
			'section_foundation_accessibility',
			array(
				'label' => esc_html__( 'Accessibility', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			)
		);

		$this->add_control(
			'widget_aria_label',
			array(
				'label'       => esc_html__( 'Screen Reader Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Optional. Adds an accessible label to the widget region when no visible heading already labels it.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'widget_disable_motion',
			array(
				'label'        => esc_html__( 'Reduce Motion In This Widget', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'description'  => esc_html__( 'Applies low-motion helper styles to this widget instance without changing its layout.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Build safe root attributes for the widget shell.
	 *
	 * @param array<string, mixed> $settings Widget settings.
	 * @param array<string, mixed> $attrs Existing attributes.
	 * @return string
	 */
	protected function get_widget_root_attributes( array $settings, array $attrs = array() ) {
		$classes = array();

		if ( ! empty( $attrs['class'] ) ) {
			$classes = is_array( $attrs['class'] ) ? $attrs['class'] : preg_split( '/\s+/', (string) $attrs['class'] );
		}

		$classes   = array_filter( array_map( 'trim', $classes ) );
		$classes[] = 'foundation-widget-shell';

		if ( 'yes' === ( $settings['widget_disable_motion'] ?? '' ) ) {
			$classes[]                      = 'foundation-widget--motion-off';
			$attrs['data-foundation-motion'] = 'off';
		}

		$attrs['class']                 = implode( ' ', array_unique( $classes ) );
		$attrs['data-foundation-widget'] = $this->get_name();

		if ( empty( $attrs['aria-label'] ) && empty( $attrs['aria-labelledby'] ) && ! empty( $settings['widget_aria_label'] ) ) {
			$attrs['aria-label'] = sanitize_text_field( (string) $settings['widget_aria_label'] );
		}

		$compiled = array();

		foreach ( $attrs as $name => $value ) {
			if ( null === $value || false === $value || '' === $value ) {
				continue;
			}

			if ( true === $value ) {
				$compiled[] = sprintf( '%1$s="%1$s"', esc_attr( $name ) );
				continue;
			}

			$compiled[] = sprintf( '%s="%s"', esc_attr( $name ), esc_attr( is_scalar( $value ) ? (string) $value : wp_json_encode( $value ) ) );
		}

		return implode( ' ', $compiled );
	}
}
