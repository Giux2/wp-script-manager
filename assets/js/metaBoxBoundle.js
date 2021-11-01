/**
 * @package metaBoxBoundle -> Scripts
 * Metabox Scripts
 */
jQuery.noConflict();
(function($) {
    'use-strict';
    
    /**
     * @package metaBoxBoundle
     * Fires transients clearing action
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
            success: function() {
                $('#ajax-log-resp').text('Done! Transients deleted!');
                $('#loader-id').hide();
                refreshTables();
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
            $('#ajax-log-resp').text('Done! Lookup completed!');
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
            $('#ui-refresh-target').attr('style', 'opacity: .2');
            $.ajax({
                type: "POST",
                dataType: "html",
                url: metaBox.ajaxUrl,
                data: {
                    action: "scripts_handler",
                    nonce: metaBox.ajaxNonce,
                    page_id: $('#dashboard-container-id').attr('data-pageid'),
                    handler: $(this).attr('data-handler'),
                    script_id: $(this).attr('data-id'),
                    type: $(this).attr('data-type')
                },
                success: function() {
                    refreshTables();
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
     * Generate/refresh table
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
                $('#ui-refresh-target').attr('style', 'opacity: 1');
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
     * Table Init
     */
    $(document).ready(function() {
        refreshTables();
    });

})(jQuery);
