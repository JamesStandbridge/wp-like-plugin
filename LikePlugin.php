<?php

/**
 * @package LikePlugin
 */

/*
Plugin Name: Simple Like
Description: The lightest plugin to integrate a like in your posts
Version: 1.0.0
Author: james.standbridge.git@gmail.com
Author URI: https://github.com/JamesStandbridge
Licence: GPLv2 or ob_deflatehandler
Text Domain: LikePlugin
*/

if (!defined('ABSPATH'))
    exit;

define( 'LIKEPLUGIN_DIR', plugin_dir_path( __FILE__ ) );

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

require_once LIKEPLUGIN_DIR . "admin/AdminCore.php";

register_deactivation_hook(__FILE__, array($likePlugin, 'deactivate'));
register_activation_hook(__FILE__, array($likePlugin, 'activate'));


add_action('admin_menu', 'plugin_setup_menu');

function like_plugin_init()
{
	my_admin_page_contents();
}

function plugin_setup_menu()
{
    add_submenu_page('options-general.php', 'LikePlugin Page', 'LikePlugin', 'manage_options', 'like-plugin', 'like_plugin_init' );
}



function load_custom_wp_admin_style($hook)
{
	if($hook != 'settings_page_like-plugin')
		return;
	wp_enqueue_style( 'custom_wp_admin_css', plugins_url('assets/css/AdminLikePlugin.min.css', __FILE__) );
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');


/**
 * Register default settings
 */
if ( get_option( 'like_message' ) === false )
	update_option( 'like_message', 'Like');
if ( get_option( 'unlike_message' ) === false )
	update_option( 'unlike_message', 'Unlike');
if ( get_option( 'display_counter_if_0' ) === false )
	update_option( 'display_counter_if_0', '1');
if ( get_option( 'counter_label' ) === false )
	update_option( 'counter_label', 'Like');
if ( get_option( 'counter_label_plural' ) === false )
	update_option( 'counter_label_plural', 'Likes');
if ( get_option( 'markdown_type' ) === false )
	update_option( 'markdown_type', 'span');



/**
 * This function allows you to generate a button
 * allowing you to "like" a post
 *
 * @param integer $postID
 * @param string $isLikedMessage default=admin_option
 * @param string $isNotLikedMessage default=admin_option
 * @param string $class default = "like-button
 * @return string htmlButton
 */
function the_like_button(
	int $postID,
	?string $isLikedMessage = null,
	?string $isNotLikedMessage = null,
	string $class = "like-button"
)
{
	if(!$isLikedMessage)
		$isLikedMessage = get_option('like_message');
	if(!$isNotLikedMessage)
		$isNotLikedMessage = get_option('unlike_message');

	echo "
		<button
			data-post=\"$postID\"
			data-liked=\"$isLikedMessage\"
			data-not-liked=\"$isNotLikedMessage\"
			onclick=\"handleLike($postID)\"
			class=\"$class\"
			id=\"like-button-$postID\"
		>
		</button>
	";
}

/**
 * This function retrieves a span tag displaying
 * the number of likes of the given post
 *
 * @param integer $postID
 * @param bool    $displayIf0 default=admin_option
 * @param string  $word default=admin_option
 * @param string  $pluralWord default=admin_option
 * @param string  $class
 * @return string htmlSpan
 */
function the_like_counter(
	int $postID,
	?bool $displayIf0 = null,
	?string $word = null,
	?string $pluralWord = null,
	?string $class = null
)
{
	if(!$displayIf0)
		$displayIf0 = boolVal(get_option('display_counter_if_0'));

	if(!$pluralWord)
		$pluralWord = get_option('counter_label_plural');
	if(!$word)
		$word = get_option('counter_label');

	$count = get_count_likes($postID);


	if($word)
		$wordspan = "<span id=\"span-counter-like-word-$postID\" class=\"span-counter-like-word\">$word</span>";
	else
		$wordspan = null;

	$markdown = get_option('markdown_type');

	echo
	"
		<$markdown
			class=\"span-like-counter\"
			id=\"span-like-counter-$postID\"
			data-display-0=\"$displayIf0\"
			data-plural-word=\"$pluralWord\"
			data-word=\"$word\"
			data-value=$count
		>
			<span class=\"$class\" id=\"like-counter-$postID\">$count</span>
			$wordspan
		</$markdown>
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
	require_once LIKEPLUGIN_DIR . "/includes/LikeManager.php";
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
	require_once LIKEPLUGIN_DIR . "/includes/LikeManager.php";
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
	require_once LIKEPLUGIN_DIR . "/includes/LikeManager.php";
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
add_action( 'wp_footer', 'my_custom_script_load' );

function my_custom_script_load()
{
		wp_enqueue_script(
			'my-custom-script',
			plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array('jquery')
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

/**
 * Add settings link to plugin actions
*/
function my_plugin_settings_link($links)
{
  $settings_link = '<a href="admin.php?page=like-plugin">Settings</a>';
  array_unshift($links, $settings_link);
  return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'my_plugin_settings_link' );
