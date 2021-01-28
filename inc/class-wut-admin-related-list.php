<?php
/**
 * Admin panel: related posts list.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * Create the related posts list option page.
 */
class WUT_Admin_Related_List extends WUT_Admin_Panel {

	/**
	 * Constructor of Related Posts option panel.
	 */
	public function __construct() {
		parent::__construct( __( 'Related Posts List', 'wut' ) );
	}

	public static function me() {
		return new self();
	}

	/**
	 * Option page.
	 *
	 * @param array $options Related posts list options.
	 * @return void
	 */
	public function form( $options ) {
		$enabled = isset( $options['enabled'] ) ? (bool) $options['enabled'] : false;
		$title   = isset( $options['title'] ) ? $options['title'] : '';
		$number  = isset( $options['number'] ) ? absint( $options['number'] ) : 5;
		?>
		<table class="form-table" role="presentation"><tbody>
			<tr valign="top">
				<th scope="row"><label for="related_list_enabled"><?php _e( 'Enable this feature', 'wut' ); ?></label></th>
				<td><input
						name="<?php echo $this->get_field_name( 'enabled' ); ?>"
						type="hidden"
						value="0"/>
					<input
						id="related_list_enabled"
						name="<?php echo $this->get_field_name( 'enabled' ); ?>"
						type="checkbox"
						value="1"<?php checked( $enabled ); ?>/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="related_list_number"><?php _e( 'Number of posts', 'wut' ); ?></label></th>
				<td><input
					id="related_list_number"
					name="<?php echo $this->get_field_name( 'number' ); ?>"
					type="text" size="10"
					value="<?php echo $number; ?>"/></td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="related_list_title"><?php _e( 'Related list title', 'wut' ); ?></label></th>
				<td><input
					id="related_list_title"
					name="<?php echo $this->get_field_name( 'title' ); ?>"
					type="text" class="regular-text"
					value="<?php echo esc_attr( $title ); ?>"/></td>
			</tr>
		</tbody></table>
		<?php
	}

	/**
	 * Filter and sanitize user submitted option values.
	 *
	 * @param array $new_options User submitted options.
	 * @param array $old_options Original options.
	 * @return array
	 */
	public function update( $new_options, $old_options ) {
		return $new_options;
	}

	/**
	 * Default options of related posts list.
	 *
	 * @return array
	 */
	public function default_options() {
		return array(
			'enabled' => false,
			'title'   => __( 'Related Posts', 'wut' ),
		);
	}
}
