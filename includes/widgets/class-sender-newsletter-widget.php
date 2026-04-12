<?php

namespace FoundationElementorPlus\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use FoundationElementorPlus\Sender_Newsletter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sender_Newsletter_Widget extends Widget_Base {
	public function get_name() {
		return 'foundation-sender-newsletter';
	}

	public function get_title() {
		return esc_html__( 'Foundation Sender Newsletter', 'foundation-elementor-plus' );
	}

	public function get_icon() {
		return 'eicon-mail';
	}

	public function get_categories() {
		return array( \FoundationElementorPlus\Plugin::CATEGORY_SLUG );
	}

	public function get_keywords() {
		return array( 'foundation', 'sender', 'newsletter', 'signup', 'email' );
	}

	public function get_style_depends(): array {
		return array( 'foundation-elementor-plus-sender-newsletter' );
	}

	public function get_script_depends(): array {
		return array( 'foundation-elementor-plus-sender-newsletter' );
	}

	protected function register_controls() {
		$group_options = Sender_Newsletter::get_sender_groups();
		$default_group = Sender_Newsletter::get_default_group_id();

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Subscribe to our newsletter', 'foundation-elementor-plus' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'intro_text',
			array(
				'label'       => esc_html__( 'Intro Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Occasional updates, useful thoughts, no spam.', 'foundation-elementor-plus' ),
				'rows'        => 3,
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'       => esc_html__( 'Button Text', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Join the list', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'success_message',
			array(
				'label'       => esc_html__( 'Success Message', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'You’re in. Please check your inbox if confirmation is needed.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'privacy_note',
			array(
				'label'       => esc_html__( 'Privacy Note', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'We’ll keep it human, occasional, and worth opening.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'privacy_statement',
			array(
				'label'       => esc_html__( 'Privacy Statement', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'We value your privacy and will never send irrelevant information.', 'foundation-elementor-plus' ),
			)
		);

		$this->add_control(
			'show_first_name',
			array(
				'label'        => esc_html__( 'Show First Name Field', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'show_interest_options',
			array(
				'label'        => esc_html__( 'Show Contact Options', 'foundation-elementor-plus' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'foundation-elementor-plus' ),
				'label_off'    => esc_html__( 'Hide', 'foundation-elementor-plus' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'sender_group_id',
			array(
				'label'       => esc_html__( 'Sender Group', 'foundation-elementor-plus' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $group_options,
				'default'     => $default_group,
				'description' => esc_html__( 'Choose which connected Sender group this form should subscribe to.', 'foundation-elementor-plus' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_style_section',
			array(
				'label' => esc_html__( 'Layout', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'form_padding',
			array(
				'label'      => esc_html__( 'Form Padding', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-sender-newsletter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_gap',
			array(
				'label'      => esc_html__( 'Form Gap', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'rem' ),
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
					'{{WRAPPER}} .foundation-sender-newsletter' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_radius',
			array(
				'label'      => esc_html__( 'Form Radius', 'foundation-elementor-plus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 22,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 48,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .foundation-sender-newsletter' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_typography_section',
			array(
				'label' => esc_html__( 'Title Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .foundation-sender-newsletter__title',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'body_typography_section',
			array(
				'label' => esc_html__( 'Body Typography', 'foundation-elementor-plus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'body_typography',
				'selector' => '{{WRAPPER}} .foundation-sender-newsletter__text, {{WRAPPER}} .foundation-sender-newsletter input, {{WRAPPER}} .foundation-sender-newsletter button, {{WRAPPER}} .foundation-sender-newsletter__message, {{WRAPPER}} .foundation-sender-newsletter__privacy',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings       = $this->get_settings_for_display();
		$widget_id      = 'foundation-sender-newsletter-' . $this->get_id();
		$show_first     = isset( $settings['show_first_name'] ) && 'yes' === $settings['show_first_name'];
		$show_interests = isset( $settings['show_interest_options'] ) && 'yes' === $settings['show_interest_options'];
		$group_id       = ! empty( $settings['sender_group_id'] ) ? (string) $settings['sender_group_id'] : Sender_Newsletter::get_default_group_id();
		$ajax_url       = admin_url( 'admin-ajax.php' );
		$nonce          = wp_create_nonce( Sender_Newsletter::NONCE_ACTION );
		$success_copy   = ! empty( $settings['success_message'] ) ? (string) $settings['success_message'] : '';
		$button_text    = ! empty( $settings['button_text'] ) ? (string) $settings['button_text'] : esc_html__( 'Join the list', 'foundation-elementor-plus' );
		$privacy_line   = ! empty( $settings['privacy_statement'] ) ? (string) $settings['privacy_statement'] : '';
		$form_class     = 'foundation-sender-newsletter';
		$interest_items = Sender_Newsletter::get_interest_option_labels();

		if ( ! Sender_Newsletter::is_ready() ) {
			echo '<div class="foundation-sender-newsletter__notice">' . esc_html__( 'Sender is not configured yet. Connect Sender first, then use this widget.', 'foundation-elementor-plus' ) . '</div>';
			return;
		}
		?>
		<div
			id="<?php echo esc_attr( $widget_id ); ?>"
			class="foundation-sender-newsletter-wrap"
			data-foundation-sender-newsletter
			data-ajax-url="<?php echo esc_url( $ajax_url ); ?>"
			data-nonce="<?php echo esc_attr( $nonce ); ?>"
			data-group-id="<?php echo esc_attr( $group_id ); ?>"
			data-button-text="<?php echo esc_attr( $button_text ); ?>"
			data-success-message="<?php echo esc_attr( $success_copy ); ?>"
		>
			<form class="<?php echo esc_attr( $form_class ); ?>" method="post" aria-label="<?php echo esc_attr__( 'Newsletter signup form', 'foundation-elementor-plus' ); ?>" novalidate>
				<div class="foundation-sender-newsletter__intro">
					<?php if ( ! empty( $settings['title'] ) ) : ?>
						<h3 class="foundation-sender-newsletter__title"><?php echo esc_html( $settings['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( ! empty( $settings['intro_text'] ) ) : ?>
						<p class="foundation-sender-newsletter__text"><?php echo esc_html( $settings['intro_text'] ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( $show_first ) : ?>
					<div class="foundation-sender-newsletter__field">
						<label class="foundation-sender-newsletter__sr" for="<?php echo esc_attr( $widget_id ); ?>-first-name"><?php echo esc_html__( 'First name', 'foundation-elementor-plus' ); ?></label>
						<input type="text" id="<?php echo esc_attr( $widget_id ); ?>-first-name" name="first_name" placeholder="<?php echo esc_attr__( 'First name', 'foundation-elementor-plus' ); ?>" autocomplete="given-name">
					</div>
				<?php endif; ?>

				<div class="foundation-sender-newsletter__field foundation-sender-newsletter__field--email">
					<label class="foundation-sender-newsletter__sr" for="<?php echo esc_attr( $widget_id ); ?>-email"><?php echo esc_html__( 'Email address', 'foundation-elementor-plus' ); ?></label>
					<input type="email" id="<?php echo esc_attr( $widget_id ); ?>-email" name="email" placeholder="<?php echo esc_attr__( 'Email address', 'foundation-elementor-plus' ); ?>" autocomplete="email" required>
				</div>

				<div class="foundation-sender-newsletter__honeypot" aria-hidden="true">
					<label for="<?php echo esc_attr( $widget_id ); ?>-company"><?php echo esc_html__( 'Company', 'foundation-elementor-plus' ); ?></label>
					<input type="text" id="<?php echo esc_attr( $widget_id ); ?>-company" name="company" tabindex="-1" autocomplete="off">
				</div>

				<div class="foundation-sender-newsletter__action">
					<button type="submit"><?php echo esc_html( $button_text ); ?></button>
				</div>

				<?php if ( $show_interests && ! empty( $interest_items ) ) : ?>
					<div class="foundation-sender-newsletter__interests" aria-label="<?php echo esc_attr__( 'Contact preferences', 'foundation-elementor-plus' ); ?>">
						<?php foreach ( $interest_items as $interest_key => $interest_label ) : ?>
							<label class="foundation-sender-newsletter__check">
								<input type="checkbox" name="interests[]" value="<?php echo esc_attr( $interest_key ); ?>">
								<span class="foundation-sender-newsletter__check-box" aria-hidden="true"></span>
								<span class="foundation-sender-newsletter__check-label"><?php echo esc_html( $interest_label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="foundation-sender-newsletter__message" aria-live="polite"></div>

				<?php if ( '' !== $privacy_line ) : ?>
					<p class="foundation-sender-newsletter__privacy"><?php echo esc_html( $privacy_line ); ?></p>
				<?php endif; ?>
			</form>
		</div>
		<?php
	}
}
