// assets/js/login-guard.js
// Prevent login pages from being accessed via back button after successful login
// Also clears forward history to prevent getting stuck

(function () {
    'use strict';

    // Add cache control meta tags to prevent caching
    const meta1 = document.createElement('meta');
    meta1.httpEquiv = 'Cache-Control';
    meta1.content = 'no-cache, no-store, must-revalidate';

    const meta2 = document.createElement('meta');
    meta2.httpEquiv = 'Pragma';
    meta2.content = 'no-cache';

    const meta3 = document.createElement('meta');
    meta3.httpEquiv = 'Expires';
    meta3.content = '0';

    document.head.appendChild(meta1);
    document.head.appendChild(meta2);
    document.head.appendChild(meta3);

    // Replace history entry to prevent back button loops
    // This prevents user from getting stuck when pressing back repeatedly
    if (window.history && window.history.pushState) {
        // Replace the current history state
        window.history.replaceState(null, null, window.location.href);

        // Listen for popstate (back/forward button)
        window.addEventListener('popstate', function (event) {
            // Replace state again to prevent navigation
            window.history.pushState(null, null, window.location.href);
        });

        // Push a new state to prevent going back to protected pages
        window.history.pushState(null, null, window.location.href);
    }
})();
