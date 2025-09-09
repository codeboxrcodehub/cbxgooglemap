(function ($) {
    'use strict';

    /*function cbxgogolemap_copyStringToClipboard (str) {
        // Create new element
        var el = document.createElement('textarea');
        // Set value (string to be copied)
        el.value = str;
        // Set non-editable to avoid focus and move outside of view
        el.setAttribute('readonly', '');
        el.style = {position: 'absolute', left: '-9999px'};
        document.body.appendChild(el);
        // Select text inside element
        el.select();
        // Copy text to clipboard
        document.execCommand('copy');
        // Remove temporary element
        document.body.removeChild(el);
    }//end function cbxgogolemap_copyStringToClipboard*/
    
    $(document).ready(function ($) {

        //click to copy shortcode
        /*$('.cbxballon_ctp').on('click', function (e) {
            e.preventDefault();

            var $this = $(this);
            cbxgogolemap_copyStringToClipboard($this.prev('.cbxshortcode').text());
            $this.attr('aria-label', cbxgooglemap_listing.copycmds.copied_tip);
            window.setTimeout(function () {
                $this.attr('aria-label', cbxgooglemap_listing.copycmds.copy_tip);
            }, 1000);
        });*/

        $('.wrap').addClass('cbx-chota cbxgooglemap-page-wrapper cbxgooglemap-addedit-wrapper');
        $('#search-submit').addClass('button primary');
        $('#post-query-submit').addClass('button primary');
        $('.button.action').addClass('button primary');

        $('.page-title-action').addClass('button primary');
        $('#save-post').addClass('button primary');
        $('#publish').addClass('button primary');

        $('#screen-meta').addClass('cbx-chota cbxgooglemap-page-wrapper cbxgooglemap-googlemaps-wrapper');
        $('#screen-options-apply').addClass('primary');
        $('#post-search-input').attr('placeholder', cbxgooglemap_listing.placeholder.search);

        $('.button.button-primary.save').addClass('primary');
        $('.button.cancel').addClass('outline primary');

        //edit
        $('.preview.button').addClass('button outline secondary');
        $('.button.tagadd').addClass('button outline secondary');
        $('.edit-slug.button.button-small').addClass('button outline secondary small');
        $('#remove-post-thumbnail').addClass('button outline error');

    });
})(jQuery);
