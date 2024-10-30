<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 17/09/2018
 * Time: 18:54
 */
defined('ABSPATH') or die("No script kiddies please!");
@session_start();
@define('ROOTPATH', __DIR__);
if( !class_exists ( 'Mastodon_api'))
	require_once(ROOTPATH.'/../../../Class/Mastodon_api.php');
global $active_tab;
global $wpdb;

//Grab all options
$options = get_option($this->plugin_name.'settings');
$optionsAccount = get_option($this->plugin_name.'account');
$optionsPreferences = get_option($this->plugin_name.'preferences');
if( isset($optionsAccount['disconnect']) && $optionsAccount['disconnect'] == 1){
    $_SESSION['mc_step'] = 1;
    unset($_SESSION['instance_name']);
    unset($_SESSION['client_id']);
    unset($_SESSION['client_secret']);
    unset($_SESSION['token']);
    $wpdb->query('DELETE FROM '. $wpdb->prefix . 'mastalab_comments_users');

	delete_option($this->plugin_name.'account');
}

if( isset($optionsPreferences['peertubecomments']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[peertubecomments]", $optionsPreferences['peertubecomments'] );
}
if( isset($optionsPreferences['mastodon_bg']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[mastodon_bg]", $optionsPreferences['mastodon_bg'] );
}
if( isset($optionsPreferences['mastodon_text']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[mastodon_text]", $optionsPreferences['mastodon_text'] );
}
if( isset($optionsPreferences['mastodon_link']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[mastodon_link]", $optionsPreferences['mastodon_link'] );
}
if( isset($optionsPreferences['peertube_bg']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[peertube_bg]", $optionsPreferences['peertube_bg'] );
}
if( isset($optionsPreferences['peertube_text']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[peertube_text]", $optionsPreferences['peertube_text'] );
}
if( isset($optionsPreferences['peertube_link']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[peertube_link]", $optionsPreferences['peertube_link'] );
}
if( isset($optionsPreferences['cache']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[cache]", $optionsPreferences['cache'] );
}
if( isset($optionsPreferences['selector']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[selector]", $optionsPreferences['selector'] );
}

if( isset($optionsPreferences['where']) ){
	register_setting( $this->plugin_name.'preferences', $this->plugin_name."preferences[where]", $optionsPreferences['where'] );
}

if( ! isset($_SESSION['mc_step']))
    $_SESSION['mc_step'] = 1;

$currentUser = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "mastalab_comments_users ORDER BY date DESC LIMIT 1", OBJECT );



//Check if there is a valid account in DB
if( !empty($currentUser) && !empty($currentUser[0]->mastodon_instance) && strlen(trim($currentUser[0]->mastodon_instance)) > 0 )
{
    $account['mastodon_instance'] = $currentUser[0]->mastodon_instance;
    $account['user_id'] = $currentUser[0]->user_id;
    $account['token'] = $currentUser[0]->token;
    $Mastodon_api = new Mastodon_api();
    $Mastodon_api->set_url("https://" . $account['mastodon_instance']);
    $Mastodon_api->set_token($account['token'], "bearer");
    $response = $Mastodon_api->accounts($account['user_id']);
    ;?>
    <div class="card" style="position: relative;" >
        <form method="post" name="<?php echo $this->plugin_name.'account';?>" style="position: absolute;right:0;bottom:0;" action="options.php">
            <?php settings_fields($this->plugin_name.'account');?>
            <input type="hidden" id="<?php echo $this->plugin_name;?>-disconnect" name="<?php echo $this->plugin_name;?>account[disconnect]" value="1" />
            <?php submit_button(__('Disconnect', $this->plugin_name), 'primary','submit', TRUE); ?>
        </form>
        <img src="<?php echo $response['html']['avatar'];?>" alt="<?php echo $response['html']['display_name'];?>" style="width:100px;">
        <div class="container" style="margin-right: 50px;">
            <h4><b><?php echo $response['html']['display_name'];?></b> - <?php echo "@".$response['html']['username'];?></h4>
            <p><?php echo $response['html']['note'];?></p>
        </div>
    </div>
    <?php
	$_SESSION['mc_step'] = 3;
}else{
    if($_SESSION['mc_step'] == 3 )
	    $_SESSION['mc_step'] = 1;
}
$redirectURI  = null;
//No valid account, the user need to login in (two steps : Client + Credentials)
if(  $_SESSION['mc_step'] == 1 && isset($options['instance_name']) ){
    $_SESSION['instance_name'] = $options['instance_name'];
    $Mastodon_api = new Mastodon_api();
    $Mastodon_api->set_url("https://" . $_SESSION['instance_name']);

    $data = $Mastodon_api->create_app("Mastalab Comments", array('read'), '', "https://mastalab.app");
    if (! isset($data['html']['id'])) {
        $_SESSION['mc_step'] = 1;
	    unset($_SESSION['instance_name']);
	    unset($_SESSION['client_secret']);
	    unset($_SESSION['client_id']);
	    if(isset($data['html']['error_description']))
		    $error_message = $data['html']['error_description'];
	    elseif( !preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/', $options['instance_name']))
	        $error_message = esc_html__('The domain does not seem to be valid!', $this->plugin_name);
	    else
		    $error_message = esc_html__('An error occured!', $this->plugin_name);
	    delete_option( $this->plugin_name."settings");
    }else{
	    $Mastodon_api->set_scopes(array('read'));
        $_SESSION['client_id'] = $data['html']['client_id'];
        $_SESSION['client_secret']  = $data['html']['client_secret'];
        $_SESSION['mc_step'] = 2;
	    $redirectURI = $Mastodon_api->getAuthorizationUrl();
    }

}
if (  isset($options['oauth']) ){
    $Mastodon_api = new Mastodon_api();
    $Mastodon_api->set_url("https://" . $_SESSION['instance_name']);
    $Mastodon_api->set_scopes(array('read'));
    $Mastodon_api->set_client($_SESSION['client_id'], $_SESSION['client_secret']);

    $data = $Mastodon_api->loginAuthorization($options['oauth']);
    if ( isset($data['html']['error'])) {
        $_SESSION['mc_step'] = 1;
        unset($_SESSION['instance_name']);
        unset($_SESSION['client_secret']);
        unset($_SESSION['client_id']);
	    delete_option( $this->plugin_name."settings");
        if(isset($data['html']['error']))
	        $error_message = $data['html']['error'];
        else
            $error_message = esc_html__('An error occured!', $this->plugin_name);
    }else{
        $_SESSION['token'] = $data['html']['access_token'];
        $dateInsert = new DateTime();
        if( isset($_SESSION['token'] ) ){
            $Mastodon_api->set_token($_SESSION['token'], "bearer");
            $response = $Mastodon_api->accounts_verify_credentials();
            if( ! isset($response['html']['error'])) {
                $wpdb->insert(
                    $wpdb->prefix . 'mastalab_comments_users',
                    array(
                        'date' => $dateInsert->format('Y-m-d H:i:s'),
                        'user_id' => $response['html']['id'],
                        'mastodon_instance' => $_SESSION['instance_name'],
                        'token' => $_SESSION['token']
                    )
                );
                ?>
                <div class="card" style="position: relative;">
                    <form method="post" name="<?php echo $this->plugin_name.'account';?>" style="position: absolute;right:0;bottom:0;" action="options.php">
                        <?php settings_fields($this->plugin_name.'account');?>
                        <input type="hidden" id="<?php echo $this->plugin_name;?>-disconnect" name="<?php echo $this->plugin_name;?>account[disconnect]" value="1" />
                        <?php submit_button(__('Disconnect', $this->plugin_name), 'primary','submit', TRUE); ?>
                    </form>
                    <img src="<?php echo $response['html']['avatar'];?>" alt="<?php echo $response['html']['display_name'];?>" style="width:100px;">
                    <div class="container" style="margin-right: 50px;">
                        <h4><b><?php echo $response['html']['display_name'];?></b> - <?php echo "@".$response['html']['username'];?></h4>
                        <p><?php echo $response['html']['note'];?></p>
                    </div>
                </div>
                <?php
	            unset($_SESSION['instance_name']);
	            unset($_SESSION['client_secret']);
	            unset($_SESSION['client_id']);
	            unset($_SESSION['token']);
	            delete_option( $this->plugin_name."settings");
	            $_SESSION['mc_step'] = 3;
            }
        }
    }

}
?>
<?php //Forms for connecting an account (two steps : Client + Credentials) ?>

<?php if ( $_SESSION['mc_step'] < 3){?>
<form method="post" name="<?php echo $this->plugin_name.'settings';?>" class="mw_highlight" action="options.php">
    <?php settings_fields($this->plugin_name.'settings');?>
        <h2 class="nav-tab-wrapper"><?php _e('Connect an account', $this->plugin_name);?></h2>

        <?php if( $_SESSION['mc_step'] == 1 ){?>
        <fieldset>
            <?php
            if( !empty($error_message) ) { ?>
                <div class="notice notice-error"><?php echo $error_message;?></div>
            <?php } ?>
            <p><?php _e('Enter your instance name', $this->plugin_name);?></p>

            <label for="<?php echo $this->plugin_name;?>-instance_name"><?php _e('Instance', $this->plugin_name);?></label>
            <input type="text" id="<?php echo $this->plugin_name;?>-instance_name" name="<?php echo $this->plugin_name;?>settings[instance_name]" value="<?php echo $_SESSION['instance_name']; ?>" />
        </fieldset>
        <?php }else if( $_SESSION['mc_step'] == 2  ){?>
        <fieldset>
            <p><?php printf(esc_html__('Authentification on %s', $this->plugin_name), $_SESSION['instance_name']);?></p>
            <p>
                <a href="<?php echo $redirectURI;?>" target="_blank"><?php _e('Click here to get your authorization code', $this->plugin_name);?></a><br/>
            </p>
            <label for="<?php echo $this->plugin_name;?>-oauth"><?php _e('Authorization code', $this->plugin_name);?></label>
            <input type="text" id="<?php echo $this->plugin_name;?>-oauth" name="<?php echo $this->plugin_name;?>settings[oauth]"  />
        </fieldset>
        <?php }?>
    <?php submit_button(__('Connect', $this->plugin_name), 'primary','submit', TRUE); ?>
</form>
<?php }?>
<?php //The form is always displayed, it allows to save targeted DOM element?>
<form method="post" name="<?php echo $this->plugin_name.'preferences';?>" class="mw_highlight" action="options.php">
	<?php settings_fields($this->plugin_name.'preferences');?>
    <h2 class="nav-tab-wrapper"><?php _e('Targeted element', $this->plugin_name);?></h2>
    <fieldset style="margin-top: 10px;">
        <input type="radio"
                name="<?php echo $this->plugin_name;?>preferences[where]"
                value="custom"
	        <?php if( $optionsPreferences['where'] != "end_article" ) {echo "checked";}?>>
            <label for="<?php echo $this->plugin_name;?>-selector"><?php _e('Selector', $this->plugin_name);?></label>
            <input type="text" id="<?php echo $this->plugin_name;?>-selector"
                   placeholder="<?php _e('Use a jquery selector', $this->plugin_name);?>"
                   name="<?php echo $this->plugin_name;?>preferences[selector]"
                   value="<?php echo isset($optionsPreferences['selector'])?$optionsPreferences['selector']:"#comments";?>" />
        </input>
        <a href="https://api.jquery.com/category/selectors/"  target="_blank" title="<?php _e('JQuery selectors', $this->plugin_name);?>" ><?php _e('More about JQuery selectors', $this->plugin_name);?></a>
        <br/>
        <input type="radio"
            name="<?php echo $this->plugin_name;?>preferences[where]"
            value="end_article"
            <?php if( $optionsPreferences['where'] == "end_article" ) {echo "checked";}?>>
	        <?php _e('Automatically add comments at the end of the article', $this->plugin_name);?>
        </input>
    <br/><br/>
        <label for="<?php echo $this->plugin_name;?>-peertubecomments"><?php _e('Display peertube comments', $this->plugin_name);?></label>
        <input type="checkbox" id="<?php echo $this->plugin_name;?>-peertubecomments"
               name="<?php echo $this->plugin_name;?>preferences[peertubecomments]"
               <?php if(
                       isset($optionsPreferences['peertubecomments']) )
               {echo "checked";}?>
                />
        <br/><br/>
        <label for="<?php echo $this->plugin_name;?>-cache"><?php _e('Cache in minutes', $this->plugin_name);?></label>
        <input type="number" id="<?php echo $this->plugin_name;?>-cache"
               name="<?php echo $this->plugin_name;?>preferences[cache]"
               value="<?php echo isset($optionsPreferences['cache'])?$optionsPreferences['cache']:5;?>"
        />
    </fieldset>
    <h2 class="nav-tab-wrapper"><?php _e('Custom colors', $this->plugin_name);?></h2>
    <fieldset style="margin-top: 10px;">
        <table>
            <tr>
                <th>Mastodon</th>
                <th>Peertube</th>
            </tr>
            <tr>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-mastodon_bg"><?php _e('Background color for Mastodon comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-mastodon_bg"
                           name="<?php echo $this->plugin_name;?>preferences[mastodon_bg]"
                           value="<?php echo isset($optionsPreferences['mastodon_bg'])?$optionsPreferences['mastodon_bg']: "#eee";?>"
                           class="my-color-field" /></td>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-peertube_bg"><?php _e('Background color for Peertube comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-peertube_bg"
                           name="<?php echo $this->plugin_name;?>preferences[peertube_bg]"
                           value="<?php echo isset($optionsPreferences['peertube_bg'])?$optionsPreferences['peertube_bg']: "#eee";?>"
                           class="my-color-field" />
                </td>

            </tr>
            <tr>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-mastodon_text"><?php _e('Text color for Mastodon Comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-mastodon_text"
                           name="<?php echo $this->plugin_name;?>preferences[mastodon_text]"
                           value="<?php echo isset($optionsPreferences['mastodon_text'])?$optionsPreferences['mastodon_text']: "#000";?>"
                           class="my-color-field" /></td>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-peertube_text"><?php _e('Text color for Peertube Comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-peertube_text"
                           name="<?php echo $this->plugin_name;?>preferences[peertube_text]"
                           value="<?php echo isset($optionsPreferences['peertube_text'])?$optionsPreferences['peertube_text']: "#000";?>"
                           class="my-color-field" />
                </td>
            </tr>
            <tr>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-mastodon_link"><?php _e('Link color for Mastodon Comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-mastodon_link"
                           name="<?php echo $this->plugin_name;?>preferences[mastodon_link]"
                           value="<?php echo isset($optionsPreferences['mastodon_link'])?$optionsPreferences['mastodon_link']: "#000";?>"
                           class="my-color-field" /></td>
                <td valign="top" style="padding:0 15px 0 15px;">
                    <label for="<?php echo $this->plugin_name;?>-peertube_link"><?php _e('Link color for Peertube Comments', $this->plugin_name);?></label>
                    <input type="text" id="<?php echo $this->plugin_name;?>-peertube_link"
                           name="<?php echo $this->plugin_name;?>preferences[peertube_link]"
                           value="<?php echo isset($optionsPreferences['peertube_link'])?$optionsPreferences['peertube_link']: "#000";?>"
                           class="my-color-field" />
                </td>
            </tr>
        </table>


    </fieldset>
    <fieldset style="margin-top: 10px;">

    </fieldset>
	<?php submit_button(__('Save', $this->plugin_name), 'primary','submit', TRUE); ?>
</form>
