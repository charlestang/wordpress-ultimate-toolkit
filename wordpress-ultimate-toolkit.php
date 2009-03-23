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
	Copyright 2008  Charles (email : charlestang@foxmail.com)

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
require($wut_base_name . '/inc/class.optionsmanager.php');
require($wut_base_name . '/inc/class.querybox.php');
require($wut_base_name . '/inc/class.utils.php');
require($wut_base_name . '/inc/class.admin.php');
require($wut_base_name . '/inc/tags.php');
require($wut_base_name . '/inc/widgets.php');

add_action('plugins_loaded','wut_init');
function wut_init(){
	global $wut_querybox, $wut_utils,$wut_optionsmanager;
    $wut_optionsmanager = new WUT_OptionsManager();
	$wut_querybox = new WUT_QueryBox();
    $wut_utils = new WUT_Utils($wut_optionsmanager->get_options());

    $widgets = $wut_optionsmanager->get_options("widgets");
    foreach($widgets['load'] as $callback){
        add_action('widgets_init', $callback);
    }

    add_filter('get_the_excerpt', array(&$wut_utils,'excerpt'), 9);
    add_filter('wp_list_pages_excludes', array(&$wut_utils, 'exclude_pages'), 9);

    if (is_admin()){
        $wut_admin = new WUT_Admin($wut_optionsmanager->get_options());
        add_action('admin_menu',array(&$wut_admin, 'add_menu_items'));
    }
}
?>