<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Portfolio_Mega_Menu_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-portfolio-mega-menu';
	}

	public function get_title() {
		return esc_html__( 'Foundation Inkfire In Action Mega Menu', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'inkfire in action', 'portfolio', 'mega menu', 'header', 'case studies', 'stories', 'blog' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-portfolio-mega-menu' );
	}

	public function get_script_depends(): array {
		return array();
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'eyebrow',
			array(
				'label'   => esc_html__( 'Eyebrow', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Inkfire in action', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Latest work, stories & updates', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'   => esc_html__( 'Intro', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'Follow the latest from Inkfire, from portfolio launches and case studies to back-to-work stories, client stories, blog posts, and social updates.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'posts_to_show',
			array(
				'label'   => esc_html__( 'Activity Cards', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 3,
				'min'     => 1,
				'max'     => 3,
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order By', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Newest First', 'foundation-elementor-plus' ),
					'menu_order' => esc_html__( 'Menu Order', 'foundation-elementor-plus' ),
				),
			)
		);

		$this->add_control(
			'show_nav',
			array(
				'label'        => esc_html__( 'Show Menu Buttons', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'No', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'all_work_label',
			array(
				'label'   => esc_html__( 'All Work Label', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Inkfire in Action', 'foundation-elementor-plus' ),
				'condition' => array(
					'show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'all_work_url',
			array(
				'label'         => esc_html__( 'All Work URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'default'       => array(
					'url' => home_url( '/inkfire-in-action/' ),
				),
				'show_external' => false,
				'condition'     => array(
					'show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'cta_eyebrow',
			array(
				'label'   => esc_html__( 'CTA Eyebrow', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Work with us', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cta_title',
			array(
				'label'   => esc_html__( 'CTA Title', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Have something new to build?', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cta_body',
			array(
				'label'   => esc_html__( 'CTA Body', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'Bring your next website, campaign, or support project to a disabled-led team that cares about clarity, accessibility, and delivery.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cta_button_text',
			array(
				'label'   => esc_html__( 'CTA Button Text', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Start a project', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'cta_button_url',
			array(
				'label'         => esc_html__( 'CTA Button URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'default'       => array(
					'url' => home_url( '/contact-us/' ),
				),
				'show_external' => false,
			)
		);

		$this->add_control(
			'cta_popup_id',
			array(
				'label'       => esc_html__( 'Elementor Popup ID', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'description' => esc_html__( 'Optional. If set and Elementor Popup is available, the CTA opens that popup instead of the fallback URL.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'secondary_link_text',
			array(
				'label'   => esc_html__( 'Secondary Link Text', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Explore Inkfire in Action', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'secondary_link_url',
			array(
				'label'         => esc_html__( 'Secondary Link URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'default'       => array(
					'url' => home_url( '/inkfire-in-action/' ),
				),
				'show_external' => false,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'panel_padding',
			array(
				'label'      => esc_html__( 'Panel Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'default'    => array(
					'top'      => 24,
					'right'    => 24,
					'bottom'   => 24,
					'left'     => 24,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mega-menu__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'grid_gap',
			array(
				'label'      => esc_html__( 'Grid Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-portfolio-mega-menu' => '--foundation-portfolio-mega-menu-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_nav_typography',
			array(
				'label' => esc_html__( 'Navigation Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'nav_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__nav-link',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_intro_typography',
			array(
				'label' => esc_html__( 'Intro Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_eyebrow_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__eyebrow, {{WRAPPER}} .foundation-portfolio-mega-menu__cta-eyebrow, {{WRAPPER}} .foundation-portfolio-mega-menu__breadcrumb',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_title_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__title, {{WRAPPER}} .foundation-portfolio-mega-menu__cta-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'intro_body_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__intro, {{WRAPPER}} .foundation-portfolio-mega-menu__cta-copy',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_card_typography',
			array(
				'label' => esc_html__( 'Card Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'project_title_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__project-title',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'project_body_typography',
				'selector' => '{{WRAPPER}} .foundation-portfolio-mega-menu__project-excerpt',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings   = $this->normalize_inkfire_action_settings( $this->get_settings_for_display() );
		$widget_id  = 'foundation-portfolio-mega-menu-' . $this->get_id();
		$items      = $this->get_activity_items( $settings );
		$nav_links  = $this->get_nav_links( $settings );
		$socials    = $this->get_social_links();
		$popup_id   = ! empty( $settings['cta_popup_id'] ) ? absint( $settings['cta_popup_id'] ) : 0;
		$button_key = 'cta_button';
		$button_url = ! empty( $settings['cta_button_url']['url'] ) ? $settings['cta_button_url'] : array( 'url' => home_url( '/contact-us/' ) );
		$all_key    = 'all_work_button';
		$all_url    = ! empty( $settings['all_work_url']['url'] ) ? $settings['all_work_url'] : array( 'url' => home_url( '/inkfire-in-action/' ) );
		$sec_key    = 'secondary_link';
		$sec_url    = ! empty( $settings['secondary_link_url']['url'] ) ? $settings['secondary_link_url'] : array( 'url' => home_url( '/inkfire-in-action/' ) );

		$this->add_link_attributes( $button_key, $button_url );
		$this->add_link_attributes( $all_key, $all_url );
		$this->add_link_attributes( $sec_key, $sec_url );

		if ( $popup_id ) {
			wp_enqueue_script( 'foundation-elementor-plus-portfolio-mega-menu' );
		}
		?>
		<section id="<?php echo esc_attr( $widget_id ); ?>" class="foundation-portfolio-mega-menu" data-foundation-portfolio-mega-menu>
			<div class="foundation-portfolio-mega-menu__panel">
				<?php if ( ( 'yes' === ( $settings['show_nav'] ?? 'yes' ) && ! empty( $nav_links ) ) || ! empty( $socials ) ) : ?>
					<div class="foundation-portfolio-mega-menu__topbar">
						<?php if ( 'yes' === ( $settings['show_nav'] ?? 'yes' ) && ! empty( $nav_links ) ) : ?>
							<nav class="foundation-portfolio-mega-menu__nav" aria-label="<?php echo esc_attr__( 'Inkfire In Action menu links', 'foundation-elementor-plus' ); ?>">
								<?php foreach ( $nav_links as $index => $link ) : ?>
									<?php
									$key = 'nav_link_' . $index;
									$this->add_link_attributes( $key, $link['url'] );
									?>
									<a class="foundation-portfolio-mega-menu__nav-link<?php echo ! empty( $link['is_all'] ) ? ' is-all' : ''; ?>" <?php echo $this->get_render_attribute_string( $key ); ?>>
										<?php echo esc_html( $link['label'] ); ?>
									</a>
								<?php endforeach; ?>
							</nav>
						<?php endif; ?>

						<?php if ( ! empty( $socials ) ) : ?>
							<div class="foundation-portfolio-mega-menu__socials" aria-label="<?php esc_attr_e( 'Inkfire social links', 'foundation-elementor-plus' ); ?>">
								<?php foreach ( $socials as $social ) : ?>
									<a class="foundation-portfolio-mega-menu__social-link" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>" title="<?php echo esc_attr( $social['label'] ); ?>">
										<?php echo $this->get_social_icon( $social['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<span class="screen-reader-text"><?php echo esc_html( $social['label'] ); ?></span>
									</a>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="foundation-portfolio-mega-menu__grid">
					<div class="foundation-portfolio-mega-menu__intro-card foundation-portfolio-mega-menu__card foundation-portfolio-mega-menu__card--cta">
						<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
							<p class="foundation-portfolio-mega-menu__eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<h3 class="foundation-portfolio-mega-menu__title"><?php echo wp_kses_post( nl2br( esc_html( $settings['title'] ) ) ); ?></h3>
						<?php endif; ?>

						<?php if ( ! empty( $settings['intro'] ) ) : ?>
							<p class="foundation-portfolio-mega-menu__intro"><?php echo esc_html( $settings['intro'] ); ?></p>
						<?php endif; ?>

						<div class="foundation-portfolio-mega-menu__cta-box">
							<?php if ( ! empty( $settings['cta_eyebrow'] ) ) : ?>
								<p class="foundation-portfolio-mega-menu__cta-eyebrow"><?php echo esc_html( $settings['cta_eyebrow'] ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $settings['cta_title'] ) ) : ?>
								<h4 class="foundation-portfolio-mega-menu__cta-title"><?php echo wp_kses_post( nl2br( esc_html( $settings['cta_title'] ) ) ); ?></h4>
							<?php endif; ?>

							<?php if ( ! empty( $settings['cta_body'] ) ) : ?>
								<p class="foundation-portfolio-mega-menu__cta-copy"><?php echo esc_html( $settings['cta_body'] ); ?></p>
							<?php endif; ?>

							<div class="foundation-portfolio-mega-menu__cta-actions">
								<a
									class="foundation-portfolio-mega-menu__primary"
									<?php echo $this->get_render_attribute_string( $button_key ); ?>
									<?php echo $popup_id ? ' data-foundation-portfolio-menu-popup="' . esc_attr( (string) $popup_id ) . '"' : ''; ?>
								>
									<span><?php echo esc_html( $settings['cta_button_text'] ?? esc_html__( 'Start a project', 'foundation-elementor-plus' ) ); ?></span>
									<?php echo $this->get_arrow_icon(); ?>
								</a>

								<?php if ( ! empty( $settings['secondary_link_text'] ) ) : ?>
									<a class="foundation-portfolio-mega-menu__secondary" <?php echo $this->get_render_attribute_string( $sec_key ); ?>>
										<span><?php echo esc_html( $settings['secondary_link_text'] ); ?></span>
										<?php echo $this->get_arrow_icon(); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$key = 'portfolio_item_' . $index;
						$this->add_link_attributes( $key, $item['url'] );
						?>
						<article class="foundation-portfolio-mega-menu__card foundation-portfolio-mega-menu__card--project">
							<a class="foundation-portfolio-mega-menu__project-link" <?php echo $this->get_render_attribute_string( $key ); ?> aria-label="<?php echo esc_attr( sprintf( __( 'Open Inkfire In Action item: %s', 'foundation-elementor-plus' ), $item['title'] ) ); ?>">
								<div class="foundation-portfolio-mega-menu__project-media">
									<?php if ( ! empty( $item['image'] ) ) : ?>
										<img class="foundation-portfolio-mega-menu__image" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['image_alt'] ); ?>" loading="lazy" />
									<?php else : ?>
										<div class="foundation-portfolio-mega-menu__image-placeholder" aria-hidden="true"></div>
									<?php endif; ?>
								</div>
								<div class="foundation-portfolio-mega-menu__project-copy">
									<p class="foundation-portfolio-mega-menu__breadcrumb">
										<span><?php echo esc_html( $item['section'] ); ?></span>
										<?php if ( ! empty( $item['term'] ) ) : ?>
											<span class="foundation-portfolio-mega-menu__breadcrumb-sep" aria-hidden="true">/</span>
											<span><?php echo esc_html( $item['term'] ); ?></span>
										<?php endif; ?>
									</p>
									<h4 class="foundation-portfolio-mega-menu__project-title"><?php echo esc_html( $item['title'] ); ?></h4>
									<?php if ( ! empty( $item['excerpt'] ) ) : ?>
										<p class="foundation-portfolio-mega-menu__project-excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
									<?php endif; ?>
									<span class="foundation-portfolio-mega-menu__project-open">
										<?php echo esc_html__( 'Open story', 'foundation-elementor-plus' ); ?>
										<?php echo $this->get_arrow_icon(); ?>
									</span>
								</div>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	private function normalize_inkfire_action_settings( array $settings ): array {
		$text_replacements = array(
			'eyebrow'             => array(
				'old' => array( 'Selected work' ),
				'new' => esc_html__( 'Inkfire in action', 'foundation-elementor-plus' ),
			),
			'title'               => array(
				'old' => array( 'Explore our portfolio' ),
				'new' => esc_html__( 'Latest work, stories & updates', 'foundation-elementor-plus' ),
			),
			'intro'               => array(
				'old' => array( 'Browse recent work, jump to the service area that fits, or head straight into the full archive.', 'Browse the latest case studies, jump by discipline, or head straight into the full archive.' ),
				'new' => esc_html__( 'Follow the latest from Inkfire, from portfolio launches and case studies to back-to-work stories, client stories, blog posts, and social updates.', 'foundation-elementor-plus' ),
			),
			'all_work_label'      => array(
				'old' => array( 'All work', 'all work', 'open all', 'Open all' ),
				'new' => esc_html__( 'Inkfire in Action', 'foundation-elementor-plus' ),
			),
			'cta_title'           => array(
				'old' => array( 'Ready to make something worth showing off?', 'Need something bespoke?' ),
				'new' => esc_html__( 'Have something new to build?', 'foundation-elementor-plus' ),
			),
			'secondary_link_text' => array(
				'old' => array( 'Open all work', 'Explore all work' ),
				'new' => esc_html__( 'Explore Inkfire in Action', 'foundation-elementor-plus' ),
			),
		);

		foreach ( $text_replacements as $key => $replacement ) {
			$current = isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';

			if ( '' === $current || in_array( $current, $replacement['old'], true ) ) {
				$settings[ $key ] = $replacement['new'];
			}
		}

		foreach ( array( 'all_work_url', 'secondary_link_url' ) as $link_key ) {
			$url = isset( $settings[ $link_key ]['url'] ) ? trim( (string) $settings[ $link_key ]['url'] ) : '';

			if ( '' === $url || $this->is_portfolio_archive_url( $url ) ) {
				$settings[ $link_key ] = array( 'url' => home_url( '/inkfire-in-action/' ) );
			}
		}

		if ( empty( $settings['order_by'] ) || 'menu_order' === $settings['order_by'] ) {
			$settings['order_by'] = 'date';
		}

		return $settings;
	}

	private function is_portfolio_archive_url( string $url ): bool {
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );

		return in_array( untrailingslashit( $path ), array( '/portfolio', 'portfolio' ), true );
	}

	private function get_activity_items( array $settings ): array {
		$limit   = ! empty( $settings['posts_to_show'] ) ? min( 3, max( 1, (int) $settings['posts_to_show'] ) ) : 3;
		$orderby = 'menu_order' === ( $settings['order_by'] ?? 'date' ) ? 'menu_order' : 'date';
		$order   = 'menu_order' === $orderby ? 'ASC' : 'DESC';
		$query   = new \WP_Query(
			array(
				'post_type'           => array( 'ink_portfolio', 'post' ),
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'orderby'             => $orderby,
				'order'               => $order,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		$items   = array();

		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			$image_id = get_post_thumbnail_id( $post->ID );
			$image    = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '';
			$alt      = $image_id ? (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
			$section  = esc_html__( 'Latest activity', 'foundation-elementor-plus' );
			$term     = '';

			if ( 'ink_portfolio' === $post->post_type ) {
				$terms   = get_the_terms( $post->ID, 'ink_portfolio_type' );
				$section = esc_html__( 'Portfolio', 'foundation-elementor-plus' );
				$term    = ( ! is_wp_error( $terms ) && ! empty( $terms ) && $terms[0] instanceof \WP_Term ) ? $terms[0]->name : '';
			} else {
				$terms   = get_the_category( $post->ID );
				$section = esc_html__( 'Stories', 'foundation-elementor-plus' );

				foreach ( $terms as $term_candidate ) {
					if ( in_array( $term_candidate->slug, array( 'blog', 'featured' ), true ) ) {
						continue;
					}

					$term = $term_candidate->name;
					break;
				}

				if ( '' === $term && ! empty( $terms ) && $terms[0] instanceof \WP_Term ) {
					$term = $terms[0]->name;
				}
			}

			$excerpt  = trim( (string) get_post_meta( $post->ID, '_ink_portfolio_grid_description', true ) );

			if ( '' === $excerpt ) {
				$excerpt = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 16 );
			}

			$items[] = array(
				'title'     => get_the_title( $post ),
				'url'       => array( 'url' => get_permalink( $post ) ),
				'image'     => $image,
				'image_alt' => $alt ? $alt : get_the_title( $post ),
				'section'   => $section,
				'term'      => $term,
				'excerpt'   => $excerpt,
			);
		}

		wp_reset_postdata();

		return $items;
	}

	private function get_nav_links( array $settings ): array {
		$links = array();
		$all   = ! empty( $settings['all_work_url']['url'] ) ? $settings['all_work_url'] : array( 'url' => home_url( '/inkfire-in-action/' ) );

		$links[] = array(
			'label'  => ! empty( $settings['all_work_label'] ) ? $settings['all_work_label'] : esc_html__( 'Inkfire in Action', 'foundation-elementor-plus' ),
			'url'    => $all,
			'is_all' => true,
		);

		$links = array_merge(
			$links,
			array(
				array(
					'label' => esc_html__( 'Portfolio', 'foundation-elementor-plus' ),
					'url'   => array( 'url' => home_url( '/portfolio/' ) ),
				),
				array(
					'label' => esc_html__( 'Case studies', 'foundation-elementor-plus' ),
					'url'   => array( 'url' => $this->get_category_url_by_slug( 'case-studies' ) ),
				),
				array(
					'label' => esc_html__( 'Back to work', 'foundation-elementor-plus' ),
					'url'   => array( 'url' => $this->get_category_url_by_slug( 'back-to-work' ) ),
				),
				array(
					'label' => esc_html__( 'Client stories', 'foundation-elementor-plus' ),
					'url'   => array( 'url' => $this->get_page_url_by_path( 'client-stories' ) ),
				),
				array(
					'label' => esc_html__( 'Latest posts', 'foundation-elementor-plus' ),
					'url'   => array( 'url' => $this->get_page_url_by_path( 'blog' ) ),
				),
			)
		);

		return $links;
	}

	private function get_category_url_by_slug( string $slug ): string {
		$term = get_category_by_slug( $slug );

		if ( $term instanceof \WP_Term ) {
			$link = get_category_link( $term );

			if ( ! is_wp_error( $link ) ) {
				return $link;
			}
		}

		return home_url( '/category/' . trim( $slug, '/' ) . '/' );
	}

	private function get_page_url_by_path( string $path ): string {
		$page = get_page_by_path( $path );

		if ( $page instanceof \WP_Post ) {
			return get_permalink( $page );
		}

		return home_url( '/' . trim( $path, '/' ) . '/' );
	}

	private function get_social_links(): array {
		return array(
			array(
				'label' => esc_html__( 'LinkedIn', 'foundation-elementor-plus' ),
				'url'   => 'https://uk.linkedin.com/company/inkfire',
				'icon'  => 'linkedin',
			),
			array(
				'label' => esc_html__( 'Instagram', 'foundation-elementor-plus' ),
				'url'   => 'https://www.instagram.com/inkfirelimited/',
				'icon'  => 'instagram',
			),
			array(
				'label' => esc_html__( 'Facebook', 'foundation-elementor-plus' ),
				'url'   => 'https://facebook.com/inkfirelimited',
				'icon'  => 'facebook',
			),
			array(
				'label' => esc_html__( 'X', 'foundation-elementor-plus' ),
				'url'   => 'https://twitter.com/Inkfirelimited',
				'icon'  => 'x',
			),
		);
	}

	private function get_social_icon( string $icon ): string {
		$icons = array(
			'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M13.5 21v-8.2h2.77l.41-3.2H13.5V7.56c0-.93.26-1.56 1.59-1.56h1.69V3.14c-.82-.09-1.65-.13-2.48-.12-2.45 0-4.13 1.49-4.13 4.24V9.6H7.4v3.2h2.77V21h3.33Z"/></svg>',
			'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5Zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7Zm5 3.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5Zm0 2A2.5 2.5 0 1 0 14.5 12 2.5 2.5 0 0 0 12 9.5Zm5.25-3a1.25 1.25 0 1 1-1.25 1.25 1.25 1.25 0 0 1 1.25-1.25Z"/></svg>',
			'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.94 8.5A1.56 1.56 0 1 1 6.9 5.37a1.56 1.56 0 0 1 .04 3.13ZM5.5 10h2.88v8.5H5.5V10Zm4.7 0h2.76v1.16h.04c.38-.73 1.32-1.5 2.72-1.5 2.91 0 3.45 1.92 3.45 4.42v4.42h-2.88v-3.92c0-.93-.02-2.13-1.3-2.13s-1.5 1.01-1.5 2.06v3.99H10.2V10Z"/></svg>',
			'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m4 4 6.92 9.22L4.44 20h2.44l5.16-5.4L16.1 20H20l-7.25-9.66L18.8 4h-2.44l-4.73 4.95L7.9 4H4Zm3.12 1.8h1.88l7.88 12.4H15L7.12 5.8Z"/></svg>',
		);

		return $icons[ $icon ] ?? $icons['linkedin'];
	}

	private function get_arrow_icon(): string {
		return '<svg class="foundation-portfolio-mega-menu__arrow" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>';
	}
}
