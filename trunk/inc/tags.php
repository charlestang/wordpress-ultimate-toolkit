<?php
/**
 * All the template tags user can use are in this 
 * file.
 */
function wut_recent_posts($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'			=> 5, 
		'offset'		=> 0,
		'before'		=> '<li>', 
		'after'			=> '</li>', 
		'skipposts'	=> '',
		'echo'			=> 1
	);
	$r = wp_parse_args($args, $defaults);
	$items = $wut_querybox->get_recent_posts($r);
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= strip_tags($item->post_title);
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_random_posts($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'				=> 5,
		'length'			=> 400, 
		'before'			=> '<li>', 
		'after'				=> '</li>', 
		'showexcerpt'	=> 1,
		'echo'				=> 1
	);
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_random_posts($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= strip_tags($item->post_title);
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_related_posts($args = '') {
	global $wut_querybox;
	$defaults = array(
		'postid'				=> false,
		'limit'					=> 10,
		'offset'				=> 0,
		'before'				=> '<li>',
		'after'					=> '</li>',
		'commentcount'	=> 1,
		'echo'					=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_related_posts($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= strip_tags($item->post_title);
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_same_classified_posts($args = '') {
	global $wut_querybox;
	$defaults = array(
		'postid'				=> false,
		'orderby'				=> 'rand', //'date', 'comment_count'
		'order'					=> 'asc', //'desc'
		'before'				=> '<li>',
		'after'					=> '</li>',
		'limit'					=> 5,
		'offset'				=> 0,
		'commentcount'	=> 1,
		'echo'					=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_same_classified_posts($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= strip_tags($item->post_title);
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_most_commented_posts($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'				=> 5,
		'offset'			=> 0,
		'days'				=> 7,
		'before'			=> '<li>',
		'after'				=> '</li>',
		'type'				=> 'post', //'page', 'both'
		'commentcount'=> 1,
		'echo'				=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_most_commented_posts($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= strip_tags($item->post_title);
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_recent_comments($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'				=> 5,
		'offset'			=> 0,
		'before'			=> '<li>',
		'after'				=> '</li>',
		'length'			=> 50,
		'skipusers'		=> 'admin', //comma seperated name list
		'avatarsize'	=> 16,
		'xformat'			=> '<a class="commentor" href="%comment_author_url%" >%comment_author%</a> : <a class="comment_content" href="%permalink%" title="View the entire comment by %comment_author%" >%comment_excerpt%</a>',
		'echo'				=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_recent_comments($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= $item->comment_author . ':' . $item->comment_content;
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_active_commentators($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'				=> 10,
		'threshhold'	=> 5, 
		'days'				=> 7, 
		'skipusers'		=> 'admin',//comma seperated name list
		'before'			=> '<li class="wkc_most_active">',
		'after'				=> '</li>',
		'echo'				=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_active_commentators($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= $item->comment_author;
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}
function wut_recent_commentators($args = '') {
	global $wut_querybox;
	$defaults = array(
		'limit'				=> 10,
		'threshhold'	=> -1, //minus one to disable this functionality 
		'type'				=> 'week',
		'skipusers'		=> 'admin',
		'before'			=> '<li class="wkc_recent_commentors">',
		'after'				=> '</li>',
		'echo'				=> 1
	);	
	$r = wp_parse_args($args, $defaults);
	
	$items = $wut_querybox->get_recent_commentators($r);
	
	$html = '';
	foreach($items as $item){
		$html .= $r['before'];
		$html .= $item->comment_author;
		$html .= $r['after'];
	}
	if ($r['echo'])
		echo $html;
	else
		return $html;
}

?>