<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: WUT Tags Test Page
*/
?>
<?php get_header(); ?>
<?php //global $wut_querybox;?>
<div id="content" class="widecolumn">
<h2>WUT Template Tags Test</h2>
<h3>function wut_recent_posts($args = '')</h3>
<?php wut_recent_posts();?>

<h3>function wut_random_posts($args = '')</h3>
<?php wut_random_posts();?>

<h3>function wut_related_posts($args = '')</h3>
<?php wut_related_posts();?>

<h3>function wut_same_classified_posts($args = '')</h3>
<?php wut_same_classified_posts();?>

<h3>function wut_most_commented_posts($args = '')</h3>
<?php wut_most_commented_posts();?>

<h3>function wut_recent_comments($args = '')</h3>
<?php wut_recent_comments();?>

<h3>function wut_active_commentators($args = '')</h3>
<?php wut_active_commentators();?>

<h3>function wut_recent_commentators($args = '')</h3>
<?php wut_recent_commentators();?>
</div>

<?php get_footer(); ?>
