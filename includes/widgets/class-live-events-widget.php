<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Live_Events_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-live-events';
	}

	public function get_title() {
		return esc_html__( 'Foundation Live Events', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-calendar';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'events', 'calendar', 'accordion', 'glass' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-live-events' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-live-events' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_style_controls();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$widget_id      = 'foundation-live-events-' . $this->get_id();
		$show_past      = isset( $settings['show_past_events'] ) && 'yes' === $settings['show_past_events'];
		$default_open   = isset( $settings['default_open_first'] ) && 'yes' === $settings['default_open_first'];
		$upcoming_label = ! empty( $settings['upcoming_heading'] ) ? $settings['upcoming_heading'] : esc_html__( 'Upcoming Events', 'foundation-elementor-plus' );
		$past_label     = ! empty( $settings['past_heading'] ) ? $settings['past_heading'] : esc_html__( 'Past Events', 'foundation-elementor-plus' );
		$empty_state    = ! empty( $settings['empty_state'] ) ? $settings['empty_state'] : esc_html__( 'There are no upcoming events right now.', 'foundation-elementor-plus' );
		$upcoming       = $this->get_events_data( 'upcoming' );
		$past           = $show_past ? $this->get_events_data( 'past' ) : array();
		$has_any        = ! empty( $upcoming ) || ! empty( $past );
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="foundation-live-events"
			data-foundation-live-events
			data-default-open="<?php echo $default_open ? 'yes' : 'no'; ?>"
		>
			<div class="foundation-live-events__shell">
				<div class="foundation-live-events__hero">
					<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
						<p class="foundation-live-events__eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $settings['heading'] ) ) : ?>
						<h2 class="foundation-live-events__title"><?php echo wp_kses_post( nl2br( esc_html( $settings['heading'] ) ) ); ?></h2>
					<?php endif; ?>

					<?php if ( ! empty( $settings['intro'] ) ) : ?>
						<div class="foundation-live-events__intro">
							<?php echo wp_kses_post( wpautop( $settings['intro'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $upcoming ) ) : ?>
					<?php $this->render_event_group( $upcoming_label, $upcoming, $widget_id, 'upcoming' ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $past ) ) : ?>
					<?php $this->render_event_group( $past_label, $past, $widget_id, 'past' ); ?>
				<?php endif; ?>

				<?php if ( ! $has_any ) : ?>
					<div class="foundation-live-events__empty"><?php echo esc_html( $empty_state ); ?></div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	private function render_event_group( string $heading, array $events, string $widget_id, string $group_slug ) {
		?>
		<section class="foundation-live-events__group foundation-live-events__group--<?php echo esc_attr( $group_slug ); ?>">
			<h3 class="foundation-live-events__group-title"><?php echo esc_html( $heading ); ?></h3>

			<div class="foundation-live-events__list" role="list">
				<?php foreach ( $events as $index => $event ) : ?>
					<?php
					$panel_id  = $widget_id . '-' . $group_slug . '-panel-' . $index;
					$toggle_id = $widget_id . '-' . $group_slug . '-toggle-' . $index;
					?>
					<article class="foundation-live-events__item" data-live-events-item data-event-group="<?php echo esc_attr( $group_slug ); ?>" role="listitem">
						<button
							id="<?php echo esc_attr( $toggle_id ); ?>"
							type="button"
							class="foundation-live-events__toggle"
							data-live-events-toggle
							aria-expanded="false"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						>
							<?php if ( ! empty( $event['image_html'] ) ) : ?>
								<div class="foundation-live-events__media">
									<?php echo $event['image_html']; ?>
								</div>
							<?php endif; ?>

							<div class="foundation-live-events__content">
								<div class="foundation-live-events__meta">
									<?php if ( ! empty( $event['formatted_date'] ) ) : ?>
										<span class="foundation-live-events__meta-pill"><?php echo esc_html( $event['formatted_date'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $event['time_text'] ) ) : ?>
										<span class="foundation-live-events__meta-pill"><?php echo esc_html( $event['time_text'] ); ?></span>
									<?php endif; ?>
									<?php if ( ! empty( $event['location'] ) ) : ?>
										<span class="foundation-live-events__meta-pill"><?php echo esc_html( $event['location'] ); ?></span>
									<?php endif; ?>
								</div>

								<h4 class="foundation-live-events__item-title"><?php echo esc_html( $event['title'] ); ?></h4>

								<?php if ( ! empty( $event['summary'] ) ) : ?>
									<p class="foundation-live-events__summary"><?php echo esc_html( $event['summary'] ); ?></p>
								<?php endif; ?>
							</div>

							<span class="foundation-live-events__chevron" aria-hidden="true">
								<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
									<path d="M6 9.5 12 15.5 18 9.5"></path>
								</svg>
							</span>
						</button>

						<div
							id="<?php echo esc_attr( $panel_id ); ?>"
							class="foundation-live-events__panel"
							data-live-events-panel
							hidden
							aria-labelledby="<?php echo esc_attr( $toggle_id ); ?>"
						>
							<?php if ( ! empty( $event['content'] ) ) : ?>
								<div class="foundation-live-events__panel-copy">
									<?php echo $event['content']; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $event['url'] ) ) : ?>
								<div class="foundation-live-events__panel-footer">
									<a class="foundation-live-events__link" href="<?php echo esc_url( $event['url'] ); ?>" target="_blank" rel="noopener noreferrer">
										<span><?php esc_html_e( 'More info', 'foundation-elementor-plus' ); ?></span>
										<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
											<path d="M7 17 17 7"></path>
											<path d="M9 7h8v8"></path>
										</svg>
									</a>
								</div>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}

	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Where to find us next', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'       => esc_html__( 'Heading', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 3,
				'default'     => "Events we're\nattending",
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'   => esc_html__( 'Intro', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'A clean list of upcoming appearances, talks, and community events. Expand each card for the fuller details and booking information.', 'foundation-elementor-plus' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_past_events',
			array(
				'label'        => esc_html__( 'Show Past Events', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'upcoming_heading',
			array(
				'label'       => esc_html__( 'Upcoming Heading', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Upcoming Events', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'past_heading',
			array(
				'label'       => esc_html__( 'Past Heading', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Past Events', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'show_past_events' => 'yes',
				),
			)
		);

		$this->add_control(
			'default_open_first',
			array(
				'label'        => esc_html__( 'Open First Upcoming Event', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'empty_state',
			array(
				'label'       => esc_html__( 'Empty State Message', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'There are no upcoming events right now.', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
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
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-events' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'content_width',
			array(
				'label'      => esc_html__( 'Content Width', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'default'    => array(
					'unit' => '%',
					'size' => 95,
				),
				'range'      => array(
					'%'  => array( 'min' => 60, 'max' => 100 ),
					'px' => array( 'min' => 420, 'max' => 1800 ),
					'vw' => array( 'min' => 40, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-events__shell' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				),
			)
		);

		$this->add_responsive_control(
			'shell_padding',
			array(
				'label'      => esc_html__( 'Shell Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 30,
					'right'    => 30,
					'bottom'   => 30,
					'left'     => 30,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-events__shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'shell_radius',
			array(
				'label'      => esc_html__( 'Shell Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 38,
				),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 120 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-events' => '--foundation-live-events-shell-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_style_controls() {
		$this->start_controls_section(
			'section_shell_style',
			array(
				'label' => esc_html__( 'Shell', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'shell_background',
			array(
				'label'     => esc_html__( 'Shell Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 20, 33, 0.94)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-events' => '--foundation-live-events-shell-bg: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'shell_border',
				'selector' => '{{WRAPPER}} .foundation-live-events__shell',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'shell_shadow',
				'selector' => '{{WRAPPER}} .foundation-live-events__shell',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-events__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-live-events__title',
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => esc_html__( 'Intro Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-events__intro, {{WRAPPER}} .foundation-live-events__intro p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_cards_style',
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
				'default'   => 'rgba(255, 255, 255, 0.07)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-events' => '--foundation-live-events-card-bg: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .foundation-live-events__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .foundation-live-events__item',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'card_title_typography',
				'selector' => '{{WRAPPER}} .foundation-live-events__item-title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Query event posts and normalize them for the widget.
	 *
	 * @param string $bucket Either upcoming or past.
	 * @return array<int, array<string, string>>
	 */
	private function get_events_data( string $bucket ) {
		$today = current_time( 'Y-m-d' );
		$query = new \WP_Query(
			array(
				'post_type'              => 'ink_event',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'meta_key'               => '_ink_event_date',
				'orderby'                => 'meta_value',
				'order'                  => 'past' === $bucket ? 'DESC' : 'ASC',
				'meta_type'              => 'DATE',
				'meta_query'             => array(
					array(
						'key'     => '_ink_event_date',
						'value'   => $today,
						'compare' => 'past' === $bucket ? '<' : '>=',
						'type'    => 'DATE',
					),
				),
			)
		);

		$events = array();

		if ( ! $query->have_posts() ) {
			return $events;
		}

		while ( $query->have_posts() ) {
			$query->the_post();
			$post_id    = get_the_ID();
			$event_date = (string) get_post_meta( $post_id, '_ink_event_date', true );
			$time_text  = (string) get_post_meta( $post_id, '_ink_event_time_text', true );
			$location   = (string) get_post_meta( $post_id, '_ink_event_location', true );
			$url        = (string) get_post_meta( $post_id, '_ink_event_url', true );
			$summary    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : $this->build_summary_fallback( get_post_field( 'post_content', $post_id ) );
			$content    = apply_filters( 'the_content', get_post_field( 'post_content', $post_id ) );
			$image_html = has_post_thumbnail( $post_id )
				? get_the_post_thumbnail(
					$post_id,
					'large',
					array(
						'class'   => 'foundation-live-events__image',
						'loading' => 'lazy',
						'alt'     => trim( wp_strip_all_tags( get_the_title( $post_id ) ) ),
					)
				)
				: '';

			$events[] = array(
				'title'          => get_the_title( $post_id ),
				'summary'        => $summary,
				'content'        => $content,
				'formatted_date' => $event_date ? wp_date( get_option( 'date_format' ), strtotime( $event_date ) ) : '',
				'time_text'      => $time_text,
				'location'       => $location,
				'url'            => $url,
				'image_html'     => $image_html,
			);
		}

		wp_reset_postdata();

		return $events;
	}

	/**
	 * Create a short fallback summary from the main content.
	 *
	 * @param string $content Raw post content.
	 * @return string
	 */
	private function build_summary_fallback( string $content ) {
		$stripped = wp_strip_all_tags( strip_shortcodes( $content ) );
		return wp_trim_words( $stripped, 28, '...' );
	}
}
