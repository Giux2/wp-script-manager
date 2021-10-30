<?php

/**
 * @package wp-script-manager
 * Metabox output
 */
if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

global $post;
$scripts = unserialize(get_transient('wp_queued_scripts_pageid_' . $post->ID));
$styles = unserialize(get_transient('wp_queued_styles_pageid_' . $post->ID));
ob_start();
?>

    <div class="dashboard-container" id="dashboard-container-id" data-pageid="<?php echo $post->ID; ?>">
        <div class="ui-container">
            <div class="ui-row" id="ui-refresh-target"></div> 
            <div class="global-controls">
                <button type="button" id="scrape-page"> Page Lookup </button>
                <button type="button" id="clear-transients"> Clear Transients </button>
                <div class="response-grid">
                    <div class="loader" id="loader-id" style="display: none;"></div>    
                    <div id="ajax-log-resp"></div>
                </div>
            </div>
        </div>    
    </div>

<?php 
$html = ob_get_contents();
ob_get_clean();
echo $html;