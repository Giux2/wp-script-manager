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
                    logs.text(responseText)
                        .show()
                        .delay(2500)
                        .fadeOut(400);
                } else {
                   logs.text('Something went wrong whit your request! Please refresh the page and try again..');
                }
                loader.fadeOut(400);
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
        loader.fadeIn(400);
        $.get(metaBox.pageUrl).done(function() {
            logs.text('Done! Page lookup completed correctly!')
                .show()
                .delay(2500)
               .fadeOut(400);
            loader.fadeOut(400);
        });
    });

})(jQuery);
