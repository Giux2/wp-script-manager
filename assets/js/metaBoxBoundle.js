/**
 * @package metaBoxBoundle -> Scripts
 * Metabox Scripts
 */
jQuery.noConflict();
(function($) {
    'use-strict';

    /**
     * @package metaBoxBoundle
     * Global variables
     */
    const logs = $('#ajax-log-resp');
    const loader = $('#loader-id');
    const noPageIdProvided = 'Error! No page id was provided!';
    const transientsAltreadyClear = 'Transients already clear!';
    const transientsDeleted = 'Transients deleted!';
    const casualResponse = 'Something went wrong whit your request! Refresh the page and try again..';
    const scrapeOk = 'Done! Page lookup completed correctly!';
    
    /**
     * @package metaBoxBoundle
     * Clear transient request
     */
    $('#clear-transients').click(function(event) {
        event.preventDefault;
        loader.fadeIn(400);
        $.ajax({
            type: "post",
            dataType: "html",
            url: metaBox.ajaxUrl,
            data: {
                action: "clear_spiders_transients",
                nonce: metaBox.ajaxNonce,
                page_id: $(this).attr('data-pageid')
            },
            success: function(response) {
                if (typeof response === 'string') {
                    switch (response) {
                        case 'no-page-id-provided':
                            logs.text(noPageIdProvided)
                            .show()
                            .delay(2500)
                            .fadeOut(400);
                            break;
                        case 'transient-already-clear':
                            logs.text(transientsAltreadyClear)
                            .show()
                            .delay(2500)
                            .fadeOut(400);
                            break;
                        default: 
                            logs.text(transientsDeleted)
                            .show()
                            .delay(2500)
                            .fadeOut(400);
                    }
                } else {
                   logs.text(casualResponse);
                }
                loader.fadeOut(400);
            },
            error: function(err) {
                console.log(err);
                logs.text(casualResponse);
            }
        });
    });

    /**
     * @package metaBoxBoundle
     * Fires frontend spider
     */
    $('#scrape-page').click(function(event) {
        event.preventDefault;
        loader.fadeIn(400);
        $.get(metaBox.pageUrl).done(function() {
            logs.text(scrapeOk)
                .show()
                .delay(2500)
               .fadeOut(400);
            loader.fadeOut(400);
        });
    });

})(jQuery);
