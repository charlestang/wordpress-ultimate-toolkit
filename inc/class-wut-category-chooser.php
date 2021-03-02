<?php

class WUT_Category_Chooser {
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
		<a href="admin-ajax.php?action=wut_category_chooser&height=300&width=300" class="thickbox"><?php echo __( 'Choose categories', 'wordpress-ultimate-toolkit' ); ?></a>
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
			wut_category_chooser();
		</script>
		<div id="<?php echo self::TREE_BOX_ID; ?>"></div>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Decide', 'wordpress-ultimate-toolkit' ); ?>"></p
		<?php
		wp_die();
	}

}
