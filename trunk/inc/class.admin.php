<?php
class WUT_Admin{
    function add_menu_items(){
        add_menu_page(__("WordPress Ultimate Toolkit Options"), __("WUT Options"),8,__FILE__);
        add_submenu_page(__FILE__, __("Hide Widgets"), __("WUT &gt; Hide Widgets"), 8, __FILE__, array(&$this, "hide_widgets"));
        add_submenu_page(__FILE__, __("Excerpt Options"),__("WUT &gt; Excerpt Options"), 8, "wut_admin_excerpt_options", array(&$this, "excerpt_options"));
        add_submenu_page(__FILE__, __("Hide Pages"), __("WUT &gt; Hide Pages"), 8, "wut_admin_hide_pages", array(&$this, "hide_pages"));
        add_submenu_page(__FILE__, __("Uninstall"), __("Uninstall"), 8, "wut_admin_uninstall", array(&$this, "uninstall"));
    }

    function hide_pages(){
        ?>
        <div class="wrap">This is Hide Pages</div>
        <?php
    }

    function hide_widgets(){
        ?>
        <div class="wrap">This is Hide Widgets</div>
        <?php
    }

    function excerpt_options(){
        ?>
        <div class="wrap">This is Excerpt Options</div>
        <?php
    }

    function uninstall(){
        ?>
        <div class="wrap">Do you really want to uninstall?</div>
        <?php
    }
}
?>