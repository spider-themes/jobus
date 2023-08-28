;(function($) {
    'use strict';

    $(document).ready(function() {


        /**
         * Copy URL to clipboard
         * @param text
         */
        function copyToClipboard(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
        }

        // Attach click event to element with class "copy-url"
        var copyButton = document.querySelectorAll('.copy-url');
        if ( copyButton.length > 0 ) {
            copyButton.addEventListener('click', function (event) {
                event.preventDefault();

                // Get the current page's URL
                const currentPageURL = window.location.href;

                // Copy the URL to the clipboard
                copyToClipboard(currentPageURL);

            });
        }// end copyButton click event


        // Nice Select for search form
        function niceSelect() {
            let niceSelect = $('.nice-select');
            if(niceSelect.length > 0  ) {
                niceSelect.niceSelect();
            }
        }

        niceSelect();// end Nice Select

        /**
         * Search Keywords
         */
        $('.keywords_search_form ul li a').on('click', function (event) {
            event.preventDefault();
            var content = $(this).text();

            console.log(content);

            $('#searchInput').val(content).focus();
            fetchResults();
        });
       
    });

})(jQuery);