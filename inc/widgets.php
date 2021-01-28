<?php
/**
 * This file is for all the widgets included in this plugin.
 */

function wut_widget_random_posts_init() {
	function wut_widget_random_posts_body( $args, $widget_args ) {
		extract( $args, EXTR_SKIP );
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-random-posts' );

		if ( ! isset( $options[ $number ] ) ) {
			return;
		}
		$tag_args = array(
			'limit'    => $options[ $number ]['limit'],
			'before'   => $options[ $number ]['before'],
			'after'    => $options[ $number ]['after'],
			'type'     => $options[ $number ]['type'],
			'skips'    => $options[ $number ]['skips'],
			'none'     => $options[ $number ]['none'],
			'password' => $options[ $number ]['password'],
			'xformat'  => $options[ $number ]['xformat'],
		);
		echo $before_widget, $before_title, $options[ $number ]['title'], $after_title;
		echo '<ul>', wut_random_posts( $tag_args ), '</ul>';
		echo $after_widget;
	}
	function wut_widget_random_posts_control( $widget_args ) {
		global $wp_registered_widgets;
		static $update = false;
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-random-posts' );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! $update && ! empty( $_POST['sidebar'] ) ) {
			$sidebar          = (string) $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset( $sidebars_widgets[ $sidebar ] ) ) {
				$this_sidebar =& $sidebars_widgets[ $sidebar ];
			} else {
				$this_sidebar = array();
			}
			foreach ( $this_sidebar as $_widget_id ) {
				if ( 'wut_widget_random_posts_body' == $wp_registered_widgets[ $_widget_id ]['callback'] && isset( $wp_registered_widgets[ $_widget_id ]['params'][0]['number'] ) ) {
					$widget_number = $wp_registered_widgets[ $_widget_id ]['params'][0]['number'];
					if ( ! in_array( "wut-widget-random-posts-$widget_number", $_POST['widget-id'] ) ) {
						unset( $options[ $widget_number ] );
					}
				}
			}
			foreach ( (array) $_POST['wut-random-posts'] as $widget_number => $posted ) {
				$options_this['title']     = strip_tags( stripslashes( $posted['title'] ) );
				$options_this['limit']     = intval( $posted['limit'] );
				$options_this['none']      = stripslashes( $posted['none'] );
				$options_this['before']    = stripslashes( $posted['before'] );
				$options_this['after']     = stripslashes( $posted['after'] );
				$options_this['type']      = $posted['type'];
				$options_this['skips']     = $posted['skips'];
				$options_this['password']  = $posted['password'];
				$options_this['xformat']   = stripslashes( $posted['xformat'] );
				$options[ $widget_number ] = $options_this;
			}
			update_option( 'wut-widget-random-posts', $options );
			$update = true;
		}
		if ( $number == -1 ) {
			$title    = 'WUT Random Posts';
			$limit    = 10;
			$before   = esc_attr( '<li>' );
			$after    = esc_attr( '</li>' );
			$type     = 'both';
			$skips    = '';
			$none     = 'No Posts.';
			$password = 'hide';
			$xformat  = esc_attr( '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)' );
			$number   = '%i%';
		} else {
			$title    = esc_attr( $options[ $number ]['title'] );
			$limit    = $options[ $number ]['limit'];
			$before   = esc_attr( $options[ $number ]['before'] );
			$after    = esc_attr( $options[ $number ]['after'] );
			$none     = esc_attr( $options[ $number ]['none'] );
			$password = $options[ $number ]['password'];
			$xformat  = esc_attr( $options[ $number ]['xformat'] );
			$type     = $options[ $number ]['type'];
			$skips    = $options[ $number ]['skips'];
		}

		?>
		<table>
			<tbody>
				<tr>
					<td class="alignright"><?php _e( 'Widget title:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-title-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Number of posts:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-limit-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][limit]" value="<?php echo $limit; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags before a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-before-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][before]" value="<?php echo $before; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags after a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-after-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][after]" value="<?php echo $after; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Posts to exclude:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-skips-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][skips]" value="" /></td>
				</tr>            
				<tr>
					<td class="alignright"><?php _e( 'Post type to show:', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-random-posts-type-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][type]" value="both" 
																					<?php
																					if ( $type == 'both' ) {
																						echo 'checked="checked"';}
																					?>
						/>both</p>
						<p><input type="radio" id="wut-random-posts-type-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][type]" value="page" 
																					<?php
																					if ( $type == 'page' ) {
																						echo 'checked="checked"';}
																					?>
						/>page only</p>
						<p><input type="radio" id="wut-random-posts-type-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][type]" value="post" 
																					<?php
																					if ( $type == 'post' ) {
																						echo 'checked="checked"';}
																					?>
						/>post only</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'If the list is empty, show:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-none-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][none]" value="<?php echo $none; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Show password protected post?', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-random-posts-password-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][password]" value="show" 
																						<?php
																						if ( $password == 'show' ) {
																							echo 'checked="checked"';}
																						?>
						 />Show.</p>
						<p><input type="radio" id="wut-random-posts-password-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][password]" value="hide" 
																						<?php
																						if ( $password == 'hide' ) {
																							echo 'checked="checked"';}
																						?>
						 />Hide.</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'The list item format:', 'wut' ); ?></td>
					<td><input type="text" id="wut-random-posts-xformat-<?php echo $number; ?>" name="wut-random-posts[<?php echo $number; ?>][xformat]" value="<?php echo $xformat; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<input id="wut-random-posts-submit-<?php echo $number; ?>" name="wut-random-posts-submit-<?php echo $number; ?>" type="hidden" value="1"/>
		<?php
	}
	$options = get_option( 'wut-widget-random-posts' );
	if ( ! $options ) {
		$options = array();
	}

	$widget_ops  = array(
		'classname'   => 'wut-widget-random-posts',
		'description' => __( 'List the random posts', 'wut' ),
	);
	$control_ops = array(
		'width'   => 400,
		'height'  => 200,
		'id_base' => 'wut-widget-random-posts',
	);
	$name        = __( 'WUT Random Posts', 'wut' );
	$widget_cb   = 'wut_widget_random_posts_body';
	$control_cb  = 'wut_widget_random_posts_control';
	// Register Widgets
	$registered = false;
	foreach ( array_keys( $options ) as $o ) {
		if ( ! isset( $options[ $o ]['title'] ) ) {
			continue;
		}
		$id         = "wut-widget-random-posts-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, $widget_cb, $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, $control_cb, $control_ops, array( 'number' => $o ) );
	}
	if ( ! $registered ) {
		wp_register_sidebar_widget( 'wut-widget-random-posts-1', $name, $widget_cb, $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'wut-widget-random-posts-1', $name, $control_cb, $control_ops, array( 'number' => -1 ) );
	}
}

function wut_widget_posts_by_category_init() {
	function wut_widget_posts_by_category_body( $args, $widget_args ) {
		if ( ! is_single() ) {
			return;
		}
		extract( $args, EXTR_SKIP );
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-posts-by-category' );

		if ( ! isset( $options[ $number ] ) ) {
			return;
		}

		$tag_args = array(
			'limit'    => $options[ $number ]['limit'],
			'before'   => $options[ $number ]['before'],
			'after'    => $options[ $number ]['after'],
			'type'     => 'post',
			'skips'    => $options[ $number ]['skips'],
			'password' => $options[ $number ]['password'],
			'orderby'  => $options[ $number ]['orderby'],
			'order'    => $options[ $number ]['order'],
			'xformat'  => $options[ $number ]['xformat'],
			'none'     => $options[ $number ]['none'],
		);

		echo $before_widget, $before_title, $options[ $number ]['title'], $after_title;
		echo '<ul>', wut_posts_by_category( $tag_args ), '</ul>';
		echo $after_widget;
	}
	function wut_widget_posts_by_category_control( $widget_args ) {
		global $wp_registered_widgets;
		static $update = false;
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-posts-by-category' );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! $update && ! empty( $_POST['sidebar'] ) ) {
			$sidebar          = (string) $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset( $sidebars_widgets[ $sidebar ] ) ) {
				$this_sidebar =& $sidebars_widgets[ $sidebar ];
			} else {
				$this_sidebar = array();
			}
			foreach ( $this_sidebar as $_widget_id ) {
				if ( 'wut_widget_posts_by_category_body' == $wp_registered_widgets[ $_widget_id ]['callback'] && isset( $wp_registered_widgets[ $_widget_id ]['params'][0]['number'] ) ) {
					$widget_number = $wp_registered_widgets[ $_widget_id ]['params'][0]['number'];
					if ( ! in_array( "wut-widget-posts-by-category-$widget_number", $_POST['widget-id'] ) ) {
						unset( $options[ $widget_number ] );
					}
				}
			}

			foreach ( (array) $_POST['wut-posts-by-category'] as $widget_number => $posted ) {
				$options_this['title']     = strip_tags( stripslashes( $posted['title'] ) );
				$options_this['limit']     = intval( $posted['limit'] );
				$options_this['before']    = stripslashes( $posted['before'] );
				$options_this['after']     = stripslashes( $posted['after'] );
				$options_this['skips']     = $posted['skips'];
				$options_this['password']  = $posted['password'];
				$options_this['orderby']   = $posted['orderby'];
				$options_this['order']     = $posted['order'];
				$options_this['xformat']   = stripslashes( $posted['xformat'] );
				$options_this['none']      = stripslashes( $posted['none'] );
				$options[ $widget_number ] = $options_this;
			}
			update_option( 'wut-widget-posts-by-category', $options );
			$update = true;
		}
		if ( $number == -1 ) {
			$title    = 'WUT Posts by Category';
			$limit    = 10;
			$before   = esc_attr( '<li>' );
			$after    = esc_attr( '</li>' );
			$skips    = '';
			$none     = 'No Posts.';
			$password = 'hide';
			$orderby  = 'post_date';
			$order    = 'desc';
			$xformat  = esc_attr( '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)' );
			$number   = '%i%';
		} else {
			$title    = esc_attr( $options[ $number ]['title'] );
			$limit    = $options[ $number ]['limit'];
			$before   = esc_attr( $options[ $number ]['before'] );
			$after    = esc_attr( $options[ $number ]['after'] );
			$skips    = $options[ $number ]['skips'];
			$none     = esc_attr( $options[ $number ]['none'] );
			$password = $options[ $number ]['password'];
			$orderby  = $options[ $number ]['orderby'];
			$order    = $options[ $number ]['order'];
			$xformat  = esc_attr( $options[ $number ]['xformat'] );
		}
		?>
		<table>
			<tbody>
				<tr>
					<td class="alignright"><?php _e( 'Widget title:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-title-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Number of posts:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-limit-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][limit]" value="<?php echo $limit; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags before a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-before-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][before]" value="<?php echo $before; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags after a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-after-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][after]" value="<?php echo $after; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Posts to exclude:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-skips-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][skips]" value="" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Order by:', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-posts-by-category-orderby-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][orderby]" value="post_date" 
																							<?php
																							if ( $orderby == 'post_date' ) {
																								echo 'checked="checked"';}
																							?>
						 />Post date.</p>
						<p><input type="radio" id="wut-posts-by-category-orderby-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][orderby]" value="post_modified" 
																							<?php
																							if ( $orderby == 'post_modified' ) {
																								echo 'checked="checked"';}
																							?>
						 />Post modified date.</p>
					</td>
				</tr>
				<tr>
				<td class="alignright"><?php _e( 'Order:', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-posts-by-category-order-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][order]" value="desc" 
																						  <?php
																							if ( $order == 'desc' ) {
																								echo 'checked="checked"';}
																							?>
						 />Descend.</p>
						<p><input type="radio" id="wut-posts-by-category-order-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][order]" value="asc" 
																						  <?php
																							if ( $order == 'asc' ) {
																								echo 'checked="checked"';}
																							?>
						 />Ascend.</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'If the list is empty, show:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-none-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][none]" value="<?php echo $none; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Show password protected post?', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-posts-by-category-password-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][password]" value="show" 
																							 <?php
																								if ( $password == 'show' ) {
																									echo 'checked="checked"';}
																								?>
						 />Show.</p>
						<p><input type="radio" id="wut-posts-by-category-password-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][password]" value="hide" 
																							 <?php
																								if ( $password == 'hide' ) {
																									echo 'checked="checked"';}
																								?>
						 />Hide.</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'The list item format:', 'wut' ); ?></td>
					<td><input type="text" id="wut-posts-by-category-xformat-<?php echo $number; ?>" name="wut-posts-by-category[<?php echo $number; ?>][xformat]" value="<?php echo $xformat; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<input id="wut-posts-by-category-submit-<?php echo $number; ?>" name="wut-posts-by-category-submit-<?php echo $number; ?>" type="hidden" value="1"/>
		<?php
	}
	$options = get_option( 'wut-widget-posts-by-category' );
	if ( ! $options ) {
		$options = array();
	}

	$widget_ops  = array(
		'classname'   => 'wut-widget-posts-by-category',
		'description' => __( 'List posts categorized by the current post\'s categories on SINGLE POST PAGE ONLY.', 'wut' ),
	);
	$control_ops = array(
		'width'   => 400,
		'height'  => 200,
		'id_base' => 'wut-widget-posts-by-category',
	);
	$name        = __( 'WUT Posts by Category', 'wut' );
	$widget_cb   = 'wut_widget_posts_by_category_body';
	$control_cb  = 'wut_widget_posts_by_category_control';
	// Register Widgets
	$registered = false;
	foreach ( array_keys( $options ) as $o ) {
		if ( ! isset( $options[ $o ]['title'] ) ) {
			continue;
		}
		$id         = "wut-widget-posts-by-category-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, $widget_cb, $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, $control_cb, $control_ops, array( 'number' => $o ) );
	}
	if ( ! $registered ) {
		wp_register_sidebar_widget( 'wut-widget-posts-by-category-1', $name, $widget_cb, $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'wut-widget-posts-by-category-1', $name, $control_cb, $control_ops, array( 'number' => -1 ) );
	}
}

function wut_widget_most_commented_posts_init() {
	function wut_widget_most_commented_posts_body( $args, $widget_args ) {
		extract( $args, EXTR_SKIP );
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-most-commented-posts' );

		if ( ! isset( $options[ $number ] ) ) {
			return;
		}
		$title = $options[ $number ]['title'];

		$tag_args = array(
			'limit'    => $options[ $number ]['limit'],
			'before'   => $options[ $number ]['before'],
			'after'    => $options[ $number ]['after'],
			'type'     => $options[ $number ]['type'],
			'skips'    => $options[ $number ]['skips'],
			'days'     => $options[ $number ]['days'],
			'none'     => $options[ $number ]['none'],
			'password' => $options[ $number ]['password'],
			'xformat'  => $options[ $number ]['xformat'],
		);

		echo $before_widget, $before_title, $title, $after_title;
		echo '<ul>', wut_most_commented_posts( $tag_args ), '</ul>';
		echo $after_widget;
	}
	function wut_widget_most_commented_posts_control( $widget_args ) {
		global $wp_registered_widgets;
		static $update = false;
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-most-commented-posts' );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! $update && ! empty( $_POST['sidebar'] ) ) {
			$sidebar          = (string) $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset( $sidebars_widgets[ $sidebar ] ) ) {
				$this_sidebar =& $sidebars_widgets[ $sidebar ];
			} else {
				$this_sidebar = array();
			}
			foreach ( $this_sidebar as $_widget_id ) {
				if ( 'wut_widget_most_commented_posts_body' == $wp_registered_widgets[ $_widget_id ]['callback'] && isset( $wp_registered_widgets[ $_widget_id ]['params'][0]['number'] ) ) {
					$widget_number = $wp_registered_widgets[ $_widget_id ]['params'][0]['number'];
					if ( ! in_array( "wut-widget-most-commented-posts-$widget_number", $_POST['widget-id'] ) ) {
						unset( $options[ $widget_number ] );
					}
				}
			}

			foreach ( (array) $_POST['wut-most-commented-posts'] as $widget_number => $posted ) {
				$options_this['title']     = strip_tags( stripslashes( $posted['title'] ) );
				$options_this['limit']     = intval( $posted['limit'] );
				$options_this['before']    = stripslashes( $posted['before'] );
				$options_this['after']     = stripslashes( $posted['after'] );
				$options_this['type']      = $posted['type'];
				$options_this['skips']     = $posted['skips'];
				$options_this['days']      = intval( $posted['days'] );
				$options_this['none']      = strip_tags( stripslashes( $posted['none'] ) );
				$options_this['password']  = $posted['password'];
				$options_this['xformat']   = stripcslashes( $posted['xformat'] );
				$options[ $widget_number ] = $options_this;
			}
			update_option( 'wut-widget-most-commented-posts', $options );
			$update = true;
		}
		if ( $number == -1 ) {
			$title    = 'WUT Most Commented Posts';
			$limit    = 10;
			$before   = esc_attr( '<li>' );
			$after    = esc_attr( '</li>' );
			$type     = 'both';
			$skips    = '';
			$days     = 30;
			$none     = 'No Posts.';
			$password = 'hide';
			$xformat  = esc_attr( '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)' );
			$number   = '%i%';
		} else {
			$title    = esc_attr( $options[ $number ]['title'] );
			$limit    = $options[ $number ]['limit'];
			$before   = esc_attr( $options[ $number ]['before'] );
			$after    = esc_attr( $options[ $number ]['after'] );
			$type     = $options[ $number ]['type'];
			$skips    = $options[ $number ]['skips'];
			$days     = $options[ $number ]['days'];
			$none     = esc_attr( $options[ $number ]['none'] );
			$password = $options[ $number ]['password'];
			$xformat  = esc_attr( $options[ $number ]['xformat'] );
		}
		?>
		<table>
			<tbody>
				<tr>
					<td class="alignright"><?php _e( 'Widget title:', 'wut' ); ?></td>
					<td><input id="wut-most-commented-posts-title-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>"/></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Number of posts:', 'wut' ); ?></td>
					<td><input id="wut-most-commented-posts-limit-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][limit]" type="text" value="<?php echo $limit; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags before a item:', 'wut' ); ?></td>
					<td><input id="wut-most-commented-posts-before-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][before]" type="text" value="<?php echo $before; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags after a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-most-commented-posts-after-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][after]" value="<?php echo $after; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Post type to show:', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-most-commented-posts-type-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][type]" value="both" 
																							<?php
																							if ( $type == 'both' ) {
																								echo 'checked="checked"';}
																							?>
						/>both</p>
						<p><input type="radio" id="wut-most-commented-posts-type-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][type]" value="page" 
																							<?php
																							if ( $type == 'page' ) {
																								echo 'checked="checked"';}
																							?>
						/>page only</p>
						<p><input type="radio" id="wut-most-commented-posts-type-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][type]" value="post" 
																							<?php
																							if ( $type == 'post' ) {
																								echo 'checked="checked"';}
																							?>
						/>post only</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Posts to exclude:', 'wut' ); ?></td>
					<td><input type="text" id="wut-most-commented-posts-skips-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][skips]" value="" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Time Limit:', 'wut' ); ?></td>
					<td><?php _e( 'In', 'wut' ); ?><input id="wut-most-commented-posts-days-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][days]" type="text" value="<?php echo $days; ?>" size="7" /><?php _e( 'days.', 'wut' ); ?></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'If the list is empty, show:', 'wut' ); ?></td>
					<td><input type="text" id="wut-most-commented-posts-none-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][none]" value="<?php echo $none; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Show password protected post?', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-most-commented-posts-password-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][password]" value="show" 
																								<?php
																								if ( $password == 'show' ) {
																									echo 'checked="checked"';}
																								?>
						 />Show.</p>
						<p><input type="radio" id="wut-most-commented-posts-password-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][password]" value="hide" 
																								<?php
																								if ( $password == 'hide' ) {
																									echo 'checked="checked"';}
																								?>
						 />Hide.</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'The list item format:', 'wut' ); ?></td>
					<td><input id="wut-most-commented-posts-xformat-<?php echo $number; ?>" name="wut-most-commented-posts[<?php echo $number; ?>][xformat]" type="text" value="<?php echo $xformat; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<input id="wut-most-commented-posts-submit-<?php echo $number; ?>" name="wut-most-commented-posts-submit-<?php echo $number; ?>" type="hidden" value="1"/>
		<?php
	}
	$options = get_option( 'wut-widget-most-commented-posts' );
	if ( ! $options ) {
		$options = array();
	}

	$widget_ops  = array(
		'classname'   => 'wut-widget-most-commented-posts',
		'description' => __( 'List posts order by the number of comments.', 'wut' ),
	);
	$control_ops = array(
		'width'   => 400,
		'height'  => 200,
		'id_base' => 'wut-widget-most-commented-posts',
	);
	$name        = __( 'WUT Most Commented Posts', 'wut' );
	$widget_cb   = 'wut_widget_most_commented_posts_body';
	$control_cb  = 'wut_widget_most_commented_posts_control';
	// Register Widgets
	$registered = false;
	foreach ( array_keys( $options ) as $o ) {
		if ( ! isset( $options[ $o ]['title'] ) ) {
			continue;
		}
		$id         = "wut-widget-most-commented-posts-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, $widget_cb, $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, $control_cb, $control_ops, array( 'number' => $o ) );
	}
	if ( ! $registered ) {
		wp_register_sidebar_widget( 'wut-widget-most-commented-posts-1', $name, $widget_cb, $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'wut-widget-most-commented-posts-1', $name, $control_cb, $control_ops, array( 'number' => -1 ) );
	}
}

function wut_widget_active_commentators_init() {
	function wut_widget_active_commentators_body( $args, $widget_args ) {
		extract( $args, EXTR_SKIP );
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-active-commentators' );

		if ( ! isset( $options[ $number ] ) ) {
			return;
		}
		$title = $options[ $number ]['title'];

		$tag_args = array(
			'limit'      => $options[ $number ]['limit'],
			'before'     => $options[ $number ]['before'],
			'after'      => $options[ $number ]['after'],
			'skipusers'  => $options[ $number ]['skipusers'],
			'avatarsize' => $options[ $number ]['avatarsize'],
			'threshhold' => $options[ $number ]['threshhold'],
			'days'       => $options[ $number ]['days'],
			'none'       => $options[ $number ]['none'],
			'xformat'    => $options[ $number ]['xformat'],
		);

		echo $before_widget, $before_title, $title, $after_title;
		echo '<ul>', wut_active_commentators( $tag_args ), '</ul>';
		echo $after_widget;
	}
	function wut_widget_active_commentators_control( $widget_args ) {
		global $wp_registered_widgets;
		static $update = false;
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-active-commentators' );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! $update && ! empty( $_POST['sidebar'] ) ) {
			$sidebar          = (string) $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset( $sidebars_widgets[ $sidebar ] ) ) {
				$this_sidebar =& $sidebars_widgets[ $sidebar ];
			} else {
				$this_sidebar = array();
			}
			foreach ( $this_sidebar as $_widget_id ) {
				if ( 'wut_widget_active_commentators_body' == $wp_registered_widgets[ $_widget_id ]['callback'] && isset( $wp_registered_widgets[ $_widget_id ]['params'][0]['number'] ) ) {
					$widget_number = $wp_registered_widgets[ $_widget_id ]['params'][0]['number'];
					if ( ! in_array( "wut-widget-active-commentators-$widget_number", $_POST['widget-id'] ) ) {
						unset( $options[ $widget_number ] );
					}
				}
			}

			foreach ( (array) $_POST['wut-active-commentators'] as $widget_number => $posted ) {
				$options_this['title']      = strip_tags( stripslashes( $posted['title'] ) );
				$options_this['limit']      = intval( $posted['limit'] );
				$options_this['before']     = stripslashes( $posted['before'] );
				$options_this['after']      = stripslashes( $posted['after'] );
				$options_this['length']     = intval( $posted['length'] );
				$options_this['skipusers']  = $posted['skipusers'];
				$options_this['avatarsize'] = intval( $posted['avatarsize'] );
				$options_this['threshhold'] = intval( $posted['threshhold'] );
				$options_this['days']       = intval( $posted['days'] );
				$options_this['none']       = strip_tags( stripslashes( $posted['none'] ) );
				$options_this['xformat']    = stripcslashes( $posted['xformat'] );
				$options[ $widget_number ]  = $options_this;
			}
			update_option( 'wut-widget-active-commentators', $options );
			$update = true;
		}

		if ( $number == -1 ) {
			$title      = 'WUT Active Commentators';
			$limit      = 10;
			$before     = esc_attr( '<li>' );
			$after      = esc_attr( '</li>' );
			$skipusers  = '';
			$avatarsize = 16;
			$threshhold = 2;
			$days       = -1;
			$none       = 'Anybody.';
			$xformat    = esc_attr( '%avatar%<a href="%url%" rel="nofollow">%author%</a>' );
			$number     = '%i%';
		} else {
			$title      = esc_attr( $options[ $number ]['title'] );
			$limit      = $options[ $number ]['limit'];
			$before     = esc_attr( $options[ $number ]['before'] );
			$after      = esc_attr( $options[ $number ]['after'] );
			$skipusers  = $options[ $number ]['skipusers'];
			$avatarsize = $options[ $number ]['avatarsize'];
			$threshhold = $options[ $number ]['threshhold'];
			$days       = $options[ $number ]['days'];
			$none       = esc_attr( $options[ $number ]['none'] );
			$xformat    = esc_attr( $options[ $number ]['xformat'] );
		}
		?>
		<table>
			<tbody>
				<tr>
					<td class="alignright"><?php _e( 'Widget title:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-title-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>"/></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Number of posts:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-limit-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][limit]" type="text" value="<?php echo $limit; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags before a item:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-before-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][before]" type="text" value="<?php echo $before; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags after a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-active-commentators-after-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][after]" value="<?php echo $after; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Users to exclude:', 'wut' ); ?></td>
					<td><input type="text" id="wut-active-commentators-skipusers-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][skipusers]" value="<?php echo $skipusers; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Avatar Size:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-avatarsize-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][avatarsize]" type="text" value="<?php echo $avatarsize; ?>" size="9"/><?php _e( 'pixels', 'wut' ); ?></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Threshhold:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-threshhold-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][threshhold]" type="text" value="<?php echo $threshhold; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Time limit:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-days-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][days]" type="text" value="<?php echo $days; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'If the list is empty, show:', 'wut' ); ?></td>
					<td><input type="text" id="wut-active-commentators-none-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][none]" value="<?php echo $none; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'The list item format:', 'wut' ); ?></td>
					<td><input id="wut-active-commentators-xformat-<?php echo $number; ?>" name="wut-active-commentators[<?php echo $number; ?>][xformat]" type="text" value="<?php echo $xformat; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<input id="wut-active-commentators-submit-<?php echo $number; ?>" name="wut-active-commentators-submit-<?php echo $number; ?>" type="hidden" value="1"/>
		<?php
	}

	$options = get_option( 'wut-widget-active-commentators' );
	if ( ! $options ) {
		$options = array();
	}

	$widget_ops  = array(
		'classname'   => 'wut-widget-active-commentators',
		'description' => __( 'List Active Commentators\' Names.', 'wut' ),
	);
	$control_ops = array(
		'width'   => 400,
		'height'  => 200,
		'id_base' => 'wut-widget-active-commentators',
	);
	$name        = __( 'WUT Active Commentators', 'wut' );
	$widget_cb   = 'wut_widget_active_commentators_body';
	$control_cb  = 'wut_widget_active_commentators_control';
	// Register Widgets
	$registered = false;
	foreach ( array_keys( $options ) as $o ) {
		if ( ! isset( $options[ $o ]['title'] ) ) {
			continue;
		}
		$id         = "wut-widget-active-commentators-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, $widget_cb, $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, $control_cb, $control_ops, array( 'number' => $o ) );
	}
	if ( ! $registered ) {
		wp_register_sidebar_widget( 'wut-widget-active-commentators-1', $name, $widget_cb, $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'wut-widget-active-commentators-1', $name, $control_cb, $control_ops, array( 'number' => -1 ) );
	}
}

function wut_widget_recent_commentators_init() {
	function wut_widget_recent_commentators_body( $args, $widget_args ) {
		extract( $args, EXTR_SKIP );
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}
		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-recent-commentators' );

		if ( ! isset( $options[ $number ] ) ) {
			return;
		}
		$title = $options[ $number ]['title'];

		$tag_args = array(
			'limit'      => $options[ $number ]['limit'],
			'before'     => $options[ $number ]['before'],
			'after'      => $options[ $number ]['after'],
			'skipusers'  => $options[ $number ]['skipusers'],
			'avatarsize' => $options[ $number ]['avatarsize'],
			'threshhold' => $options[ $number ]['threshhold'],
			'type'       => $options[ $number ]['type'],
			'days'       => $options[ $number ]['days'],
			'none'       => $options[ $number ]['none'],
			'xformat'    => $options[ $number ]['xformat'],
		);

		echo $before_widget, $before_title, $title, $after_title;
		echo '<ul>', wut_recent_commentators( $tag_args ), '</ul>';
		echo $after_widget;
	}
	function wut_widget_recent_commentators_control( $widget_args ) {
		global $wp_registered_widgets;
		static $update = false;
		if ( is_numeric( $widget_args ) ) {
			$widget_args = array( 'number' => $widget_args );
		}

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );

		extract( $widget_args, EXTR_SKIP );

		$options = get_option( 'wut-widget-recent-commentators' );

		if ( ! is_array( $options ) ) {
			$options = array();
		}

		if ( ! $update && ! empty( $_POST['sidebar'] ) ) {
			$sidebar          = (string) $_POST['sidebar'];
			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset( $sidebars_widgets[ $sidebar ] ) ) {
				$this_sidebar =& $sidebars_widgets[ $sidebar ];
			} else {
				$this_sidebar = array();
			}
			foreach ( $this_sidebar as $_widget_id ) {
				if ( 'wut_widget_recent_commentators_body' == $wp_registered_widgets[ $_widget_id ]['callback'] && isset( $wp_registered_widgets[ $_widget_id ]['params'][0]['number'] ) ) {
					$widget_number = $wp_registered_widgets[ $_widget_id ]['params'][0]['number'];
					if ( ! in_array( "wut-widget-recent-commentators-$widget_number", $_POST['widget-id'] ) ) {
						unset( $options[ $widget_number ] );
					}
				}
			}

			foreach ( (array) $_POST['wut-recent-commentators'] as $widget_number => $posted ) {
				$options_this['title']      = strip_tags( stripslashes( $posted['title'] ) );
				$options_this['limit']      = intval( $posted['limit'] );
				$options_this['before']     = stripslashes( $posted['before'] );
				$options_this['after']      = stripslashes( $posted['after'] );
				$options_this['length']     = intval( $posted['length'] );
				$options_this['skipusers']  = $posted['skipusers'];
				$options_this['avatarsize'] = intval( $posted['avatarsize'] );
				$options_this['threshhold'] = intval( $posted['threshhold'] );
				$options_this['type']       = $posted['type'];
				$options_this['days']       = intval( $posted['days'] );
				$options_this['none']       = strip_tags( stripslashes( $posted['none'] ) );
				$options_this['xformat']    = stripcslashes( $posted['xformat'] );
				$options[ $widget_number ]  = $options_this;
			}
			update_option( 'wut-widget-recent-commentators', $options );
			$update = true;
		}

		if ( $number == -1 ) {
			$title      = 'WUT Recent Commentators';
			$limit      = 10;
			$before     = esc_attr( '<li>' );
			$after      = esc_attr( '</li>' );
			$skipusers  = '';
			$avatarsize = 16;
			$threshhold = 2;
			$type       = 'month';
			$days       = -1;
			$none       = 'Anybody.';
			$xformat    = esc_attr( '%avatar%<a href="%url%" rel="nofollow">%author%</a>' );
			$number     = '%i%';
		} else {
			$title      = esc_attr( $options[ $number ]['title'] );
			$limit      = $options[ $number ]['limit'];
			$before     = esc_attr( $options[ $number ]['before'] );
			$after      = esc_attr( $options[ $number ]['after'] );
			$skipusers  = $options[ $number ]['skipusers'];
			$avatarsize = $options[ $number ]['avatarsize'];
			$threshhold = $options[ $number ]['threshhold'];
			$type       = $options[ $number ]['type'];
			$days       = $options[ $number ]['days'];
			$none       = esc_attr( $options[ $number ]['none'] );
			$xformat    = esc_attr( $options[ $number ]['xformat'] );
		}
		?>
		<table>
			<tbody>
				<tr>
					<td class="alignright"><?php _e( 'Widget title:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-title-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>"/></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Number of posts:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-limit-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][limit]" type="text" value="<?php echo $limit; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags before a item:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-before-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][before]" type="text" value="<?php echo $before; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'HTML tags after a item:', 'wut' ); ?></td>
					<td><input type="text" id="wut-recent-commentators-after-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][after]" value="<?php echo $after; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Users to exclude:', 'wut' ); ?></td>
					<td><input type="text" id="wut-recent-commentators-skipusers-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][skipusers]" value="<?php echo $skipusers; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Avatar Size:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-avatarsize-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][avatarsize]" type="text" value="<?php echo $avatarsize; ?>" size="9"/><?php _e( 'pixels', 'wut' ); ?></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Threshhold:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-threshhold-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][threshhold]" type="text" value="<?php echo $threshhold; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Choose the time limit:', 'wut' ); ?></td>
					<td>
						<p><input type="radio" id="wut-recent-commentators-type-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][type]" value="month" 
																						   <?php
																							if ( $type == 'month' ) {
																								echo 'checked="checked"';}
																							?>
						 />In this Month.</p>
						<p><input type="radio" id="wut-recent-commentators-type-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][type]" value="week" 
																						   <?php
																							if ( $type == 'week' ) {
																								echo 'checked="checked"';}
																							?>
						 />In this Week.</p>
					</td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'Time limit:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-days-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][days]" type="text" value="<?php echo $days; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'If the list is empty, show:', 'wut' ); ?></td>
					<td><input type="text" id="wut-recent-commentators-none-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][none]" value="<?php echo $none; ?>" /></td>
				</tr>
				<tr>
					<td class="alignright"><?php _e( 'The list item format:', 'wut' ); ?></td>
					<td><input id="wut-recent-commentators-xformat-<?php echo $number; ?>" name="wut-recent-commentators[<?php echo $number; ?>][xformat]" type="text" value="<?php echo $xformat; ?>" /></td>
				</tr>
			</tbody>
		</table>
		<input id="wut-recent-commentators-submit-<?php echo $number; ?>" name="wut-recent-commentators-submit-<?php echo $number; ?>" type="hidden" value="1"/>
		<?php
	}

	$options = get_option( 'wut-widget-recent-commentators' );
	if ( ! $options ) {
		$options = array();
	}

	$widget_ops  = array(
		'classname'   => 'wut-widget-recent-commentators',
		'description' => __( 'List Recent Commentators\' Names.', 'wut' ),
	);
	$control_ops = array(
		'width'   => 400,
		'height'  => 200,
		'id_base' => 'wut-widget-recent-commentators',
	);
	$name        = __( 'WUT Recent Commentators', 'wut' );
	$widget_cb   = 'wut_widget_recent_commentators_body';
	$control_cb  = 'wut_widget_recent_commentators_control';
	// Register Widgets
	$registered = false;
	foreach ( array_keys( $options ) as $o ) {
		if ( ! isset( $options[ $o ]['title'] ) ) {
			continue;
		}
		$id         = "wut-widget-recent-commentators-$o";
		$registered = true;
		wp_register_sidebar_widget( $id, $name, $widget_cb, $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, $control_cb, $control_ops, array( 'number' => $o ) );
	}
	if ( ! $registered ) {
		wp_register_sidebar_widget( 'wut-widget-recent-commentators-1', $name, $widget_cb, $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'wut-widget-recent-commentators-1', $name, $control_cb, $control_ops, array( 'number' => -1 ) );
	}
}
