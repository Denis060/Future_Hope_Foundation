/**
 * Script to remove unwanted mobile date widget
 */
(function() {
    // Function to run when DOM is fully loaded
    function removeUnwantedElements() {
        // Look for elements matching the date widget pattern at the top of the page
        const possibleDateWidgets = [
            // Match by text content
            document.evaluate("//div[contains(text(), 'Sep') and contains(text(), '2025')]", 
                document, null, XPathResult.ANY_TYPE, null),
            
            // Match by structure - blue bar with calendar icon
            document.querySelectorAll('div.bg-primary i.fa-calendar-alt, i.fa-calendar'),
            
            // Match any div containing the date pattern at top of page
            document.querySelectorAll('body > div:first-child:not(.top-bar):not(.header)')
        ];

        // Process XPath result
        let node = possibleDateWidgets[0].iterateNext();
        while(node) {
            let parent = node;
            // Go up to find a suitable container to remove
            while (parent && parent.nodeName !== 'BODY' && 
                   (!parent.classList || !parent.classList.contains('container-fluid'))) {
                if (parent.parentElement) {
                    parent = parent.parentElement;
                } else {
                    break;
                }
            }
            
            if (parent && parent !== document.body) {
                parent.style.display = 'none';
            }
            node = possibleDateWidgets[0].iterateNext();
        }

        // Remove elements found with querySelector
        possibleDateWidgets[1].forEach(el => {
            let parent = el;
            while (parent && parent.nodeName !== 'BODY' && 
                  (!parent.classList || !parent.classList.contains('container-fluid'))) {
                parent = parent.parentElement;
                if (!parent) break;
            }
            
            if (parent && parent !== document.body) {
                parent.style.display = 'none';
            }
        });

        // Remove first child of body if it matches our criteria
        possibleDateWidgets[2].forEach(el => {
            // Check if this element contains a date-like string or calendar icon
            const text = el.innerText || '';
            const hasIcon = el.querySelector('i.fa-calendar, i.fa-calendar-alt');
            
            if (text.match(/\d{1,2}.*\d{4}.*\d{1,2}:\d{2}/) || hasIcon) {
                el.style.display = 'none';
            }
        });
    }

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', removeUnwantedElements);
    } else {
        removeUnwantedElements();
    }

    // Also run after window loads (for dynamic elements)
    window.addEventListener('load', removeUnwantedElements);
})();
