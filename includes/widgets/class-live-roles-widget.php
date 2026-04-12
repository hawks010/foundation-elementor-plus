<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Live_Roles_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-live-roles';
	}

	public function get_title() {
		return esc_html__( 'Foundation Live Roles', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'careers', 'jobs', 'roles', 'vacancies' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-live-roles' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-live-roles' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_shell_style_controls();
		$this->register_role_style_controls();
	}

	protected function render() {
		$settings         = $this->get_settings_for_display();
		$roles            = $this->get_roles_data( $settings );
		$widget_id        = 'foundation-live-roles-' . $this->get_id();
		$default_filter   = ! empty( $settings['default_filter'] ) ? $this->normalize_filter( $settings['default_filter'] ) : 'all';
		$show_filters     = isset( $settings['show_filters'] ) && 'yes' === $settings['show_filters'];
		$show_summary     = isset( $settings['show_summary'] ) && 'yes' === $settings['show_summary'];
		$default_open     = isset( $settings['default_open_first'] ) && 'yes' === $settings['default_open_first'];
		$summary_icon     = $this->get_icon_markup( $settings['summary_icon'] ?? array(), 'foundation-live-roles__summary-icon' );
		$link_icon_markup = $this->get_icon_markup( $settings['role_link_icon'] ?? array(), 'foundation-live-roles__link-icon' );
		$counts           = array(
			'open'       => 0,
			'processing' => 0,
			'closed'     => 0,
		);

		foreach ( $roles as $role ) {
			$status = $this->normalize_status( $role['status'] ?? 'open' );
			if ( isset( $counts[ $status ] ) ) {
				$counts[ $status ]++;
			}
		}

		$open_copy   = ! empty( $settings['open_summary_copy'] ) ? $settings['open_summary_copy'] : esc_html__( 'Open now', 'foundation-elementor-plus' );
		$review_copy = ! empty( $settings['processing_summary_copy'] ) ? $settings['processing_summary_copy'] : esc_html__( 'Being reviewed', 'foundation-elementor-plus' );
		$closed_copy = ! empty( $settings['closed_summary_copy'] ) ? $settings['closed_summary_copy'] : esc_html__( 'Now closed', 'foundation-elementor-plus' );
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="foundation-live-roles"
			data-foundation-live-roles
			data-default-filter="<?php echo esc_attr( $default_filter ); ?>"
			data-default-open="<?php echo $default_open ? 'yes' : 'no'; ?>"
		>
			<div class="foundation-live-roles__shell">
				<div class="foundation-live-roles__hero">
					<div class="foundation-live-roles__hero-main">
						<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
							<p class="foundation-live-roles__eyebrow">
								<span class="foundation-live-roles__eyebrow-dot" aria-hidden="true"></span>
								<span><?php echo esc_html( $settings['eyebrow'] ); ?></span>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $settings['heading'] ) ) : ?>
							<h2 class="foundation-live-roles__title"><?php echo wp_kses_post( nl2br( esc_html( $settings['heading'] ) ) ); ?></h2>
						<?php endif; ?>
					</div>

					<?php if ( $show_summary ) : ?>
						<aside class="foundation-live-roles__summary" aria-label="<?php echo esc_attr__( 'Role status summary', 'foundation-elementor-plus' ); ?>">
							<div class="foundation-live-roles__summary-head">
								<?php if ( $summary_icon ) : ?>
									<span class="foundation-live-roles__summary-icon-wrap" aria-hidden="true"><?php echo $summary_icon; ?></span>
								<?php endif; ?>
								<div>
									<?php if ( ! empty( $settings['summary_heading'] ) ) : ?>
										<p class="foundation-live-roles__summary-eyebrow"><?php echo esc_html( $settings['summary_heading'] ); ?></p>
									<?php endif; ?>
									<?php if ( ! empty( $settings['summary_text'] ) ) : ?>
										<p class="foundation-live-roles__summary-text"><?php echo esc_html( $settings['summary_text'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<ul class="foundation-live-roles__summary-list">
								<li class="foundation-live-roles__summary-item foundation-live-roles__summary-item--open">
									<span class="foundation-live-roles__summary-count"><?php echo esc_html( (string) $counts['open'] ); ?></span>
									<span class="foundation-live-roles__summary-label"><?php echo esc_html( $open_copy ); ?></span>
								</li>
								<li class="foundation-live-roles__summary-item foundation-live-roles__summary-item--processing">
									<span class="foundation-live-roles__summary-count"><?php echo esc_html( (string) $counts['processing'] ); ?></span>
									<span class="foundation-live-roles__summary-label"><?php echo esc_html( $review_copy ); ?></span>
								</li>
								<li class="foundation-live-roles__summary-item foundation-live-roles__summary-item--closed">
									<span class="foundation-live-roles__summary-count"><?php echo esc_html( (string) $counts['closed'] ); ?></span>
									<span class="foundation-live-roles__summary-label"><?php echo esc_html( $closed_copy ); ?></span>
								</li>
							</ul>
						</aside>
					<?php endif; ?>

					<?php if ( ! empty( $settings['intro'] ) ) : ?>
						<div class="foundation-live-roles__intro">
							<?php echo wp_kses_post( wpautop( $settings['intro'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $show_filters ) : ?>
					<div class="foundation-live-roles__filters" aria-label="<?php echo esc_attr__( 'Filter roles by status', 'foundation-elementor-plus' ); ?>">
						<?php foreach ( $this->get_filter_options() as $filter_key => $filter_label ) : ?>
							<?php
							$filter_count = 'all' === $filter_key ? count( $roles ) : ( $counts[ $filter_key ] ?? 0 );
							$is_active    = $filter_key === $default_filter;
							?>
							<button
								type="button"
								class="foundation-live-roles__filter<?php echo $is_active ? ' is-active' : ''; ?>"
								data-live-roles-filter="<?php echo esc_attr( $filter_key ); ?>"
								aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"
							>
								<span><?php echo esc_html( $filter_label ); ?></span>
								<span class="foundation-live-roles__filter-count"><?php echo esc_html( (string) $filter_count ); ?></span>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $roles ) ) : ?>
					<div class="foundation-live-roles__list" role="list">
						<?php foreach ( $roles as $index => $role ) : ?>
							<?php
							$status       = $this->normalize_status( $role['status'] ?? 'open' );
							$status_label = ! empty( $role['status_label'] ) ? $role['status_label'] : $this->get_status_label( $status );
							$panel_id     = $widget_id . '-panel-' . $index;
							$toggle_id    = $widget_id . '-toggle-' . $index;
							$is_expanded  = $default_open && 0 === $index;
							$link_text    = ! empty( $role['link_text'] ) ? $role['link_text'] : '';
							$link_key     = 'role_link_' . $index;
							$link_data    = ( ! empty( $role['link'] ) && is_array( $role['link'] ) ) ? $role['link'] : array();
							$show_link    = ! empty( $link_text ) && ! empty( $link_data['url'] );

							if ( $show_link ) {
								$this->add_link_attributes( $link_key, $link_data );
							}
							?>
							<article class="foundation-live-roles__role foundation-live-roles__role--<?php echo esc_attr( $status ); ?>" data-live-roles-item data-role-status="<?php echo esc_attr( $status ); ?>" role="listitem">
								<button
									id="<?php echo esc_attr( $toggle_id ); ?>"
									type="button"
									class="foundation-live-roles__toggle"
									data-live-roles-toggle
									aria-expanded="<?php echo $is_expanded ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $panel_id ); ?>"
								>
									<span class="foundation-live-roles__toggle-main">
										<span class="foundation-live-roles__status-dot foundation-live-roles__status-dot--<?php echo esc_attr( $status ); ?>" aria-hidden="true"></span>
										<span class="foundation-live-roles__role-title"><?php echo esc_html( $role['title'] ?? esc_html__( 'Role title', 'foundation-elementor-plus' ) ); ?></span>
									</span>

									<span class="foundation-live-roles__toggle-meta">
										<span class="foundation-live-roles__status-pill foundation-live-roles__status-pill--<?php echo esc_attr( $status ); ?>">
											<?php echo esc_html( $status_label ); ?>
										</span>
										<span class="foundation-live-roles__chevron" aria-hidden="true">
											<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
												<path d="M6 9.5 12 15.5 18 9.5"></path>
											</svg>
										</span>
									</span>
								</button>

								<div
									id="<?php echo esc_attr( $panel_id ); ?>"
									class="foundation-live-roles__panel"
									data-live-roles-panel
									<?php echo $is_expanded ? '' : 'hidden'; ?>
									aria-labelledby="<?php echo esc_attr( $toggle_id ); ?>"
								>
									<?php if ( ! empty( $role['summary'] ) ) : ?>
										<div class="foundation-live-roles__panel-copy">
											<?php echo wp_kses_post( wpautop( $role['summary'] ) ); ?>
										</div>
									<?php endif; ?>

									<?php if ( $show_link ) : ?>
										<div class="foundation-live-roles__panel-footer">
											<a class="foundation-live-roles__link" <?php echo $this->get_render_attribute_string( $link_key ); ?>>
												<span class="foundation-live-roles__link-text"><?php echo esc_html( $link_text ); ?></span>
												<?php echo $link_icon_markup; ?>
											</a>
										</div>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="foundation-live-roles__empty" data-live-roles-empty <?php echo ! empty( $roles ) && 'all' === $default_filter ? 'hidden' : ''; ?>>
					<?php echo esc_html( ! empty( $settings['empty_state'] ) ? $settings['empty_state'] : esc_html__( 'There are no roles in this status right now.', 'foundation-elementor-plus' ) ); ?>
				</div>
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
			'roles_source',
			array(
				'label'   => esc_html__( 'Roles Source', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'dynamic',
				'options' => array(
					'dynamic' => esc_html__( 'Careers CPT', 'foundation-elementor-plus' ),
					'manual'  => esc_html__( 'Manual roles', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'board_page_url',
			array(
				'label'         => esc_html__( 'Careers Board URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'default'       => array(
					'url' => '',
				),
				'show_external' => false,
				'description'   => esc_html__( 'Optional. Leave blank to auto-detect the careers page.', 'foundation-elementor-plus' ),
				'condition'     => array(
					'roles_source' => 'dynamic',
				),
			)
		);

		$this->add_control(
			'dynamic_limit',
			array(
				'label'      => esc_html__( 'Maximum Roles', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 6,
				'min'        => 1,
				'max'        => 24,
				'condition'  => array(
					'roles_source' => 'dynamic',
				),
			)
		);

		$this->add_control(
			'dynamic_statuses',
			array(
				'label'       => esc_html__( 'Statuses To Show', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array( 'open', 'reviewing-applicants', 'closed' ),
				'options'     => array(
					'open'                 => esc_html__( 'Open', 'foundation-elementor-plus' ),
					'reviewing-applicants' => esc_html__( 'Reviewing Applicants', 'foundation-elementor-plus' ),
					'closed'               => esc_html__( 'Closed', 'foundation-elementor-plus' ),
				),
				'condition'   => array(
					'roles_source' => 'dynamic',
				),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'       => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Live roles at Inkfire', 'foundation-elementor-plus' ),
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
				'default'     => "Explore roles worth\nshowing up for",
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
				'default' => esc_html__( 'See what is currently open, what is in review, and where we are still shaping the role. Built for careers pages, service pages, or quick insert blocks.', 'foundation-elementor-plus' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_summary',
			array(
				'label'        => esc_html__( 'Show Summary Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'summary_heading',
			array(
				'label'       => esc_html__( 'Summary Eyebrow', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Hiring pulse', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'summary_text',
			array(
				'label'       => esc_html__( 'Summary Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'A quick view of what is live right now.', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'summary_icon',
			array(
				'label'     => esc_html__( 'Summary Icon', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-briefcase',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'open_summary_copy',
			array(
				'label'       => esc_html__( 'Open Summary Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Open now', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'processing_summary_copy',
			array(
				'label'       => esc_html__( 'Processing Summary Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Being reviewed', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'closed_summary_copy',
			array(
				'label'       => esc_html__( 'Closed Summary Label', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Now closed', 'foundation-elementor-plus' ),
				'label_block' => true,
				'condition'   => array(
					'show_summary' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => esc_html__( 'Show Status Filters', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'default_filter',
			array(
				'label'     => esc_html__( 'Default Filter', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'all',
				'options'   => $this->get_filter_options(),
				'condition' => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'default_open_first',
			array(
				'label'        => esc_html__( 'Open First Role By Default', 'foundation-elementor-plus' ),
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
				'default'     => esc_html__( 'There are no roles in this status right now.', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_roles',
			array(
				'label'     => esc_html__( 'Manual Roles', 'foundation-elementor-plus' ),
				'condition' => array(
					'roles_source' => 'manual',
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Role Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Junior Front-End Developer', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'status',
			array(
				'label'   => esc_html__( 'Status', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'open',
				'options' => array(
					'open'       => esc_html__( 'Open', 'foundation-elementor-plus' ),
					'processing' => esc_html__( 'Processing', 'foundation-elementor-plus' ),
					'closed'     => esc_html__( 'Closed', 'foundation-elementor-plus' ),
				),
			)
		);

		$repeater->add_control(
			'status_label',
			array(
				'label'       => esc_html__( 'Status Label Override', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => esc_html__( 'Leave blank to use the default status label.', 'foundation-elementor-plus' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'summary',
			array(
				'label'   => esc_html__( 'Expanded Summary', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 4,
				'default' => esc_html__( 'Add the role summary here.', 'foundation-elementor-plus' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link_text',
			array(
				'label'       => esc_html__( 'Link Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'View role details', 'foundation-elementor-plus' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'         => esc_html__( 'Link URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => 'https://',
				'default'       => array(
					'url' => '',
				),
				'show_external' => true,
				'dynamic'       => array( 'active' => true ),
			)
		);

		$this->add_control(
			'roles',
			array(
				'label'       => esc_html__( 'Roles', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => $this->get_default_roles(),
			)
		);

		$this->add_control(
			'role_link_icon',
			array(
				'label'   => esc_html__( 'Role Link Icon', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				),
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
					'{{WRAPPER}} .foundation-live-roles' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .foundation-live-roles__shell' => 'width: {{SIZE}}{{UNIT}}; max-width: none;',
				),
			)
		);

		$this->add_control(
			'stretch_to_parent',
			array(
				'label'        => esc_html__( 'Stretch To Fit Row', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => array(
					'{{WRAPPER}}' => 'height: 100%;',
					'{{WRAPPER}} .elementor-widget-container' => 'height: 100%;',
					'{{WRAPPER}} .foundation-live-roles' => 'height: 100%;',
					'{{WRAPPER}} .foundation-live-roles__shell' => 'height: 100%;',
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
					'top'      => 32,
					'right'    => 32,
					'bottom'   => 32,
					'left'     => 32,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles__shell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'%'  => array( 'min' => 0, 'max' => 50 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-shell-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'hero_gap',
			array(
				'label'      => esc_html__( 'Hero Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 100 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-hero-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'role_gap',
			array(
				'label'      => esc_html__( 'Role Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 14,
				),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 60 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-role-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'filter_gap',
			array(
				'label'      => esc_html__( 'Filter Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 12,
				),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 40 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-filter-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'role_padding',
			array(
				'label'      => esc_html__( 'Role Row Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'      => 18,
					'right'    => 20,
					'bottom'   => 18,
					'left'     => 20,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles__role' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'role_radius',
			array(
				'label'      => esc_html__( 'Role Row Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array( 'min' => 0, 'max' => 80 ),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-role-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_shell_style_controls() {
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
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-shell-bg: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'shell_accent_a',
			array(
				'label'     => esc_html__( 'Accent Glow A', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(251, 204, 190, 0.16)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-accent-a: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'shell_accent_b',
			array(
				'label'     => esc_html__( 'Accent Glow B', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(18, 181, 165, 0.22)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-accent-b: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'shell_border',
				'selector' => '{{WRAPPER}} .foundation-live-roles__shell',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'shell_shadow',
				'selector' => '{{WRAPPER}} .foundation-live-roles__shell',
			)
		);

		$this->add_control(
			'eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FBD0C3',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__eyebrow',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Heading Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__title',
			)
		);

		$this->add_control(
			'intro_color',
			array(
				'label'     => esc_html__( 'Intro Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__intro, {{WRAPPER}} .foundation-live-roles__intro p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__intro, {{WRAPPER}} .foundation-live-roles__intro p',
			)
		);

		$this->end_controls_section();
	}

	private function register_role_style_controls() {
		$this->start_controls_section(
			'section_role_style',
			array(
				'label' => esc_html__( 'Roles', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'role_background',
			array(
				'label'     => esc_html__( 'Role Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.07)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-role-bg: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'role_border',
				'selector' => '{{WRAPPER}} .foundation-live-roles__role',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'role_shadow',
				'selector' => '{{WRAPPER}} .foundation-live-roles__role',
			)
		);

		$this->add_control(
			'role_title_color',
			array(
				'label'     => esc_html__( 'Role Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__role-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_title_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__role-title',
			)
		);

		$this->add_control(
			'body_color',
			array(
				'label'     => esc_html__( 'Body Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.80)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__panel-copy, {{WRAPPER}} .foundation-live-roles__panel-copy p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'body_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__panel-copy, {{WRAPPER}} .foundation-live-roles__panel-copy p',
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'     => esc_html__( 'Role Link Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles__link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .foundation-live-roles__link',
			)
		);

		$this->add_control(
			'status_heading',
			array(
				'label'     => esc_html__( 'Status Colours', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'status_open_color',
			array(
				'label'     => esc_html__( 'Open', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#18B5A5',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-status-open: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'status_processing_color',
			array(
				'label'     => esc_html__( 'Processing', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F4B063',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-status-processing: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'status_closed_color',
			array(
				'label'     => esc_html__( 'Closed', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F27979',
				'selectors' => array(
					'{{WRAPPER}} .foundation-live-roles' => '--foundation-live-roles-status-closed: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_default_roles() {
		return array(
			array(
				'title'     => esc_html__( 'Junior Front-End Developer', 'foundation-elementor-plus' ),
				'status'    => 'open',
				'summary'   => esc_html__( 'A role for someone who cares about polished implementation, accessible frontend work, and learning inside a thoughtful team.', 'foundation-elementor-plus' ),
				'link_text' => esc_html__( 'View role details', 'foundation-elementor-plus' ),
				'link'      => array( 'url' => '#' ),
			),
			array(
				'title'     => esc_html__( 'Copywriter', 'foundation-elementor-plus' ),
				'status'    => 'processing',
				'summary'   => esc_html__( 'We are reviewing how this role should be shaped. Expect clear updates, transparent timelines, and no black-hole applications.', 'foundation-elementor-plus' ),
				'link_text' => esc_html__( 'Register interest', 'foundation-elementor-plus' ),
				'link'      => array( 'url' => '#' ),
			),
			array(
				'title'     => esc_html__( 'Accessibility Consultant', 'foundation-elementor-plus' ),
				'status'    => 'closed',
				'summary'   => esc_html__( 'This position is now closed, but the row is kept here to show the specialist roles we hire for over time.', 'foundation-elementor-plus' ),
				'link_text' => esc_html__( 'See careers page', 'foundation-elementor-plus' ),
				'link'      => array( 'url' => '#' ),
			),
		);
	}

	private function get_roles_data( $settings ) {
		$source = ! empty( $settings['roles_source'] ) ? $settings['roles_source'] : 'dynamic';

		if ( 'manual' === $source ) {
			return ! empty( $settings['roles'] ) && is_array( $settings['roles'] ) ? array_values( $settings['roles'] ) : array();
		}

		return $this->get_dynamic_roles( $settings );
	}

	private function get_dynamic_roles( $settings ) {
		$limit         = ! empty( $settings['dynamic_limit'] ) ? max( 1, (int) $settings['dynamic_limit'] ) : 6;
		$allowed       = ! empty( $settings['dynamic_statuses'] ) && is_array( $settings['dynamic_statuses'] ) ? array_values( array_filter( array_map( 'sanitize_key', $settings['dynamic_statuses'] ) ) ) : array( 'open', 'reviewing-applicants', 'closed' );
		$board_url     = $this->resolve_board_url( $settings );
		$query_args    = array(
			'post_type'      => 'ink_career',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$roles         = array();

		if ( ! empty( $allowed ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'ink_career_status',
					'field'    => 'slug',
					'terms'    => $allowed,
				),
			);
		}

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			return array();
		}

		foreach ( $query->posts as $job ) {
			if ( ! $job instanceof \WP_Post ) {
				continue;
			}

			$status_term = wp_get_post_terms( $job->ID, 'ink_career_status' );
			$status_slug = ! empty( $status_term[0] ) ? $status_term[0]->slug : 'open';
			$role_url    = $board_url ? add_query_arg( array( 'career' => $job->ID ), $board_url ) : '';
			$summary     = has_excerpt( $job->ID ) ? get_the_excerpt( $job->ID ) : wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $job->ID ) ), 30 );
			$role_status = 'processing';

			if ( 'open' === $status_slug ) {
				$role_status = 'open';
			} elseif ( 'closed' === $status_slug ) {
				$role_status = 'closed';
			}

			$roles[] = array(
				'title'        => get_the_title( $job->ID ),
				'status'       => $role_status,
				'status_label' => $this->get_status_label( $role_status ),
				'summary'      => $summary,
				'link_text'    => esc_html__( 'View role details', 'foundation-elementor-plus' ),
				'link'         => array(
					'url' => $role_url,
				),
			);
		}

		wp_reset_postdata();

		return $roles;
	}

	private function resolve_board_url( $settings ) {
		if ( ! empty( $settings['board_page_url']['url'] ) ) {
			return $settings['board_page_url']['url'];
		}

		$careers_page = get_page_by_path( 'about-us/careers' );
		if ( $careers_page instanceof \WP_Post ) {
			return get_permalink( $careers_page->ID );
		}

		$careers_page = get_page_by_path( 'careers' );
		if ( $careers_page instanceof \WP_Post ) {
			return get_permalink( $careers_page->ID );
		}

		return home_url( '/about-us/careers/' );
	}

	private function get_filter_options() {
		return array(
			'all'        => esc_html__( 'All roles', 'foundation-elementor-plus' ),
			'open'       => esc_html__( 'Open', 'foundation-elementor-plus' ),
			'processing' => esc_html__( 'Processing', 'foundation-elementor-plus' ),
			'closed'     => esc_html__( 'Closed', 'foundation-elementor-plus' ),
		);
	}

	private function normalize_status( $status ) {
		$normalized = sanitize_key( (string) $status );

		if ( ! in_array( $normalized, array( 'open', 'processing', 'closed' ), true ) ) {
			return 'open';
		}

		return $normalized;
	}

	private function normalize_filter( $filter ) {
		$normalized = sanitize_key( (string) $filter );

		if ( 'all' === $normalized ) {
			return 'all';
		}

		return $this->normalize_status( $normalized );
	}

	private function get_status_label( $status ) {
		$labels = array(
			'open'       => esc_html__( 'Open', 'foundation-elementor-plus' ),
			'processing' => esc_html__( 'Processing', 'foundation-elementor-plus' ),
			'closed'     => esc_html__( 'Closed', 'foundation-elementor-plus' ),
		);

		return $labels[ $status ] ?? esc_html__( 'Open', 'foundation-elementor-plus' );
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
