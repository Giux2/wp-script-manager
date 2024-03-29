<?php

/**
 * @package wp-script-manager
 * Metabox output
 */
if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

global $post;
ob_start();
?>

    <div class="dashboard-container" id="dashboard-container-id" data-pageid="<?php echo $post->ID; ?>">
        <div class="ui-container">
            <div class="ui-row" id="ui-refresh-target"></div> 
            <div class="global-controls">
                <button type="button" id="scrape-page"> Page Lookup </button>
                <button type="button" id="clear-transients"> Clear Transients </button>
                <div class="response-grid">
                    <div id="ajax-log-resp"></div>
                    <div class="loader" id="loader-id" style="display: none;"></div>    
                </div>
            </div>
        </div>    
    </div>

<?php 
$html = ob_get_contents();
ob_get_clean();
echo $html;