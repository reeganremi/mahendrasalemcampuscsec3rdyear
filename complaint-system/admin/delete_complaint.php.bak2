<?php
// Admin: soft-delete (archive) a complaint. POST + CSRF + confirm.
// Row stays in DB with is_deleted=1 so history/audits are preserved.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}
if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    set_flash('Security check failed. Please try again.', 'error');
    redirect(BASE_URL . '/admin/complaints.php');
}

$id = (int)($_POST['complaint_id'] ?? 0);
if ($id <= 0) {
    set_flash('Invalid complaint reference.', 'error');
    redirect(BASE_URL . '/admin/complaints.php');
}

$stmt = $conn->prepare('UPDATE complaints SET is_deleted = 1, deleted_at = NOW() WHERE id = ? AND is_deleted = 0');
$stmt->bind_param('i', $id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected > 0) {
    // Audit the archival as a history comment (status unchanged).
    try {
        $adminId = (int)$_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT status FROM complaints WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $st = $stmt->get_result()->fetch_assoc()['status'] ?? 'Pending';
        $stmt->close();
        $note = 'Archived by admin (soft-delete).';
        $stmt = $conn->prepare('INSERT INTO complaint_history (complaint_id, admin_id, old_status, new_status, comment) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisss', $id, $adminId, $st, $st, $note);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $t) { error_log('archive history failed: ' . $t->getMessage()); }
    set_flash('Complaint ' . complaint_ref($id) . ' archived (soft-deleted).', 'success');
} else {
    set_flash('Complaint not found or already archived.', 'error');
}

redirect(BASE_URL . '/admin/complaints.php');
