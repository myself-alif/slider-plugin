<?php

/**
 * Plugin Name: Slider
 * Plugin URI: #
 * Description: Simple WordPress Plugin to show sliders
 * Version: 1.0
 * Requires at least: 5.6
 * Author: Al Mahmud Alif
 * Author URI: #
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: slider
 * Domain Path: /languages
 */



if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Slider')) {
    class Slider
    {
        function __construct()
        {
            $this->define_constants();

            $this->load_textdomain();

            require_once(SLIDER_PATH . 'functions/functions.php');

            add_action('admin_menu', array($this, 'add_menu'));

            require_once(SLIDER_PATH . 'post-types/class.slider-cpt.php');
            $Slider_Post_Type = new Slider_Post_Type();

            require_once(SLIDER_PATH . 'class.slider-settings.php');
            $Slider_Settings = new Slider_Settings();

            require_once(SLIDER_PATH . 'shortcodes/class.slider-shortcode.php');
            $Slider_Shortcode = new Slider_Shortcode();

            add_action('wp_enqueue_scripts', array($this, 'register_scripts'), 999);
            add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
        }

        public function define_constants()
        {
            define('SLIDER_PATH', plugin_dir_path(__FILE__));
            define('SLIDER_URL', plugin_dir_url(__FILE__));
            define('SLIDER_VERSION', '1.0.0');
        }

        public static function activate()
        {
            update_option('rewrite_rules', '');
        }

        public static function deactivate()
        {
            flush_rewrite_rules();
            unregister_post_type('slider');
        }

        public static function uninstall()
        {

            delete_option('slider_options');

            $posts = get_posts(
                array(
                    'post_type' => 'slider',
                    'number_posts'  => -1,
                    'post_status'   => 'any'
                )
            );

            foreach ($posts as $post) {
                wp_delete_post($post->ID, true);
            }
        }

        public function load_textdomain()
        {
            load_plugin_textdomain(
                'slider',
                false,
                dirname(plugin_basename(__FILE__)) . '/languages/'
            );
        }

        public function add_menu()
        {
            add_menu_page(
                esc_html__('Slider Options', 'slider'),
                'Slider',
                'manage_options',
                'slider_admin',
                array($this, 'slider_settings_page'),
                'dashicons-images-alt2'
            );

            add_submenu_page(
                'slider_admin',
                esc_html__('Manage Slides', 'slider'),
                esc_html__('Manage Slides', 'slider'),
                'manage_options',
                'edit.php?post_type=slider',
                null,
                null
            );

            add_submenu_page(
                'slider_admin',
                esc_html__('Add New Slide', 'slider'),
                esc_html__('Add New Slide', 'slider'),
                'manage_options',
                'post-new.php?post_type=slider',
                null,
                null
            );
        }

        public function slider_settings_page()
        {
            if (!current_user_can('manage_options')) {
                return;
            }

            if (isset($_GET['settings-updated'])) {
                add_settings_error('slider_options', 'slider_message', esc_html__('Settings Saved', 'slider'), 'success');
            }

            settings_errors('slider_options');

            require(SLIDER_PATH . 'views/settings-page.php');
        }

        public function register_scripts()
        {
            wp_register_script('slider-main-jq', SLIDER_URL . 'vendor/flexslider/jquery.flexslider-min.js', array('jquery'), SLIDER_VERSION, true);
            wp_register_style('slider-main-css', SLIDER_URL . 'vendor/flexslider/flexslider.css', array(), SLIDER_VERSION, 'all');
            wp_register_style('slider-style-css', SLIDER_URL . 'assets/css/frontend.css', array(), SLIDER_VERSION, 'all');
        }

        public function register_admin_scripts()
        {
            global $typenow;
            if ($typenow == 'slider') {
                wp_enqueue_style('slider-admin', SLIDER_URL . 'assets/css/admin.css');
            }
        }
    }
}

if (class_exists('Slider')) {
    register_activation_hook(__FILE__, array('Slider', 'activate'));
    register_deactivation_hook(__FILE__, array('Slider', 'deactivate'));
    register_uninstall_hook(__FILE__, array('Slider', 'uninstall'));

    $slider = new Slider();
}
