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
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/include
 * @author     Bwps <support@bwps.us>
 */

if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

if (!class_exists('Prevent_Page_Pup_Function')) {
    class Prevent_Page_Pup_Function
    {

        function get_htaccess_file_path()
        {

            //global $wp_rewrite;
            $home_path = get_home_path();
            $htaccess_file = $home_path . '.htaccess';

            return $htaccess_file;
        }

        function htaccess_writable()
        {

            $htaccess_file = $this->get_htaccess_file_path();

            if (!file_exists($htaccess_file)) {
                error_log('.htaccess file not existed ');
                return '.htaccess file not existed';
            }

            error_log('.htaccess is writeable: ' . is_writable($htaccess_file));
            if (is_writable($htaccess_file)) {
                return true;
            }

            @chmod($htaccess_file, 0666);

            if (!is_writable($htaccess_file)) {
                error_log('Please ask host manager to grant write permission for .htaccess file.');
                return 'Please ask host manager to grant write permission for .htaccess file.';
            }

            return true;
        }

        function get_htaccess_content()
        {

            //global $wp_rewrite;

            $htaccess_file = $this->get_htaccess_file_path();

            if (!file_exists($htaccess_file)) {
                return false;
            }

            if (!is_writable($htaccess_file)) {
                @chmod($htaccess_file, 0666);
            }

            if (!$f = fopen($htaccess_file, 'r')) {
                return false;
            }

            return file_get_contents($htaccess_file);
        }

        function sanitized_rule($rule)
        {
            $rule = trim($rule);
            $rule = str_replace('\\\\', '\\', $rule);
            $rule = str_replace('\"', '"', $rule);

            return $rule;
        }

        // Generator random Post/Page GUID
        function post_page_token_generator()
        {
            return md5(uniqid(rand(), true));
        }
    }
}
