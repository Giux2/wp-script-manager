(function(){
    'use-strict';

    /**
     * @package metaBoxBoundle -> dataTable init
     * Setup dataTable on ".table-initable"
     */
    jQuery(document).ready(function($) {
        $('.table-initable').each(function() {
            $(this).dataTable({
                'autoWidth': true,
                'paging': true,  
                'lengthChange': false,
                'pageLength': 5
            });
        });
    });
    
})();