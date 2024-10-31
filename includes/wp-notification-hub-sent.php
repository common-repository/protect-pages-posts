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
 * Defines Functions
 *
 * @package    WP Notification Hub
 * @subpackage WP Notification Hub Setting
 * @author     Bwps <support@bwps.us>
 */
require_once dirname(__FILE__) . '/constants.php';

if (!class_exists('WP_Notification_Hub_Sent')) {
    class WP_Notification_Hub_Sent
    {
        var $API_KEY = 'jiId8O87Fo51szcGwBuJ89ggb58a40Dw1JlLH16g';

        function check_notification()
        {
            // Check Notification Hub
            $check_notification_hub = false;

            $notification_settings = get_option('FREE_NOTIFICATION_SETTINGS');
            if ($notification_settings)
                $check_notification_hub = $notification_settings['allow_notification'] === 'on' ? true : false;
                if ($check_notification_hub)
                    $check_notification_hub = $notification_settings[Prevent_Page_BWPSConstant::ALLOW_NOTIFICATION] === 'on' ? true : false;

            return $check_notification_hub;

        }

        function get_mesasge()
        {
            $notification_settings = get_option('FREE_NOTIFICATION_SETTINGS');
            $format = str_replace("\\", "", $notification_settings["message_format"]);
            $actual_link = get_site_url().$_SERVER['REQUEST_URI'];

            $message = str_replace( "#URL", $actual_link, $format);
            $message = str_replace("\r\n",'', $message);
            $message = str_replace(" ",'', $message);
            return $message;
        }


        function get_notification_setting()
        {

            $notification_settings = get_option('FREE_NOTIFICATION_SETTINGS');

            // Lambda AWS Server
            $lambda_server = $notification_settings['lambda_server'];

            // App ID
            $app_id = $notification_settings['app_id'];

            // App Key
            $app_key = $notification_settings['app_key'];

            // Incoming WebHooks
            $slack_url_hook = $notification_settings['slack_hook_url'];

            // Notification Channel
            $notification_channel = $notification_settings['notification_channel'];

            // Message Format
            $message_format = $notification_settings['message_format'];


            $headerRequest = array(
                'lambda_server'=> $lambda_server,
                'app_id' => $app_id,
                'app_key' => $app_key,
                'slack_hook_url' => $slack_url_hook,
                'notification_channel' => $notification_channel,
                'message_format' => $message_format
            );

            return $headerRequest;
        }

        function send_notification($headerRequest, $message)
        {
            //API Url
            $serviceUrl = $headerRequest["lambda_server"];

            //The JSON data.
            $jsonData = array(
                'AppId' => $headerRequest["app_id"],
                'AppKey' => $headerRequest["app_key"],
                'Config' => false,
                "ConfigSetting"=> null,
                'Sns' => json_decode($message)
            );

            // Json encode Format Message
            $jsonData = json_encode($jsonData);

            $args = array(
                'body' => $jsonData,
                'timeout' => '100',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(
                    'x-api-key' => $this->API_KEY,
                    'Content-Type' => 'application/json'
                ),
                'cookies' => array()
            );

            $response = wp_remote_post( $serviceUrl, $args );

            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
            } else {
                $status_code = wp_remote_retrieve_response_code( $response );
                if($status_code != 200) {
                    $data = wp_remote_retrieve_body( $response );
                } else {
                    $body = json_decode(wp_remote_retrieve_body( $response ));
                    return $body;
                }
            }

        }
    }

}
?>
