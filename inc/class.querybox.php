<?php
/**
 * All the queries.
 *
 * All the Database queries used in this plugin are
 * put here.
 * 
 */
class WPUTK_QueryBox{

	function get_recent_posts($args = ''){
		global $wpdb;
	}
	function get_random_posts($args = ''){
		global $wpdb;
				$query = "SELECT ID, post_title, post_date, post_content, post_name 
				  FROM {$wpdb->posts} 
				  WHERE post_status = 'publish' 
				  {$show_pass_post} 
				  {$ptype}
				  ORDER BY RAND() 
				  LIMIT $limit";
	}
	function get_related_posts($args = ''){
		global $wpdb;
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
	}
	function get_same_classified_posts($args = ''){
		global $wpdb;
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
	}
	function get_most_commented_posts($args = ''){
		global $wpdb;
			    $query  = "SELECT ID, post_title, post_name, COUNT(comment_post_ID) AS comment_total
	    		   FROM {$wpdb->posts} 
	    		   LEFT JOIN {$wpdb->comments} ON ID = comment_post_ID
	    		   WHERE comment_approved = 1
	    		   {$ptype}
	    		   {$days}
	    		   AND post_status = 'publish' AND post_password = ''
	    		   GROUP BY comment_post_ID
	    		   ORDER BY comment_total DESC
	    		   LIMIT {$offset},{$limit}";
	}
	function get_recent_comments($args = ''){
		global $wpdb;
		$query = "SELECT ID, comment_ID, comment_content, comment_author, comment_author_url, comment_author_email, post_title, comment_count
		  FROM {$wpdb->posts},{$wpdb->comments}
		  WHERE ID = comment_post_ID 
		  AND (post_status = 'publish' OR post_status = 'static') 
		  AND comment_type = ''
		  {$show_pass_post}
		  {$skips} 
		  AND (comment_author != '')
		  AND comment_approved = '1' 
		  ORDER BY comment_date DESC 
		  LIMIT {$offset}, {$limit}";
	}
	function get_active_commentators($args = ''){
		global $wpdb;
				$query  = "SELECT comment_author, comment_author_url, COUNT(comment_ID) AS 'comment_total' 
				   FROM {$wpdb->comments}
				   WHERE comment_approved = '1'
				   {$skips}
				   AND (comment_author != '') AND (comment_type = '')
				   {$days}
				   GROUP BY comment_author 
				   ORDER BY comment_total DESC";
	}
	function get_recent_commentators($args = ''){
		global $wpdb;
				$query  = "SELECT comment_author, comment_author_url, COUNT(comment_ID) AS 'comment_total' 
				   FROM {$wpdb->comments}
				   WHERE comment_approved = '1'
				   {$skips}
				   AND (comment_author != '') AND (comment_type = '')
				   {$type}
				   GROUP BY comment_author 
				   ORDER BY comment_total DESC";
	}
}
?>