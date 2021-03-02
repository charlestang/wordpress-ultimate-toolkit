( function ( $, window) {
	window.wut_category_chooser = function() {
		$('#jstree_category').jstree({
			'core': {
				'data': wut_tree_data
			}
		});
	}
} )( jQuery, window );