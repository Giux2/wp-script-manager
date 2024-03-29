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
        // add_action('admin_menu', array($this, 'setup_admin_dashboard'));
        add_filter('plugin_action_links' . $this->plugin_filename, array($this, 'setup_plugin_settings_link'));
        add_action('add_meta_boxes', array($this, 'setup_meta_box'));
        add_action('admin_enqueue_scripts', array($this, 'admin_assets_enqueue'));
        add_action('wp_print_scripts', array($this, 'scripts_scraper_init'));
        add_action('wp_ajax_clear_spiders_transients', array($this, 'clear_spiders_transients'));
        add_action('wp_ajax_tables_generator', array($this, 'tables_generator'));
        add_action('wp_ajax_scripts_handler', array($this, 'scripts_handler'));
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
     * Output dashboard template
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
     * Setting up metabox on post_type = page, callback on "wp_script_manager_metabox_callback"
     */
    public function setup_meta_box() {
        add_meta_box( 
            'wp_script_manager',
            'WPScripts',
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
     * Scraping global $wp_scripts right before printing, applying scripts/styles dequeue
     */
    public function scripts_scraper_init() {
        if (!is_admin()) {
            global $post;
            $dequeued_scripts = unserialize(get_option('wp_dequeued_scripts_pageid_' . $post->ID));
            if ($dequeued_scripts && $dequeued_scripts != '') {
                foreach ($dequeued_scripts as $dequeued_script) {
                    wp_dequeue_script($dequeued_script);
                }
            }
            $dequeued_styles = unserialize(get_option('wp_dequeued_styles_pageid_' . $post->ID));
            if ($dequeued_styles || $dequeued_styles != '') {
                foreach ($dequeued_styles as $dequeued_style) {
                    wp_dequeue_style($dequeued_style);
                }
            }
            require_once plugin_dir_path(__FILE__).'class-frontend-spider.php';
            if(class_exists('FrontendSpider')) {
                $update = get_option('wp_queued_scripts_pageid_' . $post->ID);
                if (!$update) {
                    $frontend_spider = new FrontendSpider();
                    $package = $frontend_spider->prepare_transient_data();
                    update_option('wp_queued_scripts_pageid_' . $post->ID, $package['scripts']);
                    update_option('wp_queued_styles_pageid_' . $post->ID, $package['styles']);
                }
            }
        }
    }

    /**
     * @package WPScriptManager -> "clear_spiders_transients"
     * Delete spider transients action
     */
    public function clear_spiders_transients() {
        if (!wp_verify_nonce($_POST['nonce'], 'metabox_nonce')) {
            die('You should not be here dumbass! Be Gone!');
        }
        delete_option('wp_queued_scripts_pageid_' . $_POST['page_id']);
        delete_option('wp_queued_styles_pageid_' . $_POST['page_id']);
        delete_option('wp_dequeued_scripts_pageid_' . $_POST['page_id']);
        delete_option('wp_dequeued_styles_pageid_' . $_POST['page_id']);
        die();
    }

    /**
     * @package WPScriptManager -> "tables_generator"
     * Generating tables
     */
    public function tables_generator() {
        if (!wp_verify_nonce($_POST['nonce'], 'metabox_nonce')) {
            die('You should not be here dumbass! Be Gone!');
        }
        $scripts = unserialize(get_option('wp_queued_scripts_pageid_' . $_POST['page_id']));
        $dequeued_scripts = unserialize(get_option('wp_dequeued_scripts_pageid_' . $_POST['page_id']));
        $styles = unserialize(get_option('wp_queued_styles_pageid_' . $_POST['page_id']));
        $dequeued_styles = unserialize(get_option('wp_dequeued_styles_pageid_' . $_POST['page_id']));
        ob_start();
        ?>
        <div class="ui-scripts-block">
            <h2 class="table-title"> Scripts </h2>
            <table id="scripts-table" class="table-initable">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Actions </th>
                        <th> Status </th>
                    </tr>    
                </thead>
                <tbody>
                    <?php
                    if ($scripts && $scripts != '') {
                        foreach ($scripts as $script) { ?>
                            <tr>
                                <td class="names"> <?php echo $script; ?> </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-dismiss" data-id="<?php echo $script; ?>" data-handler="dequeue" data-type="scripts"></span> 
                                    <span class="dashicons dashicons-yes-alt" data-id="<?php echo $script; ?>" data-handler="enqueue" data-type="scripts"></span>
                                </td>
                                <td class="status"> Enqueued </td>
                            </tr>
                        <?php } 
                    } 
                    if ($dequeued_scripts && $dequeued_scripts != '') {
                        foreach ($dequeued_scripts as $dequeued_script) { ?>
                            <tr>
                                <td class="names"> <?php echo $dequeued_script; ?> </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-dismiss" data-id="<?php echo $dequeued_script; ?>" data-handler="dequeue" data-type="scripts"></span> 
                                    <span class="dashicons dashicons-yes-alt" data-id="<?php echo $dequeued_script; ?>" data-handler="enqueue" data-type="scripts"></span>
                                </td>
                                <td class="status"> Dequeued </td>
                            </tr>
                        <?php }
                    } ?>    
                </tbody>
            </table>
        </div>
        <div class="ui-styles-block">
            <h2 class="table-title"> Styles </h2>
            <table id="styles-table" class="table-initable">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Actions </th>
                        <th> Status </th>
                    </tr>    
                </thead>
                <tbody>
                    <?php
                    if ($styles && $styles != '') { 
                        foreach ($styles as $style) { ?>
                            <tr>
                                <td class="name"> <?php echo $style; ?> </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-dismiss" data-id="<?php echo $style; ?>" data-handler="dequeue" data-type="styles"></span> 
                                    <span class="dashicons dashicons-yes-alt" data-id="<?php echo $style; ?>" data-handler="enqueue" data-type="styles"></span>
                                </td>
                            <td class="status"> Enqueued </td> 
                            </tr>
                        <?php }
                    } 
                    if ($dequeued_styles && $dequeued_styles != '') {
                        foreach ($dequeued_styles as $dequeued_style) { ?>
                            <tr>
                                <td class="name"> <?php echo $dequeued_style; ?> </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-dismiss" data-id="<?php echo $dequeued_style; ?>" data-handler="dequeue" data-type="styles"></span> 
                                    <span class="dashicons dashicons-yes-alt" data-id="<?php echo $dequeued_style; ?>" data-handler="enqueue" data-type="styles"></span>
                                </td>
                            <td class="status"> Dequeued </td> 
                            </tr>
                        <?php }
                    } ?>    
                </tbody>
            </table>
        </div>
        <?php
        $html = ob_get_contents();
        ob_get_clean();
        echo $html;
        die();
    }

    /**
     * @package WPScriptManager -> "scripts_handler"
     * Handles enqueue/dequeue actions
     */
    public function scripts_handler() {
        if (!wp_verify_nonce($_POST['nonce'], 'metabox_nonce')) {
            die('You should not be here dumbass! Be Gone!');
        }
        switch ($_POST['handler']) {
            case 'dequeue':
                $dequeued_scripts = unserialize(get_option('wp_dequeued_' . $_POST['type'] . '_pageid_' . $_POST['page_id']));
                if (!$dequeued_scripts || $dequeued_scripts == '') {
                    $dequeued_scripts = array();
                }
                if (!in_array($_POST['script_id'], $dequeued_scripts)) {
                    array_push($dequeued_scripts, $_POST['script_id']);
                    update_option('wp_dequeued_' . $_POST['type'] . '_pageid_' . $_POST['page_id'], serialize($dequeued_scripts));
                    $scripts = unserialize(get_option('wp_queued_' . $_POST['type'] . '_pageid_' . $_POST['page_id']));
                    $key = array_search($_POST['script_id'], $scripts);
                    array_splice($scripts, $key, 1);
                    update_option( 'wp_queued_' . $_POST['type'] . '_pageid_' . $_POST['page_id'], serialize($scripts));
                }
                break;
            case 'enqueue':
                $dequeued_scripts = unserialize(get_option('wp_dequeued_' . $_POST['type'] . '_pageid_' . $_POST['page_id']));
                if (!$dequeued_scripts || $dequeued_scripts == '') {
                    break;
                } else {
                    $key = array_search($_POST['script_id'], $dequeued_scripts);
                    array_splice($dequeued_scripts, $key, 1);
                    update_option('wp_dequeued_' . $_POST['type'] . '_pageid_' . $_POST['page_id'], serialize($dequeued_scripts));
                    $scripts = unserialize(get_option('wp_queued_' . $_POST['type'] . '_pageid_' . $_POST['page_id']));
                    array_push($scripts, $_POST['script_id']);
                    update_option( 'wp_queued_' . $_POST['type'] . '_pageid_' . $_POST['page_id'], serialize($scripts));
                }
                break;
        }
        die();
    }

    /**
     * @package WPScriptManager -> "ajax_script_localizer"
     * Passing variables to JS
     */
    public function ajax_script_localizer() {
        global $post;
        wp_localize_script('metabox-scripts-boundle', 'metaBox', array( 
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'ajaxNonce' => wp_create_nonce('metabox_nonce'),
            'pageUrl'   => get_post_permalink()
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