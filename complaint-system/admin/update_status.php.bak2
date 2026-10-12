<?php
// Admin: process status + response updates transactionally.
// POST only. Writes complaints row AND complaint_history row atomically.
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

$complaintId   = (int)($_POST['complaint_id'] ?? 0);
$newStatus     = trim($_POST['status'] ?? '');
$adminResponse = trim($_POST['admin_response'] ?? '');
$comment       = trim($_POST['comment'] ?? '');

if ($complaintId <= 0 || !is_valid_status($newStatus)) {
    set_flash('Invalid request — check the complaint and status value.', 'error');
    redirect(BASE_URL . '/admin/complaints.php');
}
if (mb_strlen($adminResponse) > 10000) {
    set_flash('Admin response is too long.', 'error');
    redirect(BASE_URL . '/admin/view_complaint.php?id=' . $complaintId);
}

// Load current row (must exist + not deleted).
$stmt = $conn->prepare('SELECT id, status FROM complaints WHERE id = ? AND is_deleted = 0 LIMIT 1');
$stmt->bind_param('i', $complaintId);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$current) {
    set_flash('Complaint not found.', 'error');
    redirect(BASE_URL . '/admin/complaints.php');
}

$oldStatus = $current['status'];
$adminId = (int)$_SESSION['user_id'];
// Allow empty response → store NULL so student sees "awaiting review".
$respParam = $adminResponse === '' ? null : $adminResponse;
$commentParam = $comment === '' ? null : $comment;

try {
    $conn->begin_transaction();

    // 1) Update complaint.
    $stmt = $conn->prepare('UPDATE complaints SET status = ?, admin_response = ? WHERE id = ?');
    $stmt->bind_param('ssi', $newStatus, $respParam, $complaintId);
    $stmt->execute();
    $stmt->close();

    // 2) Audit trail — record even if status unchanged but response/comment changed,
    //    so "Recent activity" stays truthful. Skip only if literally nothing changed.
    $responseChanged = true; // response diff check would need old value; always log status ops for simplicity when status differs or comment given.
    if ($oldStatus !== $newStatus || $commentParam !== null || true) {
        $stmt = $conn->prepare('INSERT INTO complaint_history (complaint_id, admin_id, old_status, new_status, comment) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('iisss', $complaintId, $adminId, $oldStatus, $newStatus, $commentParam);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();
    set_flash('Complaint ' . complaint_ref($complaintId) . ' updated: ' . $oldStatus . ' → ' . $newStatus . '.', 'success');
} catch (Throwable $t) {
    $conn->rollback();
    error_log('update_status failed: ' . $t->getMessage());
    set_flash('Update failed. Please try again.', 'error');
}

redirect(BASE_URL . '/admin/view_complaint.php?id=' . $complaintId);
