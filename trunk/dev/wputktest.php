<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: WordPress Ultimate Toolkit test page
*/
?>
<?php
function outputdata($objects){
?>
<table>
	<thead><tr>
<?php
	$headcol = array();
	foreach ($objects[0] as $key => $value){
		echo '<td>',$key,'</td>',"\n";
		$headcol[] = $key;
	}
?>
	</tr></thead>
	<tbody>
<?php
	foreach($objects as $object){
		echo '<tr>';
		foreach($headcol as $head){
			$val = strip_tags($object->$head);
			$val = mb_substr($val,0,30);
			echo '<td>',$val,'</td>',"\n";
		}
		echo '</tr>';
	}
?>
	</tbody>
</table>
<?php
}
?>
<?php get_header(); ?>
<style type="text/css">
table {
	border:1px solid #000;
	border-spacing:0;
}
table thead{
	background-color:#bbb;
}
table td{
	border:1px solid #000;
}
</style>
<?php //global $wut_querybox;?>
<div id="content" class="widecolumn">
<h2>WPUTK_QueryBox Object Test</h2>
<h3>function get_recent_posts($args = '')</h3>
<?php outputdata($wut_querybox->get_recent_posts());?>

<h3>function get_random_posts($args = '')</h3>
<?php outputdata($wut_querybox->get_random_posts());?>

<h3>function get_related_posts($args = '')</h3>
<?php outputdata($wut_querybox->get_related_posts());?>

<h3>function get_same_classified_posts($args = '')</h3>
<?php outputdata($wut_querybox->get_same_classified_posts());?>

<h3>function get_most_commented_posts($args = '')</h3>
<?php outputdata($wut_querybox->get_most_commented_posts());?>

<h3>function get_recent_comments($args = '')</h3>
<?php outputdata($wut_querybox->get_recent_comments());?>

<h3>function get_active_commentators($args = '')</h3>
<?php outputdata($wut_querybox->get_active_commentators());?>

<h3>function get_recent_commentators($args = '')</h3>
<?php outputdata($wut_querybox->get_recent_commentators());?>
</div>

<?php get_footer(); ?>
