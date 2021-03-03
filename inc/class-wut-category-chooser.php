<?php

class WUT_Category_Chooser extends Walker {
	const TREE_BOX_ID = 'jstree_category';
	public function __construct() {
	}

	public function enqueue_scripts() {
		add_thickbox();
		wp_enqueue_style( 'jstree' );
		wp_enqueue_script( 'jstree' );
		wp_enqueue_script( 'wut-category-chooser' );
	}

	public function inject_link() {
		?>
		<a title="<?php echo __( 'Category Chooser', 'wordpress-ultimate-toolkit' ); ?>" href="admin-ajax.php?action=wut_category_chooser&height=300&width=300" class="thickbox"><?php echo __( 'Choose categories', 'wordpress-ultimate-toolkit' ); ?></a>
		<?php
	}

	public static function show_category_tree() {
		?>
		<script>
			var wut_tree_data = [
				'Simple root node',
				{
					'id' : 'node_2',
					'text' : 'Root node with options',
					'state' : { 'opened' : true, 'selected' : true },
					'children' : [ { 'text' : 'Child 1' }, 'Child 2']
				}
			];
			wut_tree_data = [
				<?php
					$categories        = get_categories();
					$walker            = new WUT_Category_Chooser();
					$walker->db_fields = array(
						'id'     => 'term_id',
						'parent' => 'parent',
					);
					echo $walker->walk( $categories, 0 );
					?>
			];
			wut_category_chooser();
		</script>
		<p><?php echo __( 'Choose the categories:', 'wordpress-ultimate-toolkit' ); ?></p>
		<div id="<?php echo self::TREE_BOX_ID; ?>"></div>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Decide', 'wordpress-ultimate-toolkit' ); ?>"></p
		<?php
		wp_die();
	}

	// The walker implementation.

	/**
	 * Starts the list before the elements are added.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. This method is called at the start of the output list.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= '"children": [';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. This method finishes the list at the end of output of the elements.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$output .= ']';
	}

	/**
	 * Start the element output.
	 *
	 * The $args parameter holds additional values that may be used with the child
	 * class methods. Includes the element output also.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output            Used to append additional content (passed by reference).
	 * @param object $object            The data object.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $current_object_id ID of the current item.
	 */
	public function start_el( &$output, $object, $depth = 0, $args = array(), $current_object_id = 0 ) {
		$output .= '{';
		$output .= '"id": ' . $current_object_id . ',';
		$output .= '"text": "' . $object->name . '",';
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * The $args parameter holds additional values that may be used with the child class methods.
	 *
	 * @since 2.1.0
	 * @abstract
	 *
	 * @param string $output Used to append additional content (passed by reference).
	 * @param object $object The data object.
	 * @param int    $depth  Depth of the item.
	 * @param array  $args   An array of additional arguments.
	 */
	public function end_el( &$output, $object, $depth = 0, $args = array() ) {
		$output .= '},';
	}


}
