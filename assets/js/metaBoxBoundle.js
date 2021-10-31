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
        $('#loader-id').show();
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
                    $('#ajax-log-resp').text('Something went wrong whit your request! Check the browser console for more informations!');
                    console.log(response);
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
        $('#loader-id').show();
        $.get(metaBox.pageUrl).done(function() {
            $('#ajax-log-resp').text('Done! Page lookup completed correctly!');
            $('#loader-id').hide();
            refreshTables();
        });
    });

    /**
     * @package metaBoxBoundle
     * Handles enqueue/dequeue event
     */
    function eventBinder() {
        $('td.actions span.dashicons').click(function(event) {
            event.preventDefault;
            $.ajax({
                type: "POST",
                dataType: "html",
                url: metaBox.ajaxUrl,
                data: {
                    action: "scripts_handler",
                    nonce: metaBox.ajaxNonce,
                    page_id: $('#dashboard-container-id').attr('data-pageid'),
                    handler: $(this).attr('data-handler')
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(err) {
                    $('#ui-refresh-target').html('<p>Something went wrong! Check console for more informations!</p>');
                    console.log(err);
                }
            });
        });
    }

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
                action: "tables_generator",
                nonce: metaBox.ajaxNonce,
                page_id: $('#dashboard-container-id').attr('data-pageid')
            },
            success: function(response) {
                $('#ui-refresh-target').html(response);
                eventBinder();
            },
            error: function(err) {
                $('#ui-refresh-target').html('<p>Something went wrong! Check console for more informations!</p>');
                console.log(err);
            }
        });
    }

    /**
     * @package metaBoxBoundle
     * Init
     */
    $(document).ready(function() {
        refreshTables();
    });

})(jQuery);
