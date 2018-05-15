<?php

class HC_Privacy_Customizer {
	private $plugin_main_file;

	private $plugin_version = '1.1.0';

	public function __construct( $main_plugin_file ) {
		$this->init( $main_plugin_file );
	}

	public function init( $main_plugin_file ) {
		$this->plugin_main_file = $main_plugin_file;

		add_action( 'customize_register', array( $this, 'customize_register' ), 10, 1 );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'hc-privacy-compactor-backend', plugins_url( '/assets/js/backend.js', $this->plugin_main_file ), array( 'customize-controls', 'jquery' ), $this->plugin_version, true );

		wp_localize_script( 'hc-privacy-compactor-backend', 'hc_privacy_compactor', array(
			'privacy_url' => $this->get_privacy_policy_url(),
		) );
	}

	public function enqueue_styles() {
		if ( HC_Privacy_Compactor::is_privacy_page() ) {
			$customizer = get_theme_mod( 'hc_privacy_compactor' );

			$css = "
				.hc-accordion .hc-accordion-trigger {
					color: " . ( isset( $customizer['colors']['h2'] ) ? $customizer['colors']['h2'] : '#212121' ) . ";
					background: " . ( isset( $customizer['colors']['accordion']['default'] ) ? $customizer['colors']['accordion']['default'] : '#fff' ) . ";
				}
				.hc-accordion .hc-accordion-trigger:hover,
				.hc-accordion .hc-accordion-trigger:focus,
				.hc-accordion .hc-accordion-trigger:active {
					background: " . ( isset( $customizer['colors']['accordion']['focus'] ) ? $customizer['colors']['accordion']['focus'] : '#dedede' ) . ";
				}
				.hc-accordion,
				.hc-accordion p,
				.hc-accordion ul,
				.hc-accordion ol,
				.hc-accordion div,
				.hc-accordion span {
					font-size: " . ( isset( $customizer['fonts']['size']['default'] ) ? $customizer['fonts']['size']['default'] : 'inherit' ) . ";
				}
				.hc-accordion h2 {
					font-size: " . ( isset( $customizer['fonts']['size']['h2'] ) ? $customizer['fonts']['size']['h2'] : 'inherit' ) . ";
				}
				.hc-accordion h3 {
					font-size: " . ( isset( $customizer['fonts']['size']['h3'] ) ? $customizer['fonts']['size']['h3'] : 'inherit' ) . ";
				}
				.hc-accordion h4 {
					font-size: " . ( isset( $customizer['fonts']['size']['h4'] ) ? $customizer['fonts']['size']['h4'] : 'inherit' ) . ";
				}
				.hc-accordion h5 {
					font-size: " . ( isset( $customizer['fonts']['size']['h5'] ) ? $customizer['fonts']['size']['h5'] : 'inherit' ) . ";
				}
				.hc-accordion h6 {
					font-size: " . ( isset( $customizer['fonts']['size']['h6'] ) ? $customizer['fonts']['size']['h6'] : 'inherit' ) . ";
				}
				.hc-accordion .hc-accordion-trigger span {
					font-size: " . ( isset( $customizer['fonts']['size']['accordion'] ) ? $customizer['fonts']['size']['accordion'] : 'inherit' ) . ";
					line-height: " . ( isset( $customizer['fonts']['size']['accordion'] ) ? $customizer['fonts']['size']['accordion'] : 'inherit' ) . ";
				}
			";

			wp_add_inline_style(
				'hc-privacy-compactor',
				$css
			);
		}
	}

	public function customize_register( $wp_customize ) {
		$wp_customize->add_panel(
			'hc-privacy-compactor',
			array(
				'title' => __( 'Privacy Page', 'hc-privacy-compactor' ),
			)
		);

		$this->customizer_register_colors( $wp_customize );

		$this->customizer_register_fonts( $wp_customize );

		$this->customizer_register_settings( $wp_customize );
	}

	private function customizer_register_fonts( $wp_customize ) {
		$wp_customize->add_section(
			'hc-privacy-compactor-fonts',
			array(
				'title'       => __( 'Fonts', 'hc-privacy-compactor' ),
				'description' => __( 'If you wish to make changes to fonts, you may do so here, the fields are free-text so you may use any measurement you wish, the default values are to inherit what your theme uses for the best default compatibility.', 'hc-privacy-compactor' ),
				'panel'       => 'hc-privacy-compactor',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][default]',
			array(
				'default' => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][default]',
			array(
				'label'   => __( 'Font size', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][accordion]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][accordion]',
			array(
				'label'   => __( 'Section toggle', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][h2]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][h2]',
			array(
				'label'   => __( 'Heading 2', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][h3]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][h3]',
			array(
				'label'   => __( 'Heading 3', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][h4]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][h4]',
			array(
				'label'   => __( 'Heading 4', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][h5]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][h5]',
			array(
				'label'   => __( 'Heading 5', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[fonts][size][h6]',
			array(
				'default'   => 'inherit',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[fonts][size][h6]',
			array(
				'label'   => __( 'Heading 6', 'hc-privacy-compactor' ),
				'section' => 'hc-privacy-compactor-fonts',
			)
		);
	}

	private function customizer_register_colors( $wp_customize ) {
		$wp_customize->add_section(
			'hc-privacy-compactor-colors',
			array(
				'title' => __( 'Colors', 'hc-privacy-compactor' ),
				'panel' => 'hc-privacy-compactor',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[colors][h2]',
			array(
				'default'   => '#212121',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'hc_privacy_compactor[colors][h2]',
				array(
					'label'   => __( 'Heading text', 'hc-privacy-compactor' ),
					'section' => 'hc-privacy-compactor-colors',
				)
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[colors][accordion][default]',
			array(
				'default'   => '#fff',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'hc_privacy_compactor[colors][accordion][default]',
				array(
					'label'   => __( 'Heading background', 'hc-privacy-compactor' ),
					'section' => 'hc-privacy-compactor-colors',
				)
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[colors][accordion][focus]',
			array(
				'default'   => '#dedede',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'hc_privacy_compactor[colors][accordion][focus]',
				array(
					'label'   => __( 'Heading background (hover)', 'hc-privacy-compactor' ),
					'section' => 'hc-privacy-compactor-colors',
				)
			)
		);
	}

	private function customizer_register_settings( $wp_customize ) {
		$wp_customize->add_section(
			'hc-privacy-compactor-settings',
			array(
				'title' => __( 'Settings', 'hc-privacy-compactor' ),
				'panel' => 'hc-privacy-compactor',
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[settings][expand]',
			array(
				'default' => 'no',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[settings][expand]',
			array(
				'type'    => 'select',
				'section' => 'hc-privacy-compactor-settings',
				'label'   => __( 'Expand sections by default', 'hc-privacy-compactor' ),
				'choices' => array(
					'no'    => __( 'No', 'hc-privacy-compactor' ),
					'yes'   => __( 'Yes', 'hc-privacy-compactor' ),
					'first' => __( 'Only the first section', 'hc-privacy-compactor' ),
				),
			)
		);

		$wp_customize->add_setting(
			'hc_privacy_compactor[settings][splitter]',
			array(
				'default' => 'h2',
			)
		);
		$wp_customize->add_control(
			'hc_privacy_compactor[settings][splitter]',
			array(
				'type'        => 'select',
				'section'     => 'hc-privacy-compactor-settings',
				'label'       => __( 'Split sections by which heading type', 'hc-privacy-compactor' ),
				'description' => __( 'Some sites use different headings at different times, if you use a different heading you may pick the one you used here.', 'hc-privacy-compactor' ),
				'choices'     => array(
					'h2' => __( 'Heading 2', 'hc-privacy-compactor' ),
					'h3' => __( 'Heading 3', 'hc-privacy-compactor' ),
					'h4' => __( 'Heading 4', 'hc-privacy-compactor' ),
					'h5' => __( 'Heading 5', 'hc-privacy-compactor' ),
					'h6' => __( 'Heading 6', 'hc-privacy-compactor' ),
				)
			)
		);
	}

	function get_privacy_policy_url() {
		if ( function_exists( 'get_privacy_policy_url' ) ) {
			return get_privacy_policy_url();
		}

		$url            = '';
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );

		if ( ! empty( $policy_page_id ) && get_post_status( $policy_page_id ) === 'publish' ) {
			$url = (string) get_permalink( $policy_page_id );
		}

		return apply_filters( 'privacy_policy_url', $url, $policy_page_id );
	}
}
