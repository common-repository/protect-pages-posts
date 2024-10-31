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
 * Defines the Constants
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 * @author     Bwps <support@bwps.us>
 */

if (!class_exists('Prevent_Page_BWPSConstant')) {

    class Prevent_Page_BWPSConstant
    {

        // Plugin Version
        const PLUGIN_VERSION = "1.0.0";

        // Plugin Table Name
        const PUP_TABLE_NAME = "pur_private_links";

        // Plugin EndPoint Private
        const PRE_END_POINT = "pre_endpoint_pup";

        // Custom Column Array Admin
        const PUP_CUSTOM_COLUMN_POST = "pup_custom_col_post";

        const PUP_CUSTOM_COLUMN_PAGE = "pup_custom_col_page";

        // Private link
        const PRIVATE_URL = "privatepup";

        // Post Mode
        const PRIVATE_POST_MODE = 1;

        // Page mode
        const PRIVATE_PAGE_MODE = 2;
        
        // Allow Notification 
        const ALLOW_NOTIFICATION = "allow_pup";
        
        // Check Limit for free Version
        const ALLOW_FREE_VERSION_LIMIT = 3;
        
        // Message using free version exceed limit
        const MESSGE_EXCCED_LIMIT = "Free version: limit number of of protected posts/pages to 3 each! Please Upgrade to Golde Version"; 

        static function getTableName()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . Prevent_Page_BWPSConstant::PUP_TABLE_NAME;
            return $table_name;
        }
    }
}
?>