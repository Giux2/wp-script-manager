/**
 * @package metaBoxBoundle -> dataTable init
 * Metabox Javascript Scripts
 */
jQuery.noConflict();
(function($) {
    'use-strict';
    
    /**
     * @package metaBoxBoundle
     * Datatable init
     */
    $(document).ready(function() {
        $('.table-initable').each(function() {
            $(this).dataTable({
                'autoWidth': true,
                'paging': true,  
                'lengthChange': false,
                'pageLength': 5,
                'searching': false
            });
        });

        /**
         * @package metaBoxBoundle
         * Clear transient request
         */
        $('#clear-transients').click(function(event) {
            event.preventDefault;
            $.ajax({
                type: 'POST',
                dataType: 'HTML',
                url: metaBox.ajaxUrl,
                data: {
                    action: 'clear_spiders_transients', 
                    pageId: $(this).attr('data-pageid'),
                    nonce: metaBox.ajaxNonce
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        });
    });
})(jQuery);
