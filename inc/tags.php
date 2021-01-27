<?php
/**
 * Template tags.
 *
 * @package WordPress_Ultimate_Toolkit
 */

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
			$permalink  = get_the_permalink( $post->ID );
			$post_title = get_the_title( $post->ID );
			$record     = str_replace(
				array(
					'%permalink%',
					'%title%',
					'%postdate%',
					'%commentcount%',
				),
				array(
					$permalink,
					( ! empty( $post_title ) ) ? $post_title : __( '(no title)' ),
					get_the_date( $r['date_format'], $post->ID ),
					$post->comment_count,
				),
				$r['xformat']
			);
			$sanitize   = apply_filters( 'wut_recent_post_item', $record, $post );
			$html      .= $r['before'] . $sanitize . $r['after'] . "\n";
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
			$permalink = get_the_permalink( $post->ID );
			$record    = str_replace(
				array(
					'%permalink%',
					'%title%',
					'%postdate%',
					'%viewcount%',
				),
				array(
					$permalink,
					get_the_title( $post->ID ),
					get_the_date( 'Y-m-d', $post->post_date ),
					get_post_meta( $post->ID, 'views', true ),
				),
				$r['xformat']
			);
			$sanitize  = apply_filters( 'wut_most_viewed_item', $record, $post );
			$html     .= $r['before'] . $sanitize . $r['after'] . "\n";
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
 */
function wut_random_posts( $args = '' ) {
	$defaults = array(
		'limit'    => 5,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'post',
		'skips'    => '',
		'none'     => 'No Posts.',
		'password' => 'hide',
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'echo'     => 1,
	);
	$r        = wp_parse_args( $args, $defaults );

	$query = new WP_Query(
		array(
			'post_per_page'       => $r['limit'],
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array_filter( explode( ',', $r['skips'] ) ),
			'orderby'             => 'RAND(' . wp_rand() . ')',
			'has_password'        => ! 'hide' === $r['password'],
		)
	);

	$html = '';
	if ( ! $query->have_posts() ) {
		$html .= $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $query->posts as $post ) {
			$permalink = get_the_permalink( $post->ID );
			$record    = str_replace(
				array(
					'%permalink%',
					'%title%',
					'%postdate%',
					'%commentcount%',
				),
				array(
					$permalink,
					get_the_title( $post->ID ),
					get_the_date( 'Y-m-d', $post->post_date ),
					$post->comment_count,
				),
				$r['xformat']
			);
			$record    = apply_filters( 'wut_random_post_item', $record, $post );
			$html     .= $r['before'] . $record . $r['after'] . "\n";
		}
	}

	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
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
	$items         = WUT::$me->query->get_related_posts( $r );

	$html = '';
	if ( empty( $items ) ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $items as $item ) {
			$permalink = _wut_get_permalink( $item );
			$html     .= $r['before'] . $r['xformat'];
			$html      = str_replace( '%permalink%', $permalink, $html );
			$html      = str_replace( '%title%', $item->post_title, $html );
			$html      = str_replace( '%postdate%', $item->post_date, $html );
			$html      = str_replace( '%commentcount%', $item->comment_count, $html );
			$html      = apply_filters( 'wut_related_post_item', $html, $item );
			$html     .= $r['after'] . "\n";
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

	$r['password'] = 'hide' === $r['password'] ? 0 : 1;
	$items         = WUT::$me->query->get_posts_by_category( $r );

	$html = '';
	if ( empty( $items ) ) {
		$html = $before . $none . $after;
	} else {
		foreach ( $items as $item ) {
			$permalink = _wut_get_permalink( $item );
			$html     .= $before . $xformat;
			$html      = str_replace( '%permalink%', $permalink, $html );
			$html      = str_replace( '%title%', $item->post_title, $html );
			$html      = str_replace( '%postdate%', $item->post_date, $html );
			$html      = str_replace( '%commentcount%', $item->comment_count, $html );
			$html      = apply_filters( 'wut_same_classified_post_item', $html, $item );
			$html     .= $after . "\n";
		}
	}
	if ( $echo ) {
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
	$defaults = array(
		'limit'    => 5,
		'offset'   => 0,
		'days'     => 7,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'post',
		'skips'    => '',
		'days'     => 30, // use -1 to disable the time limit.
		'password' => 'hide',
		'none'     => 'No Posts.',
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'echo'     => 1,
	);

	$r = wp_parse_args( $args, $defaults );

	$r['password'] = 'hide' === $r['password'] ? 0 : 1;
	$items         = WUT::$me->query->get_most_commented_posts( $r );

	$html = '';
	if ( empty( $items ) ) {
		$html = $before . $none . $after;
	}
	foreach ( $items as $item ) {
			$permalink = _wut_get_permalink( $item );
			$html     .= $r['before'] . $r['xformat'];
			$html      = str_replace( '%permalink%', $permalink, $html );
			$html      = str_replace( '%title%', htmlspecialchars( $item->post_title ), $html );
			$html      = str_replace( '%postdate%', $item->post_date, $html );
			$html      = str_replace( '%commentcount%', $item->comment_count, $html );
			$html      = apply_filters( 'wut_most_commented_post_item', $html, $item );
			$html     .= $r['after'] . "\n";
	}
	if ( $r['echo'] ) {
		wut_print_html( $html );
	} else {
		return $html;
	}
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
	// TODO: this may be replaced by WP_Comment_Query objcet API.
	$items = WUT::$me->query->get_recent_comments( $r );

	$html = '';
	foreach ( $items as $item ) {
		$permalink       = _wut_get_permalink( $item ) . '#comment-' . $item->comment_ID;
		$comment_content = mb_substr( wp_strip_all_tags( $item->comment_content ), 0, $r['length'] ) . '...';
		$html           .= $r['before'] . $r['xformat'];
		$html            = str_replace( '%gravatar%', get_avatar( $item->comment_author_email, $r['avatarsize'] ), $html );
		$html            = str_replace( '%permalink%', $permalink, $html );
		$html            = str_replace( '%commentauthor%', $item->comment_author, $html );
		$html            = str_replace( '%commentexcerpt%', $comment_content, $html );
		$html            = str_replace( '%posttile%', $item->post_title, $html );
		$html            = apply_filters( 'wut_recent_comment_item', $html, $item );
		$html           .= $r['after'] . "\n";
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
