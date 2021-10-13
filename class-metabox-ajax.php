<?php

/**
 * @package wp-script-manager
 * Metabox Ajax Handler
 */
if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

/**
 * @package wp-script-manager
 * Ajax actions class
 */
class MetaboxAjax {
    
    /**
     * @package WPScriptManager -> "__construct"
     * Class constructor
     */
    public function __construct() {
        add_action('wp_ajax_ultime_novita_home', array($this, 'clear_spiders_transients'));
        add_action('wp_ajax_nopriv_ultime_novita_home', array($this, 'clear_spiders_transients'));
        add_action('admin_enqueue_scripts', array($this, 'ajax_script_localizer'));
    }

    /**
     * @package WPScriptManager -> "__construct"
     * Delete spider transients action
     */
    public function clear_spiders_transients() {
        if (!wp_verify_nonce($_POST['nonce'], 'clear_spiders_transients_nonce')) {
            die('You should not be here dumbass! Be Gone!');
        }
        echo 'response';
        die();
    }

    /**
     * @package WPScriptManager -> "script_localizer"
     * Passing variables to JS
     */
    public function ajax_script_localizer() {
        wp_localize_script('metabox-scripts-boundle', 'metaBox', array( 
            'ajaxUrl'   => admin_url('admin-ajax.php'),
            'ajaxNonce' => wp_create_nonce('clear_spiders_transients_nonce')
        ));       
    }
}