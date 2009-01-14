<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
/*
Template Name: WordPress Ultimate Toolkit test page
*/
?>

<?php get_header(); ?>
<?php //global $wputk_querybox;?>
<div id="content" class="widecolumn">
<h2>WPUTK_QueryBox Object Test</h2>
<h3>function get_recent_posts($args = '')</h3>
<?php //var_dump($wputk_querybox->get_recent_posts());?>

<h3>function get_random_posts($args = '')</h3>
<?php var_dump($wputk_querybox->get_random_posts());?>

<h3>function get_related_posts($args = '')</h3>
<?php var_dump($wputk_querybox->get_related_posts());?>

<h3>function get_same_classified_posts($args = '')</h3>
<?php var_dump($wputk_querybox->get_same_classified_posts());?>

<h3>function get_most_commented_posts($args = '')</h3>
<?php var_dump($wputk_querybox->get_most_commented_posts());?>

<h3>function get_recent_comments($args = '')</h3>
<?php var_dump($wputk_querybox->get_recent_comments());?>

<h3>function get_active_commentators($args = '')</h3>
<?php var_dump($wputk_querybox->get_active_commentators());?>

<h3>function get_recent_commentators($args = '')</h3>
<?php var_dump($wputk_querybox->get_recent_commentators());?>
</div>

<?php get_footer(); ?>
