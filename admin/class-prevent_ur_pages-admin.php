<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.buildwps.com/
 * @since      1.0.0
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prevent_ur_pages
 * @subpackage Prevent_ur_pages/admin
 * @author     Bwps <support@bwps.us>
 */

require_once dirname(__FILE__) . '/../includes/constants.php';
require_once dirname(__FILE__) . '/../includes/db.php';

if (!class_exists('Prevent_ur_pages_Admin')) {

    class Prevent_ur_pages_Admin
    {

        /**
         * The ID of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $plugin_name The ID of this plugin.
         */
        private $plugin_name;

        /**
         * The version of this plugin.
         *
         * @since    1.0.0
         * @access   private
         * @var      string $version The current version of this plugin.
         */
        private $version;

        private $db;

        /**
         * Initialize the class and set its properties.
         *
         * @since    1.0.0
         * @param      string $plugin_name The name of this plugin.
         * @param      string $version The version of this plugin.
         */
        public function __construct($plugin_name, $version)
        {

            $this->plugin_name = $plugin_name;
            $this->version = $version;
            $this->db = new Prevent_Page_Pup_Database();
        }

        /**
         * Register the stylesheets for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_styles()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Prevent_ur_pages_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Prevent_ur_pages_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */

            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/prevent_ur_pages-admin.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/loading.css', array(), $this->version, 'all');

        }

        /**
         * Register the JavaScript for the admin area.
         *
         * @since    1.0.0
         */
        public function enqueue_scripts()
        {

            /**
             * This function is provided for demonstration purposes only.
             *
             * An instance of this class should be passed to the run() function
             * defined in Prevent_ur_pages_Loader as all of the hooks are defined
             * in that particular class.
             *
             * The Prevent_ur_pages_Loader will then create the relationship
             * between the defined hooks and the functions defined in this
             * class.
             */
//            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../client/app/dist/ppp_bundle.js', array(), $this->version, 'all');
        }

        public function pup_add_custom_column_post($columns)
        {
            $columns[Prevent_Page_BWPSConstant::PUP_CUSTOM_COLUMN_POST] = __('Protect Your Post');
            return $columns;
        }

        public function pup_add_custom_column_page($columns)
        {
            $columns[Prevent_Page_BWPSConstant::PUP_CUSTOM_COLUMN_PAGE] = __('Protect Your Page');
            return $columns;
        }

        /* Display custom column */
        public function pup_display_post($column, $post_id)
        {
            if ($column == Prevent_Page_BWPSConstant::PUP_CUSTOM_COLUMN_POST) {
                // Add PostID to Input Tag
                $post = get_post($post_id);
                $checkProtected = $this->db->check_exist_post_protected($post->ID);

                ?>
                <input type="hidden" value="<?php echo $post->ID ?>" id="postId_<?php echo $post->ID ?>"></input>
                <input type="checkbox" name="is_featured" class="check-protected-post"
                       id="checkProtected_<?php echo $post->ID ?>"
                    <?php if ($checkProtected) echo "checked"; ?>
                /> Is it protected?</br>
                <div id = "regionProtect_<?php echo $post->ID; ?>"  <?php if (!$checkProtected) echo 'style="display: none;"'; ?> >
                    <?php
                    echo "Access your posts via this link:";
                    ?></br>
                    <input type="text" name="protectedUrl" id="protectedUrl_<?php echo $post->ID ?>"
                           placeholder="Here is protected Post URL"
                           value="<?php if ($checkProtected) echo network_site_url(Prevent_Page_BWPSConstant::PRIVATE_URL) . "/" . $checkProtected; ?>" >
                    <?php
                    ?>
                    <button class="copy-to-clipboard-post" id="copyToClipBoard_<?php echo $post->ID ?>">Copy Url</button>
                    <!--<button class ="generate-url" id="generateUrl_<?php echo $post->ID ?>" > Generate URL</button>-->
    
                    <!-- Create Loading Layout when using ajax-->
                </div>
                <?php
            }
        }

        /* Display custom column */
        public function pup_display_page($column, $page_id)
        {
            if ($column == Prevent_Page_BWPSConstant::PUP_CUSTOM_COLUMN_PAGE) {
                // Add PostID to Input Tag
                $page = get_page($page_id);
                $checkProtected = $this->db->check_exist_page_protected($page->ID);

                ?>
                <input type="hidden" value="<?php echo $page->ID ?>" id="pageId_<?php echo $page->ID ?>"></input>
                <input type="checkbox" name="is_featured" class="check-protected-page"
                       id="checkProtected_<?php echo $page->ID ?>"
                    <?php if ($checkProtected) echo "checked"; ?>
                /> Is it protected? </br>
                <div id = "regionProtect_<?php echo $page->ID; ?>"  <?php if (!$checkProtected) echo 'style="display: none;"'; ?> >
                    
                    <?php
                    echo "Access your pages via this link:";
                    ?></br>
                    <input type="text" name="protectedUrl" id="protectedUrl_<?php echo $page->ID ?>"
                           placeholder="Here is protected Page URL"
                           value="<?php if ($checkProtected) echo network_site_url(Prevent_Page_BWPSConstant::PRIVATE_URL) . "/" . $checkProtected; ?>">
                    <?php
                    ?>
                    <button class="copy-to-clipboard-page" id="copyToClipBoard_<?php echo $page->ID ?>" >Copy Url</button>
                    <!--<button class ="generate-url" id="generateUrl_<?php ?>" > Generate URL</button>-->
    
                    <!-- Create Loading Layout when using ajax-->
                </div>
                <?php
            }
        }

    }
}
