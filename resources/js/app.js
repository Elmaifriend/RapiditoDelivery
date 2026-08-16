document.addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        const activeElement = document.activeElement;
        
        if (activeElement && activeElement.tagName === 'INPUT') {
            const excludedTypes = ['submit', 'button', 'reset', 'file', 'checkbox', 'radio', 'hidden'];
            
            if (!excludedTypes.includes(activeElement.type)) {
                const focusableSelectors = 'input:not([type="hidden"]):not([disabled]):not([readonly]), textarea:not([disabled]):not([readonly]), select:not([disabled]):not([readonly])';
                const focusableElements = Array.from(document.querySelectorAll(focusableSelectors));
                
                const currentIndex = focusableElements.indexOf(activeElement);
                
                if (currentIndex > -1 && currentIndex < focusableElements.length - 1) {
                    event.preventDefault();
                    focusableElements[currentIndex + 1].focus();
                }
            }
        }
    }
});