<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use FoundationElementorPlus\Widgets\Base_Widget;
use FoundationElementorPlus\Dark_Animated_Hero_Renderer;
use FoundationElementorPlus\Team_Inline_Images;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dark_Animated_Hero_Widget extends Base_Widget {
	public function get_name() {
		return 'foundation-dark-animated-hero';
	}

	public function get_title() {
		return esc_html__( 'Dark Animated Hero', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-slider-album';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'dark', 'animated', 'hero', 'fluid', 'splash' );
	}

	public function get_style_depends(): array {
		return $this->get_foundation_style_depends();
	}

	public function get_script_depends(): array {
		return array();
	}

	protected function register_controls() {
		$preset_options = Dark_Animated_Hero_Renderer::get_preset_options();

		$this->start_controls_section(
			'section_preset',
			array(
				'label' => esc_html__( 'Preset & Inheritance', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'preset',
			array(
				'label'       => esc_html__( 'Preset', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'home',
				'options'     => $preset_options,
				'label_block' => true,
			)
		);

		$this->add_control(
			'preset_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Leave override fields blank to inherit from the selected preset.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Copy Overrides', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'override_intro_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Only fill these fields when you want this widget instance to override the selected preset. Leaving a field empty keeps the preset value.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'HTML allowed. Leave blank to use the preset eyebrow.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'eyebrow_style',
			array(
				'label'   => esc_html__( 'Eyebrow Style', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'green_glass',
				'options' => array(
					'green_glass'  => esc_html__( 'Green Glass', 'foundation-elementor-plus' ),
					'orange_glass' => esc_html__( 'Orange Glass', 'foundation-elementor-plus' ),
					'white_glass'  => esc_html__( 'White Glass', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'h1_html',
			array(
				'label'       => esc_html__( 'Headline Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'HTML allowed. Leave blank to use the preset headline.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'kicker_html',
			array(
				'label'       => esc_html__( 'Kicker Line Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Optional line between the eyebrow and headline. HTML allowed.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'subhead',
			array(
				'label'       => esc_html__( 'Subhead Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'HTML allowed. Leave blank to use the preset subhead.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'features',
			array(
				'label'       => esc_html__( 'Feature Pills Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 4,
				'label_block' => true,
				'description' => esc_html__( 'One feature per line. Leave blank to use the preset features.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'content_display_heading',
			array(
				'label'     => esc_html__( 'Display Overrides', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'feature_position',
			array(
				'label'       => esc_html__( 'Feature Pill Position', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''              => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'after_title'   => esc_html__( 'After title', 'foundation-elementor-plus' ),
					'after_subhead' => esc_html__( 'After subhead', 'foundation-elementor-plus' ),
					'before_buttons'=> esc_html__( 'Before buttons', 'foundation-elementor-plus' ),
					'hidden'        => esc_html__( 'Hidden', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'show_team',
			array(
				'label'   => esc_html__( 'Legacy Team Strip', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''  => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'1' => esc_html__( 'Show', 'foundation-elementor-plus' ),
					'0' => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				),
			)
		);

		$project_team_options = class_exists( '\FoundationElementorPlus\Team_Inline_Images' )
			? Team_Inline_Images::get_department_options()
			: array(
				'all'        => esc_html__( 'All Team', 'foundation-elementor-plus' ),
				'management' => esc_html__( 'Management', 'foundation-elementor-plus' ),
				'it'         => esc_html__( 'IT', 'foundation-elementor-plus' ),
				'web'        => esc_html__( 'Web', 'foundation-elementor-plus' ),
				'marketing'  => esc_html__( 'Marketing', 'foundation-elementor-plus' ),
				'branding'   => esc_html__( 'Branding', 'foundation-elementor-plus' ),
			);

		$this->add_control(
			'project_team_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Live portfolio pages use the team selected on each portfolio post. The group picker below is mainly for Elementor preview, so you can see the strip while editing the template.', 'foundation-elementor-plus' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'preset' => 'portfolio_post',
				),
			)
		);

		$this->add_control(
			'project_team_visibility',
			array(
				'label'     => esc_html__( 'Project Team Strip', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post',
				'options'   => array(
					'post' => esc_html__( 'Use post setting', 'foundation-elementor-plus' ),
					'show' => esc_html__( 'Show', 'foundation-elementor-plus' ),
					'hide' => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				),
				'condition' => array(
					'preset' => 'portfolio_post',
				),
			)
		);

		$this->add_control(
			'project_team_department',
			array(
				'label'     => esc_html__( 'Preview Team Group', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'post',
				'options'   => array_merge(
					array(
						'post' => esc_html__( 'Use post setting / All in preview', 'foundation-elementor-plus' ),
					),
					$project_team_options
				),
				'condition' => array(
					'preset' => 'portfolio_post',
				),
			)
		);

		$this->add_control(
			'show_trust',
			array(
				'label'   => esc_html__( 'Trust Logos', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''  => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'1' => esc_html__( 'Show', 'foundation-elementor-plus' ),
					'0' => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_trust',
			array(
				'label' => esc_html__( 'Trust Strip', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'trust_label',
			array(
				'label'       => esc_html__( 'Trust Label Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Leave blank to use the global trust label.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'trust_logos',
			array(
				'label'       => esc_html__( 'Trust Logos Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::GALLERY,
				'description' => esc_html__( 'Optional per-widget trust logos. Leave empty to use the global trust logos.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_buttons',
			array(
				'label' => esc_html__( 'CTA / Actions', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'btn_text',
			array(
				'label'       => esc_html__( 'Primary Button Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Leave blank to use the preset primary button.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'show_primary_button',
			array(
				'label'   => esc_html__( 'Primary Button Visibility', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''  => esc_html__( 'Automatic', 'foundation-elementor-plus' ),
					'1' => esc_html__( 'Show', 'foundation-elementor-plus' ),
					'0' => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'btn_url',
			array(
				'label'       => esc_html__( 'Primary Button URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Leave blank to use the preset primary URL.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'secondary_text',
			array(
				'label'       => esc_html__( 'Secondary Button Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Leave blank to use the preset secondary button.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'show_secondary_button',
			array(
				'label'   => esc_html__( 'Secondary Button Visibility', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''  => esc_html__( 'Automatic', 'foundation-elementor-plus' ),
					'1' => esc_html__( 'Show', 'foundation-elementor-plus' ),
					'0' => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'secondary_url',
			array(
				'label'       => esc_html__( 'Secondary Button URL', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'description' => esc_html__( 'Leave blank to use the preset secondary URL.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout & Sizing', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'size',
			array(
				'label'   => esc_html__( 'Hero Size', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''        => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'full'    => esc_html__( 'Full', 'foundation-elementor-plus' ),
					'compact' => esc_html__( 'Compact', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'align',
			array(
				'label'   => esc_html__( 'Content Alignment', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''       => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'center' => esc_html__( 'Center', 'foundation-elementor-plus' ),
					'left'   => esc_html__( 'Left', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'content_vertical_align',
			array(
				'label'   => esc_html__( 'Vertical Content Alignment', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => array(
					''           => esc_html__( 'Use preset', 'foundation-elementor-plus' ),
					'flex-start' => esc_html__( 'Start', 'foundation-elementor-plus' ),
					'center'     => esc_html__( 'Center', 'foundation-elementor-plus' ),
					'flex-end'   => esc_html__( 'End', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'min_height',
			array(
				'label'       => esc_html__( 'Minimum Height', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => '72svh',
				'description' => esc_html__( 'Leave blank to use the preset minimum height. This is used by the preset/custom height strategy and accepts CSS values like 72svh or 100dvh.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'height_strategy',
			array(
				'label'       => esc_html__( 'Height Strategy', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => array(
					''                => esc_html__( 'Use preset / minimum height', 'foundation-elementor-plus' ),
					'viewport'        => esc_html__( 'Fit viewport', 'foundation-elementor-plus' ),
					'viewport_offset' => esc_html__( 'Viewport minus header offset', 'foundation-elementor-plus' ),
					'content'         => esc_html__( 'Fit content only', 'foundation-elementor-plus' ),
				),
				'description' => esc_html__( 'Use viewport minus header offset when a fixed header or overlapping next section is squeezing the hero on shorter screens.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_responsive_control(
			'responsive_min_height',
			array(
				'label'      => esc_html__( 'Responsive Min Height', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'svh', 'dvh' ),
				'range'      => array(
					'px'  => array(
						'min' => 320,
						'max' => 1800,
					),
					'svh' => array(
						'min' => 30,
						'max' => 140,
					),
					'dvh' => array(
						'min' => 30,
						'max' => 140,
					),
				),
				'description' => esc_html__( 'Override the preset minimum height per device without editing raw CSS values.', 'foundation-elementor-plus' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-section-min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'viewport_header_offset',
			array(
				'label'      => esc_html__( 'Header Offset', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 320,
					),
					'rem' => array(
						'min' => 0,
						'max' => 20,
					),
					'svh' => array(
						'min' => 0,
						'max' => 40,
					),
					'dvh' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'condition'  => array(
					'height_strategy' => 'viewport_offset',
				),
				'description' => esc_html__( 'Subtract fixed headers or intentional overlaps from the available viewport height.', 'foundation-elementor-plus' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-header-offset: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_content_top_space',
			array(
				'label'      => esc_html__( 'Top Content Space', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 320,
					),
					'rem' => array(
						'min' => 0,
						'max' => 20,
					),
					'svh' => array(
						'min' => 0,
						'max' => 40,
					),
					'dvh' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'description' => esc_html__( 'Controls the space above the hero content without inflating the section height calculation.', 'foundation-elementor-plus' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-hero-padding-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_content_bottom_space',
			array(
				'label'      => esc_html__( 'Bottom Content Space', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'range'      => array(
					'px'  => array(
						'min' => 0,
						'max' => 360,
					),
					'rem' => array(
						'min' => 0,
						'max' => 24,
					),
					'svh' => array(
						'min' => 0,
						'max' => 50,
					),
					'dvh' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'description' => esc_html__( 'Reserve space below the hero content so the next section or overlap treatment does not crowd it on shorter screens.', 'foundation-elementor-plus' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-hero-padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => esc_html__( 'Inner Content Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'default'    => array(
					'unit' => '%',
					'size' => 95,
				),
				'range'      => array(
					'%' => array(
						'min' => 60,
						'max' => 100,
					),
					'px' => array(
						'min' => 720,
						'max' => 1800,
					),
				),
				'description' => esc_html__( 'Choose % for responsive layouts. Pixel widths are available for fixed-width layouts and legacy edits.', 'foundation-elementor-plus' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-hero__inner' => 'width: {{SIZE}}{{UNIT}}; max-width: none !important;',
				),
			)
		);

		$this->add_responsive_control(
			'headline_max_width',
			array(
				'label'      => esc_html__( 'Headline Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw', 'rem' ),
				'range'      => array(
					'%' => array(
						'min' => 40,
						'max' => 100,
					),
					'px' => array(
						'min' => 320,
						'max' => 1400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-headline' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'subhead_max_width',
			array(
				'label'      => esc_html__( 'Subhead Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw', 'rem' ),
				'range'      => array(
					'%' => array(
						'min' => 40,
						'max' => 100,
					),
					'px' => array(
						'min' => 280,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-subhead' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_row_gap',
			array(
				'label'      => esc_html__( 'Button Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-button-row' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'trust_top_space',
			array(
				'label'      => esc_html__( 'Trust Strip Top Space', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-trust-bar' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'palette_key',
			array(
				'label'   => esc_html__( 'Animation Palette', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => Dark_Animated_Hero_Renderer::get_palette_preset_options(),
			)
		);

		$this->add_control(
			'palette',
			array(
				'label'       => esc_html__( 'Palette Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => '#08352F,#0E6055,#138170,#07A079,#32B190',
				'description' => esc_html__( 'Optional comma-separated palette override for the fluid animation. This takes priority over the palette selector above.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'class',
			array(
				'label'       => esc_html__( 'Additional Class', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => esc_html__( 'Optional extra class for this hero instance.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_layout',
			array(
				'label' => esc_html__( 'Shell & Spacing', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'hero_padding',
			array(
				'label'      => esc_html__( 'Hero Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-hero-padding-top: {{TOP}}{{UNIT}}; --foundation-inkfire-hero-padding-right: {{RIGHT}}{{UNIT}}; --foundation-inkfire-hero-padding-bottom: {{BOTTOM}}{{UNIT}}; --foundation-inkfire-hero-padding-left: {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_padding_top',
			array(
				'label'      => esc_html__( 'Top Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-hero-padding-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_padding_bottom',
			array(
				'label'      => esc_html__( 'Bottom Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'svh', 'dvh' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-splash' => '--foundation-inkfire-hero-padding-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'eyebrow_padding',
			array(
				'label'      => esc_html__( 'Eyebrow Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-inkfire-eyebrow, {{WRAPPER}} .foundation-inkfire-eyebrow--green-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--orange-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--white-glass' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'hero_overlay_opacity',
			array(
				'label'   => esc_html__( 'Overlay Opacity', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-splash__mask' => 'opacity: calc({{SIZE}} / 100);',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_kicker_typography',
			array(
				'label' => esc_html__( 'Kicker Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'kicker_typography',
				'selector' => '{{WRAPPER}} .foundation-inkfire-kicker',
			)
		);

		$this->add_control(
			'kicker_color',
			array(
				'label'     => esc_html__( 'Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-kicker' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_headline_typography',
			array(
				'label' => esc_html__( 'Headline Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'headline_typography',
				'selector' => '{{WRAPPER}} .foundation-inkfire-headline',
			)
		);

		$this->add_control(
			'headline_color',
			array(
				'label'     => esc_html__( 'Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-headline' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_eyebrow_typography',
			array(
				'label' => esc_html__( 'Eyebrow Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-inkfire-eyebrow, {{WRAPPER}} .foundation-inkfire-eyebrow--green-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--orange-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--white-glass',
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => esc_html__( 'Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-eyebrow, {{WRAPPER}} .foundation-inkfire-eyebrow--green-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--orange-glass, {{WRAPPER}} .foundation-inkfire-eyebrow--white-glass' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_body_typography',
			array(
				'label' => esc_html__( 'Body Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subhead_typography',
				'selector' => '{{WRAPPER}} .foundation-inkfire-subhead',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'trust_typography',
				'selector' => '{{WRAPPER}} .foundation-inkfire-trust-label, {{WRAPPER}} .foundation-inkfire-logo-label',
			)
		);

		$this->add_control(
			'subhead_color',
			array(
				'label'     => esc_html__( 'Body Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-subhead' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'trust_text_color',
			array(
				'label'     => esc_html__( 'Trust Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-trust-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label' => esc_html__( 'Buttons', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'primary_button_text_color',
			array(
				'label'     => esc_html__( 'Primary Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-main-btn, {{WRAPPER}} .foundation-inkfire-main-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'primary_button_background',
			array(
				'label'     => esc_html__( 'Primary Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-main-btn' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'primary_button_hover_background',
			array(
				'label'     => esc_html__( 'Primary Hover Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-main-btn:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'secondary_button_text_color',
			array(
				'label'     => esc_html__( 'Secondary Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-secondary-btn, {{WRAPPER}} .foundation-inkfire-secondary-btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'secondary_button_background',
			array(
				'label'     => esc_html__( 'Secondary Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-secondary-btn' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'secondary_button_hover_background',
			array(
				'label'     => esc_html__( 'Secondary Hover Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .foundation-inkfire-secondary-btn:hover' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->normalize_content_width_settings( $this->get_settings_for_display() );
		echo Dark_Animated_Hero_Renderer::render( $this->map_settings_to_atts( $settings ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	private function normalize_content_width_settings( array $settings ) {
		$responsive_keys = array(
			'content_width',
			'content_width_laptop',
			'content_width_tablet_extra',
			'content_width_tablet',
			'content_width_mobile_extra',
			'content_width_mobile',
		);

		foreach ( $responsive_keys as $key ) {
			if ( empty( $settings[ $key ] ) || ! is_array( $settings[ $key ] ) ) {
				continue;
			}

			$unit = isset( $settings[ $key ]['unit'] ) ? (string) $settings[ $key ]['unit'] : '';
			$size = isset( $settings[ $key ]['size'] ) ? (float) $settings[ $key ]['size'] : 0;

			// Legacy hero instances occasionally stored percentage-like values as px.
			if ( 'px' === $unit && $size > 0 && $size <= 100 ) {
				$settings[ $key ]['unit'] = '%';
			}
		}

		return $settings;
	}

	private function map_settings_to_atts( array $settings ) {
		$atts = array(
			'preset' => ! empty( $settings['preset'] ) ? (string) $settings['preset'] : 'home',
		);

		$optional_keys = array(
			'palette_key',
			'size',
			'align',
			'content_vertical_align',
			'min_height',
			'height_strategy',
			'eyebrow',
			'eyebrow_style',
			'h1_html',
			'kicker_html',
			'subhead',
			'btn_text',
			'btn_url',
			'secondary_text',
			'secondary_url',
			'palette',
			'features',
			'feature_position',
			'class',
			'trust_label',
			'project_team_visibility',
			'project_team_department',
		);

		foreach ( $optional_keys as $key ) {
			if ( isset( $settings[ $key ] ) && '' !== trim( (string) $settings[ $key ] ) ) {
				$atts[ $key ] = (string) $settings[ $key ];
			}
		}

		foreach ( array( 'show_team', 'show_trust', 'show_primary_button', 'show_secondary_button' ) as $key ) {
			if ( isset( $settings[ $key ] ) && '' !== (string) $settings[ $key ] ) {
				$atts[ $key ] = (string) $settings[ $key ];
			}
		}

		if ( ! empty( $settings['trust_logos'] ) && is_array( $settings['trust_logos'] ) ) {
			$trust_logo_urls = array();

			foreach ( $settings['trust_logos'] as $logo ) {
				if ( ! empty( $logo['url'] ) ) {
					$trust_logo_urls[] = (string) $logo['url'];
				}
			}

			if ( ! empty( $trust_logo_urls ) ) {
				$atts['trust_logos'] = $trust_logo_urls;
			}
		}

		return $atts;
	}
}
