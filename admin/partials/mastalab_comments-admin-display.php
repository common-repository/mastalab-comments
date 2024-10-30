<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://gitlab.com/tom79
 * @since      1.0.0
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/admin/partials
 */
defined('ABSPATH') or die("No script kiddies please!");
global $active_tab;
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h2 class="nav-tab-wrapper">
    <a href="?page=<?php echo $this->plugin_name;?>" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Settings', $this->plugin_name);?></a>
    <a href="?page=<?php echo $this->plugin_name;?>&tab=help" class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Help', $this->plugin_name);?></a>
    <a href="?page=<?php echo $this->plugin_name;?>&tab=donate" class="nav-tab <?php echo $active_tab == 'donate' ? 'nav-tab-active' : ''; ?>"><?php esc_attr_e('Donate', $this->plugin_name);?></a>
</h2>

<div class="wrap">
    <?php
    if ($active_tab == "settings") {
        include_once('inc/settings.inc.php');
    }elseif ($active_tab == "help") {
        include_once('inc/help.inc.php');
    }elseif ($active_tab == "donate") {
	    include_once('inc/donate.inc.php');
    }?>
</div>