<?php
/**
 * All the queries.
 *
 * All the Database queries used in this plugin are
 * put here.
 * 
 */
class WUT_QueryBox{
    /**
     * @version 1.0
     * @author Charles
     */
    function get_recent_posts($args = ''){
        global $wpdb;
        $defaults = array(
            'offset'    =>    0,
            'limit'     =>    10,
            'type'      =>    'post', //'both' or 'page' 
            'skips'     =>    '',
            'password'  =>    0, //show password protected post or not
            'orderby'   =>    'post_date'  //or 'post_modified'
        );

        $r = wp_parse_args($args, $defaults);

        $posttype = $this->_post_type_clause($r['type']);
        $skipclause = $this->_skip_clause('ID', $r['skips']);
        $password = $this->_password_clause($r['password']);
        $orderby = $this->_orderby_clause($r['orderby'], 'DESC');
        
        $query = "SELECT ID, post_author, post_title, post_date, post_content,
                        post_name, post_excerpt, post_modified, comment_count
                  FROM {$wpdb->posts}
                  WHERE post_status = 'publish'
                  {$password}
                  {$posttype}
                  {$skipclause}
                  {$orderby}
                  LIMIT {$r['offset']},{$r['limit']}";
        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_random_posts($args = ''){
        global $wpdb;
        $defaults = array(
            'limit'    =>   10,
            'type'     =>   'post',
            'skips'    =>   '',
            'password' =>   0
        );

        $r = wp_parse_args($args,$defaults);

        $posttype = $this->_post_type_clause($r['type']);
        $skipclause = $this->_skip_clause('ID', $r['skips']);
        $password = $this->_password_clause($r['password']);
        
        $query = "SELECT ID, post_author, post_title, post_date, post_content,
                        post_name, post_excerpt, post_modified, comment_count
                  FROM {$wpdb->posts}
                  WHERE post_status = 'publish'
                  {$password}
                  {$posttype}
                  {$skipclause}
                  ORDER BY RAND()
                  LIMIT {$r['limit']}";
        return $wpdb->get_results($query);
    }

    /**
     *
     * @version 1.0
     * @author Charles
     */
    function get_related_posts($args = ''){
        global $wpdb;
        $defaults = array(
            'offset'    => 0,
            'limit'     => 10,
            'postid'    => false,
            'type'      => 'both', //@deprecated, begin from 2.7 page does not have tags or cates
            'skips'     => '',
            'leastshare'=> 1,
            'password'  => 0,
            'orderby'   => 'post_date',
            'order'     => 'DESC'
        );
        $r = wp_parse_args($args,$defaults);

        $r['postid'] = (int) $r['postid'];
        if (!$r['postid']) {
            global $post;
            $r['postid'] = $post->ID;
        }

        $tags = wp_get_object_terms($r['postid'],'post_tag');

        $tag_ids = '';
        foreach($tags as $tag) {
            $tag_ids .= '"' . $tag->term_id . '", ';
        }
        $tag_ids = substr($tag_ids, 0, strlen($tag_ids) - 2);
        if(empty($tag_ids)){
            return '';
        }

        $posttype = $this->_post_type_clause($r['type']);
        $skipclause = $this->_skip_clause('ID', $r['skips']);
        $password = $this->_password_clause($r['password']);

        $query  = "SELECT ID, post_author, post_title, post_date, post_content,
                        post_name, post_excerpt, post_modified, comment_count,
                       COUNT(tr.object_id) as max_share
                   FROM {$wpdb->posts}
                   INNER JOIN {$wpdb->term_relationships} AS tr
                              ON (ID = tr.object_id)
                   INNER JOIN {$wpdb->term_taxonomy} AS tt
                              ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                   WHERE tt.taxonomy = 'post_tag'
                   AND ID <> {$r['postid']}
                   {$password}
                   {$posttype}
                   {$skipclause}
                   AND post_status = 'publish'
                   AND tt.term_id IN ({$tag_ids})
                   GROUP BY tr.object_id 
                   ORDER BY max_share DESC, {$r['orderby']} {$r['order']}
                   LIMIT {$r['offset']}, {$r['limit']}";

        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_posts_by_category($args = ''){
        global $wpdb;
        $defaults = array(
            'offset'        => 0,
            'limit'         => 10,
            'postid'        => false,
            'skips'         => '',
            'type'          => 'both', //@deprecated
            'orderby'       => 'post_date', //'post_modified', 'comment_count' 'rand'
            'order'         => 'DESC',
            'password'      => 0
        );

        $r = wp_parse_args($args, $defaults);

        $r['postid'] = (int) $r['postid'];
        if (!$r['postid']) {
            global $post;
            $r['postid'] = $post->ID;
        }

        $categories = wp_get_object_terms($r['postid'],'category');

        $cat_ids = '';
        foreach($categories as $cat) {
            $cat_ids .= '"' . $cat->term_id . '", ';
        }
        $cat_ids = substr($cat_ids, 0, strlen($cat_ids) - 2);

        $posttype = $this->_post_type_clause($r['type']);
        $skipclause = $this->_skip_clause('ID', $r['skips']);
        $password = $this->_password_clause($r['password']);
        if ($r['orderby'] !== 'rand'){
            $orderby = $this->_orderby_clause($r['orderby'],$r['order']);
            $orderby = str_replace('ORDER BY ','ORDER BY max_share, ',$orderby);
        }else{
            $orderby = "ORDER BY RAND()";
        }
        
        $query  = "SELECT ID, post_author, post_title, post_date, post_content,
                        post_name, post_excerpt, post_modified, comment_count,
                        COUNT(tr.object_id) as max_share
                   FROM {$wpdb->posts}
                   INNER JOIN {$wpdb->term_relationships} AS tr
                              ON (ID = tr.object_id)
                   INNER JOIN {$wpdb->term_taxonomy} AS tt
                              ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                   WHERE post_status='publish' AND tt.taxonomy = 'category'
                   {$posttype}
                   {$skipclause}
                   {$password}
                   AND ID <> {$r['postid']}
                   AND tt.term_id IN ({$cat_ids})
                   GROUP BY tr.object_id
                   {$orderby}
                   LIMIT {$r['offset']}, {$r['limit']}";

        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_most_commented_posts($args = ''){
        global $wpdb;
        $defaults = array(
            'offset'            => 0,
            'limit'             => 10,
            'type'              => 'post',
            'skips'             => '',
            'password'          => 0,
            'days'              => 30 //use -1 to disable the time limit
        );
        $r = wp_parse_args($args,$defaults);

        $posttype = $this->_post_type_clause($r['type']);
        $skipclause = $this->_skip_clause('ID', $r['skips']);
        $password = $this->_password_clause($r['password']);

	    $days = intval($r['days']);
		if ($days > 0){
			$limit_date = current_time('timestamp') - ($days*86400);
			$limit_date = date('Y-m-d H:i:s', $limit_date);
			$days = "AND post_date < '" . current_time('mysql')
                ."' AND post_date > '" . $limit_date."'";
		}else{
			$days = '';
		}
        $query  = "SELECT ID, post_author, post_title, post_date, post_content,
                        post_name, post_excerpt, post_modified, comment_count
                   FROM {$wpdb->posts}
                   WHERE post_status='publish'
                   {$password}
                   {$posttype}
                   {$skipclause}
                   {$days}
                   ORDER BY comment_count DESC
                   LIMIT {$r['offset']},{$r['limit']}";
        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_recent_comments($args = ''){
        global $wpdb;
        $defaults = array(
            'limit'         => 10,
            'offset'        => 0,
            'skipusers'     => '',     //comma seperated name list
            'password'      => 0,
            'postid'        => false,
            'posttype'      => 'both', //'page' or 'post'
            'commenttype'   => 'comment'      //'pingback' or 'trackback'
        );
        $r = wp_parse_args($args,$defaults);
        $skipuserclause = $this->_skip_clause("comment_author", $r['skipusers']);
        $posttype = $this->_post_type_clause($r['posttype']);
        $password = $this->_password_clause($r['password']);
        switch($r['commenttype']){
            case 'comment':
                $commenttype = "AND comment_type=''";
                break;
            case 'pingback':
                $commenttype = "AND comment_type='pingback'";
                break;
            case 'trackback':
                $commenttype = "AND comment_type='trackback'";
                break;
            default:
                $commenttype = "";
        }
        if($r['postid']){
            $r['postid'] = (int) $r['postid'];
            $belongpost = "AND ID='{$r['postid']}'";
        }else{
            $belongpost = "";
        }

        $query = "SELECT ID, comment_ID, comment_content, comment_author,
                         comment_author_url, comment_author_email, post_title,
                         comment_date, post_name, comment_type
                  FROM {$wpdb->posts},{$wpdb->comments}
                  WHERE ID = comment_post_ID
                  AND (post_status = 'publish' OR post_status = 'static')
                  {$belongpost}
                  {$password}
                  {$posttype}
                  {$commenttype}
                  {$skipuserclause}
                  AND (comment_author != '')
                  AND comment_approved = '1'
                  ORDER BY comment_date DESC
                  LIMIT {$r['offset']}, {$r['limit']}";

        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_active_commentators($args = ''){
        global $wpdb;
        $defaults = array(
            'limit'         => 10, //-1 to disable the limit
            'offset'        => 0,
            'skipusers'     => '',
            'days'          => 15  //-1 to disable the days limit
        );

        $r = wp_parse_args($args, $defaults);
        $skipuserclause = $this->_skip_clause("comment_author", $r['skipusers']);
        if ($r['limit'] < 0) {
            $limit = '';
        }else{
            $limit = "LIMIT {$r['offset']},{$r['limit']}";
        }
        if ($r['days'] > 0){
			$limit_date = current_time('timestamp') - ($r['days']*86400);
			$limit_date = date('Y-m-d H:i:s', $limit_date);
			$days = 'AND comment_date > "'.$limit_date.'" ';
		}else {
            $days = '';
        }

        $query  = "SELECT comment_author, comment_author_url,comment_author_email,
                          COUNT(comment_ID) AS comment_total
                   FROM {$wpdb->comments}
                   WHERE comment_approved = '1'
                   {$skipuserclause}
                   AND (comment_author != '') AND (comment_type = '')
                   {$days}
                   GROUP BY comment_author
                   ORDER BY comment_total DESC
                   {$limit}";
        return $wpdb->get_results($query);
    }

    /**
     * @version 1.0
     * @author Charles
     */
    function get_recent_commentators($args = ''){
        global $wpdb;
        $defaults = array(
            'limit'         => 10,
            'offset'        => 0,
            'skipusers'     => '',
            'type'          => 'week'
        );

        $r = wp_parse_args($args,$defaults);

        $skipuserclause = $this->_skip_clause("comment_author", $r['skipusers']);

        if ($r['type'] == 'week'){
			$type = 'AND YEARWEEK(comment_date) = YEARWEEK(NOW())';
		}elseif ($r['type'] == 'month'){
			$type = 'AND (MONTH(comment_date) = MONTH(NOW()) AND YEAR(comment_date) = YEAR(NOW()))';
		}else{
            $type = '';
        }
        if ($r['limit'] < 0) {
            $limit = '';
        }else{
            $limit = "LIMIT {$r['offset']},{$r['limit']}";
        }
        $query  = "SELECT comment_author, comment_author_url, comment_author_email,
                          COUNT(comment_ID) AS 'comment_total'
                   FROM {$wpdb->comments}
                   WHERE comment_approved = '1'
                   AND (comment_author != '') AND (comment_type = '')
                   {$skipuserclause}
                   {$type}
                   GROUP BY comment_author
                   ORDER BY comment_total DESC
                   {$limit}";
        return $wpdb->get_results($query);
    }

    function _post_type_clause($posttype){
        if ('both' == $posttype){
            return '';
        } else if ('post' == $posttype) {
            return 'AND post_type = \'post\'';
        } else if ('page' == $posttype) {
            return 'AND post_type = \'page\'';
        } else {
            return '';
        }
    }

    function _skip_clause($filedtoskip, $skipstr){
        if (empty($skipstr)) return '';
        $skips = explode(',', $skipstr);
        $skips = implode('\',\'', $skips);
        return "AND {$filedtoskip} NOT IN('{$skips}')";
    }

    function _password_clause($show){
        if(!$show){
            return "AND post_password = '' ";
        }else{
            return " ";
        }
    }

    function _orderby_clause($field, $order){
        //TODO: validate the $field name
        return "ORDER BY {$field} {$order}";
    }
}/*End class WUT_QueryBox*/
?>