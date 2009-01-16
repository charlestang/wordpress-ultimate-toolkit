<?php
/**
 * All the queries.
 *
 * All the Database queries used in this plugin are
 * put here.
 * 
 */
class WUT_QueryBox{

	function get_recent_posts($args = ''){
		global $wpdb;
		$default = array(
			'offset'			=>			0,
			'limit'				=>			10,
			'type'				=>			'both'
			);
		
		$r = wp_parse_args($args, $default);
		
		$posttype = $this->_post_type_clause($r['type']);
		
		$query = "SELECT ID, post_title, post_date, post_content, post_name
						  FROM {$wpdb->posts}
						  WHERE post_status = 'publish'
						  {$posttype}
						  ORDER BY post_date DESC
						  LIMIT {$r['offset']},{$r['limit']}
						  ";
		return $wpdb->get_results($query);
	}
	
	function get_random_posts($args = ''){
		global $wpdb;
		$default = array(
			'limit'			=>			10,
			'type'			=>			'both'
			);
		
		$r = wp_parse_args($args,$default);
		
		$posttype = $this->_post_type_clause($r['type']);
		
		$query = "SELECT ID, post_title, post_date, post_content, post_name 
						  FROM {$wpdb->posts} 
						  WHERE post_status = 'publish'
				  	  {$posttype}
				  	  ORDER BY RAND() 
				  	  LIMIT {$r['limit']}";
		return $wpdb->get_results($query);
	}
	
	function get_related_posts($args = ''){
		global $wpdb;
		$default = array(
			'offset'			=>			0,
			'limit'				=>			10
			);
		
		$query  = "SELECT p.ID, p.post_title, p.post_date, p.comment_count, p.post_name
						   FROM {$wpdb->posts} AS p 
				   		 INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				   		 INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				   		 WHERE tt.taxonomy = 'post_tag'
				   		 AND p.ID <> {$postid}
				   		 AND p.post_status = 'publish'
				   		 AND tt.term_id IN ({$tag_ids})
				   		 GROUP BY tr.object_id
						   LIMIT {$offset}, {$limit} ";
		return $wpdb->get_results($query);
	}
	function get_same_classified_posts($args = ''){
		global $wpdb;
		$default = array(
			'offset'		=>			0,
			'limit'			=>			10
			);
		
		$r = wp_parse_args($args, $default);
		
				$query  = "SELECT p.ID, p.post_title, p.post_date, p.comment_count, p.post_name
				   FROM {$wpdb->posts} AS p
				   INNER JOIN {$wpdb->term_relationships} AS tr ON (p.ID = tr.object_id)
				   INNER JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
				   WHERE tt.taxonomy = 'category'
				   AND p.ID <> {$postid}
				   AND tt.term_id IN ({$cat_ids})
				   GROUP BY tr.object_id
				   ORDER BY $orderby
				   LIMIT $offset, $limit ";
		return $wpdb->get_results($query);
	}
	
	function get_most_commented_posts($args = ''){
		global $wpdb;
		$default = array(
			'offset'			=>			0,
			'limit'				=>			10,
			'type'				=>			'both'
			);
		$r = wp_parse_args($args,$default);
		$posttype = $this->_post_type_clause($r['type']);
		$query  = "SELECT ID, post_title, post_name, COUNT(comment_post_ID) AS comment_total
		    		   FROM {$wpdb->posts} 
		    		   LEFT JOIN {$wpdb->comments} ON ID = comment_post_ID
		    		   WHERE comment_approved = 1
		    		   {$posttype}
		    		   {$days}
		    		   AND post_status = 'publish' AND post_password = ''
		    		   GROUP BY comment_post_ID
		    		   ORDER BY comment_total DESC
		    		   LIMIT {$r['offset']},{$r['limit']}";
		return $wpdb->get_results($query);
	}
	
	function get_recent_comments($args = ''){
		global $wpdb;
		$default = array(
			'limit'			=>			10,
			'offset'		=>			0
			);
		$r = wp_parse_args($args,$default);
		
		$query = "SELECT ID, comment_ID, comment_content, comment_author, comment_author_url, comment_author_email, post_title, comment_count
						  FROM {$wpdb->posts},{$wpdb->comments}
						  WHERE ID = comment_post_ID 
						  AND (post_status = 'publish' OR post_status = 'static') 
						  AND comment_type = ''
						  {$skips} 
						  AND (comment_author != '')
						  AND comment_approved = '1' 
						  ORDER BY comment_date DESC 
						  LIMIT {$r['offset']}, {$r['limit']}";

		return $wpdb->get_results($query);
	}
	function get_active_commentators($args = ''){
		global $wpdb;
		$default = array(
			
			);
		$query  = "SELECT comment_author, comment_author_url, COUNT(comment_ID) AS 'comment_total' 
						   FROM {$wpdb->comments}
						   WHERE comment_approved = '1'
						   {$skips}
						   AND (comment_author != '') AND (comment_type = '')
						   GROUP BY comment_author 
						   ORDER BY comment_total DESC";
		
		return $wpdb->get_results($query);
	}
	
	function get_recent_commentators($args = ''){
		global $wpdb;
		$default = array(
			'skips'			=>			'',
			'type'			=>			'both'
			);
		
		$r = wp_parse_args($args,$default);
		
		$posttype = $this->_post_type_clause($r['type']);
		
		$query  = "SELECT comment_author, comment_author_url, COUNT(comment_ID) AS 'comment_total' 
						   FROM {$wpdb->comments}
						   WHERE comment_approved = '1'
						   {$r['skips']}
						   AND (comment_author != '') AND (comment_type = '')
						   {$posttype}
						   GROUP BY comment_author 
						   ORDER BY comment_total DESC";
		return $wpdb->get_results($query);
	}
	
	function _post_type_clause($posttype = ''){
		if ('both' == $posttype){
			return '';
		} else if ('post' == $posttype) {
			return 'AND post_type = "post"';
		} else if ('page' == $posttype) {
			return 'AND post_type = "page"';
		} else {
			return '';
		}
	}
}/*End class WUT_QueryBox*/
?>