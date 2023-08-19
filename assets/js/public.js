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
        const copyButton = document.querySelector('.copy-url');
        copyButton.addEventListener('click', function(event) {
            event.preventDefault();

            // Get the current page's URL
            const currentPageURL = window.location.href;

            // Copy the URL to the clipboard
            copyToClipboard(currentPageURL);

        }); // end copyButton click event
       
    });

})(jQuery);