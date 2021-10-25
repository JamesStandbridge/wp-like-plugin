<?php

/**
 * @package LikePlugin
 */

/*
Plugin Name: LikePlugin
Description: Make possible to like post
Version: 1.0.0
Author: james.standbridge.git@gmail.com
Author URI: https://github.com/JamesStandbridge
Licence: GPLv2 or ob_deflatehandler
Text Domain: LikePlugin
*/

if (!defined('ABSPATH')) 
    exit;

class LikePlugin
{
	function activate() {
		global $wpdb;
	
		$table_name = $wpdb->prefix . 'liked_post';
		$user_table = $wpdb->prefix . 'users';
		$post_table = $wpdb->prefix . 'posts';
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS $table_name
		(
			user_ip varchar(45) NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			is_liked boolean NOT NULL,
			CONSTRAINT user_post_pkey PRIMARY KEY (user_ip, post_id),
			CONSTRAINT fk_post_liked FOREIGN KEY (post_id)
			REFERENCES $post_table (ID)
		) $charset_collate;";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta( $sql );
	}

	function deactivate() {
		// global $wpdb;

		// $table_name = $wpdb->prefix . 'liked_post';
		// $sql = "DROP TABLE IF EXISTS $table_name";
		// $wpdb->query($sql);

		// delete_option('wp_install_uninstall_config');
	}

	function uninstall() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'liked_post';
		$sql = "DROP TABLE IF EXISTS $table_name";
		$wpdb->query($sql);

		delete_option('wp_install_uninstall_config');
	}
}

if(class_exists('LikePlugin')) {
	$likePlugin = new LikePlugin();
}

register_deactivation_hook(__FILE__, array($likePlugin, 'deactivate'));
register_activation_hook(__FILE__, array($likePlugin, 'activate'));






add_action('admin_menu', 'plugin_setup_menu');
 
function plugin_setup_menu(){
    add_menu_page( 'LikePlugin Page', 'LikePlugin', 'manage_options', 'like-plugin', 'like_plugin_init' );
}
 
function like_plugin_init(){
    echo "<h1>Hello World!</h1>";
}














/**
 * This function allows you to generate a button 
 * allowing you to "like" a post
 *
 * @param integer $postID
 * @param string $isLikedMessage default="Aimer"
 * @param string $isNotLikedMessage default="Ne plus aimer"
 * @return string htmlButton
 */
function the_like_button(
	int $postID, 
	?string $isLikedMessage = "Aimer", 
	?string $isNotLikedMessage = "Ne plus aimer"
) : string
{
	return "
	<button 
		data-post=\"$postID\" 
		data-liked=\"$isLikedMessage\" 
		data-not-liked=\"$isNotLikedMessage\" 
		onclick=\"handleLike($postID)\" 
		class=\"like-button\"
		id=\"like-button-$postID\"
	>
	</button>";
}

/**
 * This function retrieves a span tag displaying 
 * the number of likes of the given post
 *
 * @param integer $postID
 * @param bool    $displayIf0
 * @param string  $word default=null
 * @param string  $pluralWord default="s"
 * @param string  $class
 * @return string htmlSpan
 */
function the_like_counter(
	int $postID, 
	bool $displayIf0 = true, 
	?string $word = null,
	?string $pluralWord = "s",
	?string $class = "like-counter"
) : ?string
{
	$count = get_count_likes($postID);
	

	if($word)
		$wordspan = "<span id=\"span-counter-like-word-$postID\" class=\"span-counter-like-word\">$word</span>";
	else
		$wordspan = null;

	return 
	"
		<span 
			class=\"span-like-counter\"
			id=\"span-like-counter-$postID\"
			data-display-0=\"$displayIf0\"
			data-plural-word=\"$pluralWord\"
			data-word=\"$word\"
		>
			<span class=\"$class\" id=\"like-counter-$postID\">$count</span>
			$wordspan
		</span>
	";
}


/**
 * Returns the number of likes on a given post
 *
 * @param integer $postID
 * @return int
 */
function get_count_likes(int $postID) : int
{
	require_once plugin_dir_path(__FILE__) . "/src/LikeManager.php";
	$manager = new LikeManager();
	$count = $manager->countLikes($postID);

	return $count;
}

/**
 * This function takes as argument the id of 
 * a post and the ip of a user. Allows to change 
 * the state of the like of a post for an ip.
 *
 * @return void
 */
function like_post_action() 
{
	require_once plugin_dir_path(__FILE__) . "/src/LikeManager.php";
	$manager = new LikeManager();

	$user_ip = $_SERVER['REMOTE_ADDR'];
	$post_id = $_POST['post_id'];
	$isLiked = $manager->changeLikeStatePost($user_ip, $post_id);

	echo json_encode($isLiked);

	wp_die();
}

add_action( 'wp_ajax_like_post', 'like_post_action' );
add_action( 'wp_ajax_nopriv_like_post', 'like_post_action' );

/**
 * Ajax function
 * This function takes as argument the user's ip and the post id. 
 * Return the state of the post like : 1 or 0
 *
 * @return void
 */
function get_post_current_like_state()
{
	require_once plugin_dir_path(__FILE__) . "/src/LikeManager.php";
	$manager = new LikeManager();

	$user_ip = $_SERVER['REMOTE_ADDR'];
	$post_id = $_POST['post_id'];

	$result = $manager->getCurrentPostState($user_ip, $post_id);
	echo $result;

	wp_die();
}

add_action( 'wp_ajax_get_post_like_state', 'get_post_current_like_state' );
add_action( 'wp_ajax_nopriv_get_post_like_state', 'get_post_current_like_state' );

/**
 * Loading jquery scripts in the front end.
 * Insert LikePlugin const variable to access to ajax url
 */
add_action( 'wp_enqueue_scripts', 'my_custom_script_load' );

function my_custom_script_load()
{
		wp_enqueue_script(
			'my-custom-script', 
			plugin_dir_url( __FILE__ ) . '/js/script.js', array('jquery') 
		);

		wp_add_inline_script(
			'my-custom-script', 
			'const LikePlugin = ' . json_encode( 
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' )
				) 
			), 
			'before' 
		);
}