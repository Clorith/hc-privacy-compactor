<?php

class HC_Privacy_Meta {
	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! isset( $_POST['hc-privacy-page-nonce'] ) || ! wp_verify_nonce( $_POST['hc-privacy-page-nonce'], 'hc-privacy-page' ) ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}


		if ( ! isset( $_POST['hc-privacy-page-selected'] ) ) {
			if ( HC_Privacy_Compactor::is_privacy_page( $post_id ) ) {
				delete_option( 'wp_page_for_privacy_policy' );
			}

			return $post_id;
		}

		update_option( 'wp_page_for_privacy_policy', $post_id );

		return $post_id;
	}

	public function add_meta_boxes() {
		add_meta_box(
			'hc_privacy_mark',
			__( 'Privacy page', 'hc-privacy-compactor' ),
			array( $this, 'meta_html' ),
			'page',
			'side'
		);
	}

	public function meta_html( $post ) {
		wp_nonce_field( 'hc-privacy-page', 'hc-privacy-page-nonce' );
?>

		<p>
			<label>
				<input type="checkbox" name="hc-privacy-page-selected" <?php echo ( HC_Privacy_Compactor::is_privacy_page( $post->ID ) ? 'checked="checked"' : '' ); ?>>
				<?php esc_html_e( 'Use this as your Privacy Policy page', 'hc-privacy-compactor' ); ?>
			</label>
		</p>

<?php
	}
}
