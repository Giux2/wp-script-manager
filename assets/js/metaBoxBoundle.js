/**
 * @package metaBoxBoundle -> Scripts
 * Metabox Scripts
 */
jQuery.noConflict();
(function($) {
    'use-strict';
    
    /**
     * @package metaBoxBoundle
     * Clears transients
     */
    $('#clear-transients').click(function(event) {
        event.preventDefault;
        $('#loader-id').fadeIn(400);
        $.ajax({
            type: "POST",
            dataType: "html",
            url: metaBox.ajaxUrl,
            data: {
                action: "clear_spiders_transients",
                nonce: metaBox.ajaxNonce,
                page_id: $('#dashboard-container-id').attr('data-pageid')
            },
            success: function(response) {
                if (typeof response === 'string') {
                    let responseText = '';
                    switch (response) {
                        case 'no-page-id-provided':
                            responseText = 'Error! No page id was provided!';
                            break;
                        case 'transient-already-clear':
                            responseText = 'Transients already clear!';
                            break;
                        default: 
                        responseText = 'Transients deleted!';
                    }
                    $('#ajax-log-resp').text(responseText);
                    refreshTables();
                } else {
                    $('#ajax-log-resp').text('Something went wrong whit your request! Please refresh the page and try again..');
                }
                $('#loader-id').hide();
            },
            error: function(err) {
                console.log(err);
            }
        });
    });

    /**
     * @package metaBoxBoundle
     * Fires frontend spider
     */
    $('#scrape-page').click(function(event) {
        event.preventDefault;
        $('#loader-id').fadeIn(400);
        $.get(metaBox.pageUrl).done(function() {
            $('#ajax-log-resp').text('Done! Page lookup completed correctly!');
            $('#loader-id').hide();
            refreshTables();
        });
    });

    /**
     * @package metaBoxBoundle
     * Fires frontend spider
     */
    function refreshTables() {
        $.ajax({
            type: "POST",
            dataType: "html",
            url: metaBox.ajaxUrl,
            data: {
                action: "refresh_tables",
                nonce: metaBox.ajaxNonce,
                page_id: $('#dashboard-container-id').attr('data-pageid')
            },
            success: function(response) {
                $('#ui-refresh-target').html(response);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    /**
     * @package metaBoxBoundle
     * Init
     */
    $(document).ready(function(){
        refreshTables();
    });

})(jQuery);
