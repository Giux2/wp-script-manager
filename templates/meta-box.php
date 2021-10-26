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

    <div class="dashboard-container">
        <div class="ui-container">
            
            <!-- Row -->
            <div class="ui-row">

                <!-- Scripts Block -->
                <div class="ui-scripts-block">
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
                            foreach ($scripts as $script) { ?>
                                <tr>
                                    <td class="names"> <?php echo $script; ?> </td>
                                    <td class="actions"> 
                                        <span class="dashicons dashicons-dismiss"></span> 
                                        <span class="dashicons dashicons-yes-alt"></span>
                                    </td>
                                    <td class="status"> Enqueued </td>
                                </tr>
                            <?php } ?>    
                        </tbody>
                    </table>
                </div>

                <!-- Styles Block -->
                <div class="ui-styles-block">
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
                            foreach ($styles as $style) { ?>
                                <tr>
                                    <td class="name"> <?php echo $style; ?> </td>
                                    <td class="actions"> 
                                        <span class="dashicons dashicons-dismiss"></span> 
                                        <span class="dashicons dashicons-yes-alt"></span>
                                    </td>
                                   <td class="status"> Enqueued </td> 
                                </tr>
                            <?php } ?>    
                        </tbody>
                    </table>
                </div>
            
            <!-- Row -->
            </div> 

            <!-- Global Controls -->
            <div class="global-controls">
                <button type="button" id="clear-transients" data-pageid="<?php echo $post->ID; ?>"> Clear Transients </button>
                <p id="ajax-log-resp"></p>
            </div>

        </div>    
    </div>

<?php 
$html = ob_get_contents();
ob_get_clean();
echo $html;