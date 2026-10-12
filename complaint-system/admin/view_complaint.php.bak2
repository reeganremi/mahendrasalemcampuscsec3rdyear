<?php
// Admin: view a single complaint + student info + history + update form.
// The actual write happens in update_status.php (POST, transactional).
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { set_flash('Invalid complaint reference.', 'error'); redirect(BASE_URL . '/admin/complaints.php'); }

$stmt = $conn->prepare("SELECT c.*, u.name AS student_name, u.email AS student_email, u.created_at AS student_since
                        FROM complaints c JOIN users u ON u.id = c.user_id
                        WHERE c.id = ? AND c.is_deleted = 0 LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) { set_flash('Complaint not found (may have been archived).', 'error'); redirect(BASE_URL . '/admin/complaints.php'); }

$stmt = $conn->prepare("SELECT h.old_status, h.new_status, h.comment, h.created_at, u.name AS admin_name
                        FROM complaint_history h LEFT JOIN users u ON u.id = h.admin_id
                        WHERE h.complaint_id = ? ORDER BY h.created_at DESC");
$stmt->bind_param('i', $id);
$stmt->execute();
$history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = complaint_ref($c['id']);
$page_subtitle = $c['subject'];
$active = 'admin-complaints';
require_once __DIR__ . '/../includes/header.php';
?>

<a class="back-link" href="<?= BASE_URL ?>/admin/complaints.php"><i class="fa-solid fa-arrow-left"></i> Back to complaints</a>

<div class="detail-grid">
  <div class="main-stack">
    <div class="card panel">
      <div class="detail-head">
        <span class="badge <?= e(status_badge_class($c['status'])) ?> badge-lg"><?= e($c['status']) ?></span>
        <span class="cat-pill"><?= e($c['category']) ?></span>
        <span class="mono muted"><?= e(complaint_ref($c['id'])) ?></span>
      </div>
      <h2 class="detail-subject"><?= e($c['subject']) ?></h2>
      <div class="meta-row">
        <span><i class="fa-regular fa-calendar"></i> <?= e(date('M j, Y · h:i A', strtotime($c['created_at']))) ?></span>
        <span><i class="fa-solid fa-rotate"></i> Updated <?= e(time_ago($c['updated_at'])) ?></span>
        <?php if (!empty($c['location'])): ?><span><i class="fa-solid fa-location-dot"></i> <?= e($c['location']) ?></span><?php endif; ?>
      </div>
      <h3>Description</h3>
      <p class="desc"><?= nl2br(e($c['description'])) ?></p>
    </div>

    <form class="card panel" method="post" action="<?= BASE_URL ?>/admin/update_status.php" id="statusForm">
      <?= csrf_field() ?>
      <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
      <h2><i class="fa-solid fa-wrench"></i> Update status &amp; response</h2>
      <div class="grid-2">
        <label class="field"><span>New status</span>
          <select name="status" required>
            <?php foreach (complaint_statuses() as $s): ?>
              <option value="<?= e($s) ?>" <?= $c['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="field"><span>History comment <span class="opt">(optional, shown in timeline)</span></span>
          <input type="text" name="comment" maxlength="255" placeholder="e.g. Technician assigned">
        </label>
      </div>
      <label class="field"><span>Administrative response <span class="opt">(visible to student)</span></span>
        <textarea name="admin_response" rows="4" placeholder="Explain what was done or what happens next…"><?= e($c['admin_response'] ?? '') ?></textarea>
      </label>
      <div class="form-actions">
        <button class="btn btn-danger-ghost" type="submit" name="action" value="archive_confirm" form="deleteForm" title="Archive"><i class="fa-regular fa-trash-can"></i></button>
        <span class="spacer"></span>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i> Save changes</button>
      </div>
      <small class="hint">Saving writes to <code>complaints</code> + appends a row to <code>complaint_history</code> in one transaction.</small>
    </form>

    <!-- Separate soft-delete form (avoids nested forms) -->
    <form id="deleteForm" method="post" action="<?= BASE_URL ?>/admin/delete_complaint.php" data-confirm="Archive <?= e(complaint_ref($c['id'])) ?>? It will be hidden from queues but kept in the database.">
      <?= csrf_field() ?>
      <input type="hidden" name="complaint_id" value="<?= (int)$c['id'] ?>">
    </form>
  </div>

  <aside class="side-stack">
    <div class="card panel">
      <h2><i class="fa-solid fa-user"></i> Student</h2>
      <div class="user-line"><div class="avatar lg"><?= e(strtoupper(mb_substr($c['student_name'], 0, 1, 'UTF-8'))) ?></div>
        <div><strong><?= e($c['student_name']) ?></strong><br><small class="muted"><?= e($c['student_email']) ?></small><br><small class="muted">Member since <?= e(date('M Y', strtotime($c['student_since']))) ?></small></div>
      </div>
    </div>
    <div class="card panel">
      <h2><i class="fa-solid fa-timeline"></i> History (<?= count($history) ?>)</h2>
      <?php if (!$history): ?><p class="muted">No status changes yet.</p>
      <?php else: ?><ul class="timeline">
        <?php foreach ($history as $h): ?>
        <li><span class="t-dot"></span><div>
          <strong><?= e(($h['old_status'] ?: '—') . ' → ' . $h['new_status']) ?></strong>
          <small>by <?= e($h['admin_name'] ?? 'Admin') ?> · <?= e(date('M j, Y h:i A', strtotime($h['created_at']))) ?></small>
          <?php if (!empty($h['comment'])): ?><p><?= e($h['comment']) ?></p><?php endif; ?>
        </div></li>
        <?php endforeach; ?>
      </ul><?php endif; ?>
    </div>
  </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
