<?php

/**
 * @package wp-script-manager
 * Dashboard page output
 */

if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

/**
 * @package wp-script-manager
 * Frontend scaper class
 */
class FrontendSpider {

    /**
     * @package FrontendSpider -> globals
     * Global variables
     */
    public $page_id;
    public $scripts = [];
    public $styles = [];

    /**
     * @package FrontendSpider -> "__construct"
     * Class constructor
     */
    public function __construct() {
        $this->page_id = get_queried_object_id();
        global $wp_scripts;
        foreach ($wp_scripts->queue as $script) {
            array_push($this->scripts, $script);
        }
        global $wp_styles;
        foreach ($wp_styles->queue as $style) {
            array_push($this->styles, $style);
        }
    }

    /**
     * @package FrontendSpider -> "prepare_scripts_for_database"
     * Serializing data for transient storage
     */
    public function prepare_transient_data() {
        $pack = array(
            'scripts' => serialize($this->scripts),
            'styles'  => serialize($this->styles)     
        );
        return $pack;
    }

}