<?php

class WUT_Admin_About extends WUT_Admin_Panel {

	public function __construct() {
		parent::__construct( __( 'About', 'wordpress-ultimate-toolkit' ), '' );
		$this->id = 'admin-about';
	}

	public static function me() {
		return new self();
	}

	public function form( $options ) {
		?>
		<p><?php _e( 'Please leave me a message if you have any problem.', 'wordpress-ultimate-toolkit' ); ?>
			<a target="_blank" href="https://sexywp.com/wut"><?php _e( 'Plugin author\'s site.', 'wordpress-ultimate-toolkit' ); ?>
			</a></p>
		<p><a target="_blank" href="https://wordpress.org/plugins/wordpress-ultimate-toolkit/#reviews">
			<?php _e( 'Reviews', 'wordpress-ultimate-toolkit' ); ?></a>
			<?php _e( ' will be very appriciated. Thank you!', 'wordpress-ultimate-toolkit' ); ?></p>
		<?php
	}

	public function update( $new_options, $old_options ) {
		return array();
	}
}
