<?php
/**
 * All the template tags user can use are in this
 * file.
 */


function _wut_get_permalink( $post ) {
	if ( isset( WUT::$me->options ) ) {
		$options =& WUT::$me->options->get_options( 'other' );
		if ( $options['enabled'] ) {
			if ( $options['perma_struct'] ) : // if the user enabled the permalink feature in wp
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
				$unixtime       = strtotime( $post->post_date );
				$date           = explode( ' ', date( 'Y m d H i s', $unixtime ) );
				$rewritereplace = array(
					$date[0],
					$date[1],
					$date[2],
					$date[3],
					$date[4],
					$date[5],
					$post->post_name,
					$post->ID,
					'%error%',                     // cannot fetch category info
					'%error%',                     // cannot fetch author info
					$post->post_name,
				);
				$permalink      = $options['wphome'] . str_replace( $rewritecode, $rewritereplace, $permalink );
				$permalink      = user_trailingslashit( $permalink, 'single' );
			else :                          // if user use default link structure
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
 * @version 1.0
 * @author Charles
 */
function wut_recent_posts( $args = '' ) {
	$defaults = array(
		'limit'    => 5, // how many items should be show
		'offset'   => 0,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'both', // 'post' or 'page' or 'both'
		'skips'    => '', // comma seperated post_ID list
		'none'     => 'No Posts.', // tips to show when results is empty
		'password' => 'hide', // show password protected post or not
		'orderby'  => 'post_date', // 'post_modified' is alternative
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'echo'     => 1,
	);
	$r        = wp_parse_args( array_filter( $args ), $defaults );
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact( 'limit', 'offset', 'type', 'skips', 'password', 'orderby' );
	$items      = WUT::$me->query->get_recent_posts( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $items as $item ) {
			$permalink    = _wut_get_permalink( $item );
			$record       = str_replace(
				array(
					'%permalink%',
					'%title%',
					'%postdate%',
					'%commentcount%',
				),
				array(
					$permalink,
					$item->post_title,
					$item->post_date,
					$item->comment_count,
				),
				$r['xformat']
			);
				$sanitize = apply_filters( 'wut_recent_post_item', $record, $item );
				$html    .= $r['before'] . $sanitize . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}

function wut_most_viewed_posts( $args = '' ) {
	$defaults = array(
		'limit'    => 5, // how many items should be show
		'offset'   => 0,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'post', // 'post' or 'page' or 'both'
		'skips'    => '', // comma seperated post_ID list
		'none'     => 'No Posts.', // tips to show when results is empty
		'password' => 'hide', // show password protected post or not
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%viewcount%)',
		'echo'     => 1,
	);
	$r        = wp_parse_args( array_filter( $args ), $defaults );
	extract( $r, EXTR_SKIP );

	$password   = ( 'hide' === $password ) ? 0 : 1;
	$query_args = compact( 'limit', 'offset', 'type', 'skips', 'password', 'time_range' );
	$items      = WUT::$me->query->get_most_viewed_posts( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		$html = $r['before'] . $r['none'] . $r['after'];
	} else {
		foreach ( $items as $item ) {
			$permalink    = _wut_get_permalink( $item );
			$record       = str_replace(
				array(
					'%permalink%',
					'%title%',
					'%postdate%',
					'%viewcount%',
				),
				array(
					$permalink,
					$item->post_title,
					get_date_from_gmt( $item->post_date_gmt, 'Y-m-d' ),
					$item->post_views,
				),
				$r['xformat']
			);
				$sanitize = apply_filters( 'wut_recent_post_item', $record, $item );
				$html    .= $r['before'] . $sanitize . $r['after'] . "\n";
		}
	}
	if ( $r['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_random_posts( $args = '' ) {
	global $wut;
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
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact( 'limit', 'type', 'skips', 'password' );
	$items      = $wut->query->get_random_posts( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		$html .= $before . $none . $after;
	} else {
		foreach ( $items as $item ) {
			$permalink = _wut_get_permalink( $item );
			$html     .= $before . $xformat;
			$html      = str_replace( '%permalink%', $permalink, $html );
			$html      = str_replace( '%title%', $item->post_title, $html );
			$html      = str_replace( '%postdate%', $item->post_date, $html );
			$html      = str_replace( '%commentcount%', $item->comment_count, $html );
			$html      = apply_filters( 'wut_random_post_item', $html, $item );
			$html     .= $after . "\n";
		}
	}
	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_related_posts( $args = '' ) {
	global $wut;
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
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact(
		'offset',
		'limit',
		'postid',
		'skips',
		'password',
		'orderby',
		'order',
		'leastshare',
		'type'
	);
	$items      = $wut->query->get_related_posts( $query_args );

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
			$html      = apply_filters( 'wut_related_post_item', $html, $item );
			$html     .= $after . "\n";
		}
	}
	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_posts_by_category( $args = '' ) {
	global $wut;
	$defaults = array(
		'postid'   => false,
		'orderby'  => 'rand', // 'post_date', 'comment_count', 'post_modified'
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
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact( 'offset', 'limit', 'postid', 'skips', 'type', 'orderby', 'order', 'password' );
	$items      = $wut->query->get_posts_by_category( $query_args );

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
		echo $html;
	} else {
		return $html;
	}
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_most_commented_posts( $args = '' ) {
	global $wut;
	$defaults = array(
		'limit'    => 5,
		'offset'   => 0,
		'days'     => 7,
		'before'   => '<li>',
		'after'    => '</li>',
		'type'     => 'post', // 'page', 'both'
		'skips'    => '',
		'days'     => 30, // use -1 to disable the time limit
		'password' => 'hide',
		'none'     => 'No Posts.',
		'xformat'  => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
		'echo'     => 1,
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact( 'offset', 'limit', 'type', 'skips', 'password', 'days' );
	$items      = $wut->query->get_most_commented_posts( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		$html = $before . $none . $after;
	}
	foreach ( $items as $item ) {
			$permalink = _wut_get_permalink( $item );
			$html     .= $before . $xformat;
			$html      = str_replace( '%permalink%', $permalink, $html );
			$html      = str_replace( '%title%', htmlspecialchars( $item->post_title ), $html );
			$html      = str_replace( '%postdate%', $item->post_date, $html );
			$html      = str_replace( '%commentcount%', $item->comment_count, $html );
			$html      = apply_filters( 'wut_most_commented_post_item', $html, $item );
			$html     .= $after . "\n";
	}
	if ( $r['echo'] ) {
		echo $html;
	} else {
		return $html;
	}
}
/**
 * @version 1.0
 * @author Charles
 */
function wut_recent_comments( $args = '' ) {
	$defaults = array(
		'limit'       => 5,
		'offset'      => 0,
		'before'      => '<li>',
		'after'       => '</li>',
		'length'      => 50,
		'skipusers'   => '', // comma seperated name list
		'avatarsize'  => 16,
		'password'    => 'hide',
		'posttype'    => 'post',
		'commenttype' => 'comment',
		'xformat'     => '%gravatar%<a class="commentator" href="%permalink%" >%commentauthor%</a> : %commentexcerpt%',
		'echo'        => 1,
	);
	$r        = wp_parse_args( array_filter( $args ), $defaults );
	extract( $r, EXTR_SKIP );

	$password   = $password == 'hide' ? 0 : 1;
	$query_args = compact( 'limit', 'offset', 'skipusers', 'password', 'posttype', 'commenttype' );
	$items      = WUT::$me->query->get_recent_comments( $query_args );

	$html = '';
	if ( empty( $items ) ) {
		if ( $echo ) {
			echo '';
		} else {
			return '';
		}
	} else {
		foreach ( $items as $item ) {
			$permalink       = _wut_get_permalink( $item ) . '#comment-' . $item->comment_ID;
			$comment_content = mb_substr( strip_tags( $item->comment_content ), 0, $length ) . '...';
			$html           .= $before . $xformat;
			$html            = str_replace( '%gravatar%', get_avatar( $item->comment_author_email, $avatarsize ), $html );
			$html            = str_replace( '%permalink%', $permalink, $html );
			$html            = str_replace( '%commentauthor%', $item->comment_author, $html );
			$html            = str_replace( '%commentexcerpt%', $comment_content, $html );
			$html            = str_replace( '%posttile%', $item->post_title, $html );
			$html            = apply_filters( 'wut_recent_comment_item', $html, $item );
			$html           .= $after . "\n";
		}
	}
	if ( $echo ) {
		echo $html;
	} else {
		return $html;
	}
}
/**
 * @version 1.0
 * @author Charles
 */
function wut_active_commentators( $args = '' ) {
	global $wut;
	$defaults = array(
		'limit'      => 10, // -1 to disable the limit
		'threshhold' => 2,
		'avatarsize' => 16,
		'days'       => -1,
		'skipusers'  => 'admin', // comma seperated name list
		'before'     => '<li class="wkc_most_active">',
		'after'      => '</li>',
		'none'       => 'No Results.',
		'xformat'    => '%avatar%<a href="%url%" rel="nofollow">%author%</a>',
		'echo'       => 1,
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	$query_args = compact( 'limit', 'offset', 'skipusers', 'days' );
	$items      = $wut->query->get_active_commentators( $query_args );

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
		echo $html;
	} else {
		return $html;
	}
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_recent_commentators( $args = '' ) {
	global $wut;
	$defaults = array(
		'limit'      => 10, // -1 to disable
		'offset'     => 0,
		'threshhold' => -1, // minus one to disable this functionality
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
	$items      = $wut->query->get_recent_commentators( $query_args );

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
		echo $html;
	} else {
		return $html;
	}
}

