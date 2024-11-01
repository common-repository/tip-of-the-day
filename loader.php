<?php
/*
Plugin Name: Tip of the Day
Plugin URI: http://dev.pellicule.org/tip-of-the-day/
Description: Tip of The Day is a plugin that display random tips, quotes, polls... for your users, in a widget.
Version: 0.1
Revision Date: November 11, 2010
Requires at least: Wordpress 3, BuddyPress 1.2
Tested up to: Wordpress 3.0.1, BuddyPress 1.2.6
License: (Example: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html)
Author: G.Breant
Author URI: http://dev.pellicule.org
Site Wide Only: true
*/

if ( !defined( 'TOTD_PLUGIN_NAME' ) ) {
	global $wpdb;
	define ( 'TOTD_PLUGIN_NAME', 'tip-of-the-day');
	define ( 'TOTD_IS_INSTALLED', 1 );
	define ( 'TOTD_VERSION', '0.1' );
	//Core Path
	
	define ( 'TOTD_DIRNAME', str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );

	define ( 'TOTD_PLUGIN_DIR',  WP_PLUGIN_DIR . '/' . TOTD_DIRNAME );
	define ( 'TOTD_PLUGIN_URL', WP_PLUGIN_URL . '/' . TOTD_DIRNAME );

	define ( 'TOTD_WORDPRESS_URL', 'http://wordpress.org/extend/plugins/'.TOTD_PLUGIN_NAME.'/' );
	define ( 'TOTD_SUPPORT_URL', 'http://dev.pellicule.org/bbpress/forum/'.TOTD_PLUGIN_NAME.'/' );
	define ( 'TOTD_DONATION_URL', 'http://dev.pellicule.org/'.TOTD_PLUGIN_NAME.'/#donate' );
}

require_once( TOTD_PLUGIN_DIR . '/includes/totd-post-type.php' );
require_once( TOTD_PLUGIN_DIR . '/admin/totd-admin.php' );
require_once( TOTD_PLUGIN_DIR . '/includes/totd-core.php' );
require_once( TOTD_PLUGIN_DIR . '/includes/totd-widgets.php' );
require_once( TOTD_PLUGIN_DIR . '/includes/totd-ajax.php' );
require_once( TOTD_PLUGIN_DIR . '/includes/totd-theme.php' );


/* Only load the component if BuddyPress is loaded and initialized. */
function bptotd_init() {
}

if ( defined( 'BP_VERSION' ) || did_action( 'bp_init' ) )
	bptotd_init();
else
	add_action( 'bp_init', 'bptotd_init' );

/* Put setup procedures to be run when the plugin is activated in the following function */
function totd_activate() {
	do_action('totd_activate');
}
register_activation_hook( __FILE__, 'totd_activate' );

/* On deacativation, clean up anything your component has added. */
function totd_deactivate() {
	do_action('totd_deactivate');
}
register_deactivation_hook( __FILE__, 'totd_deactivate' );

function totd_plugin_settings_action( $links, $file ) {
    //Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;

	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if ( $file == $this_plugin ){
        $settings_link = '<a href="edit.php?post_type=totd&page='.TOTD_PLUGIN_NAME.'">' . __( 'Settings' ) . '</a>';
	    array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
} // end function si_contact_plugin_action_links
// adds "Settings" link to the plugin action page

//keep in loader
add_filter( 'plugin_action_links', 'totd_plugin_settings_action',10,2);

?>