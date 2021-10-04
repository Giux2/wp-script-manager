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
                        foreach ($scripts as $script) {
                            echo '<tr>
                                <td class="names"> '. $script .' </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-thumbs-up"></span>
                                    <span class="dashicons dashicons-thumbs-down"></span>
                                </td>
                                <td class="status"> 
                                    <span class="dashicons dashicons-yes-alt"></span>
                                </td>
                            </tr>';
                        }    
                        ?>
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
                        foreach ($styles as $style) {
                            echo '<tr>
                                <td class="name"> '. $style .' </td>
                                <td class="actions"> 
                                    <span class="dashicons dashicons-edit"></span> 
                                    <span class="dashicons dashicons-update"></span>
                                </td>
                               <td class="status"> 
                                    <span class="dashicons dashicons-yes-alt"></span>
                               </td> 
                            </tr>';
                        }    
                        ?>
                    </tbody>
                </table>
            </div>

        </div>    
    </div>

<?php 
$html = ob_get_contents();
ob_get_clean();
echo $html;