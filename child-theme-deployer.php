<?php
/**
 * Plugin Name:     Child Theme Deployer
 * Plugin URI:      http://section214.com/
 * Description:     Deploy child themes for any installed theme with a click!
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     child-theme-deployer
 *
 * @package         ChildThemeDeployer
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


if( !class_exists( 'ChildThemeDeployer' ) ) {


    /**
     * Main ChildThemeDeployer class
     *
     * @since       1.0.0
     */
    class ChildThemeDeployer {

        /**
         * @var         ChildThemeDeployer $instance The one true ChildThemeDeployer
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true ChildThemeDeployer
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new ChildThemeDeployer();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'CHILDTHEMEDEPLOYER_VER', '1.0.0' );

            // Plugin path
            define( 'CHILDTHEMEDEPLOYER_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'CHILDTHEMEDEPLOYER_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            if( is_admin() ) {
                require_once CHILDTHEMEDEPLOYER_DIR . 'includes/functions.php';
                require_once CHILDTHEMEDEPLOYER_DIR . 'includes/admin-actions.php';
                require_once CHILDTHEMEDEPLOYER_DIR . 'includes/admin-pages.php';
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Edit plugin metalinks
            add_filter( 'plugin_row_meta', array( $this, 'plugin_metalinks' ), null, 2 );

            // Display notice on theme activation
            add_action( 'admin_init', array( $this, 'trigger_theme_activation_notice' ) );

            // Add admin page
            add_action( 'admin_menu', array( $this, 'add_admin_menu_item' ) );

            // Hide admin page link
            add_action( 'admin_head', array( $this, 'hide_admin_menu_item' ) );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'ChildThemeDeployer_language_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale     = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile     = sprintf( '%1$s-%2$s.mo', 'child-theme-deployer', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/child-theme-deployer/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/child-theme-deployer/ folder
                load_textdomain( 'child-theme-deployer', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/child-theme-deployer/languages/ folder
                load_textdomain( 'child-theme-deployer', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'child-theme-deployer', false, $lang_dir );
            }
        }


        /**
         * Modify plugin metalinks
         *
         * @access      public
         * @since       1.0.0
         * @param       array $links The current links array
         * @param       string $file A specific plugin table entry
         * @return      array $links The modified links array
         */
        public function plugin_metalinks( $links, $file ) {
            if( $file == plugin_basename( __FILE__ ) ) {
                $help_link = array(
                    '<a href="http://section214.com/support/forum/child-theme-deployer/" target="_blank">' . __( 'Support Forum', 'child-theme-deployer' ) . '</a>'
                );

                $docs_link = array(
                    '<a href="http://section214.com/docs/category/child-theme-deployer/" target="_blank">' . __( 'Docs', 'child-theme-deployer' ) . '</a>'
                );

                $links = array_merge( $links, $help_link, $docs_link );
            }

            return $links;
        }


        /**
         * Trigger notification on theme change
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function trigger_theme_activation_notice() {
            global $pagenow;

            if( ! is_child_theme() && ( $pagenow != 'themes.php' || $_GET['page'] != 'ctdeployer' ) ) {
                add_action( 'admin_notices', array( $this, 'display_theme_activation_notice' ) );
            }
        }


        /**
         * Display notification
         *
         * @access      public
         * @since       1.0-0
         * @return      void
         */
        public function display_theme_activation_notice() {
            // Build child theme name
            // Names are based on the format <sitename-themename-child> to allow
            // usage on multisite instances with individual child themes per site
            $site  = sanitize_title( get_bloginfo( 'name' ) );
            $theme = get_stylesheet();
            $name  = $site . '-' . $theme . '-child';

            $theme_root = trailingslashit( get_theme_root() );

            if( is_dir( $theme_root . $name ) ) {
                echo '<div class="error"><p>' . sprintf( __( 'The active theme is not a child theme, but a child theme exists! Please <a href="%s">click here</a> to activate it now!', 'child-theme-deployer' ), add_query_arg( array( 'ctdeployer-action' => 'activate_child_theme' ) ) ) . '</p></div>';
            } else {
                echo '<div class="error"><p>' . sprintf( __( 'The active theme is not a child theme! Please <a href="%s">click here</a> to create one now!', 'child-theme-deployer' ), admin_url( 'themes.php?page=ctdeployer' ) ) . '</p></div>';
            }
        }


        /**
         * Add Admin menu item
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function add_admin_menu_item() {
            add_theme_page(
                __( 'Child Theme Deployer', 'child-theme-deployer' ),
                __( 'Child Theme Deployer', 'child-theme-deployer' ),
                'manage_options',
                'ctdeployer',
                'ctdeployer_build_screen'
            );
        }


        /**
         * Hide Admin menu item
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function hide_admin_menu_item() {
            remove_submenu_page( 'themes.php', 'ctdeployer' );
        }
    }
}


/**
 * The main function responsible for returning the one true ChildThemeDeployer
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      ChildThemeDeployer The one true ChildThemeDeployer
 */
function ChildThemeDeployer_load() {
    return ChildThemeDeployer::instance();
}

// Off we go!
ChildThemeDeployer_load();