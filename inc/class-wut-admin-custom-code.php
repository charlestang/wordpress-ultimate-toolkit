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

		// register an ajax form table.
		add_action( 'wp_ajax_wut_custom_code', array( $this, 'print_custom_form' ) );

		// add thickbox and its behaviors.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	public function admin_enqueue_scripts() {
		add_thickbox();
		ob_start();
		$this->javascript();
		$js = ob_get_contents();
		ob_end_clean();
		$js  = trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', $js ) );
		$ret = wp_add_inline_script( 'thickbox', $js );
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
		<div class="tablenav top">
			<a class="button thickbox" href="admin-ajax.php?action=wut_custom_code&KeepThis=true&TB_iframe=true&height=400&width=600"><?php _e( '+ Add New', 'wut' ); ?></a>
		</div>
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

	public function javascript() {
		?>
		<script>
		jQuery( document ).ready( function( $ ){
			var tbWindow;

			window.tb_position = function() {
				var width = $( window ).width(),
					H = $( window ).height() - ( ( 792 < width ) ? 60 : 20 ),
					W = ( 792 < width ) ? 772 : width - 20;

				tbWindow = $( '#TB_window' );

				if ( tbWindow.length ) {
					tbWindow.width( W ).height( H );
					$( '#TB_iframeContent' ).width( W ).height( H );
					tbWindow.css({
						'margin-left': '-' + parseInt( ( W / 2 ), 10 ) + 'px'
					});
					if ( typeof document.body.style.maxWidth !== 'undefined' ) {
						tbWindow.css({
							'top': '30px',
							'margin-top': '0'
						});
					}
				}

				return $( 'a.thickbox' ).each( function() {
					var href = $( this ).attr( 'href' );
					if ( ! href ) {
						return;
					}
					href = href.replace( /&width=[0-9]+/g, '' );
					href = href.replace( /&height=[0-9]+/g, '' );
					$(this).attr( 'href', href + '&width=' + W + '&height=' + ( H ) );
				});
			};

			$( window ).resize( function() {
				tb_position();
			});

		} );
		</script>
		<?php
	}

	public function print_custom_form() {
		$title   = '';
		$remarks = '';
		wp_enqueue_style( 'colors' );
		?>
		<html>
		<head>
			<?php wp_print_styles(); ?>
		</head>
		<body class="wp-core-ui"><div style="padding-left:20px;"><div class="wpbody"><div class="wpbody-content"><div class="wrap">
			<h1>Create New Custom Code</h1>
			<hr class="wp-header-end"/>
			<form method="post"><table class="form-table"><tbody>
				<tr>
					<th scope="row"><label for="custom-code-title"> Title: </label></th>
					<td><input 
							name="<?php echo $this->get_field_name( 'title' ); ?>" 
							type="text" id="custom-code-title" 
							value="<?php echo $title; ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="custom-code-remarks"> Remarks: </label></th>
					<td><input 
							name="<?php echo $this->get_field_name( 'remarks' ); ?>" 
							type="text" id="custom-code-remarks" 
							value="<?php echo $remarks; ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="custom-code-hooks"> Hook Position: </label></th>
					<td><select name="<?php echo $this->get_field_name( 'hooks' ); ?>" 
							type="text" id="custom-code-hooks">
							<option value="wp_head" selected="selected">Inject to header part.(wp_head)</option>
							<option value="wp_footer">Inject to footer part.(wp_footer)</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="custom-code-source"> Source Code: </label></th>
					<td><textarea
							name="<?php echo $this->get_field_name( 'source' ); ?>" 
							type="text" id="custom-code-source"></textarea></td>
				</tr>
			</tbody></table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="保存更改"></p>
			</form>
		</div></div></div></div></body>
		</html>
		<?php
		wp_die();
	}
}
