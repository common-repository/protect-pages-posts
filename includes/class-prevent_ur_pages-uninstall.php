<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.buildwps.com/
 * @since      1.0.0
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.

 *
 * @since      1.0.0
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/includes
 * @author     Bwps <support@bwps.us>
 */

class Prevent_ur_pages_Uninstall {
    
    public static function uninstall() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pur_private_links';
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
        delete_option('pur_private_links');
    }
}