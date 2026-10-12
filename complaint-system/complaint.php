<?php
// Backwards-compatible alias: /complaint.php?id=… → /complaint_details.php?id=…
if (!defined('BASE_URL')) define('BASE_URL', '');
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
redirect(BASE_URL . '/complaint_details.php' . ($id > 0 ? '?id=' . $id : ''));
