<?php
/**
 * Secure admin seeder — LOCAL / CLI ONLY.
 *
 * Why this exists: students must NEVER be able to self-register as admin.
 * There is no public route that assigns role='admin'.
 *
 * Usage (from complaint-system/ folder, with MySQL running in XAMPP):
 *   C:\xampp\php\php.exe admin_seed.php "College Admin" admin2@college.edu "StrongPass123"
 *
 * This script refuses to run over HTTP to avoid exposing admin creation.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Forbidden: run this script from the command line only.\n");
}

if ($argc < 4) {
    echo "Usage: php admin_seed.php \"Full Name\" email@college.edu \"PasswordMin8Chars\"\n";
    exit(1);
}

[, $name, $email, $password] = $argv;
$name = trim($name);
$email = trim($email);

if (mb_strlen($name) < 2) { echo "Error: name too short.\n"; exit(1); }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { echo "Error: invalid email.\n"; exit(1); }
if (strlen($password) < 8) { echo "Error: password must be at least 8 characters.\n"; exit(1); }

require __DIR__ . '/config/database.php'; // provides $conn

// Prevent duplicates.
$stmt = $conn->prepare('SELECT id, role FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

$hash = password_hash($password, PASSWORD_DEFAULT);

if ($existing) {
    $stmt = $conn->prepare("UPDATE users SET name = ?, password = ?, role = 'admin', is_active = 1 WHERE id = ?");
    $stmt->bind_param('ssi', $name, $hash, $existing['id']);
    $stmt->execute();
    $stmt->close();
    echo "OK: existing user #{$existing['id']} promoted to admin.\n";
} else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param('sss', $name, $email, $hash);
    $stmt->execute();
    echo 'OK: admin created with id ' . $stmt->insert_id . ".\n";
    $stmt->close();
}
echo "Now DELETE or restrict this file before any public deployment.\n";
