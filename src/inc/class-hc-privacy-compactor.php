<?php

class HC_Privacy_Compactor {
	private $plugin_main_file;

	private $plugin_version = '1.1.0';

	public function __construct( $plugin_main_file ) {
		$this->init( $plugin_main_file );
	}

	public function init( $plugin_main_file ) {
		global $wp_version;
		$this->plugin_main_file = $plugin_main_file;

		// Conditionally allow users to declare privacy pages before core implements this feature.
		if ( version_compare( $wp_version, '4.9.6', '<' ) ) {
			new HC_Privacy_Meta();
		}

		add_filter( 'the_content', array( $this, 'format_privacy_page' ), 10, 1 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function format_privacy_page( $policy_text ) {
		if ( ! HC_Privacy_Compactor::is_privacy_page() ) {
			return $policy_text;
		}

		$customizer = get_theme_mod( 'hc_privacy_compactor' );

		$output = '';

		$policy_text = wpautop( $policy_text );

		$header_wrap = '
		<dt role="heading" aria-level="2">
			<button aria-expanded="%3$s" class="hc-accordion-trigger" aria-controls="hc-accordion-block-%1$d" id="hc-accordion-heading-%1$d" type="button">
				<span class="title">
					%2$s
				</span>
				<span class="icon"></span>
			</button>
		</dt>
		';

		$content_wrap = '
		<dd id="hc-accordion-block-%1$d" role="region" aria-labelledby="hc-accordion-heading-%1$d" class="hc-accordion-panel" %3$s>
			<div>
				%2$s
			</div>
		</dd>
		';

		preg_match_all(
			sprintf(
				'/<%1$s>(.+?)<\/%1$s>/si',
				( isset( $customizer['settings']['splitter'] ) ? $customizer['settings']['splitter'] : 'h2' )
			),
			$policy_text,
			$titles
		);

		if ( isset( $titles[0][0] ) && stripos( $policy_text, $titles[0][0] ) > 0 ) {
			$output .= substr( $policy_text, 0, stripos( $policy_text, $titles[0][0] ) );
		}

		$output .= '<dl role="presentation" class="hc-accordion">';

		$first_iteration = true;

		for( $i = 0; $i < count( $titles[1] ); $i++ ) {
			$display_block = false;

			if ( $first_iteration && 'first' === strtolower( $customizer['settings']['expand'] ) ) {
				$display_block   = true;
				$first_iteration = false;
			}
			if ( 'yes' === strtolower( $customizer['settings']['expand'] ) ) {
				$display_block = true;
			}

			$output .= sprintf(
				$header_wrap,
				$i,
				$titles[1][ $i ],
				( false === $display_block ? 'false' : 'true' )
			);

			$start = stripos( $policy_text, $titles[0][ $i ] );
			$start+= strlen( $titles[0][ $i ] );

			if ( ! isset( $titles[1][ $i + 1 ] ) ) {
				$output .= sprintf(
					$content_wrap,
					$i,
					substr( $policy_text, $start ),
					( false === $display_block ? ' hidden="hidden"' : '' )
				);
			} else {
				$end = stripos( $policy_text, $titles[0][ $i + 1 ] );

				$output .= sprintf(
					$content_wrap,
					$i,
					substr( $policy_text, $start, ( $end - $start ) ),
					( false === $display_block ? ' hidden="hidden"' : '' )
				);
			}
		}

		$output .= '</dl>';

		return $output;
	}

	static function is_privacy_page( $page_id = null ) {
		$policy_post = (int) get_option( 'wp_page_for_privacy_policy', false );

		if ( null === $page_id ) {
			$page_id = get_the_ID();
		}

		$page_id = (int) $page_id;

		if ( false !== $policy_post && $policy_post === $page_id ) {
			return true;
		}

		return false;
	}

	public function enqueue_scripts() {
		if ( ! is_admin() ) {
			if ( ! is_singular() || ! HC_Privacy_Compactor::is_privacy_page() ) {
				return;
			}

			wp_enqueue_style( 'hc-privacy-compactor', plugins_url( '/assets/css/style.css', $this->plugin_main_file ), array(), $this->plugin_version );

			wp_enqueue_script( 'hc-privacy-compactor', plugins_url( '/assets/js/hc-privacy-compactor.js', $this->plugin_main_file ), array( 'jquery' ), $this->plugin_version, true );
		}
		else {
			wp_enqueue_script( 'hc-privacy-compactor-backend', plugins_url( '/assets/js/backend.js', $this->plugin_main_file ), array( 'jquery' ), $this->plugin_version, true );
		}
	}
}
