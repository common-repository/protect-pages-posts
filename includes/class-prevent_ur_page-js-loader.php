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
 * Loading Javascript -  handle variables to javascript file.
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 * @author     Bwps <support@bwps.us>
 */
if (!class_exists('Prevent_Page_Pup_Function')) {

    class Prevent_Page_JS_Loader
    {

        // Load JS for Admin Panel
        public static function admin_load_js()
        {
            // Load Admin Script
            wp_enqueue_script("customJS", plugin_dir_url(__FILE__) . '../js/custom-file.js', array('jquery'), '1.0.0', false);

            // Define Object in Script
            wp_localize_script('customJS', 'ajax_object', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'templateurl' => network_site_url(Prevent_Page_BWPSConstant::PRIVATE_URL)));
        }
    }
}
?>