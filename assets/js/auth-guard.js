// assets/js/auth-guard.js
// Client-side session validation to prevent cached page access after logout

(function() {
    'use strict';
    
    // Prevent page caching
    if (window.performance && window.performance.navigation.type === 2) {
        // Page accessed via back/forward button
        window.location.reload();
    }
    
    // Check session on page load
    async function validateSession() {
        try {
            const response = await fetch('/smart-student-helpdesk/backend/auth/check_session.php', {
                method: 'GET',
                cache: 'no-store',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            });
            
            const result = await response.json();
            
            if (!result.logged_in) {
                // No active session - redirect to login
                const currentPath = window.location.pathname;
                let loginPage = '/smart-student-helpdesk/frontend/student/login.html';
                
                if (currentPath.includes('/admin/')) {
                    loginPage = '/smart-student-helpdesk/frontend/admin/login.html';
                } else if (currentPath.includes('/staff/')) {
                    loginPage = '/smart-student-helpdesk/frontend/staff/login.html';
                }
                
                window.location.replace(loginPage);
            }
        } catch (error) {
            console.error('Session validation error:', error);
        }
    }
    
    // Run validation immediately
    validateSession();
    
    // Add cache control meta tags dynamically
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
    
    // Prevent page from being cached
    window.addEventListener('beforeunload', function() {
        // This ensures the page is not cached
    });
})();
