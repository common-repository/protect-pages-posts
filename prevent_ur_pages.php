<?php

/**
 *
 * @link              https://www.buildwps.com/
 * @since             1.0.0
 * @package           Prevent_ur_pages
 *
 * @wordpress-plugin
 * Plugin Name:       Prevent Your Pages
 * Plugin URI:        https://www.buildwps.com/
 * Description:       Protect your wordpress pages on demand.
 * Version:           1.0.0
 * Author:            ProFaceOff
 * Author URI:        https://www.profaceoff.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prevent_ur_pages
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_prevent_ur_pages()
{
    include dirname(__FILE__) . '/includes/db.php';
    $db = new Prevent_Page_Pup_Database();
    $db->install();
    flush_rewrite_rules(); // re-trigger mod_rewrite_rules
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_prevent_ur_pages()
{
    include dirname(__FILE__) . '/includes/db.php';
    $db = new Prevent_Page_Pup_Database();
    $db->uninstall();
    $GLOBALS['wp_rewrite']->flush_rules(true);
}

register_activation_hook(__FILE__, 'activate_prevent_ur_pages');
register_deactivation_hook(__FILE__, 'deactivate_prevent_ur_pages');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-prevent_ur_pages.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_prevent_ur_pages()
{
    $plugin = new Prevent_ur_pages();
    $plugin->run();
}

run_prevent_ur_pages();


