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
     * @package FrontendSpider -> "__construct"
     * Class constructor
     */
    public function __construct() {
        add_action('wp_print_scripts', array($this, 'get_enqueued_scripts'));
    }

    /**
     * @package FrontendSpider -> "get_enqueued_scripts"
     * Scraping global $wp_scripts right before printing
     */
    public function get_enqueued_scripts() {
        if (!get_transient('wp_scrape_update_pageid_' . $page_id)) {
            global $wp_scripts;
            global $wp_styles;
            $scripts = [];
            foreach ($wp_scripts->queue as $script) {
                array_push($scripts, $script);
            }
            $styles = [];
            foreach ($wp_styles->queue as $style) {
                array_push($styles, $style);
            }
            $page_id = get_queried_object_id();
            set_transient('wp_scraped_scripts_pageid_' . $page_id, $scripts);
            set_transient('wp_scraped_styles_pageid_' . $page_id, $styles);
            set_transient('wp_scrape_update_pageid_' . $page_id, 'waiting for updates..', 604800);
        }
    }

}