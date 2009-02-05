<?php
/**
 * All the template tags user can use are in this 
 * file.
 */
function _wut_get_permalink($post){
    if(function_exists('custom_get_permalink')){
        return custom_get_permalink($post);
    }else{
        return get_permalink($post->ID);
    }
}
/**
 * @version 1.0
 * @author Charles
 */
function wut_recent_posts($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 5,           //how many items should be show
        'offset'        => 0,
        'before'        => '<li>',
        'after'         => '</li>',
        'type'          => 'both',      //'post' or 'page' or 'both'
        'skips'         => '',          //comma seperated post_ID list
        'none'          => 'No Posts.', //tips to show when results is empty
        'password'      => 'hide',      //show password protected post or not
        'orderby'       => 'post_date', //'post_modified' is alternative
        'xformat'       => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
        'echo'          => 1
    );
    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    $password = $password == 'hide' ? 0 : 1;
    $query_args = compact("limit", "offset", "type", "skips", "password", "orderby");
    $items = $wut_querybox->get_recent_posts($query_args);

    $html = '';
    if (empty($items)){
        $html = $before . $none . $after;
    }else{
        foreach($items as $item){
            $permalink = _wut_get_permalink($item);
            $html .= $before . $xformat;
            $html = str_replace('%permalink%', $permalink, $html);
            $html = str_replace('%title%', $item->post_title, $html);
            $html = str_replace('%postdate%', $item->post_date, $html);
            $html = str_replace('%commentcount%', $item->comment_count, $html);
            $html = apply_filters('wut_recent_post_item', $html, $item);
            $html .= $after . "\n";
        }
    }
    if ($echo)
        echo $html;
    else
        return $html;
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_random_posts($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 5,
        'before'        => '<li>',
        'after'         => '</li>',
        'type'          => 'post',
        'skips'         => '',
        'none'          => 'No Posts.',
        'password'      => 'hide',
        'xformat'       => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
        'echo'          => 1
    );
    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    $password = $password == 'hide' ? 0 : 1;
    $query_args = compact("limit", "type", "skips", "password");
    $items = $wut_querybox->get_random_posts($query_args);

    $html = '';
    if (empty($items)){
        $html .= $before . $none . $after;
    }else{
        foreach($items as $item){
            $permalink = _wut_get_permalink($item);
            $html .= $before . $xformat;
            $html = str_replace('%permalink%', $permalink, $html);
            $html = str_replace('%title%', $item->post_title, $html);
            $html = str_replace('%postdate%', $item->post_date, $html);
            $html = str_replace('%commentcount%', $item->comment_count, $html);
            $html = apply_filters('wut_random_post_item', $html, $item);
            $html .= $after . "\n";
        }
    }
    if ($echo)
        echo $html;
    else
        return $html;
}

/**
 * @version 1.0
 * @author Charles
 */
function wut_related_posts($args = '') {
    global $wut_querybox;
    $defaults = array(
        'postid'            => false,
        'limit'             => 10,
        'offset'            => 0,
        'before'            => '<li>',
        'after'             => '</li>',
        'type'              => 'both',
        'skips'             => '',
        'leastshare'        => 1,
        'password'          => 'hide',
        'orderby'           => 'post_date',
        'order'             => 'DESC',
        'xformat'           => '<a href="%permalink%" title="View:%title%(Posted on %postdate%)">%title%</a>(%commentcount%)',
        'none'              => 'No Related Posts.',
        'echo'              => 1
    );
    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    $password = $password == 'hide' ? 0 : 1;
    $query_args = compact('offset','limit','postid','skips','password',
        'orderby','order','leastshare','type'
    );
    $items = $wut_querybox->get_related_posts($query_args);

    $html = '';
    if(empty($items)){
        $html = $before . $none . $after;
    }else{
        foreach($items as $item){
            $permalink = _wut_get_permalink($item);
            $html .= $before . $xformat;
            $html = str_replace('%permalink%', $permalink, $html);
            $html = str_replace('%title%', $item->post_title, $html);
            $html = str_replace('%postdate%', $item->post_date, $html);
            $html = str_replace('%commentcount%', $item->comment_count, $html);
            $html = apply_filters('wut_related_post_item', $html, $item);
            $html .= $after . "\n";
        }
    }
    if ($echo)
        echo $html;
    else
        return $html;
}
function wut_same_classified_posts($args = '') {
    global $wut_querybox;
    $defaults = array(
        'postid'            => false,
        'orderby'           => 'rand', //'date', 'comment_count'
        'order'             => 'asc', //'desc'
        'before'            => '<li>',
        'after'             => '</li>',
        'limit'             => 5,
        'offset'            => 0,
        'commentcount'      => 1,
        'echo'              => 1
    );
    $r = wp_parse_args($args, $defaults);

    $items = $wut_querybox->get_same_classified_posts($r);

    $html = '';
    foreach($items as $item){
        $html .= $r['before'];
        $html .= strip_tags($item->post_title);
        $html .= $r['after'] . "\n";
    }
    if ($r['echo'])
        echo $html;
    else
        return $html;
}
function wut_most_commented_posts($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 5,
        'offset'        => 0,
        'days'          => 7,
        'before'        => '<li>',
        'after'         => '</li>',
        'type'          => 'post', //'page', 'both'
        'commentcount'  => 1,
        'echo'          => 1
    );
    $r = wp_parse_args($args, $defaults);

    $items = $wut_querybox->get_most_commented_posts($r);

    $html = '';
    foreach($items as $item){
        $html .= $r['before'];
        $html .= strip_tags($item->post_title);
        $html .= $r['after'] . "\n";
    }
    if ($r['echo'])
        echo $html;
    else
        return $html;
}
/**
 * @version 1.0
 * @author Charles
 */
function wut_recent_comments($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 5,
        'offset'        => 0,
        'before'        => '<li>',
        'after'         => '</li>',
        'length'        => 50,
        'skipusers'     => 'admin', //comma seperated name list
        'avatarsize'    => 16,
        'password'      => 'hide',
        'posttype'      => 'post',
        'commenttype'   => 'comment',
        'xformat'       => '%gravatar%<a class="commentator" href="%permalink%" >%commentauthor%</a> : %commentexcerpt%',
        'echo'          => 1
    );
    $r = wp_parse_args($args, $defaults);
    extract($r, EXTR_SKIP);

    $password = $password == 'hide' ? 0 : 1;
    $query_args = compact('limit','offset','skipusers','password','posttype','commenttype');
    $items = $wut_querybox->get_recent_comments($query_args);

    $html = '';
    if (empty($items)){
        if($echo) echo ''; else return '';
    }else{
        foreach($items as $item){
            $permalink = _wut_get_permalink($item) . "#comment-" . $item->comment_ID;
            $html .= $before . $xformat;
            $html = str_replace('%gravatar%', get_avatar($item->comment_author_email, $avatarsize), $html);
            $html = str_replace('%permalink%', $permalink, $html);
            $html = str_replace('%commentauthor%', $item->comment_author, $html);
            $html = str_replace('%commentexcerpt%', $item->comment_content, $html);
            $html = apply_filters('wut_recent_comment_item', $html, $item);
            $html .= $after . "\n";
        }
    }
    if ($echo)
        echo $html;
    else
        return $html;
}
function wut_active_commentators($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 10,
        'threshhold'    => 5,
        'days'          => 7,
        'skipusers'     => 'admin',//comma seperated name list
        'before'        => '<li class="wkc_most_active">',
        'after'         => '</li>',
        'echo'          => 1
    );
    $r = wp_parse_args($args, $defaults);

    $items = $wut_querybox->get_active_commentators($r);

    $html = '';
    foreach($items as $item){
        $html .= $r['before'];
        $html .= $item->comment_author;
        $html .= $r['after'] . "\n";
    }
    if ($r['echo'])
        echo $html;
    else
        return $html;
}
function wut_recent_commentators($args = '') {
    global $wut_querybox;
    $defaults = array(
        'limit'         => 10,
        'threshhold'    => -1, //minus one to disable this functionality
        'type'          => 'week',
        'skipusers'     => 'admin',
        'before'        => '<li class="wkc_recent_commentors">',
        'after'         => '</li>',
        'echo'          => 1
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