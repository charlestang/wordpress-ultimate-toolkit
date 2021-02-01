<?php
/**
 * Admin panel: Custom code panel.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * Create the custom code panel.
 */
class WUT_Admin_Custom_Code extends WUT_Admin_Panel {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( __( 'Custom Code', 'wut' ), 'customcode' );
	}

	/**
	 * Get an instance of this.
	 *
	 * @return WUT_Admin_Panel
	 */
	public static function me() {
		return new self();
	}

	/**
	 * This method must be implemented.
	 * Print the form of option panel.
	 *
	 * @param array $options Retrieved options array.
	 * @return void
	 */
	public function form( $options ) {

	}

	/**
	 * Filter and sanitize user submitted options.
	 *
	 * @param array $new_options New options submitted.
	 * @param array $old_options Old options.
	 * @return array
	 */
	public function update( $new_options, $old_options ) {
		return $new_options;
	}

	/**
	 * Defaults of options.
	 *
	 * @return array
	 */
	public function default_options() {
		return array();
	}
}
