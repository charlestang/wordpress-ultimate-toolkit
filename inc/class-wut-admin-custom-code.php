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
		$length = count( $options );
		?>
		<hr class="wp-header-end">
		<div class="tablenav top"></div>
		<table class="wp-list-table widefat fixed striped table-view-list">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"/></td>
					<th scope="col" id="title" class="manage-column column-title column-primary">标题</th>
					<th scope="col" id="remark" class="manage-column column-remark">备忘</th>
					<th scope="col" id="hook" class="manage-column column-hook">钩子</th>
					<th scope="col" id="date" class="manage-column column-date">日期</th>
					<th scope="col" id="action" class="manage-column column-action">操作</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<?php if ( $length > 10 ) : ?>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column"><input id="cb-select-all-1" type="checkbox"/></td>
					<th scope="col" class="manage-column column-title column-primary">标题</th>
					<th scope="col" class="manage-column column-remark">备忘</th>
					<th scope="col" class="manage-column column-hook">钩子</th>
					<th scope="col" class="manage-column column-date">日期</th>
					<th scope="col" class="manage-column column-action">操作</th>
				</tr>
			</tfoot>
			<?php endif; ?>
		</table>
		<?php
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
