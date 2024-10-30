<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 17/09/2018
 * Time: 18:55
 */
defined('ABSPATH') or die("No script kiddies please!");
global $active_tab;
$plugin_data = get_plugin_data( dirname(__FILE__)."/../../../mastalab_comments.php" );
?>

<h2 class="nav-tab-wrapper"><?php _e('Help', $this->plugin_name);?></h2>

<h3><?php printf(esc_html__(ucwords($plugin_data['Name']).' v%s', $this->plugin_name), $plugin_data['Version']);?></h3>

<p class="notice notice-info notice-large">
    <?php echo esc_html__("This plugin will display comments from Mastodon Social Network related to the current URL.", $this->plugin_name);?>
    <br/>
    <?php echo esc_html__("Your instance must support full-text search", $this->plugin_name);?>
</p>
<p>
    <p class="blockquote_mastalab_comments">
	<?php echo esc_html__("How it works?", $this->plugin_name);?>
    </p>
    <ul id="mastalab_comments_help">
        <li><?php echo esc_html__("You need to connect your account, the choice is really important. You should connect an account that publishes URLs", $this->plugin_name);?></li>
        <li><?php echo esc_html__("If you get an error with the authorization code, you can first logout your account on your Mastodon instance.", $this->plugin_name);?></li>
        <li><?php echo esc_html__("Your Wordpress theme can use a different ID for comments section, you should enter the right one in the settings page", $this->plugin_name);?></li>
        <li><?php echo esc_html__("For privacy purposes, only public toots will be displayed", $this->plugin_name);?></li>
    </ul>
</p>

<p>
    <p class="blockquote_mastalab_comments">
        <?php echo esc_html__("Your instance does not support full-text search?", $this->plugin_name);?>
    </p>
    <ol>
        <li><?php echo esc_html__("Create an account on an instance allowing full-text search", $this->plugin_name);?></li>
        <li><?php echo esc_html__("With this account follow the main account publishing URLs (it will be easier to find toot)", $this->plugin_name);?></li>
        <li><?php echo esc_html__("Use this second account on your Wordpress site", $this->plugin_name);?></li>
        <li><?php echo esc_html__("Boost statuses of the main account with the plugin", $this->plugin_name);?></li>
        <li><?php echo esc_html__("For older statuses, copy their URL from the main account and paste their URL in the search feature of the second account. You will be able to find and boost them.", $this->plugin_name);?></li>
    </ol>
</p>

