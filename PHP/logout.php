<?php
// logout.php throws away the session and sends the visitor back to login.

require_once __DIR__ . '/auth.php';

// Empty the session array first.
$_SESSION = [];

// Then delete the session cookie in the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    // An expiry in the past tells the browser to drop a cookie.
    setcookie(
        session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// session destroyed on server
session_destroy();

// makes the login page say "You have been signed out."
header('Location: login.php?out=1');
exit;