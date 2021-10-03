<?php

/**
 * @package wp-script-manager
 * Dashboard page output
 */

if (!function_exists('add_action')) {
    die('Dang! You should not be here dumbass! Be gone!');
}

ob_start();
?>

    <div class="dashboard-container">
        <div class="ui-container">
            <h1> WPScriptManager </h1>
        </div>    
    </div>

<?php 
$html = ob_get_contents();
ob_get_clean();
echo $html;