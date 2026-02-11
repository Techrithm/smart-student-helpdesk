// assets/js/session-guard.js
// Prevents access to protected pages after logout
// Validates session on page load and redirects if invalid

(function () {
    'use strict';

    // IMMEDIATE: Hide page content until session is validated
    const pageBlocker = document.createElement('div');
    pageBlocker.id = 'session-validator-blocker';
    pageBlocker.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        z-index: 999999;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
    pageBlocker.innerHTML = '<div style="text-align: center;"><div style="font-size: 18px; color: #666;">Validating session...</div></div>';
    document.documentElement.appendChild(pageBlocker);

    // Add cache control meta tags to prevent page caching
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

    // Get expected role from script tag data attribute
    const scriptTag = document.currentScript;
    const expectedRole = scriptTag ? scriptTag.getAttribute('data-role') : null;

    // Redirect to appropriate login page based on role
    function redirectToLogin(role) {
        const loginPages = {
            'student': '../../frontend/student/login.html',
            'staff': '../../frontend/staff/login.html',
            'admin': '../../frontend/admin/login.html'
        };

        const loginUrl = loginPages[role] || '../../frontend/index.html';

        // Use replace to prevent back button from returning here
        window.location.replace(loginUrl);
    }

    // Validate session on page load
    async function validateSession() {
        try {
            const response = await fetch('../../backend/auth/check_session.php', {
                method: 'GET',
                cache: 'no-cache',
                credentials: 'same-origin'
            });

            const result = await response.json();

            // If not logged in, redirect to appropriate login page
            if (!result.logged_in) {
                redirectToLogin(expectedRole);
                return false;
            }

            // If role mismatch, redirect to correct login
            if (expectedRole && result.user.role !== expectedRole) {
                redirectToLogin(expectedRole);
                return false;
            }

            // Session is valid - remove blocker and show page
            if (pageBlocker && pageBlocker.parentNode) {
                pageBlocker.parentNode.removeChild(pageBlocker);
            }
            return true;

        } catch (error) {
            console.error('Session validation failed:', error);
            // On error, redirect to login to be safe
            redirectToLogin(expectedRole);
            return false;
        }
    }

    // Prevent navigation to cached pages via back button
    if (window.history && window.history.pushState) {
        // Prevent back button from showing cached version
        window.history.replaceState(null, null, window.location.href);

        // Listen for popstate (back/forward button)
        window.addEventListener('popstate', function (event) {
            // Immediately show blocker
            pageBlocker.style.display = 'flex';
            if (!pageBlocker.parentNode) {
                document.documentElement.appendChild(pageBlocker);
            }
            // Re-validate session when user presses back
            validateSession();
        });
    }

    // Run validation immediately on page load
    validateSession();
})();
