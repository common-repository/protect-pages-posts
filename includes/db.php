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
 * Defines all the query using Database
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 * @author     Bwps <support@bwps.us>
 */
if (!defined('ABSPATH')) exit;

require_once dirname(__FILE__) . '/constants.php';
if (!class_exists('Prevent_Page_Pup_Database')) {
    class Prevent_Page_Pup_Database
    {
        private $jal_db_version;

        public function __construct()
        {
            $this->jal_db_version = '1.0';
        }

        function install()
        {

            global $wpdb;
            global $jal_db_version;

            $table_name = $wpdb->prefix . 'pur_private_links';

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			post_id mediumint(9) NULL,
            page_id mediumint(9) NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			url varchar(55) DEFAULT '' NOT NULL,
			is_prevented tinyint(1) DEFAULT 1,
			UNIQUE KEY id(id)
		) $charset_collate;";
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option('jal_db_version', $jal_db_version);
        }

        function uninstall()
        {
            global $wpdb;
            $table_name = $wpdb->prefix . 'pur_private_links';
            $sql = "DROP TABLE IF EXISTS $table_name";
            $wpdb->query($sql);
            delete_option('pur_private_links');
        }

        function insert_post($postId, $postIdToken, $isPrevented)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            $present_date = date("Y-m-d H:i:s");

            // Check Exist
            //Checking to see if the user email already exists
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = $postId");
            if ($datum) {
                // Update Database
                $postIdToken = reset($datum)->url;
                $wpdb->update($table_name, array(
                    'is_prevented' => $isPrevented,
                    'time' => $present_date,
                ), array(
                    'post_id' => $postId
                ));
            } else {
                // Insert Database
                $wpdb->insert($table_name, array(
                    'post_id' => $postId,
                    'time' => $present_date,
                    'url' => $postIdToken,
                    'is_prevented' => 1));
            }

            return $postIdToken;
        }

        function insert_page($pageId, $pageIdToken, $isPrevented)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            $present_date = date("Y-m-d H:i:s");

            // Check Exist
            //Checking to see if the user email already exists
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id = $pageId");
            if ($datum) {
                // Update Database
                $pageIdToken = reset($datum)->url;
                $wpdb->update($table_name, array(
                    'is_prevented' => $isPrevented,
                    'time' => $present_date,
                ), array(
                    'page_id' => $pageId
                ));
            } else {
                // Insert Database
                $wpdb->insert($table_name, array(
                    'page_id' => $pageId,
                    'time' => $present_date,
                    'url' => $pageIdToken,
                    'is_prevented' => 1));
            }

            return $pageIdToken;
        }

        function check_exist_post_protected($postId)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE post_id = $postId AND is_prevented = 1");
            if ($datum) {
                return $datum[0]->url;
            } else {
                return false;
            }
        }

        function check_exist_page_protected($pageId)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE page_id = $pageId AND is_prevented = 1");
            if ($datum) {
                return $datum[0]->url;
            } else {
                return false;
            }
        }

        function get_protected_post($postIdToken)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE url = '" . $postIdToken . "' AND is_prevented = 1 AND post_id IS NOT NULL");
            if ($datum) {
                return $datum[0]->post_id;
            } else {
                return false;
            }
        }

        function get_protected_page($pageIdToken)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_results("SELECT * FROM $table_name WHERE url = '" . $pageIdToken . "' AND is_prevented = 1 AND page_id IS NOT NULL");
            if ($datum) {
                return $datum[0]->page_id;
            } else {
                return false;
            }
        }
        
        // Feature Limit Post/Page for free version
        
        function check_limit_page($pageID)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE page_id <> 'NULL' AND is_prevented = 1");
            if ($datum) {
                return $datum;
            } else {
                return false;
            }
        }
        
        function check_limit_post($postID)
        {
            global $wpdb;
            $table_name = Prevent_Page_BWPSConstant::getTableName();

            // Check Exist
            $datum = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE post_id <> 'NULL' AND is_prevented = 1");
        
            if ($datum) {
                return $datum;
            } else {
                return false;
            }
        }

    }
}