document.addEventListener('DOMContentLoaded', function() {
    // Elements
    var textarea = document.getElementById('kdm_rss_xml_fd_feeds');
    var lineNumbers = document.getElementById('line-numbers');
    var copyButtons = document.querySelectorAll('.copy-button');

    // Update line numbers
    function updateLineNumbers() {
        var lines = textarea.value.split('\n');
        lineNumbers.textContent = lines.map((line, index) => index + 1).join('\n');
    }
    updateLineNumbers(); // Initial update
    textarea.addEventListener('input', updateLineNumbers); // Update on input
    textarea.addEventListener('scroll', function() {
        lineNumbers.scrollTop = textarea.scrollTop; // Sync scroll position
    });

    // Copy functionality
    copyButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            var id = event.target.getAttribute('data-id');
            var shortcodeInput = document.querySelector('.shortcode-display[data-id="' + id + '"]');
            
            if (navigator.clipboard && shortcodeInput) {
                navigator.clipboard.writeText(shortcodeInput.value).then(function() {
                    alert('Shortcode copied: ' + shortcodeInput.value  + '. Paste it where you would like to feed to display.');
                }, function(err) {
                    console.error('Could not copy text: ', err);
                });
            } else {
                console.error('Clipboard API not available.');
            }
        });
    });
});

    document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('my-textarea');
    var lineNumbers = document.getElementById('line-numbers');

    function updateLineNumbers() {
        var lines = textarea.value.split('\n').length;
        var lineNumberHtml = '';
        for (var i = 1; i <= lines; i++) {
            lineNumberHtml += i + '\n';
        }
        lineNumbers.textContent = lineNumberHtml;
    }

    // Initial update
    updateLineNumbers();

    // Update line numbers on input
    textarea.addEventListener('input', updateLineNumbers);

    // Sync scroll
    textarea.addEventListener('scroll', function() {
        lineNumbers.scrollTop = textarea.scrollTop;
    });
});