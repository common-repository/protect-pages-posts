<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.buildwps.com/
 * @since      1.0.0
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/includes
 * @author     Bwps <support@bwps.us>
 */

// Include Library
include_once 'class-prevent_ur_page-js-loader.php';
include_once 'db.php';
require_once dirname(__FILE__) . '/class-prevent_ur_page-function.php';
require_once dirname(__FILE__) . '/constants.php';

// Check Notificatino Hub
global $check_notification_hub;


if (!class_exists('Prevent_ur_pages')) {

    class Prevent_ur_pages
    {

        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      Prevent_ur_pages_Loader $loader Maintains and registers all hooks for the plugin.
         */
        protected $loader;


        /**
         * The unique identifier of this plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string $plugin_name The string used to uniquely identify this plugin.
         */
        protected $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @since    1.0.0
         * @access   protected
         * @var      string $version The current version of the plugin.
         */
        protected $version;

        protected $db;
        
        // Write htaccess file
        private $pup_function;

        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function __construct()
        {

            // Version Plugin
            $this->version = Prevent_Page_BWPSConstant::PLUGIN_VERSION;

            // Init Database
            $this->db = new Prevent_Page_Pup_Database();

            // Init htaccess Method
            $this->pup_function = new Prevent_Page_Pup_Function();
            
            // Init EndPoint
            add_action('init', array($this, 'pup_endpoint'));

            // Init htacess Access file
            add_action('admin_init', array($this, 'htaccess_updated'));

            // Define Custom ROUTE
            add_action('template_redirect', array($this, 'template_redirect_protected_post'));

            // Define Script Load
            add_action('admin_enqueue_scripts', array('Prevent_Page_JS_Loader', 'admin_load_js'));

            // Action Ajax Update Post Protected
            add_action('wp_ajax_protect_post', array($this, 'so_wp_ajax_protect_post'));

            // Action Ajax Update Page Protected
            add_action('wp_ajax_protect_page', array($this, 'so_wp_ajax_protect_page'));

            // Add custom protected URL
            add_filter('mod_rewrite_rules', array($this, 'htaccess_contents'));

            // Parse Query
            add_action('parse_query', array($this, 'parse_query'));

            // By pass if private comment
            add_filter('comment_post_redirect', array($this, 'redirect_after_comment'));

            $this->plugin_name = 'prevent_ur_pages';
            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();

        }

        // Action Protect Page
        public function so_wp_ajax_protect_page()
        {

            $page_id = $_REQUEST['id'];
            $isPrevented = $_REQUEST['isPrevented'];
        
            // Generator POST ID
            $page_token_id = $this->pup_function->post_page_token_generator();

            // Feature check limit 
            if ($isPrevented == 1)
                if ($this->db->check_limit_page($page_id) >= Prevent_Page_BWPSConstant::ALLOW_FREE_VERSION_LIMIT)
                    {
                        wp_send_json( array (
                            'free_version' => true,
                            'message' => Prevent_Page_BWPSConstant::MESSGE_EXCCED_LIMIT)
                        );
                        wp_die();
                    }
            
            // Insert to Database        
            $page_token_id = $this->db->insert_page($page_id, $page_token_id, $isPrevented);
            wp_send_json( array (
                'free_version' => false,
                'message' => $page_token_id)
            );
            wp_die();
        }

        // Action Protect Post
        public function so_wp_ajax_protect_post()
        {

            $post_id = $_REQUEST['id'];
            $isPrevented = $_REQUEST['isPrevented'];

            // Generator POST ID
            $post_token_id = $this->pup_function->post_page_token_generator();
            
            // Feature check limit 
            if ($isPrevented == 1)
                if ($this->db->check_limit_post($post_id) >= Prevent_Page_BWPSConstant::ALLOW_FREE_VERSION_LIMIT)
                    {
                        wp_send_json( array (
                            'free_version' => true,
                            'message' => Prevent_Page_BWPSConstant::MESSGE_EXCCED_LIMIT)
                        );
                        wp_die();
                    }
        
            // Insert to Database
            $post_token_id = $this->db->insert_post($post_id, $post_token_id, $isPrevented);
            wp_send_json( array (
                'free_version' => false,
                'message' => $post_token_id)
            );
            wp_die();
        }


        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - Prevent_ur_pages_Loader. Orchestrates the hooks of the plugin.
         * - Prevent_ur_pages_i18n. Defines internationalization functionality.
         * - Prevent_ur_pages_Admin. Defines all hooks for the admin area.
         * - Prevent_ur_pages_Public. Defines all hooks for the public side of the site.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since    1.0.0
         * @access   private
         */
        private function load_dependencies()
        {
            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-prevent_ur_pages-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-prevent_ur_pages-i18n.php';

            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-prevent_ur_pages-admin.php';

            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
            require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-prevent_ur_pages-public.php';

            $this->loader = new Prevent_ur_pages_Loader();

        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Prevent_ur_pages_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since    1.0.0
         * @access   private
         */
        private function set_locale()
        {

            $plugin_i18n = new Prevent_ur_pages_i18n();

            $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since    1.0.0
         * @access   private
         */
        private function define_admin_hooks()
        {

            $plugin_admin = new Prevent_ur_pages_Admin($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');

            $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

            $this->loader->add_filter('manage_posts_columns', $plugin_admin, 'pup_add_custom_column_post');

            $this->loader->add_action('manage_posts_custom_column', $plugin_admin, 'pup_display_post', 10, 2);

            $this->loader->add_filter('manage_pages_columns', $plugin_admin, 'pup_add_custom_column_page');

            $this->loader->add_action('manage_pages_custom_column', $plugin_admin, 'pup_display_page', 10, 2);

        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @since    1.0.0
         * @access   private
         */
        private function define_public_hooks()
        {

            $plugin_public = new Prevent_ur_pages_Public($this->get_plugin_name(), $this->get_version());

            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since    1.0.0
         */
        public function run()
        {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since     1.0.0
         * @return    string    The name of the plugin.
         */
        public function get_plugin_name()
        {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @since     1.0.0
         * @return    Prevent_ur_pages_Loader    Orchestrates the hooks of the plugin.
         */
        public function get_loader()
        {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @since     1.0.0
         * @return    string    The version number of the plugin.
         */
        public function get_version()
        {
            return $this->version;
        }

        // Init My EndPoint
        public function pup_endpoint()
        {
            $endpoint = Prevent_Page_BWPSConstant::PRE_END_POINT;
            add_rewrite_endpoint($endpoint, EP_ROOT);
        }

        // Check Comment from private link

        function redirect_after_comment($location)
        {
            return $_SERVER["HTTP_REFERER"];
        }

        // Redirect 404 when post is protected
        function template_redirect_protected_post()
        {
            global $wp;

            // Check preview Mode

            if (is_preview()) {
                return;
            }

            // Return If Not Single Post or Page
            if (!is_single() and !is_page()) {
                return;
            }

            // Check Post or Page exist protected link
            $id_check = get_the_ID();

            // Check Protected Post in Database and Current URL <> Base URL
            if ($this->db->check_exist_post_protected($id_check) || $this->db->check_exist_page_protected($id_check)) {
                // Redirect to Post Not found
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
                get_template_part(404);
                exit();
            }
        }

        // htaccess modify -> Convert Private Link to EndPoint
        function htaccess_contents($rules)
        {
            $private_url = Prevent_Page_BWPSConstant::PRIVATE_URL;
            $endpoint = Prevent_Page_BWPSConstant::PRE_END_POINT;

            $privatePostPageRedirect = str_replace(trailingslashit(site_url()), '', 'index.php') . "?{$endpoint}=$1 [R=301,L]" . PHP_EOL;
            $newRule = "RewriteRule {$private_url}/([a-zA-Z0-9-_]+)$ " . $privatePostPageRedirect;
            //$newRule .= "RewriteCond %{REQUEST_FILENAME} -s" . PHP_EOL;
            return $newRule . $rules . "Options -Indexes" . PHP_EOL;
        }

        // Parse Query
        public function parse_query($query)
        {
            $endpoint = Prevent_Page_BWPSConstant::PRE_END_POINT;
            if (isset($query->query_vars[$endpoint])) {
                // Logic how to catch private Post
                include(plugin_dir_path(__FILE__) . '/post-page-loader.php');
                exit;
            }
        }

        // htAccess File Update
        public function htaccess_updated()
        {
            $htaccess_writable = $this->pup_function->htaccess_writable();
            if ($htaccess_writable === true) {
                flush_rewrite_rules(); // re-trigger mod_rewrite_rules
            }
        }
    }
}
