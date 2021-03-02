( function ( $, window) {
	window.wut_category_chooser = function() {
		$('#jstree_category').jstree({
			'core': {
				'themes' : { 'stripes' : true },
				'data': wut_tree_data
			},
			'plugins': ['checkbox']
		});
	}
} )( jQuery, window );