<?php
/*
Plugin Name: WordPress Ultimate Toolkit
Plugin URI: http://wordpress-ultimate-toolkit.googlecode.com
Description: To be Added!
Author: Charles & Leo
Version: 1.00
Author URI: http://www.charlestasng.cn
*/
/*  
	Copyright 2008  Charles & Leo  (email : charlestang@foxmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
$wut_base_name = dirname(__FILE__);
require($wut_base_name . '/inc/class.querybox.php');
require($wut_base_name . '/inc/class.utils.php');
require($wut_base_name . '/inc/tags.php');
require($wut_base_name . '/inc/widgets.php');

add_action('plugins_loaded','wut_init');
function wut_init(){
	global $wut_querybox, $wut_utils;
	$wut_querybox = new WUT_QueryBox();
    $wut_utils = new WUT_Utils();

    add_action('widgets_init', 'wut_widget_recent_posts_init');
    add_action('widgets_init', 'wut_widget_random_posts_init');
    add_action('widgets_init', 'wut_widget_related_posts_init');
    add_action('widgets_init', 'wut_widget_posts_by_category_init');
    add_action('widgets_init', 'wut_widget_most_commented_posts_init');
    add_action('widgets_init', 'wut_widget_recent_comments_init');

    add_filter('get_the_excerpt', array(&$wut_utils,'excerpt'), 9);
}
?>