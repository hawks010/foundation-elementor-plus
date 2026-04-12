<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use WP_Post;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Team_Loop_Widget extends Widget_Base {
	private const TEAM_POST_TYPE           = 'ink_team_member';
	private const TEAM_TYPE_TAXONOMY       = 'ink_team_type';
	private const TEAM_DEPARTMENT_TAXONOMY = 'ink_team_department';
	private const CAREER_POST_TYPE         = 'ink_career';
	private const CAREER_STATUS_TAXONOMY   = 'ink_career_status';

	public function get_name() {
		return 'foundation-team-loop';
	}

	public function get_title() {
		return esc_html__( 'Foundation Team Loop', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-loop-builder';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'team', 'staff', 'founders', 'people', 'loop' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-team-loop' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-team-loop' );
	}

	protected function register_controls() {
		$this->register_content_controls();
		$this->register_layout_controls();
		$this->register_filter_style_controls();
		$this->register_card_style_controls();
		$this->register_image_style_controls();
		$this->register_text_style_controls();
		$this->register_badge_style_controls();
		$this->register_button_style_controls();
		$this->register_feature_card_style_controls();
	}

	protected function render() {
		$settings               = $this->get_settings_for_display();
		$members                = $this->get_members( $settings );
		$departments            = $this->get_department_options();
		$default_department     = ! empty( $settings['default_department'] ) ? sanitize_key( $settings['default_department'] ) : 'all';
		$default_group          = $this->normalize_member_group_key( $settings['default_member_group'] ?? 'all' );
		$show_filters           = 'yes' === ( $settings['show_filters'] ?? '' );
		$show_department_filter = $show_filters && 'yes' === ( $settings['show_department_filter'] ?? 'yes' );
		$show_group_filter      = $show_filters && 'yes' === ( $settings['show_group_filter'] ?? 'yes' );
		$card_skin              = ! empty( $settings['card_skin'] ) ? $settings['card_skin'] : 'detailed';
		$show_feature_card      = 'yes' === ( $settings['show_live_roles_feature'] ?? '' );
		$show_hook              = 'yes' === ( $settings['show_hook'] ?? '' );
		$show_badges            = 'yes' === ( $settings['show_badges'] ?? '' );
		$show_toggle            = 'yes' === ( $settings['show_toggle'] ?? '' );
		$feature_data           = $show_feature_card ? $this->get_live_roles_feature_data() : array();
		$widget_id              = 'foundation-team-loop-' . $this->get_id();
		$visible_member_count = 0;
		?>
		<section
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="foundation-team-loop"
			data-foundation-team-loop
			data-default-department="<?php echo esc_attr( $default_department ); ?>"
			data-default-group="<?php echo esc_attr( $default_group ); ?>"
		>
			<?php if ( $show_department_filter || $show_group_filter ) : ?>
				<div class="foundation-team-loop__filters">
					<?php if ( $show_department_filter ) : ?>
						<div class="foundation-team-loop__filter">
							<label class="foundation-team-loop__filter-label" for="<?php echo esc_attr( $widget_id . '-department' ); ?>">
								<?php echo esc_html( $settings['department_filter_label'] ?? __( 'Department', 'foundation-elementor-plus' ) ); ?>
							</label>
							<div class="foundation-team-loop__select-wrap">
								<select
									id="<?php echo esc_attr( $widget_id . '-department' ); ?>"
									class="foundation-team-loop__select"
									data-team-loop-filter="department"
								>
									<?php foreach ( $departments as $department_slug => $department_label ) : ?>
										<option value="<?php echo esc_attr( $department_slug ); ?>" <?php selected( $default_department, $department_slug ); ?>>
											<?php echo esc_html( $department_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $show_group_filter ) : ?>
						<div class="foundation-team-loop__filter">
							<label class="foundation-team-loop__filter-label" for="<?php echo esc_attr( $widget_id . '-group' ); ?>">
								<?php echo esc_html( $settings['group_filter_label'] ?? __( 'View', 'foundation-elementor-plus' ) ); ?>
							</label>
							<div class="foundation-team-loop__select-wrap">
								<select
									id="<?php echo esc_attr( $widget_id . '-group' ); ?>"
									class="foundation-team-loop__select"
									data-team-loop-filter="group"
								>
									<?php foreach ( $this->get_member_group_options() as $group_key => $group_label ) : ?>
										<option value="<?php echo esc_attr( $group_key ); ?>" <?php selected( $default_group, $group_key ); ?>>
											<?php echo esc_html( $group_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="foundation-team-loop__grid" role="list">
				<?php foreach ( $members as $index => $member ) : ?>
					<?php
					$is_visible = $this->matches_filters( $member, $default_department, $default_group );
					$panel_id   = $widget_id . '-panel-' . $index;
					$toggle_id  = $widget_id . '-toggle-' . $index;
					$first_name = $this->get_first_name( $member['name'] );
					$bio_markup = $this->format_member_content( $member['content'] );
					$has_bio    = '' !== trim( wp_strip_all_tags( $bio_markup ) );
					$has_toggle = 'detailed' === $card_skin && $show_toggle && $has_bio;

					if ( $is_visible ) {
						$visible_member_count++;
					}
					?>
					<article
						class="foundation-team-loop__card foundation-team-loop__card--<?php echo esc_attr( $card_skin ); ?>"
						data-team-loop-item
						data-team-loop-departments="<?php echo esc_attr( implode( ' ', $member['department_slugs'] ) ); ?>"
						data-team-loop-group="<?php echo esc_attr( $member['group'] ); ?>"
						role="listitem"
						<?php echo $is_visible ? '' : 'hidden'; ?>
					>
						<?php if ( ! empty( $member['image_html'] ) ) : ?>
							<div class="foundation-team-loop__media">
								<?php echo $member['image_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>

						<div class="foundation-team-loop__content">
							<?php if ( ! empty( $member['role_title'] ) ) : ?>
								<p class="foundation-team-loop__role"><?php echo esc_html( $member['role_title'] ); ?></p>
							<?php endif; ?>

							<h3 class="foundation-team-loop__name"><?php echo esc_html( $member['name'] ); ?></h3>

							<?php if ( 'detailed' === $card_skin && $show_hook && ! empty( $member['hook'] ) ) : ?>
								<div class="foundation-team-loop__hook"><?php echo esc_html( $member['hook'] ); ?></div>
							<?php endif; ?>

							<?php if ( 'detailed' === $card_skin && $show_badges && ! empty( $member['badges'] ) ) : ?>
								<div class="foundation-team-loop__badges">
									<?php foreach ( $member['badges'] as $badge ) : ?>
										<span class="foundation-team-loop__badge"><?php echo esc_html( $badge ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<?php if ( $has_toggle ) : ?>
								<button
									id="<?php echo esc_attr( $toggle_id ); ?>"
									type="button"
									class="foundation-team-loop__toggle"
									data-team-loop-toggle
									data-open-label="<?php echo esc_attr__( 'Show less', 'foundation-elementor-plus' ); ?>"
									data-closed-label="<?php echo esc_attr( sprintf( __( 'More about %s', 'foundation-elementor-plus' ), $first_name ) ); ?>"
									aria-expanded="false"
									aria-controls="<?php echo esc_attr( $panel_id ); ?>"
								>
									<span class="foundation-team-loop__toggle-text"><?php echo esc_html( sprintf( __( 'More about %s', 'foundation-elementor-plus' ), $first_name ) ); ?></span>
									<span class="foundation-team-loop__toggle-icon" aria-hidden="true">
										<svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
											<path d="M6 9.5 12 15.5 18 9.5"></path>
										</svg>
									</span>
								</button>
								<div
									id="<?php echo esc_attr( $panel_id ); ?>"
									class="foundation-team-loop__bio"
									data-team-loop-panel
									hidden
									aria-labelledby="<?php echo esc_attr( $toggle_id ); ?>"
								>
									<div class="foundation-team-loop__bio-copy">
										<?php echo $bio_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
									<?php if ( ! empty( $member['profile_link_url'] ) && ! empty( $member['profile_link_label'] ) ) : ?>
										<p class="foundation-team-loop__bio-link-wrap">
											<a class="foundation-team-loop__bio-link" href="<?php echo esc_url( $member['profile_link_url'] ); ?>">
												<?php echo esc_html( $member['profile_link_label'] ); ?>
											</a>
										</p>
									<?php endif; ?>
								</div>
							<?php elseif ( 'portrait' === $card_skin && ! empty( $member['profile_link_url'] ) && ! empty( $member['profile_link_label'] ) ) : ?>
								<p class="foundation-team-loop__portrait-link-wrap">
									<a class="foundation-team-loop__portrait-link" href="<?php echo esc_url( $member['profile_link_url'] ); ?>">
										<?php echo esc_html( $member['profile_link_label'] ); ?>
									</a>
								</p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>

				<?php if ( $show_feature_card ) : ?>
					<article
						class="foundation-team-loop__feature-card<?php echo 'yes' === ( $settings['feature_card_full_width'] ?? '' ) ? ' foundation-team-loop__feature-card--full' : ''; ?>"
						role="listitem"
					>
						<?php if ( ! empty( $settings['feature_eyebrow'] ) ) : ?>
							<p class="foundation-team-loop__feature-eyebrow">
								<span class="foundation-team-loop__feature-dot" aria-hidden="true"></span>
								<?php echo esc_html( $settings['feature_eyebrow'] ); ?>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $settings['feature_title'] ) ) : ?>
							<h3 class="foundation-team-loop__feature-title"><?php echo esc_html( $settings['feature_title'] ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $settings['feature_copy'] ) ) : ?>
							<div class="foundation-team-loop__feature-copy">
								<?php echo wp_kses_post( wpautop( $settings['feature_copy'] ) ); ?>
							</div>
						<?php endif; ?>

						<div class="foundation-team-loop__feature-stats">
							<div class="foundation-team-loop__feature-stat foundation-team-loop__feature-stat--open">
								<span class="foundation-team-loop__feature-stat-number"><?php echo esc_html( (string) $feature_data['counts']['open'] ); ?></span>
								<span class="foundation-team-loop__feature-stat-label"><?php esc_html_e( 'Open', 'foundation-elementor-plus' ); ?></span>
							</div>
							<div class="foundation-team-loop__feature-stat foundation-team-loop__feature-stat--processing">
								<span class="foundation-team-loop__feature-stat-number"><?php echo esc_html( (string) $feature_data['counts']['processing'] ); ?></span>
								<span class="foundation-team-loop__feature-stat-label"><?php esc_html_e( 'Processing', 'foundation-elementor-plus' ); ?></span>
							</div>
							<div class="foundation-team-loop__feature-stat foundation-team-loop__feature-stat--closed">
								<span class="foundation-team-loop__feature-stat-number"><?php echo esc_html( (string) $feature_data['counts']['closed'] ); ?></span>
								<span class="foundation-team-loop__feature-stat-label"><?php esc_html_e( 'Closed', 'foundation-elementor-plus' ); ?></span>
							</div>
						</div>

						<?php if ( ! empty( $feature_data['open_titles'] ) ) : ?>
							<div class="foundation-team-loop__feature-openings">
								<?php foreach ( $feature_data['open_titles'] as $open_title ) : ?>
									<span class="foundation-team-loop__feature-opening"><?php echo esc_html( $open_title ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['feature_button_text'] ) && ! empty( $settings['feature_button_url']['url'] ) ) : ?>
							<a
								class="foundation-team-loop__feature-link"
								href="<?php echo esc_url( $settings['feature_button_url']['url'] ); ?>"
								<?php echo ! empty( $settings['feature_button_url']['is_external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>
							>
								<?php echo esc_html( $settings['feature_button_text'] ); ?>
							</a>
						<?php endif; ?>
					</article>
				<?php endif; ?>
			</div>

			<div class="foundation-team-loop__empty" data-team-loop-empty<?php echo 0 === $visible_member_count ? '' : ' hidden'; ?>>
				<?php echo esc_html( $settings['empty_state_text'] ?? __( 'No team members match this filter yet.', 'foundation-elementor-plus' ) ); ?>
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
			'card_skin',
			array(
				'label'   => esc_html__( 'Card Skin', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'detailed',
				'options' => array(
					'detailed' => esc_html__( 'Current Glass Card', 'foundation-elementor-plus' ),
					'portrait' => esc_html__( 'Portrait / Title / Name', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Members to Show', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => -1,
				'min'     => -1,
				'max'     => 50,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order By', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'menu_order',
				'options' => array(
					'menu_order' => esc_html__( 'Manual / Hierarchy', 'foundation-elementor-plus' ),
					'title'      => esc_html__( 'Name', 'foundation-elementor-plus' ),
					'date'       => esc_html__( 'Date Added', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'default_department',
			array(
				'label'   => esc_html__( 'Default Department View', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => $this->get_department_options(),
			)
		);

		$this->add_control(
			'default_member_group',
			array(
				'label'   => esc_html__( 'Default Team Group', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => $this->get_member_group_options(),
			)
		);

		$this->add_control(
			'show_filters',
			array(
				'label'        => esc_html__( 'Show Filter Bar', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_department_filter',
			array(
				'label'        => esc_html__( 'Show Department Filter', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_group_filter',
			array(
				'label'        => esc_html__( 'Show Team Group Filter', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_filters' => 'yes',
				),
			)
		);

		$this->add_control(
			'department_filter_label',
			array(
				'label'     => esc_html__( 'Department Label', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Department', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_filters'           => 'yes',
					'show_department_filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'group_filter_label',
			array(
				'label'     => esc_html__( 'Team Group Label', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'View', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_filters'      => 'yes',
					'show_group_filter' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_hook',
			array(
				'label'        => esc_html__( 'Show Hook / Excerpt', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'card_skin' => 'detailed',
				),
			)
		);

		$this->add_control(
			'show_badges',
			array(
				'label'        => esc_html__( 'Show Badges', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'card_skin' => 'detailed',
				),
			)
		);

		$this->add_control(
			'show_toggle',
			array(
				'label'        => esc_html__( 'Show Bio Dropdown', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'card_skin' => 'detailed',
				),
			)
		);

		$this->add_control(
			'empty_state_text',
			array(
				'label'   => esc_html__( 'Empty State Text', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'No team members match this filter yet.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_feature_card',
			array(
				'label' => esc_html__( 'Live Roles Feature Card', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'show_live_roles_feature',
			array(
				'label'        => esc_html__( 'Show Final Feature Card', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'feature_card_full_width',
			array(
				'label'        => esc_html__( 'Feature Card Full Width', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array(
					'show_live_roles_feature' => 'yes',
				),
			)
		);

		$this->add_control(
			'feature_eyebrow',
			array(
				'label'     => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Live roles at Inkfire', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_live_roles_feature' => 'yes',
				),
			)
		);

		$this->add_control(
			'feature_title',
			array(
				'label'     => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 2,
				'default'   => esc_html__( 'Explore roles worth showing up for', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_live_roles_feature' => 'yes',
				),
			)
		);

		$this->add_control(
			'feature_copy',
			array(
				'label'     => esc_html__( 'Copy', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 3,
				'default'   => esc_html__( 'See what is currently open, what is in review, and where we are still shaping the role. Built for careers pages, service pages, or quick insert blocks.', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_live_roles_feature' => 'yes',
				),
			)
		);

		$this->add_control(
			'feature_button_text',
			array(
				'label'     => esc_html__( 'Button Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Explore live roles', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_live_roles_feature' => 'yes',
				),
			)
		);

		$this->add_control(
			'feature_button_url',
			array(
				'label'         => esc_html__( 'Button URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'default'       => array(
					'url' => home_url( '/about-us/careers/' ),
				),
				'show_external' => false,
				'condition'     => array(
					'show_live_roles_feature' => 'yes',
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
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'foundation-elementor-plus' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => array( '' ),
				'range'          => array(
					'' => array(
						'min' => 1,
						'max' => 4,
					),
				),
				'default'        => array(
					'size' => 2,
				),
				'tablet_default' => array(
					'size' => 2,
				),
				'mobile_default' => array(
					'size' => 1,
				),
				'selectors'      => array(
					'{{WRAPPER}} .foundation-team-loop__grid' => 'grid-template-columns: repeat({{SIZE}}, minmax(0, 1fr));',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Grid Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 40,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-team-loop__grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Card Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'top'    => 32,
					'right'  => 32,
					'bottom' => 32,
					'left'   => 32,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-team-loop__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'card_border_radius',
			array(
				'label'      => esc_html__( 'Card Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 32,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-team-loop__card, {{WRAPPER}} .foundation-team-loop__feature-card' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_ratio',
			array(
				'label'   => esc_html__( 'Image Ratio', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1 / 1',
				'options' => array(
					'1 / 1' => esc_html__( 'Square', 'foundation-elementor-plus' ),
					'4 / 5' => esc_html__( 'Portrait', 'foundation-elementor-plus' ),
					'3 / 4' => esc_html__( 'Tall Portrait', 'foundation-elementor-plus' ),
					'16 / 10' => esc_html__( 'Wide', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__media' => 'aspect-ratio: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'image_object_position',
			array(
				'label'   => esc_html__( 'Image Position', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center 18%',
				'options' => array(
					'center center' => esc_html__( 'Center', 'foundation-elementor-plus' ),
					'center 18%'    => esc_html__( 'Portrait Top', 'foundation-elementor-plus' ),
					'center top'    => esc_html__( 'Top', 'foundation-elementor-plus' ),
					'center bottom' => esc_html__( 'Bottom', 'foundation-elementor-plus' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__media img' => 'object-position: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_filter_style_controls() {
		$this->start_controls_section(
			'section_filter_style',
			array(
				'label' => esc_html__( 'Filters', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'filter_label_color',
			array(
				'label'     => esc_html__( 'Label Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FAC0AF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__filter-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_label_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__filter-label',
			)
		);

		$this->add_control(
			'filter_select_color',
			array(
				'label'     => esc_html__( 'Select Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__select' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_select_background',
			array(
				'label'     => esc_html__( 'Select Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(17, 20, 33, 0.94)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__select-wrap' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'filter_select_border_color',
			array(
				'label'     => esc_html__( 'Select Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.12)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__select-wrap' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'filter_select_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__select',
			)
		);

		$this->end_controls_section();
	}

	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Cards', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'card_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .foundation-team-loop__card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .foundation-team-loop__card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .foundation-team-loop__card',
			)
		);

		$this->add_control(
			'card_hover_translate',
			array(
				'label'      => esc_html__( 'Hover Lift', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 4,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-team-loop' => '--foundation-team-loop-card-hover-y: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function register_image_style_controls() {
		$this->start_controls_section(
			'section_image_style',
			array(
				'label' => esc_html__( 'Image', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_radius',
			array(
				'label'      => esc_html__( 'Image Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 24,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-team-loop__media, {{WRAPPER}} .foundation-team-loop__media img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .foundation-team-loop__media',
			)
		);

		$this->end_controls_section();
	}

	private function register_text_style_controls() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'Text', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => esc_html__( 'Role Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#F18E5C',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__role',
			)
		);

		$this->add_control(
			'name_color',
			array(
				'label'     => esc_html__( 'Name Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__name',
			)
		);

		$this->add_control(
			'hook_color',
			array(
				'label'     => esc_html__( 'Hook Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.9)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__hook' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'hook_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__hook',
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => esc_html__( 'Bio Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.9)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__bio, {{WRAPPER}} .foundation-team-loop__bio p, {{WRAPPER}} .foundation-team-loop__bio li' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bio_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__bio, {{WRAPPER}} .foundation-team-loop__bio p, {{WRAPPER}} .foundation-team-loop__bio li',
			)
		);

		$this->end_controls_section();
	}

	private function register_badge_style_controls() {
		$this->start_controls_section(
			'section_badge_style',
			array(
				'label' => esc_html__( 'Badges', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'badge_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_background',
			array(
				'label'     => esc_html__( 'Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(226, 114, 0, 0.75)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__badge' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.15)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__badge' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__badge',
			)
		);

		$this->end_controls_section();
	}

	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'Dropdown Button', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'toggle_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_background',
			array(
				'label'     => esc_html__( 'Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0E4F4D',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__toggle' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_hover_background',
			array(
				'label'     => esc_html__( 'Hover Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#E27200',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__toggle:hover, {{WRAPPER}} .foundation-team-loop__toggle:focus-visible' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.35)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__toggle' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'toggle_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__toggle',
			)
		);

		$this->end_controls_section();
	}

	private function register_feature_card_style_controls() {
		$this->start_controls_section(
			'section_feature_style',
			array(
				'label' => esc_html__( 'Feature Card', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'feature_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .foundation-team-loop__feature-card',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'feature_border',
				'selector' => '{{WRAPPER}} .foundation-team-loop__feature-card',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'feature_shadow',
				'selector' => '{{WRAPPER}} .foundation-team-loop__feature-card',
			)
		);

		$this->add_control(
			'feature_eyebrow_color',
			array(
				'label'     => esc_html__( 'Eyebrow Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FBD0C3',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-eyebrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'feature_title_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__feature-title',
			)
		);

		$this->add_control(
			'feature_copy_color',
			array(
				'label'     => esc_html__( 'Copy Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.84)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-copy, {{WRAPPER}} .foundation-team-loop__feature-copy p' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'feature_copy_typography',
				'selector' => '{{WRAPPER}} .foundation-team-loop__feature-copy, {{WRAPPER}} .foundation-team-loop__feature-copy p',
			)
		);

		$this->add_control(
			'feature_button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-link' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_button_background',
			array(
				'label'     => esc_html__( 'Button Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0E4F4D',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-link' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_stat_number_color',
			array(
				'label'     => esc_html__( 'Stat Number Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-stat-number' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_stat_label_color',
			array(
				'label'     => esc_html__( 'Stat Label Color', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.82)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-stat-label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_stat_background',
			array(
				'label'     => esc_html__( 'Stat Card Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.06)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-stat' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_stat_border_color',
			array(
				'label'     => esc_html__( 'Stat Card Border', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.10)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-stat' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_opening_background',
			array(
				'label'     => esc_html__( 'Opening Pill Background', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(255, 255, 255, 0.08)',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-opening' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'feature_opening_text_color',
			array(
				'label'     => esc_html__( 'Opening Pill Text', 'foundation-elementor-plus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array(
					'{{WRAPPER}} .foundation-team-loop__feature-opening' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function get_department_options(): array {
		$options = array(
			'all'        => esc_html__( 'All departments', 'foundation-elementor-plus' ),
			'management' => esc_html__( 'Management', 'foundation-elementor-plus' ),
			'it'         => esc_html__( 'IT', 'foundation-elementor-plus' ),
			'web'        => esc_html__( 'Web', 'foundation-elementor-plus' ),
			'marketing'  => esc_html__( 'Marketing', 'foundation-elementor-plus' ),
			'branding'   => esc_html__( 'Branding', 'foundation-elementor-plus' ),
		);

		$terms = get_terms(
			array(
				'taxonomy'   => self::TEAM_DEPARTMENT_TAXONOMY,
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( ! isset( $options[ $term->slug ] ) ) {
					$options[ $term->slug ] = $term->name;
				}
			}
		}

		return $options;
	}

	private function get_member_group_options(): array {
		return array(
			'all'      => esc_html__( 'All team', 'foundation-elementor-plus' ),
			'staff'    => esc_html__( 'Staff', 'foundation-elementor-plus' ),
			'founders' => esc_html__( 'Founders', 'foundation-elementor-plus' ),
		);
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<int, array<string, mixed>>
	 */
	private function get_members( array $settings ): array {
		if ( ! post_type_exists( self::TEAM_POST_TYPE ) ) {
			return array();
		}

		$posts_per_page = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : -1;
		$order_by       = isset( $settings['order_by'] ) ? sanitize_key( $settings['order_by'] ) : 'menu_order';

		$query = new WP_Query(
			array(
				'post_type'      => self::TEAM_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 0 === $posts_per_page ? -1 : $posts_per_page,
				'orderby'        => 'menu_order' === $order_by ? array( 'menu_order' => 'ASC', 'title' => 'ASC' ) : $order_by,
				'order'          => 'title' === $order_by ? 'ASC' : 'ASC',
				'no_found_rows'  => true,
			)
		);

		if ( ! $query->have_posts() ) {
			return array();
		}

		$members = array();

		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof WP_Post ) {
				continue;
			}

			$members[] = $this->build_member_data( $post );
		}

		wp_reset_postdata();

		return $members;
	}

	/**
	 * @param WP_Post $post Team member post.
	 * @return array<string, mixed>
	 */
	private function build_member_data( WP_Post $post ): array {
		$type_slugs       = wp_get_post_terms( $post->ID, self::TEAM_TYPE_TAXONOMY, array( 'fields' => 'slugs' ) );
		$department_slugs = wp_get_post_terms( $post->ID, self::TEAM_DEPARTMENT_TAXONOMY, array( 'fields' => 'slugs' ) );
		$role_title       = (string) get_post_meta( $post->ID, '_ink_team_role_title', true );
		$hook             = has_excerpt( $post ) ? $post->post_excerpt : '';
		$badges           = $this->get_member_badges( $post->ID );
		$group            = $this->is_founder_member( $type_slugs ) ? 'founders' : 'staff';
		$profile_link_label = (string) get_post_meta( $post->ID, '_ink_team_profile_link_label', true );
		$profile_link_url   = (string) get_post_meta( $post->ID, '_ink_team_profile_link_url', true );

		return array(
			'id'               => $post->ID,
			'name'             => get_the_title( $post ),
			'role_title'       => $role_title,
			'hook'             => $hook,
			'content'          => (string) $post->post_content,
			'badges'           => $badges,
			'type_slugs'       => is_wp_error( $type_slugs ) ? array() : $type_slugs,
			'department_slugs' => is_wp_error( $department_slugs ) ? array() : $department_slugs,
			'group'            => $group,
			'image_html'       => get_the_post_thumbnail(
				$post->ID,
				'large',
				array(
					'loading' => 'lazy',
					'alt'     => get_the_title( $post ),
				)
			),
			'profile_link_label' => $profile_link_label,
			'profile_link_url'   => $profile_link_url,
		);
	}

	/**
	 * @param int $post_id Team member post ID.
	 * @return array<int, string>
	 */
	private function get_member_badges( int $post_id ): array {
		$badges = array();

		foreach ( array( 'one', 'two', 'three' ) as $slot ) {
			$value = trim( (string) get_post_meta( $post_id, '_ink_team_badge_' . $slot, true ) );
			if ( '' !== $value ) {
				$badges[] = $value;
			}
		}

		return $badges;
	}

	/**
	 * @param array<int, string> $type_slugs Team type slugs.
	 * @return bool
	 */
	private function is_founder_member( array $type_slugs ): bool {
		return in_array( 'founder', $type_slugs, true );
	}

	/**
	 * @param array<string, mixed> $member Member data.
	 * @param string               $department Department filter.
	 * @param string               $group Group filter.
	 * @return bool
	 */
	private function matches_filters( array $member, string $department, string $group ): bool {
		$group               = $this->normalize_member_group_key( $group );
		$member_group        = $this->normalize_member_group_key( $member['group'] ?? 'staff' );
		$matches_department = 'all' === $department || in_array( $department, $member['department_slugs'], true );
		$matches_group      = 'all' === $group || $group === $member_group;

		return $matches_department && $matches_group;
	}

	private function normalize_member_group_key( $group ): string {
		$group = sanitize_key( (string) $group );

		if ( 'management' === $group ) {
			return 'founders';
		}

		if ( in_array( $group, array( 'all', 'founders', 'staff' ), true ) ) {
			return $group;
		}

		return 'all';
	}

	private function get_first_name( string $name ): string {
		$parts = preg_split( '/\s+/', trim( $name ) );
		return ! empty( $parts[0] ) ? $parts[0] : __( 'this member', 'foundation-elementor-plus' );
	}

	private function format_member_content( string $content ): string {
		$content = trim( $content );

		if ( '' === $content ) {
			return '';
		}

		if ( false === strpos( $content, '<p' ) ) {
			$content = wpautop( $content );
		}

		return wp_kses_post( do_shortcode( $content ) );
	}

	/**
	 * @return array<string, mixed>
	 */
	private function get_live_roles_feature_data(): array {
		$counts     = array(
			'open'       => 0,
			'processing' => 0,
			'closed'     => 0,
		);
		$open_titles = array();

		if ( ! post_type_exists( self::CAREER_POST_TYPE ) || ! taxonomy_exists( self::CAREER_STATUS_TAXONOMY ) ) {
			return array(
				'counts'     => $counts,
				'open_titles' => $open_titles,
			);
		}

		$roles = get_posts(
			array(
				'post_type'      => self::CAREER_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			)
		);

		foreach ( $roles as $role ) {
			$terms = wp_get_post_terms( $role->ID, self::CAREER_STATUS_TAXONOMY, array( 'fields' => 'slugs' ) );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				continue;
			}

			$status = (string) $terms[0];
			if ( 'reviewing-applicants' === $status ) {
				$counts['processing']++;
			} elseif ( 'closed' === $status ) {
				$counts['closed']++;
			} else {
				$counts['open']++;
				if ( count( $open_titles ) < 3 ) {
					$open_titles[] = get_the_title( $role );
				}
			}
		}

		return array(
			'counts'      => $counts,
			'open_titles' => $open_titles,
		);
	}
}
