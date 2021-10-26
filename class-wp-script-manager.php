<?php

/**
 * @package wp-script-manager
 * Plugin Name: Wp Script Manager
 * Description: WordPress Script Manager By Yami-no-karuro
 * Version: 1.0
 * Author: Carlo Borgna
 * License: GPLv2 or later
 * Text Domain: wp-script-manager
 */

if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

/**
 * @package wp-script-manager
 * Plugin main class
 */
class WPScriptManager {

    /**
     * @package WpScriptManager -> globals
     * Global variables
     */
    public $plugin_filename;
    public $plugin_pagename;
    public $plugin_menu_title;

    /**
     * @package WPScriptManager -> "__construct"
     * Class constructor
     */
    public function __construct() {
        $this->plugin_name = plugin_basename(__FILE__);
        $this->plugin_pagename = 'wp-script-manager';
        $this->plugin_menu_title = 'Script Manager';
        add_action('admin_menu', array($this, 'setup_admin_dashboard'));
        add_filter('plugin_action_links' . $this->plugin_filename, array($this, 'setup_plugin_settings_link'));
        add_action('add_meta_boxes', array($this, 'setup_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'admin_assets_enqueue'));
        add_action('wp_print_scripts', array($this, 'scripts_scraper_init'));
        add_action('wp_ajax_clear_spiders_transients', array($this, 'clear_spiders_transients'));
        add_action('wp_ajax_nopriv_clear_spiders_transients', array($this, 'clear_spiders_transients'));
        add_action('admin_enqueue_scripts', array($this, 'ajax_script_localizer'));
    }

    /**
     * @package WpScriptManager -> "setup_admin_dashboard"
     * Registering admin menu page, callback on "admin_dashboard_output"
     */
    public function setup_admin_dashboard() {
        add_menu_page(
            'wpscriptmanager',
            $this->plugin_menu_title,
            'manage_options',
            $this->plugin_pagename,
            array($this, 'admin_dashboard_output'),
            'dashicons-editor-code',
            50
        );  
    }

    /**
     * @package WpScriptManager -> "admin_dashboard_output"
     * Outputting dashboard template
     */
    public function admin_dashboard_output() {
        require_once plugin_dir_path(__FILE__).'templates/dashboard.php';
    }

    /**
     * @package WpScriptManager -> "setup_plugin_settings_link"
     * Pushing WpScriptManager setting link to Wordpress settings array in plugins page
     */
    public function setup_plugin_settings_link($links) {
        $settings_link = '<a href="admin.php?page='. $this->plugin_pagename .'"> Settings </a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * @package WpScriptManager -> "setup_plugin_settings_link"
     * Setting up metabox on post_type = post, callback on "wp_script_manager_metabox_callback"
     */
    public function setup_meta_box() {
        add_meta_box( 
            'wp_script_manager',
            'Script Manager',
             array($this, 'wp_script_manager_callback'),
            'page',
            'normal',
            'high'
        );
    }

    /**
     * @package WpScriptManager -> "wp_script_manager_metabox_callback"
     * WpScriptManager template output
     */
    public function wp_script_manager_callback() {
        require_once plugin_dir_path(__FILE__).'templates/meta-box.php';
    }

    /**
     * @package WpScriptManager -> "admin_assets_enqueue"
     * WpScriptManager backend assets enqueue
     */
    public function admin_assets_enqueue() {
        global $post;
        if ($post->post_type === 'page') {
            wp_register_style('metabox-styles-boundle', plugins_url('assets/css/metaBoxBoundle.css', __FILE__));
            wp_enqueue_style('metabox-styles-boundle');
            wp_register_script('metabox-scripts-boundle', plugins_url('assets/js/metaBoxBoundle.js', __FILE__), array ('jquery'), false, true);
            wp_enqueue_script('metabox-scripts-boundle');
        }
    }

    /**
     * @package WpScriptManager -> "scripts_scraper_init"
     * Scraping global $wp_scripts right before printing  
     */
    public function scripts_scraper_init() {
        if (!is_admin()) {
            require_once plugin_dir_path(__FILE__).'class-frontend-spider.php';
            if(class_exists('FrontendSpider')) {
                global $post;
                $update = get_transient('wp_queued_scripts_pageid_' . $post->ID);
                if (!$update) {
                    $frontend_spider = new FrontendSpider();
                    $package = $frontend_spider->prepare_transient_data();
                    set_transient('wp_queued_scripts_pageid_' . $post->ID, $package['scripts'], 604800);
                    set_transient('wp_queued_styles_pageid_' . $post->ID, $package['styles'], 604800);
                }
            }
        }
    }

    /**
     * @package WPScriptManager -> "clear_spiders_transients"
     * Delete spider transients action
     */
    public function clear_spiders_transients() {
        if (!wp_verify_nonce($_POST['nonce'], 'clear_spiders_transients_nonce')) {
            die('You should not be here dumbass! Be Gone!');
        }
        delete_transient('wp_queued_scripts_pageid_' . $_POST['page_id']);
        echo 'Success!';
        die();
    }

    /**
     * @package WPScriptManager -> "ajax_script_localizer"
     * Passing variables to JS
     */
    public function ajax_script_localizer() {
        wp_localize_script('metabox-scripts-boundle', 'metaBox', array( 
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'ajaxNonce' => wp_create_nonce('clear_spiders_transients_nonce')
        ));       
    }

}

/**
 * @package WpScriptManager
 * Class Init
 */
if(class_exists('WpScriptManager')) {
    $script_manager = new WpScriptManager();
}