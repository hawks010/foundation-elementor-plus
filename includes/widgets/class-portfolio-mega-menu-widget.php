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
		return esc_html__( 'Foundation Portfolio Mega Menu', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'portfolio', 'mega menu', 'header', 'case studies' );
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
				'default' => esc_html__( 'Selected work', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'   => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 2,
				'default' => esc_html__( 'Explore our portfolio', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'intro',
			array(
				'label'   => esc_html__( 'Intro', 'foundation-elementor-plus' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 3,
				'default' => esc_html__( 'Browse recent work, jump to the service area that fits, or head straight into the full archive.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'posts_to_show',
			array(
				'label'   => esc_html__( 'Portfolio Cards', 'foundation-elementor-plus' ),
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
				'default' => esc_html__( 'All work', 'foundation-elementor-plus' ),
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
					'url' => home_url( '/portfolio/' ),
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
				'default' => esc_html__( 'Ready to make something worth showing off?', 'foundation-elementor-plus' ),
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
				'default' => esc_html__( 'Open all work', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'secondary_link_url',
			array(
				'label'         => esc_html__( 'Secondary Link URL', 'foundation-elementor-plus' ),
				'type'          => Controls_Manager::URL,
				'default'       => array(
					'url' => home_url( '/portfolio/' ),
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
		$settings   = $this->get_settings_for_display();
		$widget_id  = 'foundation-portfolio-mega-menu-' . $this->get_id();
		$items      = $this->get_portfolio_items( $settings );
		$nav_links  = $this->get_nav_links( $settings );
		$popup_id   = ! empty( $settings['cta_popup_id'] ) ? absint( $settings['cta_popup_id'] ) : 0;
		$button_key = 'cta_button';
		$button_url = ! empty( $settings['cta_button_url']['url'] ) ? $settings['cta_button_url'] : array( 'url' => home_url( '/contact-us/' ) );
		$all_key    = 'all_work_button';
		$all_url    = ! empty( $settings['all_work_url']['url'] ) ? $settings['all_work_url'] : array( 'url' => home_url( '/portfolio/' ) );
		$sec_key    = 'secondary_link';
		$sec_url    = ! empty( $settings['secondary_link_url']['url'] ) ? $settings['secondary_link_url'] : array( 'url' => home_url( '/portfolio/' ) );

		$this->add_link_attributes( $button_key, $button_url );
		$this->add_link_attributes( $all_key, $all_url );
		$this->add_link_attributes( $sec_key, $sec_url );

		if ( $popup_id ) {
			wp_enqueue_script( 'foundation-elementor-plus-portfolio-mega-menu' );
		}
		?>
		<section id="<?php echo esc_attr( $widget_id ); ?>" class="foundation-portfolio-mega-menu" data-foundation-portfolio-mega-menu>
			<div class="foundation-portfolio-mega-menu__panel">
				<?php if ( 'yes' === ( $settings['show_nav'] ?? 'yes' ) && ! empty( $nav_links ) ) : ?>
					<nav class="foundation-portfolio-mega-menu__nav" aria-label="<?php echo esc_attr__( 'Portfolio menu links', 'foundation-elementor-plus' ); ?>">
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
							<a class="foundation-portfolio-mega-menu__project-link" <?php echo $this->get_render_attribute_string( $key ); ?> aria-label="<?php echo esc_attr( sprintf( __( 'Open portfolio project: %s', 'foundation-elementor-plus' ), $item['title'] ) ); ?>">
								<div class="foundation-portfolio-mega-menu__project-media">
									<?php if ( ! empty( $item['image'] ) ) : ?>
										<img class="foundation-portfolio-mega-menu__image" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['image_alt'] ); ?>" loading="lazy" />
									<?php else : ?>
										<div class="foundation-portfolio-mega-menu__image-placeholder" aria-hidden="true"></div>
									<?php endif; ?>
								</div>
								<div class="foundation-portfolio-mega-menu__project-copy">
									<p class="foundation-portfolio-mega-menu__breadcrumb">
										<span><?php echo esc_html__( 'Portfolio', 'foundation-elementor-plus' ); ?></span>
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
										<?php echo esc_html__( 'Open project', 'foundation-elementor-plus' ); ?>
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

	private function get_portfolio_items( array $settings ): array {
		$limit   = ! empty( $settings['posts_to_show'] ) ? min( 3, max( 1, (int) $settings['posts_to_show'] ) ) : 3;
		$orderby = 'menu_order' === ( $settings['order_by'] ?? 'date' ) ? 'menu_order' : 'date';
		$order   = 'menu_order' === $orderby ? 'ASC' : 'DESC';
		$query   = new \WP_Query(
			array(
				'post_type'           => 'ink_portfolio',
				'post_status'         => 'publish',
				'posts_per_page'      => $limit,
				'orderby'             => $orderby,
				'order'               => $order,
				'ignore_sticky_posts' => true,
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
			$terms    = get_the_terms( $post->ID, 'ink_portfolio_type' );
			$term     = ( ! is_wp_error( $terms ) && ! empty( $terms ) && $terms[0] instanceof \WP_Term ) ? $terms[0]->name : '';
			$excerpt  = trim( (string) get_post_meta( $post->ID, '_ink_portfolio_grid_description', true ) );

			if ( '' === $excerpt ) {
				$excerpt = $post->post_excerpt ? $post->post_excerpt : wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 16 );
			}

			$items[] = array(
				'title'     => get_the_title( $post ),
				'url'       => array( 'url' => get_permalink( $post ) ),
				'image'     => $image,
				'image_alt' => $alt ? $alt : get_the_title( $post ),
				'term'      => $term,
				'excerpt'   => $excerpt,
			);
		}

		wp_reset_postdata();

		return $items;
	}

	private function get_nav_links( array $settings ): array {
		$links = array();
		$all   = ! empty( $settings['all_work_url']['url'] ) ? $settings['all_work_url'] : array( 'url' => home_url( '/portfolio/' ) );

		$links[] = array(
			'label'  => ! empty( $settings['all_work_label'] ) ? $settings['all_work_label'] : esc_html__( 'All work', 'foundation-elementor-plus' ),
			'url'    => $all,
			'is_all' => true,
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'ink_portfolio_type',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) ) {
			$order_map = array(
				'web-branding' => 1,
				'marketing'    => 2,
				'it'           => 3,
			);

			usort(
				$terms,
				static function( $a, $b ) use ( $order_map ) {
					$a_order = $order_map[ $a->slug ] ?? 99;
					$b_order = $order_map[ $b->slug ] ?? 99;

					if ( $a_order === $b_order ) {
						return strcasecmp( $a->name, $b->name );
					}

					return $a_order <=> $b_order;
				}
			);

			foreach ( $terms as $term ) {
				if ( ! $term instanceof \WP_Term ) {
					continue;
				}

				$link = get_term_link( $term );

				if ( is_wp_error( $link ) ) {
					continue;
				}

				$links[] = array(
					'label' => $term->name,
					'url'   => array( 'url' => $link ),
				);
			}
		}

		return array_slice( $links, 0, 4 );
	}

	private function get_arrow_icon(): string {
		return '<svg class="foundation-portfolio-mega-menu__arrow" viewBox="0 0 24 24" focusable="false" aria-hidden="true"><path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path></svg>';
	}
}
