<?php
/**
 * Admin pages of the WordPress Ultimate Toolkit.
 */
class WUT_Admin{
    var $options;
    function WUT_Admin(&$opt){
        $this->options = &$opt;
    }

    function add_menu_items(){
        add_menu_page(__("WordPress Ultimate Toolkit Options"), __("WUT Options"),8,__FILE__);
        add_submenu_page(__FILE__, __("Hide Widgets"), __("Hide Widgets"), 8, __FILE__, array(&$this, "hide_widgets"));
        add_submenu_page(__FILE__, __("Excerpt Options"),__("Excerpt Options"), 8, "wut_admin_excerpt_options", array(&$this, "excerpt_options"));
        add_submenu_page(__FILE__, __("Hide Pages"), __("Hide Pages"), 8, "wut_admin_hide_pages", array(&$this, "hide_pages"));
        add_submenu_page(__FILE__, __("Uninstall"), __("Uninstall"), 8, "wut_admin_uninstall", array(&$this, "uninstall"));
    }

    function hide_pages(){
        global $wut_optionsmanager;
        //Get options
        $options =& $this->options['hide-pages'];
        
        $para_args = array(
            'numberposts' 		=> -1,
            'orderby'			=> 'menu_order',
            'order'				=> 'ASC',
            'post_type'			=> 'page',
        );
        $pages = get_posts($para_args);

        if (isset($_GET['page']) && $_GET['page'] == 'wut_admin_hide_pages'){
            if (isset($_REQUEST['action']) && 'save' == $_REQUEST['action']){
                $excludes = '';
                foreach($pages as $page){
                    if (isset($_REQUEST['post-'.$page->ID])){
                        $excludes .= $page->ID . ',';
                    }
                }
                $options = substr($excludes,0,- 1);
                $wut_optionsmanager->save_options();
            }
        }

        $hide_array = explode(',',$options); 
        ?>
        <div class="wrap">
            <h2><?php _e('Hide Pages','wut');?></h2>
            <form method="post">
                <table class="widefat">
                    <thead>
                        <tr>
                            <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                            <th id="postid" class="manage-column column-postid" scope="col"><?php _e('ID','wut');?></th>
                            <th id="title" class="manage-column column-title" scope="col"><?php _e('Title','wut');?></th>
                            <th id="date" class="manage-column column-date" scope="col"><?php _e('Date','wut');?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                            <th class="manage-column column-postid" scope="col"><?php _e('ID','wut');?></th>
                            <th class="manage-column column-title" scope="col"><?php _e('Title','wut');?></th>
                            <th class="manage-column column-date" scope="col"><?php _e('Date','wut');?></th>
                        </tr>
                    </tfoot>
                    <tbody>
        <?php
            if($pages){
                foreach($pages as $page){
                    $check = '';
                    if (in_array($page->ID,$hide_array)) $check = ' checked="checked" ';
                    echo '<tr>';
                    echo '<td>','<input id="post-',$page->ID,'" name="post-',$page->ID,'" type="checkbox"',$check,'/>','</td>';
                    echo '<td>',$page->ID,'</td>';
                    echo '<td>',$page->post_title,'</td>';
                    echo '<td>',$page->post_date,'</td>';
                    echo '</tr>';
                }
            }
        ?>
                    </tbody>
                </table>
                <input type="hidden" value="save" name="action" />
                <input type="submit" class="button" value="Submit" />
            </form>
        </div>
        <?php
    }

    function hide_widgets(){
        ?>
        <div class="wrap"><h2><?php _e('Hide Widgets','wut');?></h2>
        </div>
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