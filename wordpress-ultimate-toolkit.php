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
require(dirname(__FILE__) . '/inc/class.querybox.php');
require(dirname(__FILE__) . '/inc/tags.php');
add_action('plugins_loaded','wut_init');
function wut_init(){
	global $wut_querybox;
	$wut_querybox = new WUT_QueryBox();
}

?>