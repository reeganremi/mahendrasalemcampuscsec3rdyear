<?php
/**
 * Base auth bootstrap: secure session start + login helpers.
 * Include this at the top of every protected page (before any HTML output).
 */
if (session_status() === PHP_SESSION_NONE) {
    // Harden session cookie (works on http://localhost, Secure flag only on HTTPS).
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// Load helpers (functions.php expects a session).
$__func = __DIR__ . '/functions.php';
if (file_exists($__func)) {
    require_once $__func;
}

/** Redirect guests to login. */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? null;
        redirect(BASE_URL . '/login.php');
    }
    // Refresh current user name/role from DB in case admin deactivated the account.
    // (Light check done in student/admin guards; full check here for is_active.)
    if (isset($GLOBALS['conn']) && isset($_SESSION['user_id'])) {
        $stmt = $GLOBALS['conn']->prepare('SELECT role, is_active FROM users WHERE id = ? LIMIT 1');
        $uid = (int)$_SESSION['user_id'];
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$row || (int)$row['is_active'] === 0) {
            // Account removed or deactivated → force logout.
            session_unset();
            session_destroy();
            redirect(BASE_URL . '/login.php?deactivated=1');
        }
        // Keep role in sync.
        $_SESSION['user_role'] = $row['role'];
    }
}
