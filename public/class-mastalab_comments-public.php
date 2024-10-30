<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://gitlab.com/tom79
 * @since      1.0.0
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Mastalab_comments
 * @subpackage Mastalab_comments/public
 * @author     Thomas Schneider <tschneider.ac@gmail.com>
 */
class Mastalab_comments_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mastalab_comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mastalab_comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mastalab_comments-public.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'font-awesome', 'https://use.fontawesome.com/releases/v5.3.1/css/all.css' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mastalab_comments_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mastalab_comments_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/mastalab_comments-public.js', array( 'jquery' ), $this->version, false );
		$optionsPreferences = get_option($this->plugin_name.'preferences');
		$params =
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'post_id' => get_the_id(),
                'search' => "",
                'peertube_comment' => $optionsPreferences['peertubecomments'],
                'mastodon_bg' => $optionsPreferences['mastodon_bg'],
                'mastodon_text' => $optionsPreferences['mastodon_text'],
                'mastodon_link' => $optionsPreferences['mastodon_link'],
                'peertube_bg' => $optionsPreferences['peertube_bg'],
                'peertube_text' => $optionsPreferences['peertube_text'],
                'peertube_link' => $optionsPreferences['peertube_link'],
                'mastalab_where' =>  $optionsPreferences['where'],
                'selector' => $optionsPreferences['selector'],
                'mastalab_comments_nonce' => wp_create_nonce('mastalab_comments_nonce')
            );
        wp_localize_script( $this->plugin_name, 'params', $params );
	}


	/**
	 * Add a div at the end of the article when defined in settings
	 *
	 * @since    1.0.9
	 */
	public function mastalab_comments_add_to_footer(){
		$fullcontent =  get_the_content();
		if(is_page() || is_single()) {
			$optionsPreferences = get_option( $this->plugin_name . 'preferences' );
			$where              = $optionsPreferences['where'];
			if ( $where === "end_article" ) {
				$fullcontent =  $fullcontent . "<div style='clear: both;'>".__('Comments from Mastodon:', $this->plugin_name)."</div><div id='mastalab_comments_end_article'></div>";
			}
		}
		return $fullcontent;
	}

	/**
	 * Get remote comments for Peertube related to its URL
	 * Values are cached
	 *
	 * @since    1.0.1
	 */
	public function prefix_ajax_mastalab_comments_getcomment_peertube(){
		global  $wpdb;
		$result = ['comments' => [], 'stats' => ['reblogs' => 0, 'favs' => 0, 'replies' => 0, 'url' => '', 'root' => 0]];
		$search = isset($_GET['search']) ? $_GET['search'] : '';
		@define('ROOTPATH', __DIR__);
		if( !class_exists ( 'Mastodon_api'))
			require_once(ROOTPATH.'/../Class/Mastodon_api.php');
		$MastodonAPI = new Mastodon_api();
		$source = isset($_GET['source']) ? $_GET['source'] : '#';
		$source = str_replace("embed","watch",$source);

		$cachedURL = $wpdb->get_results(
			"SELECT * FROM ".$wpdb->prefix . "mastalab_comments_cache 
	                        WHERE URL='".urlencode($search)."' ORDER BY date DESC LIMIT 1", OBJECT );
		$needRefresh = false;
		//Something in cache
		if( !empty($cachedURL)){
			$date = $cachedURL[0]->date;
			$optionsPreferences = get_option($this->plugin_name.'preferences');
			$cache_time = $optionsPreferences['cache_time'];
			$cache_time = isset($cache_time)?$cache_time:5;
			if((time()-($cache_time*60)) > strtotime($date)){
				$needRefresh = true;
			}
		}else{
			$needRefresh = true;
		}
		if ($needRefresh) {
			$searchResult = $MastodonAPI->get_content_remote_get( $search );
			$dateInsert = new DateTime();
			//Cache the status
			if( empty($cachedURL)) {
				$wpdb->insert(
					$wpdb->prefix . 'mastalab_comments_cache',
					array(
						'date'  => $dateInsert->format( 'Y-m-d H:i:s' ),
						'URL'   => urlencode( $search ),
						'cache' => json_encode( $searchResult )
					)
				);
			} else{ //Update cache
				$wpdb->update(
					$wpdb->prefix . "mastalab_comments_cache",
					array(
						'date'  => $dateInsert->format( 'Y-m-d H:i:s' ),
						'cache' => json_encode( $searchResult )
					),
					array( 'URL'   => urlencode( $search ))
				);

			}
		}else{
			$searchResult = json_decode($cachedURL[0]->cache, true);

		}
		$resultArray = $searchResult['html']['data'];
		$result['stats']['replies'] = $searchResult['html']['total'];
		$domain = explode("/",str_replace("https://","",$source))[0];
		if (isset($resultArray)) {
			foreach ($resultArray as $status) {
				if( isset($status['account']['avatar']['path']))
					$avatar = "https://".$domain.$status['account']['avatar']['path'];
				else
					$avatar = strtolower(plugins_url($this->plugin_name))."/public/img/missing.png";
				$result['comments'][$status['id']] = [
					'author' => [
						'display_name' => $status['account']['name'],
						'avatar' => $avatar,
						'url' => $status['account']['url']
					],
					'toot' => $status['text'],
					'date' => $status['createdAt'],
					'url' => $status['url'],
					'reply_to' => null,
					'root' => null,
				];
				$result['stats']['url'] = $source;

			}
		}
		wp_send_json($result);

	}


	/**
	 * Get remote comments from Mastodon related to a URL
	 * Values are cached
	 *
	 * @since    1.0.0
	 */
	public function prefix_ajax_mastalab_comments_getcomment(){

        @define('ROOTPATH', __DIR__);
        require_once(ROOTPATH.'/../Class/CollectMastodonData.php');

        global  $wpdb;
        $result = ['comments' => [], 'stats' => ['reblogs' => 0, 'favs' => 0, 'replies' => 0, 'url' => '', 'root' => 0]];

        $search = isset($_GET['search']) ? $_GET['search'] : '';
        if( substr( $search, 0, 8 ) === "https://")
            $search = substr($search, 8);
        else if( substr( $search, 0, 7 ) === "http://")
            $search = substr($search, 7);

        $currentUser = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix . "mastalab_comments_users ORDER BY date DESC LIMIT 1", OBJECT );
        if( empty($currentUser))
            return;
        $config = [
            'mastodon-instance' => "https://" . $currentUser[0]->mastodon_instance,
            'user-id' => $currentUser[0]->user_id,
            'threshold' => $currentUser[0]->threshold,
            'token' => $currentUser[0]->token
        ];


        $collector = new CollectMastodonData($config);
        if (!empty($search)) {
	        $cachedURL = $wpdb->get_results(
	        	"SELECT * FROM ".$wpdb->prefix . "mastalab_comments_cache 
	                        WHERE URL='".urlencode($search)."' ORDER BY date DESC LIMIT 1", OBJECT );
	        $needRefresh = false;
	        //Something in cache
	        if( !empty($cachedURL)){
	        	$date = $cachedURL[0]->date;
		        $optionsPreferences = get_option($this->plugin_name.'preferences');
		        $cache_time = $optionsPreferences['cache_time'];
		        $cache_time = isset($cache_time)?$cache_time:5;
		        if((time()-($cache_time*60)) > strtotime($date)){
			        $needRefresh = true;
		        }
	        }else{
	        	$needRefresh = true;
	        }
            if ($needRefresh) {
                $data = $collector->findToots($search);
                $inc = 0;
                foreach ($data['id'] as $id) {
	                $result['stats']['root'] = $id;
                    // get comments
                    $collector->getComments($id, $result);
                    // get statistics (likes, replies, boosts,...)

                    $collector->getStatistics($id, $result);
                    $result['stats']['replies'] = count($result['comments']);
	                $result['stats']['url'] = $data['url'][$inc];
	                $inc++;
                }
	            $dateInsert = new DateTime();
	            //Cache the status
	            if( empty($cachedURL)) {
		            $wpdb->insert(
			            $wpdb->prefix . 'mastalab_comments_cache',
			            array(
				            'date'  => $dateInsert->format( 'Y-m-d H:i:s' ),
				            'URL'   => urlencode( $search ),
				            'cache' => json_encode( $result )

			            )
		            );
	            } else{ //Update cache
		            $wpdb->update(
			            $wpdb->prefix . "mastalab_comments_cache",
			            array(
				            'date'  => $dateInsert->format( 'Y-m-d H:i:s' ),
				            'cache' => json_encode( $result )
			            ),
	                    array( 'URL'   => urlencode( $search ))
		            );


	            }
            }else{
	            $result = json_decode($cachedURL[0]->cache, true);
            }
        }

        // send the result now
        wp_send_json($result);
    }




}
