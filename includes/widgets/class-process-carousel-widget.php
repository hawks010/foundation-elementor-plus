<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use FoundationElementorPlus\Widgets\Base_Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Process_Carousel_Widget extends Base_Widget {
	public function get_name() {
		return 'foundation-process-carousel';
	}

	public function get_title() {
		return esc_html__( 'Process Carousel', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-slider-3d';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'process', 'carousel', 'steps', 'timeline' );
	}

	public function get_style_depends(): array {
		return $this->get_foundation_style_depends( array( 'foundation-elementor-plus-process-carousel' ) );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-process-carousel' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_header_style_controls();
		$this->register_step_style_controls();
		$this->register_card_style_controls();
		$this->register_arrow_style_controls();
		$this->register_accessibility_controls();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$widget_id = 'foundation-process-carousel-' . $this->get_id();
		$steps     = ! empty( $settings['steps'] ) && is_array( $settings['steps'] ) ? array_values( $settings['steps'] ) : array();
		$prev_icon = $this->get_icon_markup( $settings['prev_icon'] ?? array(), 'foundation-process-carousel__arrow-icon' );
		$next_icon = $this->get_icon_markup( $settings['next_icon'] ?? array(), 'foundation-process-carousel__arrow-icon' );

		if ( empty( $steps ) ) {
			return;
		}
		?>
		<section <?php echo $this->get_widget_root_attributes( $settings, array( 'id' => $widget_id, 'class' => 'foundation-process-carousel', 'data-foundation-process-carousel' => true ) ); ?>>
			<div class="foundation-process-carousel__wrap">
				<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
					<p class="foundation-process-carousel__eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $settings['title'] ) ) : ?>
					<h2 class="foundation-process-carousel__title"><?php echo wp_kses_post( nl2br( esc_html( $settings['title'] ) ) ); ?></h2>
				<?php endif; ?>

				<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
					<div class="foundation-process-carousel__subtitle">
						<?php echo wp_kses_post( wpautop( $settings['subtitle'] ) ); ?>
					</div>
				<?php endif; ?>

				<div class="foundation-process-carousel__steps" role="tablist" aria-label="<?php esc_attr_e( 'Process steps', 'foundation-elementor-plus' ); ?>">
					<?php foreach ( $steps as $index => $step ) : ?>
						<?php
						$step_label = ! empty( $step['step_label'] ) ? $step['step_label'] : sprintf( 'Step %d', $index + 1 );
						$step_tab_id = $widget_id . '-tab-' . $index;
						$step_panel_id = $widget_id . '-panel-' . $index;
						?>
						<button
							id="<?php echo esc_attr( $step_tab_id ); ?>"
							class="foundation-process-carousel__step<?php echo 0 === $index ? ' is-active' : ''; ?>"
							type="button"
							data-process-step="<?php echo esc_attr( (string) $index ); ?>"
							role="tab"
							aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $step_panel_id ); ?>"
						>
							<?php echo esc_html( $step_label ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="foundation-process-carousel__carousel">
					<button class="foundation-process-carousel__arrow foundation-process-carousel__arrow--left" type="button" data-process-prev aria-label="<?php echo esc_attr( ! empty( $settings['prev_aria_label'] ) ? $settings['prev_aria_label'] : 'Previous step' ); ?>">
						<?php echo $prev_icon ? $prev_icon : esc_html( ! empty( $settings['prev_label'] ) ? $settings['prev_label'] : '<-' ); ?>
					</button>

					<div class="foundation-process-carousel__viewport">
						<div class="foundation-process-carousel__track" data-process-track>
							<?php foreach ( $steps as $index => $step ) : ?>
								<?php
								$heading      = ! empty( $step['card_title'] ) ? $step['card_title'] : sprintf( 'Step %d', $index + 1 );
								$description  = ! empty( $step['card_description'] ) ? $step['card_description'] : '';
								$list_entries = $this->split_list_items( $step['card_list'] ?? '' );
								$step_tab_id  = $widget_id . '-tab-' . $index;
								$step_panel_id = $widget_id . '-panel-' . $index;
								?>
								<div
									id="<?php echo esc_attr( $step_panel_id ); ?>"
									class="foundation-process-carousel__card"
									data-process-card="<?php echo esc_attr( (string) $index ); ?>"
									role="tabpanel"
									aria-labelledby="<?php echo esc_attr( $step_tab_id ); ?>"
								>
									<div class="foundation-process-carousel__card-left">
										<h3><?php echo esc_html( $heading ); ?></h3>
										<div class="foundation-process-carousel__card-copy">
											<?php echo wp_kses_post( wpautop( $description ) ); ?>
										</div>
									</div>

									<?php if ( ! empty( $list_entries ) ) : ?>
										<ul class="foundation-process-carousel__list">
											<?php foreach ( $list_entries as $list_entry ) : ?>
												<li><?php echo esc_html( $list_entry ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

					<button class="foundation-process-carousel__arrow foundation-process-carousel__arrow--right" type="button" data-process-next aria-label="<?php echo esc_attr( ! empty( $settings['next_aria_label'] ) ? $settings['next_aria_label'] : 'Next step' ); ?>">
						<?php echo $next_icon ? $next_icon : esc_html( ! empty( $settings['next_label'] ) ? $settings['next_label'] : '->' ); ?>
					</button>
				</div>
			</div>
		</section>
		<?php
	}

	private function split_list_items( $value ) {
		return array_values(
			array_filter(
				array_map(
					'trim',
					preg_split( '/\r\n|\r|\n/', (string) $value )
				)
			)
		);
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_header_content',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Our process', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => "Your WordPress development journey",
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'A structured, seamless process designed for impact.', 'foundation-elementor-plus' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'prev_label',
			array(
				'label'       => esc_html__( 'Previous Arrow Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '<-',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'prev_icon',
			array(
				'label'   => esc_html__( 'Previous Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'next_label',
			array(
				'label'       => esc_html__( 'Next Arrow Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '->',
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'next_icon',
			array(
				'label'   => esc_html__( 'Next Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'prev_aria_label',
			array(
				'label'       => esc_html__( 'Previous Arrow Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Previous step', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'next_aria_label',
			array(
				'label'       => esc_html__( 'Next Arrow Aria Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Next step', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_steps',
			array(
				'label' => esc_html__( 'Steps', 'foundation-elementor-plus' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'step_label',
			array(
				'label'       => esc_html__( 'Step Button Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Step Label', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'card_title',
			array(
				'label'       => esc_html__( 'Card Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Step title', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'card_description',
			array(
				'label'   => esc_html__( 'Card Description', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'Describe this stage of the process.', 'foundation-elementor-plus' ),
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'card_list',
			array(
				'label'       => esc_html__( 'Checklist Items', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 6,
				'description' => esc_html__( 'One item per line.', 'foundation-elementor-plus' ),
				'default'     => "First checklist item\nSecond checklist item\nThird checklist item",
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'steps',
			array(
				'label'       => esc_html__( 'Process Steps', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ step_label }}}',
				'default'     => $this->get_default_steps(),
			)
		);

		$this->end_controls_section();
	}

	private function register_layout_controls() {
		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'      => esc_html__( 'Section Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'default'    => array(
					'top'      => 20,
					'right'    => 0,
					'bottom'   => 20,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => esc_html__( 'Content Max Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 1400,
				),
				'range'      => array(
					'px' => array(
						'min' => 700,
						'max' => 1800,
					),
					'%' => array(
						'min' => 60,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'steps_gap',
			array(
				'label'      => esc_html__( 'Step Pills Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__steps' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_gap',
			array(
				'label'      => esc_html__( 'Carousel Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel' => '--foundation-process-carousel-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_peek',
			array(
				'label'      => esc_html__( 'Visible Next Slide', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 180,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 360,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel' => '--foundation-process-card-peek: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'track_gap',
			array(
				'label'      => esc_html__( 'Space Between Slides', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel' => '--foundation-process-track-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ),
				'default'    => array(
					'top'      => 50,
					'right'    => 50,
					'bottom'   => 50,
					'left'     => 50,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', '%' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 36,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_header_style_controls() {
		$this->start_controls_section(
			'section_header_styles',
			array(
				'label' => esc_html__( 'Header', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DFFF00',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E7E8EF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__title',
			)
		);

		$this->add_control(
			'subtitle_color',
			array(
				'label'     => esc_html__( 'Subtitle Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#8F94A8',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__subtitle, {{WRAPPER}} .foundation-process-carousel__subtitle p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__subtitle, {{WRAPPER}} .foundation-process-carousel__subtitle p',
			)
		);

		$this->end_controls_section();
	}

	private function register_step_style_controls() {
		$this->start_controls_section(
			'section_step_styles',
			array(
				'label' => esc_html__( 'Step Pills', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'step_background',
			array(
				'label'     => esc_html__( 'Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1C2032',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__step' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'step_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__step' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'step_active_background',
			array(
				'label'     => esc_html__( 'Active Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#DFFF00',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__step.is-active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'step_active_text_color',
			array(
				'label'     => esc_html__( 'Active Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__step.is-active' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'step_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.08)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__step' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'step_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__step',
			)
		);

		$this->add_responsive_control(
			'step_backdrop_blur',
			array(
				'label'      => esc_html__( 'Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__step' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_styles',
			array(
				'label' => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2B2F42',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__card' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__card',
			)
		);

		$this->add_responsive_control(
			'card_backdrop_blur',
			array(
				'label'      => esc_html__( 'Backdrop Blur', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 0,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel__card' => '-webkit-backdrop-filter: blur({{SIZE}}{{UNIT}}); backdrop-filter: blur({{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'card_heading_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E7E8EF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__card-left h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_heading_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__card-left h3',
			)
		);

		$this->add_control(
			'card_body_color',
			array(
				'label'     => esc_html__( 'Body Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#9AA0B3',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__card-copy, {{WRAPPER}} .foundation-process-carousel__card-copy p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_body_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__card-copy, {{WRAPPER}} .foundation-process-carousel__card-copy p',
			)
		);

		$this->add_control(
			'list_color',
			array(
				'label'     => esc_html__( 'List Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#CFD2E3',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__list li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'list_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__list li',
			)
		);

		$this->end_controls_section();
	}

	private function register_arrow_style_controls() {
		$this->start_controls_section(
			'section_arrow_styles',
			array(
				'label' => esc_html__( 'Arrows', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#1C2032',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__arrow' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255,255,255,0.1)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-process-carousel__arrow' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_icon_size',
			array(
				'label'      => esc_html__( 'Arrow Icon Size', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem', 'em', 'vw' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 20,
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-process-carousel' => '--foundation-process-arrow-icon-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'arrow_typography',
				'selector' => '{{WRAPPER}} .foundation-process-carousel__arrow',
			)
		);

		$this->end_controls_section();
	}

	private function get_default_steps() {
		return array(
			array(
				'step_label'       => esc_html__( 'Discovery & Design', 'foundation-elementor-plus' ),
				'card_title'       => esc_html__( 'Discovery, Scoping & Design', 'foundation-elementor-plus' ),
				'card_description' => esc_html__( 'We kick things off with strategy and style. Through a digital questionnaire, discovery workshop and visual moodboarding we align creative direction with your goals.', 'foundation-elementor-plus' ),
				'card_list'        => "Digital questionnaire and kick-off workshop\nUX strategy and moodboards agreed\nHomepage concept designed in Figma\nTwo rounds of revisions before rollout\nTemplate designs reviewed in Figma",
			),
			array(
				'step_label'       => esc_html__( 'Develop & Test', 'foundation-elementor-plus' ),
				'card_title'       => esc_html__( 'Develop, Engineer & Test', 'foundation-elementor-plus' ),
				'card_description' => esc_html__( 'Your site is engineered with performance and scalability in mind using modern WordPress architecture.', 'foundation-elementor-plus' ),
				'card_list'        => "Custom WordPress theme development\nAccessibility & performance optimisation\nResponsive UI implementation\nTesting across devices and browsers",
			),
			array(
				'step_label'       => esc_html__( 'Live Demo', 'foundation-elementor-plus' ),
				'card_title'       => esc_html__( 'Live Demo & Client Review', 'foundation-elementor-plus' ),
				'card_description' => esc_html__( 'You receive a live preview environment to test your site before launch.', 'foundation-elementor-plus' ),
				'card_list'        => "Staging preview site\nClient walkthrough session\nFinal tweaks and QA checks",
			),
			array(
				'step_label'       => esc_html__( 'Migration & Checks', 'foundation-elementor-plus' ),
				'card_title'       => esc_html__( 'Migration & Technical Checks', 'foundation-elementor-plus' ),
				'card_description' => esc_html__( 'We migrate your new site safely with full infrastructure and performance checks.', 'foundation-elementor-plus' ),
				'card_list'        => "DNS and hosting configuration\nContent migration\nSecurity & performance testing",
			),
			array(
				'step_label'       => esc_html__( 'Launch & Support', 'foundation-elementor-plus' ),
				'card_title'       => esc_html__( 'Launch & Ongoing Support', 'foundation-elementor-plus' ),
				'card_description' => esc_html__( 'Once approved, we launch your site and support the rollout.', 'foundation-elementor-plus' ),
				'card_list'        => "Production deployment\nPerformance monitoring\nOngoing support and iteration",
			),
		);
	}

	private function get_icon_markup( $icon, $class_name ) {
		if ( empty( $icon['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon(
			$icon,
			array(
				'aria-hidden' => 'true',
				'class'       => $class_name,
			)
		);
		return (string) ob_get_clean();
	}
}
