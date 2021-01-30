<?php
/**
 * Template tags.
 *
 * @package WordPress_Ultimate_Toolkit
 */

/**
 * Generate a permalink from a post object.
 *
 * @param Object $post The post to calculate the permalink.
 * @return string The permalink of the post.
 */
function _wut_get_permalink( $post ) {
	if ( isset( WUT::$me->options ) ) {
		$options =& WUT::$me->options->get_options( 'other' );
		if ( $options['enabled'] ) {
			if ( $options['perma_struct'] ) : // if the user enabled the permalink feature in wp.
				$permalink      = $options['perma_struct'];
				$rewritecode    = array(
					'%year%',
					'%monthnum%',
					'%day%',
					'%hour%',
					'%minute%',
					'%second%',
					'%postname%',
					'%post_id%',
					'%category%',
					'%author%',
					'%pagename%',
				);
				$date           = explode( ' ', date_create( $post->post_date )->format( 'Y m d H i s' ) );
				$rewritereplace = array(
					$date[0],
					$date[1],
					$date[2],
					$date[3],
					$date[4],
					$date[5],
					$post->post_name,
					$post->ID,
					'%error%',                     // cannot fetch category info.
					'%error%',                     // cannot fetch author info.
					$post->post_name,
				);
				$permalink      = $options['wphome'] . str_replace( $rewritecode, $rewritereplace, $permalink );
				$permalink      = user_trailingslashit( $permalink, 'single' );
			else :                          // if user use default link structure.
				$permalink = $options['wphome'] . '/?p=' . $post->ID;
			endif;
			if ( strpos( $permalink, '%error%' ) == false ) {
				return $permalink;
			}
		}
	}
	if ( function_exists( 'custom_get_permalink' ) ) {
		return custom_get_permalink( $post );
	} else {
		return get_permalink( $post->ID );
	}
}

/**
 * Template tag: Recent posts.
 *
 * @param array $args arguments to control the template tag.
 */
function wut_recent_posts( $args = array() ) {
	$defaults = array(
		'limit'       => 5,
		'offset'      => 0,
		'before'      => '<li>',
		'after'       => '</li>',
		'type'        => 'post', // the value of type could be `post`, `page` or `both`.
		'skips'       => '', // comma seperated post_ID list.
		'none'        => __( 'No Posts.', 'wut' ), // tips to show when results is empty.
		'password'    => 'hide',
		'orderby'     => 'post_date', // 'post_modified' is alternative.
		'xformat'     => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'date_format' => '',
		'echo'        => 1, // to show all results or just return.
	);
	$r        = wp_parse_args( $args, $defaults );

	$query = new WP_Query(
		array(
			'posts_per_page'      => $r['limit'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array_filter( explode( ',', $r['skips'] ) ),
			'orderby'             => $r['orderby'],
			'has_password'        => ! 'hide' === $r['password'],
		)
	);

	$html = '';
	if ( ! $query->have_posts() ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $query->posts as $post ) {
			$record   = wut_private_render_template_by_post( $r['xformat'], $post, $r['date_format'] );
			$sanitize = apply_filters( 'wut_recent_post_item', $record, $post );
			$html    .= $r['before'] . $sanitize . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Template tag print most viewed posts list.
 *
 * @param array $args Arguments array.
 * @return string|void
 */
function wut_most_viewed_posts( $args = array() ) {
	$defaults = array(
		'limit'    => 5,
		'offset'   => 0,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'post',
		'skips'    => '',
		'none'     => __( 'No Posts.', 'wut' ),
		'password' => 'hide',
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%viewcount%)',
		'echo'     => 1,
	);
	$r        = wp_parse_args( $args, $defaults );

	$date  = date_create()->sub( new DateInterval( 'P' . $r['time_range'] . 'D' ) );
	$query = new WP_Query(
		array(
			'posts_per_page'      => $r['limit'],
			'offset'              => $r['offset'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array_filter( explode( ',', $r['skips'] ) ),
			'meta_key'            => 'views',
			'orderby'             => 'meta_value_num',
			'order'               => 'DESC',
			'has_password'        => ! 'hide' === $r['password'],
			'date_query'          => array(
				array(
					'after' => $date->format( 'Y-m-d H:i:s' ),
				),
			),
		)
	);

	$html = '';
	if ( ! $query->have_posts() ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $query->posts as $post ) {
			$record   = wut_private_render_template_by_post(
				$r['xformat'],
				$post,
				'Y-m-d',
				function( $template ) use ( $post ) {
					return str_replace(
						'%viewcount%',
						get_post_meta( $post->ID, 'views', true ),
						$template
					);
				}
			);
			$sanitize = apply_filters( 'wut_most_viewed_item', $record, $post );
			$html    .= $r['before'] . $sanitize . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Template tag to output ramdom posts.
 *
 * @param array $args Control info.
 * @return string
 */
function wut_random_posts( $args = array() ) {
	if ( ! isset( $args['orderby'] ) || empty( $args['orderby'] ) ) {
		$args['orderby'] = 'RAND(' . wp_rand() . ')';
	}
	return wut_recent_posts( $args );
}

/**
 * Template tag to output related posts.
 *
 * @param array $args Control arguments.
 */
function wut_related_posts( $args = '' ) {
	$defaults = array(
		'postid'     => false,
		'limit'      => 10,
		'offset'     => 0,
		'before'     => '<li>',
		'after'      => '</li>',
		'type'       => 'both',
		'skips'      => '',
		'leastshare' => 1,
		'password'   => 'hide',
		'orderby'    => 'post_date',
		'order'      => 'DESC',
		'xformat'    => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'none'       => 'No Related Posts.',
		'echo'       => 1,
	);
	$r        = wp_parse_args( $args, $defaults );

	$r['password'] = 'hide' === $r['password'] ? 0 : 1;

	$post_ID = $r['postid'];
	if ( false === $post_ID ) {
		global $post;
		$post_ID = $post->ID;
	}
	$r['skips'] .= $post_ID;

	$tag_ids = array_map(
		function( $tag ) {
			return $tag->term_taxonomy_id;
		},
		wp_get_object_terms( $post_ID, 'post_tag' )
	);

	$query = new WP_Query(
		array(
			'posts_per_page'      => $r['limit'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array_filter( explode( ',', $r['skips'] ) ),
			'orderby'             => $r['orderby'],
			'has_password'        => ! 'hide' === $r['password'],
			'tag__in'             => $tag_ids,
		)
	);

	$html = '';
	if ( ! $query->have_posts() ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $query->posts as $p ) {
			$record = wut_private_render_template_by_post( $r['xformat'], $p );
			$record = apply_filters( 'wut_related_post_item', $record, $p );
			$html  .= $r['before'] . $record . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Template tag to display posts list in a category.
 *
 * @param array $args config.
 * @return string
 */
function wut_posts_by_category( $args = array() ) {
	$defaults = array(
		'postid'   => false,
		'orderby'  => 'rand', // 'post_date', 'comment_count', 'post_modified'.
		'order'    => 'asc', // 'desc'
		'before'   => '<li>',
		'after'    => '</li>',
		'limit'    => 5,
		'offset'   => 0,
		'type'     => 'post', // @deprecated
		'skips'    => '',
		'password' => 'hide',
		'none'     => 'No Posts.',
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'echo'     => 1,
	);

	$r = wp_parse_args( $args, $defaults );

	if ( ! isset( $r['postid'] ) || ! $r['postid'] ) {
		global $post;
		$r['postid'] = $post->ID;
	}

	if ( empty( $r['skips'] ) ) {
		$r['skips'] = $r['postid'];
	} else {
		$r['skips'] .= ',' . $r['postid'];
	}

	$categories   = wp_get_object_terms( $r['postid'], 'category' );
	$category_ids = array();
	foreach ( $categories as $category ) {
		$category_ids[] = $category->term_taxonomy_id;
	}

	$query = new WP_Query(
		array(
			'posts_per_page'      => $r['limit'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'category__in'        => $category_ids,
			'post__not_in'        => array_filter( explode( ',', $r['skips'] ) ),
			'orderby'             => $r['orderby'],
			'has_password'        => ! 'hide' === $r['password'],
		)
	);

	$html = '';
	if ( ! $query->have_posts() ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $query->posts as $p ) {
			$record = wut_private_render_template_by_post( $r['xformat'], $p );
			$record = apply_filters( 'wut_same_classified_post_item', $record, $p );
			$html  .= $r['before'] . $record . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Templdate tag to display most commented posts list.
 *
 * @param array $args config.
 * @return string
 */
function wut_most_commented_posts( $args = array() ) {
	if ( ! isset( $args['orderby'] ) || empty( $args['orderby'] ) ) {
		$args['orderby'] = 'comment_count';
	}
	return wut_recent_posts( $args );
}

/**
 * Template tag to display recent comments list.
 *
 * @param array $args Config.
 * @return string
 */
function wut_recent_comments( $args = array() ) {
	$defaults = array(
		'limit'       => 5,
		'offset'      => 0,
		'before'      => '<li>',
		'after'       => '</li>',
		'length'      => 50,
		'skipusers'   => '',
		'avatarsize'  => 16,
		'password'    => 'hide',
		'posttype'    => 'post',
		'commenttype' => 'comment',
		'xformat'     => '%gravatar%<a class="commentator" href="%permalink%" >%commentauthor%</a> : %commentexcerpt%',
		'echo'        => 1,
	);
	$r        = wp_parse_args( $args, $defaults );

	$r['password'] = 'hide' === $r['password'] ? 0 : 1;

	$query = new WP_Comment_Query(
		array(
			'number'         => $r['limit'],
			'offset'         => $r['offset'],
			'author__not_in' => array_filter( explode( ',', $r['skipusers'] ) ),
			'orderby'        => 'comment_date',
			'type'           => 'comment',
		)
	);

	$comments = $query->get_comments();
	$html     = '';
	foreach ( $comments as $comment ) {
		$permalink = get_the_permalink( $comment->comment_post_ID ) . '#comment-' . $comment->comment_ID;
		$content   = mb_substr( wp_strip_all_tags( $comment->comment_content ), 0, $r['length'] ) . '...';
		$record    = $r['before'] . $r['xformat'] . $r['after'];
		$record    = str_replace(
			array(
				'%gravatar%',
				'%permalink%',
				'%commentauthor%',
				'%commentexcerpt%',
				'%posttitle%',
			),
			array(
				get_avatar( $comment->comment_author_email, $r['avatarsize'] ),
				$permalink,
				$comment->comment_author,
				$content,
				get_the_title( $comment->comment_post_ID ),
			),
			$record
		);
		$record    = apply_filters( 'wut_recent_comment_item', $record, $comment );
		$html     .= $record . "\n";
	}

	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Template tag to display active commentators list.
 *
 * @param array $args config.
 * @return string
 */
function wut_active_commentators( $args = array() ) {
	$defaults = array(
		'limit'      => 10, // -1 to disable the limit
		'threshhold' => 2,
		'avatarsize' => 16,
		'days'       => -1,
		'skipusers'  => 'admin', // comma seperated name list.
		'before'     => '<li class="wkc_most_active">',
		'after'      => '</li>',
		'none'       => 'No Results.',
		'xformat'    => '%avatar%<a href="%url%" rel="nofollow">%author%</a>',
		'echo'       => 1,
	);

	$r = wp_parse_args( $args, $defaults );

	$items = WUT::$me->query->get_active_commentators( $r );

	$html = '';
	if ( empty( $items ) ) {
		$html = $before . $none . $after;
	} else {
		foreach ( $items as $item ) {
			if ( $item->comment_total < $threshhold ) {
				if ( empty( $html ) ) {
					$html = $before . $none . $after;
				}
				break;
			}
			$html .= $before . $xformat;
			$html  = str_replace( '%author%', $item->comment_author, $html );
			$html  = str_replace( '%url%', $item->comment_author_url, $html );
			$html  = str_replace( '%avatar%', get_avatar( $item->comment_author_email, $avatarsize ), $html );
			$html  = apply_filters( 'wut_active_commentator_item', $html, $item );
			$html .= $after;
		}
	}

	if ( $echo ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Template tag to display recent commentators list.
 *
 * @param array $args Config.
 * @return string
 */
function wut_recent_commentators( $args = array() ) {
	$defaults = array(
		'limit'      => 10, // -1 to disable
		'offset'     => 0,
		'threshhold' => -1, // minus one to disable this functionality.
		'type'       => 'month', // 'month' is the alternative
		'skipusers'  => 'admin',
		'before'     => '<li>',
		'after'      => '</li>',
		'none'       => 'No Results.',
		'avatarsize' => 16,
		'xformat'    => '%avatar%<a href="%url%" rel="nofollow">%author%</a>',
		'echo'       => 1,
	);
	$r        = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$query_args = compact( 'limit', 'offset', 'skipusers', 'type' );
	$items      = WUT::$me->query->get_recent_commentators( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		$html = $before . $none . $after;
	} else {
		foreach ( $items as $item ) {
			if ( $item->comment_total < $threshhold ) {
				if ( empty( $html ) ) {
					$html = $before . $none . $after;
				}
				break;
			}
			$html .= $before . $xformat;
			$html  = str_replace( '%author%', $item->comment_author, $html );
			$html  = str_replace( '%url%', $item->comment_author_url, $html );
			$html  = str_replace( '%avatar%', get_avatar( $item->comment_author_email, $avatarsize ), $html );
			$html  = apply_filters( 'wut_active_commentator_item', $html, $item );
			$html .= $after;
		}
	}
	if ( $echo ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
}

/**
 * Print a piece of HTML code.
 *
 * @param string $html HTML string.
 * @return void
 */
function wut_print_html( $html ) {
	echo $html;
}

/**
 * Render a template string by post object.
 *
 * @param string   $template Template string.
 * @param WP_Post  $post Post object.
 * @param string   $date_format Date format string.
 * @param Callable $custom Custom render.
 * @return string
 */
function wut_private_render_template_by_post( $template, $post, $date_format = 'Y-m-d', $custom = null ) {
	$result = str_replace(
		array(
			'%title%',
			'%postdate%',
			'%commentcount%',
			'%permalink%',
		),
		array(
			get_the_title( $post ),
			get_the_date( $date_format, $post->ID ),
			$post->comment_count,
			get_the_permalink( $post->ID ),
		),
		$template
	);

	if ( ! is_null( $custom ) && is_callable( $custom ) ) {
		$result = call_user_func( $custom, $result );
	}
	return $result;
}
