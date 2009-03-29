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
        add_menu_page(__("WordPress Ultimate Toolkit Options"), __("WUT Options"), 8, "wut_admin_default_page", array(&$this, "load_widgets"));
        add_submenu_page("wut_admin_default_page", __("Load Widgets"), __("Load Widgets"), 8, "wut_admin_default_page", array(&$this, "load_widgets"));
        add_submenu_page("wut_admin_default_page", __("Excerpt Options"),__("Excerpt Options"), 8, "wut_admin_excerpt_options", array(&$this, "excerpt_options"));
        add_submenu_page("wut_admin_default_page", __("Hide Pages"), __("Hide Pages"), 8, "wut_admin_hide_pages", array(&$this, "hide_pages"));
        add_submenu_page('wut_admin_default_page', __('Custom Code','wut'), __('Custom Code','wut'), 8, 'wut_admin_custom_code', array(&$this, 'custom_code_snippets'));
        add_submenu_page("wut_admin_default_page", __('Other Options','wut'), __('Other Options'), 8, 'wut_admin_other_options', array(&$this, 'other_options'));
        add_submenu_page("wut_admin_default_page", __("Uninstall"), __("Uninstall"), 8, "wut_admin_uninstall", array(&$this, "uninstall"));
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
                <input type="submit" class="button" value="Hide checked pages" />
            </form>
        </div>
        <?php
    }

    function load_widgets(){
        global $wut_optionsmanager;
        //Get options
        $options =& $this->options['widgets'];
        $all = $options['all'];
        $load =& $options['load'];

        if (isset($_GET['page']) && $_GET['page'] == 'wut_admin_default_page'){
            if (isset($_REQUEST['action']) && 'save' == $_REQUEST['action']){
                $load = array();
                foreach($all as $widget){
                    if (isset($_REQUEST[$widget['callback']])
                        && $_REQUEST[$widget['callback']] == 1
                    ){
                        $load[] = $widget['callback'];
                    }
                }
                $wut_optionsmanager->save_options();
            }
        }
        ?>
        <div class="wrap"><h2><?php _e('Load Widgets','wut');?></h2>
            <form method="post">
                <table class="widefat">
                    <thead>
                        <tr>
                            <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                            <th id="widgetname" class="manage-column column-widgetname" scope="col"><?php _e('Widget Name','wut');?></th>
                            <th id="decript" class="manage-column column-descript" scope="col"><?php _e('Description','wut');?></th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                            <th class="manage-column column-widgetname" scope="col"><?php _e('Widget Name','wut');?></th>
                            <th class="manage-column column-descript" scope="col"><?php _e('Description','wut');?></th>
                        </tr>
                    </tfoot>
                    <tbody>
                    <?php
                        foreach($all as $widget){
                            echo '<tr><td>';
                            echo '<input type="checkbox" id="', $widget['callback'], '" name="', $widget['callback'], '" value="1" ';
                            if (in_array($widget['callback'],$load)) echo 'checked="checked"';
                            echo ' /></td>';
                            echo '<td>', $widget['name'], '</td>';
                            echo '<td>', $widget['descript'], '</td>';
                            echo '</tr>';
                        }
                    ?>
                    </tbody>
                </table>
                <input type="hidden" value="save" name="action" />
                <input type="submit" class="button" value="Load checked Widgets" />
            </form>
        </div>
        <?php
    }

    function excerpt_options(){
        ?>
        <div class="wrap">This is Excerpt Options</div>
        <?php
    }

    function custom_code_snippets(){
        global $wut_optionsmanager;
        $options =& $this->options['customcode'];
        if (!is_array($options)){
            $options = array();
        }
        unset($new_code);
        if (isset($_GET['page']) && $_GET['page'] == 'wut_admin_custom_code'){
            if (isset($_REQUEST['add-new-snippet'])){
                $new_code = array(
                    'id'      => '%id%',
                    'name'    => 'New Code Snippet',
                    'source'  => '',
                    'hookto'  => '',
                    'priority'=> 9,
                    'display' => ''
                );
            }
            if (isset($_REQUEST['save-codes'])){
                foreach($options as $id => $snippet){
                    $snippet['name'] = $_REQUEST["$id-name"];
                    $snippet['source'] = stripslashes($_REQUEST["$id-source"]);
                    $snippet['hookto'] = $_REQUEST["$id-hookto"];
                    $snippet['priority'] = $_REQUEST["$id-priority"];
                    $snippet['display'] = $_REQUEST["$id-display"];
                    $options[$id] = $snippet;
                }
                if(isset($_REQUEST['%id%-name']) && !empty($_REQUEST['%id%-name'])){
                    $new_snippet = array(
                        'id'        => sanitize_title_with_dashes($_REQUEST['%id%-name']),
                        'name'      => $_REQUEST['%id%-name'],
                        'source'    => stripslashes($_REQUEST['%id%-source']),
                        'hookto'    => $_REQUEST['%id%-hookto'],
                        'priority'  => $_REQUEST['%id%-priority'],
                        'display'   => $_REQUEST['%id%-display']
                    );
                    $options[$new_snippet['id']] = $new_snippet;
                }
                $wut_optionsmanager->save_options();
            }
            if (isset($_REQUEST['delete-checked'])){
                $temp = array();
                $item = array_shift($options);
                while($item != null){
                    if (isset($_REQUEST[$item['id']]) && $_REQUEST[$item['id']] == 1){
                        unset($item);
                    }else{
                        array_push($temp, $item);
                    }
                    $item = array_shift($options);
                }
                $options = $temp;
                $wut_optionsmanager->save_options();
            }
        }
        ?>
        <div class="wrap"><h2><?php _e('Add Custom Code','wut');?></h2>
        <form method="post">
            <input type="submit" class="button" name="add-new-snippet" value="<?php _e('Add New','wut');?>" />
            <input type="submit" class="button" name="save-codes" value="<?php _e('Save All','wut');?>" />
            <input type="submit" class="button" name="delete-checked" value="<?php _e('Delete All Checked','wut');?>" />
            <table class="widefat">
                <thead>
                    <tr>
                        <th id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                        <th id="itemname" class="manage-column column-itemname" scope="col"><?php _e('Item Name','wut');?></th>
                        <th id="itemcontent" class="manage-column column-itemcontent" scope="col"><?php _e('Item Content','wut');?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="manage-column column-cb check-column" scope="col"><input type="checkbox" /></th>
                        <th class="manage-column column-itemname" scope="col"><?php _e('Item Name','wut');?></th>
                        <th class="manage-column column-itemcontent" scope="col"><?php _e('Item Content','wut');?></th>
                    </tr>
                </tfoot>
                <tbody>
                <?php function print_code_item($codesnippet){?>
                    <tr>
                        <td rowspan="5"><input type="checkbox" id="<?php echo $codesnippet['id'];?>" name="<?php echo $codesnippet['id'];?>" value="1" /></td>
                        <td><label for="<?php echo $codesnippet['id'];?>-name"><?php _e('Code Name:','wut');?></label></td>
                        <td><input type="text" id="<?php echo $codesnippet['id'];?>-name" name="<?php echo $codesnippet['id'];?>-name" value="<?php echo $codesnippet['name'];?>" size="15" /></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $codesnippet['id'];?>-source"><?php _e('Source Code:','wut');?></label></td>
                        <td>
                            <textarea id="<?php echo $codesnippet['id'];?>-source" name="<?php echo $codesnippet['id'];?>-source" cols="80" rows="15"><?php echo attribute_escape($codesnippet['source']);?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $codesnippet['id'];?>-hookto"><?php _e('Hook to Action:','wut');?></label></td>
                        <td><input type="text" id="<?php echo $codesnippet['id'];?>-hookto" name="<?php echo $codesnippet['id'];?>-hookto" value="<?php echo $codesnippet['hookto'];?>" size="40" /></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $codesnippet['id'];?>-priority"><?php _e('Priority:','wut');?></label></td>
                        <td><input type="text" id="<?php echo $codesnippet['id'];?>-priority" name="<?php echo $codesnippet['id'];?>-priority" value="<?php echo $codesnippet['priority'];?>" size="15" /></td>
                    </tr>
                    <tr>
                        <td><label for="<?php echo $codesnippet['id'];?>-display"><?php _e('Display on:','wut');?></label></td>
                        <td><input type="text" id="<?php echo $codesnippet['id'];?>-display" name="<?php echo $codesnippet['id'];?>-display" value="<?php echo $codesnippet['display'];?>" size="40" /></td>
                    </tr>
                <?php } ?>
                <?php 
                if(!empty($options)) : foreach($options as $codesnippet) :
                    print_code_item($codesnippet);
                endforeach;endif;
                if (isset($new_code)){
                    print_code_item($new_code);
                }
                ?>
                </tbody>
            </table>
            <input type="submit" class="button" name="add-new-snippet" value="<?php _e('Add New','wut');?>" />
            <input type="submit" class="button" name="save-codes" value="<?php _e('Save All','wut');?>" />
            <input type="submit" class="button" name="delete-checked" value="<?php _e('Delete All Checked','wut');?>" />
        </form>
        </div>
        <?php
    }

    function other_options(){
        global $wut_optionsmanager;
        //Get options
        $options =& $this->options['other'];

        if (isset($_GET['page']) && $_GET['page'] == 'wut_admin_other_options'){
            if (isset($_REQUEST['action']) && 'enable' == $_REQUEST['action']){
                $options['enabled'] = 1;
            }
            if (isset($_REQUEST['synchronize'])){
                $options['wphome'] = get_option('home');
                $options['perma_struct'] = get_option('permalink_structure');
            }
            if (isset($_REQUEST['disable'])){
                $options['enabled'] = 0;
            }
            $wut_optionsmanager->save_options();
        }
        ?>
        <div class="wrap"><h2><?php _e('Advanced Options','wut');?></h2>
            <form method="post">
                <table class="form-table">
                    <tbody>
                    <?php if ($options['enabled']) : ?>
                        <tr valign="top">
                        <th scope="row"><label for="wphome"><?php _e('Blog address (URL)') ?></label></th>
                        <td><span style="color:red;font-weight:bold"><?php echo $options['wphome']; ?></span><br />
                        <span class="setting-description"><?php _e('Enter the address here if you want your blog homepage <a href="http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">to be different from the directory</a> you installed WordPress.'); ?></span></td>
                        </tr>
                        <tr valign="top">
                        <th scope="row"><label for="perma_struct"><?php _e('Permalink Structure','wut'); ?></label></th>
                        <td><span style="color:red;font-weight:bold"><?php echo $options['perma_struct'];?></span><br />
                        <span class="setting-description"><?php _e('If you change you permalink structure, please change this.','wut');?></span></td>
                        </tr>
                        <tr valign="top">
                        <th scope="row"><label for=""></label></th>
                        <td>
                            <input type="submit" class="button" name="synchronize" value="<?php _e('Synchronize the Info with WordPress Settings.','wut');?>" />
                            <input type="submit" class="button" name="disable" value="<?php _e('Disable the Andvanced.','wut');?>" />
                        </td>
                        </tr>
                    <?php else : ?>
                        <tr valign="top">
                        <th scope="row"><label for=""></label></th>
                        <td>
                            <input type="hidden" value="enable" name="action" />
                            <input type="submit" class="button" value="<?php _e('Enable the Andvanced.','wut');?>" />
                        </td>
                        </tr>
                    <?php endif;?>
                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }
    
    function uninstall(){
        global $wut_optionsmanager;
        if (isset($_GET['page']) && $_GET['page'] == 'wut_admin_uninstall'){
            if (isset($_REQUEST['action']) && 'save' == $_REQUEST['action']){
                $wut_optionsmanager->delete_options();
                deactivate_plugins('wordpress-ultimate-toolkit/wordpress-ultimate-toolkit.php');
            }
        }
        ?>
        <div class="wrap">
            <h2><?php _e('Uninstall', 'wut');?></h2>
            <form method="post">
                <input type="hidden" value="save" name="action" />
                <input type="submit" class="button" value="Uninstall and Delete ALL Options" />
            </form>
        </div>
        <?php
    }
}
?>