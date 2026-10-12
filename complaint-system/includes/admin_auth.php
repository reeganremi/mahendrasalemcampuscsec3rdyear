<?php
/**
 * Admin-only guard. Enforced on the server on EVERY admin page.
 * Hiding nav links alone is NOT sufficient — this is the real check.
 */
require_once __DIR__ . '/auth.php';

require_login();

if (!is_admin()) {
    http_response_code(403);
    exit('Forbidden: administrators only.');
}
