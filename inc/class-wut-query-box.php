<?php
/**
 * Class: WUT_Query_Box
 *
 * The class is used to build query.
 *
 * @package wut
 */

/**
 * All the queries.
 *
 * All the Database queries used in this plugin are
 * put here.
 */
class WUT_Query_Box {
	/**
	 * Database connection.
	 *
	 * @var wpdb
	 */
	protected $db;

	/**
	 * The constructor of query box.
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * SQL query to findout active commentators.
	 *
	 * @param array $args Arguments used to query.
	 * @return Object
	 */
	public function get_active_commentators( $args = '' ) {
		global $wpdb;
		$defaults = array(
			'limit'     => 10, // -1 to disable the limit
			'offset'    => 0,
			'skipusers' => '',
			'days'      => 15,  // -1 to disable the days limit
		);

		$r              = wp_parse_args( $args, $defaults );
		$skipuserclause = $this->skip_clause( 'comment_author', $r['skipusers'] );
		if ( $r['limit'] < 0 ) {
			$limit = '';
		} else {
			$limit = "LIMIT {$r['offset']},{$r['limit']}";
		}
		if ( $r['days'] > 0 ) {
			$limit_date = current_time( 'timestamp' ) - ( $r['days'] * 86400 );
			$limit_date = date( 'Y-m-d H:i:s', $limit_date );
			$days       = 'AND comment_date > "' . $limit_date . '" ';
		} else {
			$days = '';
		}

		$query = "SELECT comment_author, comment_author_url,comment_author_email,
                          COUNT(comment_ID) AS comment_total
                   FROM {$wpdb->comments}
                   WHERE comment_approved = '1'
                   {$skipuserclause}
                   AND (comment_author != '') AND (comment_type = '')
                   {$days}
                   GROUP BY comment_author
                   ORDER BY comment_total DESC
                   {$limit}";
		return $wpdb->get_results( $query );
	}

	/**
	 * @version 1.0
	 * @author Charles
	 */
	function get_recent_commentators( $args = '' ) {
		global $wpdb;
		$defaults = array(
			'limit'     => 10,
			'offset'    => 0,
			'skipusers' => '',
			'type'      => 'week',
		);

		$r = wp_parse_args( $args, $defaults );

		$skipuserclause = $this->skip_clause( 'comment_author', $r['skipusers'] );

		if ( $r['type'] == 'week' ) {
			$type = 'AND YEARWEEK(comment_date) = YEARWEEK(NOW())';
		} elseif ( $r['type'] == 'month' ) {
			$type = 'AND (MONTH(comment_date) = MONTH(NOW()) AND YEAR(comment_date) = YEAR(NOW()))';
		} else {
			$type = '';
		}
		if ( $r['limit'] < 0 ) {
			$limit = '';
		} else {
			$limit = "LIMIT {$r['offset']},{$r['limit']}";
		}
		$query = "SELECT comment_author, comment_author_url, comment_author_email,
                          COUNT(comment_ID) AS 'comment_total'
                   FROM {$wpdb->comments}
                   WHERE comment_approved = '1'
                   AND (comment_author != '') AND (comment_type = '')
                   {$skipuserclause}
                   {$type}
                   GROUP BY comment_author
                   ORDER BY comment_total DESC
                   {$limit}";
		return $wpdb->get_results( $query );
	}

	/**
	 * Create a SQL condition about post type.
	 *
	 * @param string $posttype The post type name in SQL condition.
	 */
	protected function post_type_clause( $posttype ) {
		$condition = '';
		switch ( $posttype ) {
			case 'post':
				$condition = 'AND `post_type` = "post"';
				break;
			case 'page':
				$condition = 'AND `post_type` = "page"';
				break;
			default:
				$condition = 'AND `post_type` IN ( "post", "page" )';
		}
		return $condition;
	}

	/**
	 * Create a SQL condition to exclude values.
	 *
	 * @param string $fieldtoskip Field name to test.
	 * @param string $skipstr Value list.
	 */
	protected function skip_clause( $fieldtoskip, $skipstr ) {
		if ( empty( trim( $skipstr ) ) ) {
			return '';
		}
		$skips = implode( '\',\'', array_filter( explode( ',', $skipstr ) ) );
		return "AND {$fieldtoskip} NOT IN('{$skips}')";
	}

	/**
	 * Create a SQL condition to exclude password protected posts.
	 *
	 * @param bool $show To show password protected post or not.
	 */
	protected function password_clause( $show ) {
		return $show ? ' ' : 'AND post_password = "" ';
	}

	/**
	 * Create a SQL condition to set the order.
	 *
	 * @param string $field The field name to order.
	 * @param string $order The order "ASC" or "DESC".
	 */
	protected function orderby_clause( $field, $order ) {
		return "ORDER BY {$field} {$order}";
	}
}
