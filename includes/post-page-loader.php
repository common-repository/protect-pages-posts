<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.buildwps.com/
 * @since      1.0.0
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 */

/**
 *
 * Render Protected Page or Post
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 * @author     Bwps <support@bwps.us>
 */

if ( !defined( 'ABSPATH' ) ) exit;

include 'db.php';

ignore_user_abort( true );
set_time_limit( 0 ); // disable the time limit for this script

// Show Post From Private Link
show_post_from_private_link();

function show_post_from_private_link() {
    global $wp_query;
    $db = new Prevent_Page_Pup_Database();
    $endpoint = Prevent_Page_BWPSConstant::PRE_END_POINT;
    
    if(isset($_GET[$endpoint])) {
        $private_url = $_GET[$endpoint];

        // Protect ID Post
        $protect_id = $db->get_protected_post($private_url);

        if ($protect_id) {
            // Render Template
            $template = get_single_template();

            // Global Page to change
            global $wp_query;
            $GLOBALS['post'] = get_post($protect_id);

            $wp_query = new WP_Query(array('p' => $protect_id));

            // Send Notification
            send_notification();
            
            private_page_render_template($template);

            return;
        }

        // Protect ID Page
        $protect_id = $db->get_protected_page($private_url);

        // Show Page Content ( if Page protect)
        if ($protect_id) {
            // Render Template
            $template = get_page_template();

            // Global Page to change
            global $wp_query;
            $GLOBALS['post'] = get_posts();

            $wp_query = new WP_Query(array('page_id' => $protect_id));
            
            // Send Notification
            send_notification();
            
            private_page_render_template($template);

            return;
        }
        return return_404_template();
    }
}

function return_404_template()
{
    global $wp_query;
    // Redirect to Post Not found
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 );
    exit();
}

function private_page_render_template($template)
{
    if ($template = apply_filters('template_include', $template)) {
        include($template);
    } elseif (current_user_can('switch_themes')) {
        $theme = wp_get_theme();
        if ($theme->errors()) {
            wp_die($theme->errors());
        }
    }
}

function send_notification() 
{
   include 'wp-notification-hub-sent.php';

    if (class_exists('WP_Notification_Hub_Sent')) {

        $wp_notification_hub_setting = new WP_Notification_Hub_Sent();

        // Check if active Notification Hub
        if ($wp_notification_hub_setting->check_notification())
        {
            $requestHeader = $wp_notification_hub_setting->get_notification_setting();

            // Send Notification to API GateWay
            $wp_notification_hub_setting->send_notification($requestHeader, $wp_notification_hub_setting->get_mesasge());
        }
    }

}
