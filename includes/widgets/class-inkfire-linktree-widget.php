<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use FoundationElementorPlus\Widgets\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Inkfire_Linktree_Widget extends Base_Widget {
	public function get_name() {
		return 'foundation-inkfire-linktree';
	}

	public function get_title() {
		return esc_html__( 'Linktree', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-share';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'inkfire', 'linktree', 'quicklinks', 'social', 'newsletter' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_linktree_info',
			array(
				'label' => esc_html__( 'Content Source', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'linktree_info_note',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'This widget renders the shared Inkfire Linktree shortcode. The controls below adjust spacing and presentation safely without changing the shared quicklinks data source.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

		$this->register_layout_style_controls();
		$this->register_surface_style_controls();
		$this->register_accessibility_controls();
	}

	private function register_layout_style_controls() {
		$this->start_controls_section(
			'section_linktree_layout_style',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'linktree_shell_padding',
			array(
				'label'      => esc_html__( 'Outer Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'linktree_stage_width',
			array(
				'label'      => esc_html__( 'Content Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 320,
						'max' => 1080,
					),
					'%' => array(
						'min' => 40,
						'max' => 100,
					),
					'vw' => array(
						'min' => 35,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-stage' => 'max-width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'linktree_main_gap',
			array(
				'label'      => esc_html__( 'Main Section Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'gap: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'linktree_main_padding',
			array(
				'label'      => esc_html__( 'Main Panel Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'linktree_panel_radius',
			array(
				'label'      => esc_html__( 'Panel Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 64,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .amh-ql-main' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .amh-ql-video, {{WRAPPER}} .amh-ql-newsletter, {{WRAPPER}} .amh-ql-feed, {{WRAPPER}} .amh-ql-trust, {{WRAPPER}} .amh-ql-contact, {{WRAPPER}} .amh-ql-link, {{WRAPPER}} .amh-ql-video-frame, {{WRAPPER}} .amh-ql-post-card' => 'border-radius: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_surface_style_controls() {
		$this->start_controls_section(
			'section_linktree_surface_style',
			array(
				'label' => esc_html__( 'Surface & Text', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'linktree_text_color',
			array(
				'label'     => esc_html__( 'Primary Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-shell, {{WRAPPER}} .amh-ql-shell a, {{WRAPPER}} .amh-ql-copy h1, {{WRAPPER}} .amh-ql-section-head h2, {{WRAPPER}} .amh-ql-link-copy strong, {{WRAPPER}} .amh-ql-post-copy strong' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'linktree_muted_text_color',
			array(
				'label'     => esc_html__( 'Secondary Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-bio, {{WRAPPER}} .amh-ql-link-copy span, {{WRAPPER}} .amh-ql-post-copy span, {{WRAPPER}} .amh-ql-newsletter-copy, {{WRAPPER}} .amh-ql-newsletter-note, {{WRAPPER}} .amh-ql-company-meta span' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'linktree_accent_color',
			array(
				'label'     => esc_html__( 'Accent Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-tagline, {{WRAPPER}} .amh-ql-eyebrow, {{WRAPPER}} .amh-ql-inline-link' => 'color: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'linktree_main_background',
			array(
				'label'     => esc_html__( 'Main Panel Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-main' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'linktree_secondary_background',
			array(
				'label'     => esc_html__( 'Secondary Panel Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-video, {{WRAPPER}} .amh-ql-newsletter, {{WRAPPER}} .amh-ql-feed, {{WRAPPER}} .amh-ql-trust, {{WRAPPER}} .amh-ql-contact' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'linktree_link_background',
			array(
				'label'     => esc_html__( 'Primary Link Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .amh-ql-link' => 'background: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$widget_id = 'foundation-inkfire-linktree-' . $this->get_id();
		?>
		<div <?php echo $this->get_widget_root_attributes( $settings, array( 'id' => $widget_id, 'class' => 'foundation-inkfire-linktree-wrap' ) ); ?>>
			<?php echo do_shortcode( '[inkfire_linktree]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<?php
	}
}
