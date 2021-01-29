<?php
/**
 * Admin panel of this plugin.
 *
 * @package WordPress_Ultimate_Toolkit
 * @subpackage admin
 */

/**
 * Admin pages of the WordPress Ultimate Toolkit.
 *
 * WordPress Ultimate Toolkit has its own top level menu, because of its huge
 * amount of functionalities.
 *
 * The top level menu called WUT Opitons, and other submenu items are all under
 * this menu.
 */
class WUT_Admin {

	/**
	 * Hold the options. This the reference of the options, not the copy of it.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var array
	 */
	protected $options;

	/**
	 * Option tabs
	 *
	 * @var WUT_Admin_Panel[]
	 */
	protected $tabs = array();

	/**
	 * Add an entry point of admin panel of this plugin to WordPress admin area,
	 * or add admin only features to WordPress admin area.
	 *
	 * @return void
	 */
	public function register_admin_entry() {
		if ( is_admin() ) {
			add_action(
				'admin_menu',
				function() {
					add_options_page(
						__( 'WordPress Ultimate Toolkit', 'wut' ),
						__( 'WP Ultimate Toolkit', 'wut' ),
						'activate_plugins',
						'wut-options-page',
						array( $this, 'options_page' ),
					);
				}
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueueu_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts to admin page.
	 *
	 * @return void
	 */
	public function admin_enqueueu_scripts() {
		wp_enqueue_script( 'jquery-ui-tabs' );
	}

	/**
	 * Register option tabs to option page.
	 * The whole options page is designed a single page, every part
	 * of options will be managed by tab.
	 *
	 * @return void
	 */
	public function register_options_tabs() {
		$this->tabs[] = new WUT_Admin_Excerption();
		$this->tabs[] = new WUT_Admin_Related_List();
	}

	/**
	 * Print navigation tabs.
	 *
	 * @return void
	 */
	public function print_tab_nav() {
		$html = '';
		foreach ( $this->tabs as $tab ) {
			$html .= '<li><a href="' . $tab->get_tab_anchor() . '" class="nav-tab">';
			$html .= $tab->title;
			$html .= '</a></li>' . PHP_EOL;
		}
		?>
		<h2 class="nav-tab-wrapper"><ul>
			<?php echo $html; ?>
		</ul></h2>
		<?php
	}

	/**
	 * Print tab panels.
	 *
	 * @return void
	 */
	public function print_tab_panels() {
		$html = '';
		foreach ( $this->tabs as $tab ) {
			?>
			<div id="<?php echo $tab->id; ?>">
				<?php $tab->print_form_table(); ?>
			</div>
			<?php
		}
	}

	/**
	 * Process user submitted form data.
	 * This method will dispatch data to each option panel object.
	 *
	 * @return string
	 */
	public function process_submit_and_save() {
		$ret = true;
		$msg = '';
		if ( isset( $_POST['action'] )
			&& 'update' === $_POST['action'] ) {

			if ( isset( $_POST['_wpnonce'] )
				&& wp_verify_nonce( $_POST['_wpnonce'] ) ) {
				foreach ( $this->tabs as $tab ) {
					$tab->process_submit();
				}
				$ret = WUT_Option_Manager::me()->save_options();
				if ( ! $ret ) {
					$msg = __( 'Update failed. Options are not changed or database error occured.', 'wut' );
				} else {
					$msg = __( 'Options saved.', 'wut' );
				}
			} else {
				$ret = false;
				$msg = __( 'Nonce verify failed.', 'wut' );
			}
		}
		return array( $ret, $msg );
	}

	/**
	 * Show a dismissible notice or indismissible error message.
	 *
	 * @param string  $message The message content.
	 * @param boolean $notice Show notice or error.
	 * @return void
	 */
	public function print_message( $message, $notice = true ) {
		if ( $notice ) {
			$class = 'notice';
		} else {
			$class = 'error';
		}
		if ( ! empty( $message ) ) {
			?>
			<div id="message" class="updated <?php echo $class; ?> is-dismissible">
				<p><?php echo $message; ?></p>
			</div>
			<?php
		}
	}

	/**
	 * All WUT options will be set on this page, and seperated by tabs.
	 *
	 * @return void
	 */
	public function options_page() {
		$this->register_options_tabs();
		list( $ret, $msg) = $this->process_submit_and_save();
		?>
		<div class="wrap wut-tabs">
			<h1><?php echo __( 'WordPress Ultimate Toolkit Options', 'wut' ); ?></h1>
			<?php $this->print_message( $msg, $ret ); ?>
			<?php $this->print_tab_nav(); ?>
			<form method="post">
				<input type="hidden" name="action" value="update">
				<?php wp_nonce_field(); ?>
				<?php $this->print_tab_panels(); ?>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
				</p>
			</form>
		</div>
		<script>
			(function($) {
				$(function(){
					$('.wut-tabs').tabs({
						active: 0,
						show: { effect: "fadeIn", duration: 300 },
						activate: function( event, ui ) {
							$(ui.newTab).find('a').addClass('nav-tab-active');
							// This :focus pseudo class of <a> element is ridiculous.
							// I have to remove box-shadow style mannually.
							$(ui.newTab).find('a').css('box-shadow', 'none');
							$(ui.oldTab).find('a').removeClass('nav-tab-active');
						}
					});
					$('a.nav-tab:first').addClass('nav-tab-active');
				});
			})(jQuery);
		</script>
		<?php
	}

	/**
	 * The Constructor.
	 *
	 * @param array $opt Options of this plugin.
	 */
	public function __construct( &$opt ) {
		$this->options = &$opt;
	}

	/**
	 * Assist function, used for saving options.
	 */
	protected function _save_option() {
		WUT::$me->options->save_options();
	}

	/**
	 * Create the menu and its items.
	 */
	public function add_menu_items() {
		add_menu_page(
			__( 'WordPress Ultimate Toolkit Options', 'wut' ),
			__( 'WUT Options', 'wut' ),
			'manage_options',
			'wut_admin_default_page',
			array( &$this, 'load_widgets' )
		);
		add_submenu_page(
			'wut_admin_default_page',
			__( 'Load Widgets', 'wut' ),
			__( 'Load Widgets', 'wut' ),
			'manage_options',
			'wut_admin_default_page',
			array( &$this, 'load_widgets' )
		);
		add_submenu_page(
			'wut_admin_default_page',
			__( 'Custom Code', 'wut' ),
			__( 'Custom Code', 'wut' ),
			'manage_options',
			'wut_admin_custom_code',
			array( &$this, 'custom_code_snippets' )
		);
		add_submenu_page(
			'wut_admin_default_page',
			__( 'Other Options', 'wut' ),
			__( 'Other Options' ),
			'manage_options',
			'wut_admin_other_options',
			array( &$this, 'other_options' )
		);
	}

	public function load_widgets() {
		// Get options
		$options =& $this->options['widgets'];
		$all     = $options['all'];
		$load    =& $options['load'];

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wut_admin_default_page' ) {
			if ( isset( $_REQUEST['action'] ) && 'save' == $_REQUEST['action'] ) {
				$load = array();
				foreach ( $all as $widget ) {
					if ( isset( $_REQUEST[ $widget['callback'] ] )
						&& $_REQUEST[ $widget['callback'] ] == 1
					) {
						$load[] = $widget['callback'];
					}
				}
				$this->_save_option();
			}
		}
		?>
		<div class="wrap"><h2><?php _e( 'Load Widgets', 'wut' ); ?></h2>
			<form method="post">
				<table class="widefat">
					<thead>
						<tr>
							<td id="cb" class="manage-column column-cb check-column">
								<input type="checkbox" />
							</td>
							<th id="widgetname" class="manage-column column-widgetname" scope="col"><?php _e( 'Widget Name', 'wut' ); ?></th>
							<th id="decript" class="manage-column column-descript" scope="col"><?php _e( 'Description', 'wut' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="manage-column column-cb check-column">
								<input type="checkbox" />
							</td>
							<th class="manage-column column-widgetname" scope="col"><?php _e( 'Widget Name', 'wut' ); ?></th>
							<th class="manage-column column-descript" scope="col"><?php _e( 'Description', 'wut' ); ?></th>
						</tr>
					</tfoot>
					<tbody>
					<?php
					foreach ( $all as $widget ) {
						echo '<tr><td>';
						echo '<input type="checkbox" id="', $widget['callback'], '" name="', $widget['callback'], '" value="1" ';
						if ( in_array( $widget['callback'], $load ) ) {
							echo 'checked="checked"';
						}
						echo ' /></td>';
						echo '<td>', $widget['name'], '</td>';
						echo '<td>', $widget['descript'], '</td>';
						echo '</tr>';
					}
					?>
					</tbody>
				</table>
				<input type="hidden" value="save" name="action" />
				<input type="submit" class="button" value="Load checked Widgets" />
			</form>
		</div>
		<?php
	}

	public function custom_code_snippets() {
		$options =& $this->options['customcode'];

		unset( $new_code );
		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wut_admin_custom_code' ) {
			if ( isset( $_REQUEST['add-new-snippet'] ) ) {
				$new_code = array(
					'id'       => '%id%',
					'name'     => 'New Code Snippet',
					'source'   => '',
					'hookto'   => '',
					'priority' => 9,
					'display'  => '',
				);
			}
			if ( isset( $_REQUEST['save-codes'] ) ) {
				foreach ( $options as $id => $snippet ) {
					$snippet['name']     = $_REQUEST[ "$id-name" ];
					$snippet['source']   = stripslashes( $_REQUEST[ "$id-source" ] );
					$snippet['hookto']   = $_REQUEST[ "$id-hookto" ];
					$snippet['priority'] = $_REQUEST[ "$id-priority" ];
					$snippet['display']  = $_REQUEST[ "$id-display" ];
					$options[ $id ]      = $snippet;
				}
				if ( isset( $_REQUEST['%id%-name'] ) && ! empty( $_REQUEST['%id%-name'] ) ) {
					$new_snippet                   = array(
						'id'       => sanitize_title_with_dashes( $_REQUEST['%id%-name'] ),
						'name'     => $_REQUEST['%id%-name'],
						'source'   => stripslashes( $_REQUEST['%id%-source'] ),
						'hookto'   => $_REQUEST['%id%-hookto'],
						'priority' => $_REQUEST['%id%-priority'],
						'display'  => $_REQUEST['%id%-display'],
					);
					$options[ $new_snippet['id'] ] = $new_snippet;
				}
				WUT::$me->options->save_options();
			}
			if ( isset( $_REQUEST['delete-checked'] ) ) {
				$temp = array();
				$item = array_shift( $options );
				while ( $item != null ) {
					if ( isset( $_REQUEST[ $item['id'] ] ) && $_REQUEST[ $item['id'] ] == 1 ) {
						unset( $item );
					} else {
						array_push( $temp, $item );
					}
					$item = array_shift( $options );
				}
				$options = $temp;
				WUT::$me->options->save_options();
			}
		}
		?>
		<div class="wrap"><h2><?php _e( 'Add Custom Code', 'wut' ); ?></h2>
		<form method="post">
			<input type="submit" class="button" name="add-new-snippet" value="<?php _e( 'Add New', 'wut' ); ?>" />
			<input type="submit" class="button" name="save-codes" value="<?php _e( 'Save All', 'wut' ); ?>" />
			<input type="submit" class="button" name="delete-checked" value="<?php _e( 'Delete All Checked', 'wut' ); ?>" />
			<table class="widefat">
				<thead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
						<th id="itemname" class="manage-column column-itemname" scope="col"><?php _e( 'Item Name', 'wut' ); ?></th>
						<th id="itemcontent" class="manage-column column-itemcontent" scope="col"><?php _e( 'Item Content', 'wut' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
						<th class="manage-column column-itemname" scope="col"><?php _e( 'Item Name', 'wut' ); ?></th>
						<th class="manage-column column-itemcontent" scope="col"><?php _e( 'Item Content', 'wut' ); ?></th>
					</tr>
				</tfoot>
				<tbody>
				<?php function print_code_item( $codesnippet ) { ?>
					<tr>
						<td rowspan="5"><input type="checkbox" id="<?php echo $codesnippet['id']; ?>" name="<?php echo $codesnippet['id']; ?>" value="1" /></td>
						<td><label for="<?php echo $codesnippet['id']; ?>-name"><?php _e( 'Code Name:', 'wut' ); ?></label></td>
						<td><input type="text" id="<?php echo $codesnippet['id']; ?>-name" name="<?php echo $codesnippet['id']; ?>-name" value="<?php echo $codesnippet['name']; ?>" size="15" /></td>
					</tr>
					<tr>
						<td><label for="<?php echo $codesnippet['id']; ?>-source"><?php _e( 'Source Code:', 'wut' ); ?></label></td>
						<td>
							<textarea id="<?php echo $codesnippet['id']; ?>-source" name="<?php echo $codesnippet['id']; ?>-source" cols="80" rows="15"><?php echo esc_attr( $codesnippet['source'] ); ?></textarea>
						</td>
					</tr>
					<tr>
						<td><label for="<?php echo $codesnippet['id']; ?>-hookto"><?php _e( 'Hook to Action:', 'wut' ); ?></label></td>
						<td><input type="text" id="<?php echo $codesnippet['id']; ?>-hookto" name="<?php echo $codesnippet['id']; ?>-hookto" value="<?php echo $codesnippet['hookto']; ?>" size="40" /></td>
					</tr>
					<tr>
						<td><label for="<?php echo $codesnippet['id']; ?>-priority"><?php _e( 'Priority:', 'wut' ); ?></label></td>
						<td><input type="text" id="<?php echo $codesnippet['id']; ?>-priority" name="<?php echo $codesnippet['id']; ?>-priority" value="<?php echo $codesnippet['priority']; ?>" size="15" /></td>
					</tr>
					<tr>
						<td><label for="<?php echo $codesnippet['id']; ?>-display"><?php _e( 'Display on:', 'wut' ); ?></label></td>
						<td><input type="text" id="<?php echo $codesnippet['id']; ?>-display" name="<?php echo $codesnippet['id']; ?>-display" value="<?php echo $codesnippet['display']; ?>" size="40" /></td>
					</tr>
				<?php } ?>
				<?php
				if ( ! empty( $options ) ) {
					foreach ( $options as $codesnippet ) {
						print_code_item( $codesnippet );
					}
				}
				if ( isset( $new_code ) ) {
					print_code_item( $new_code );
				}
				?>
				</tbody>
			</table>
			<input type="submit" class="button" name="add-new-snippet" value="<?php _e( 'Add New', 'wut' ); ?>" />
			<input type="submit" class="button" name="save-codes" value="<?php _e( 'Save All', 'wut' ); ?>" />
			<input type="submit" class="button" name="delete-checked" value="<?php _e( 'Delete All Checked', 'wut' ); ?>" />
		</form>
		</div>
		<?php
	}

	public function other_options() {
		// Get options
		$options =& $this->options['other'];

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'wut_admin_other_options' ) {
			if ( isset( $_REQUEST['action'] ) && 'enable' == $_REQUEST['action'] ) {
				$options['enabled'] = 1;
			}
			if ( isset( $_REQUEST['synchronize'] ) ) {
				$options['wphome']       = get_option( 'home' );
				$options['perma_struct'] = get_option( 'permalink_structure' );
			}
			if ( isset( $_REQUEST['disable'] ) ) {
				$options['enabled'] = 0;
			}
			WUT::$me->options->save_options();
		}
		?>
		<div class="wrap"><h2><?php _e( 'Advanced Options', 'wut' ); ?></h2>
			<form method="post">
				<table class="form-table">
					<tbody>
					<?php if ( $options['enabled'] ) : ?>
						<tr valign="top">
						<th scope="row"><label for="wphome"><?php _e( 'Blog address (URL)' ); ?></label></th>
						<td><span style="color:red;font-weight:bold"><?php echo $options['wphome']; ?></span><br />
						<span class="setting-description"><?php _e( 'Enter the address here if you want your blog homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.' ); ?></span></td>
						</tr>
						<tr valign="top">
						<th scope="row"><label for="perma_struct"><?php _e( 'Permalink Structure', 'wut' ); ?></label></th>
						<td><span style="color:red;font-weight:bold"><?php echo $options['perma_struct']; ?></span><br />
						<span class="setting-description"><?php _e( 'If you change you permalink structure, please change this.', 'wut' ); ?></span></td>
						</tr>
						<tr valign="top">
						<th scope="row"><label for=""></label></th>
						<td>
							<input type="submit" class="button" name="synchronize" value="<?php _e( 'Synchronize the Info with WordPress Settings.', 'wut' ); ?>" />
							<input type="submit" class="button" name="disable" value="<?php _e( 'Disable the Andvanced.', 'wut' ); ?>" />
						</td>
						</tr>
					<?php else : ?>
						<tr valign="top">
						<th scope="row"><label for=""></label></th>
						<td>
							<input type="hidden" value="enable" name="action" />
							<input type="submit" class="button" value="<?php _e( 'Enable the Andvanced.', 'wut' ); ?>" />
						</td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</form>
		</div>
		<?php
	}
}
