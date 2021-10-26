/**
 * @package metaBoxBoundle -> Scripts
 * Metabox Scripts
 */
jQuery.noConflict();
(function($) {
    'use-strict';
    
    /**
     * @package metaBoxBoundle
     * Clear transient request
     */
    $('#clear-transients').click(function(event) {
        event.preventDefault;
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
                console.log(response);
                $('#ajax-log-resp').text('Success! Transients cleared!');
            },
            error: function(err) {
                console.log(err);
                $('#ajax-log-resp').text('Error! Something went wrong whit your request!');
            }
        });

    });
})(jQuery);
