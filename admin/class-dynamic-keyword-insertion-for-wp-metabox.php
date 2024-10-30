<?php


/**
 * Register a meta box using a class.
 */
class dki4wp_Metabox {

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}

	}

	/**
	 * Meta box initialization.
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Adds the meta box.
	 */
	public function add_metabox() {
		add_meta_box(
			'my-meta-box',
			__( 'BVNode DKI4WP', 'textdomain' ),
			array( $this, 'render_metabox' ),
			array('post', 'page'),
			'side',
			'default'
		);

	}

	/**
	 * Renders the meta box.
	 */
	public function render_metabox( $post ) {
		
		$details = get_post_meta( $post->ID, '_namespace', true ); // Get the saved values
		?>
		<fieldset>
		<div style="display: flex; align-items: center; gap: 15px;">
			<label for="_namespace_custom_metabox">
				<?php
					// This runs the text through a translation and echoes it (for internationalization)
					esc_html_e( 'Set', '_namespace' );
				?>:
			</label>
			<?php
				// The `esc_attr()` function here escapes the data for
				// HTML attribute use to avoid unexpected issues
			?>
			<select
				type="text"
				name="_namespace_custom_metabox"
				id="_namespace_custom_metabox"
				style="flex-grow: 1;">
				<option value=''>Select Set</option>
				<?php if(isset(get_option('dki4wp_sets_data')['dki4wp_sets_sets']) && json_decode(get_option('dki4wp_sets_data')['dki4wp_sets_sets'], 1)):
				 foreach (json_decode(get_option('dki4wp_sets_data')['dki4wp_sets_sets'], 1) as $index => $set): ?>
					<option value="<?php echo esc_html($index); ?>" <?php echo $details == $index ? 'selected' : '' ?>><?php echo esc_html($set['name']); ?> ID: [<?php echo esc_html($index); ?>]</option>
				<?php endforeach; endif; ?>
				
			</select>
		</div>
	</fieldset>
	<?php
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );
	}

	/**
	 * Handles saving the meta box.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ?sanitize_key( $_POST['custom_nonce'] ): '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		// Check that our custom fields are being passed along
		// This is the `name` value array. We can grab all
		// of the fields and their values at once.
		if ( !isset( $_POST['_namespace_custom_metabox'] ) ) {
			return $post->ID;
		}

		/**
		 * Sanitize the submitted data
		 * This keeps malicious code out of our database.
		 * `wp_filter_post_kses` strips our dangerous server values
		 * and allows through anything you can include a post.
		 */
		$sanitized = sanitize_text_field( $_POST['_namespace_custom_metabox'] );

		// Save our submissions to the database
		update_post_meta( $post->ID, '_namespace', $sanitized );

	}
}

