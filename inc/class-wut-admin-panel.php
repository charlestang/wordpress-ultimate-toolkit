<?php
/**
 * Admin Panel: Abstract class
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * This is the abstract class of admin panel.
 */
abstract class WUT_Admin_Panel {

	public $title;

	public $id;

	public $option_name;

	public function __construct( $title, $option_name = '' ) {
		$this->title = $title;
		$this->id    = 'admin-' . implode(
			'-',
			array_map(
				function( $e ) {
					return strtolower( $e );
				},
				explode( ' ', $title )
			)
		);
		if ( empty( $option_name ) ) {
			$this->option_name = $this->id;
		} else {
			$this->option_name = $option_name;
		}
		WUT_Option_Manager::me()->register_defaults( $this->option_name, $this->default_options() );
	}

	public function get_tab_anchor() {
		return '#' . $this->id;
	}

	abstract public function form( $options );

	abstract public function update( $new_options, $old_options );

	abstract public function default_options();

	public function print_form_table() {
		$options = WUT_Option_Manager::me()->get_options_by_key( $this->option_name );
		$this->form( $options );
	}

	public function get_field_id( $field ) {
		return $this->id . '[' . $field . ']';
	}

	public function get_field_name( $field ) {
		return $this->id . '[' . $field . ']';
	}

	public function process_submit() {
		$new_options = $this->retrieve_submit();
		$manager     = WUT_Option_Manager::me();
		$processed   = $this->update( $new_options, $manager->get_options_by_key( $this->option_name ) );
		$manager->set_options_by_key( $this->option_name, $processed );
	}

	public function retrieve_submit() {
		return isset( $_POST[ $this->id ] ) ? wp_unslash( $_POST[ $this->id ] ) : array();
	}
}
