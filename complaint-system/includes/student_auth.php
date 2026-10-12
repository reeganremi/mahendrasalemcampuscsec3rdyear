<?php
/**
 * Student-only guard. Students can only see their own complaints.
 * Admins are redirected to the admin dashboard.
 */
require_once __DIR__ . '/auth.php';

require_login();

if (!is_student()) {
    // Admins should use the admin area.
    redirect(BASE_URL . '/admin/dashboard.php');
}
