<?php
// backend/config/session.php
// Session configuration to keep users logged in

// Set session lifetime to 30 days (in seconds)
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 days
ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 days

// Make session cookies persistent across browser restarts
ini_set('session.cookie_httponly', 1); // Security: prevent JavaScript access
ini_set('session.use_strict_mode', 1); // Security: reject uninitialized session IDs

// Set session cookie path
ini_set('session.cookie_path', '/');

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Extend session lifetime on each request
if (isset($_SESSION['user_id'])) {
    // Update session cookie expiration time on each request
    setcookie(
        session_name(),
        session_id(),
        time() + (30 * 24 * 60 * 60), // 30 days from now
        '/',
        '',
        false, // Set to true if using HTTPS
        true   // httponly
    );
}
?>
