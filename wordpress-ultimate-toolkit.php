<?php
/**
Plugin Name: WordPress Ultimate Toolkit
Plugin URI: http://sexywp.com/wut
Description: Provide a variety of widgets with rich options, such as recent posts, related articles, latest comments, and popular posts, etc.
Author: Charles
Version: 2.0.5
Author URI: http://sexywp.com
 */

/*
	Copyright 2021  Charles (email : charlestang@foxmail.com)

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
require plugin_dir_path( __FILE__ ) . 'inc/class-wut.php';
WUT::run( __FILE__ );
