(function( $ ) {
    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */

    const select2 = $('.select2');

    // Default
    if (select2.length) {
        select2.each(function () {
            var $this = $(this);
            $this.wrap('<div class="position-relative"></div>').select2({
                placeholder: 'Select value',
                dropdownParent: $this.parent()
            });
        });
    }


    const tagifyBasicEl = document.querySelector('#apvc_exclude_ips');
    if (tagifyBasicEl) {
        const TagifyBasic = new Tagify(tagifyBasicEl);
    }

    const checkbox = document.getElementById('flexSwitchCheckDefault');
    if (checkbox) {
        checkbox.indeterminate = true;
    }


})( jQuery );
