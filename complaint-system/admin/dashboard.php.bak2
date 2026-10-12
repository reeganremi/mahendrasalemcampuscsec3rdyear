<?php
// Admin dashboard — college-wide stats from real DB queries.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// Status counts.
$counts = ['total' => 0, 'Pending' => 0, 'In Progress' => 0, 'Resolved' => 0, 'Closed' => 0];
$res = $conn->query("SELECT status, COUNT(*) AS c FROM complaints WHERE is_deleted = 0 GROUP BY status");
while ($row = $res->fetch_assoc()) { $counts[$row['status']] = (int)$row['c']; $counts['total'] += (int)$row['c']; }

// Category distribution.
$catDist = $conn->query("SELECT category, COUNT(*) AS c FROM complaints WHERE is_deleted = 0 GROUP BY category ORDER BY c DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);
$maxCat = 0; foreach ($catDist as $c) $maxCat = max($maxCat, (int)$c['c']);

// Recent complaints with student names.
$recent = $conn->query("SELECT c.id, c.subject, c.category, c.status, c.created_at, u.name AS student_name
                        FROM complaints c JOIN users u ON u.id = c.user_id
                        WHERE c.is_deleted = 0 ORDER BY c.created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

// Recent activity (history trail).
$activity = $conn->query("SELECT h.old_status, h.new_status, h.comment, h.created_at, h.complaint_id, u.name AS admin_name
                          FROM complaint_history h LEFT JOIN users u ON u.id = h.admin_id
                          ORDER BY h.created_at DESC LIMIT 8")->fetch_all(MYSQLI_ASSOC);

$totalUsers = (int)($conn->query("SELECT COUNT(*) AS c FROM users WHERE role='student'")->fetch_assoc()['c']);

$page_title = 'Admin Dashboard';
$page_subtitle = 'College-wide complaint overview and recent activity.';
$active = 'admin-dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="stat-grid">
  <div class="stat-card"><div class="stat-icon si-total"><i class="fa-solid fa-layer-group"></i></div><div><p class="stat-label">Total complaints</p><p class="stat-value"><?= (int)$counts['total'] ?></p></div></div>
  <div class="stat-card"><div class="stat-icon si-pending"><i class="fa-solid fa-hourglass-half"></i></div><div><p class="stat-label">Pending</p><p class="stat-value"><?= (int)$counts['Pending'] ?></p></div></div>
  <div class="stat-card"><div class="stat-icon si-progress"><i class="fa-solid fa-spinner"></i></div><div><p class="stat-label">In Progress</p><p class="stat-value"><?= (int)$counts['In Progress'] ?></p></div></div>
  <div class="stat-card"><div class="stat-icon si-resolved"><i class="fa-solid fa-circle-check"></i></div><div><p class="stat-label">Resolved</p><p class="stat-value"><?= (int)$counts['Resolved'] ?></p></div></div>
</div>

<div class="quick-actions">
  <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/complaints.php?status=Pending"><i class="fa-solid fa-inbox"></i> Review pending (<?= (int)$counts['Pending'] ?>)</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/admin/complaints.php"><i class="fa-solid fa-list"></i> All complaints</a>
  <a class="btn btn-outline" href="<?= BASE_URL ?>/admin/users.php"><i class="fa-solid fa-users"></i> <?= (int)$totalUsers ?> students</a>
</div>

<div class="panel-grid">
  <div class="card panel">
    <div class="panel-head"><h2><i class="fa-solid fa-clock-rotate-left"></i> Recently submitted</h2><a class="link" href="<?= BASE_URL ?>/admin/complaints.php">Manage all <i class="fa-solid fa-arrow-right"></i></a></div>
    <?php if (!$recent): ?><div class="empty"><div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div><h3>No complaints yet</h3><p>New student submissions will appear here.</p></div>
    <?php else: ?>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Ref</th><th>Subject</th><th>Student</th><th>Category</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recent as $c): ?>
        <tr>
          <td class="mono"><?= e(complaint_ref($c['id'])) ?></td>
          <td><strong><?= e(mb_strimwidth($c['subject'], 0, 40, '…')) ?></strong><br><small class="muted"><?= e(time_ago($c['created_at'])) ?></small></td>
          <td><?= e($c['student_name']) ?></td>
          <td><span class="cat-pill"><?= e($c['category']) ?></span></td>
          <td><span class="badge <?= e(status_badge_class($c['status'])) ?>"><?= e($c['status']) ?></span></td>
          <td><a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/admin/view_complaint.php?id=<?= (int)$c['id'] ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </div>

  <div class="side-stack">
    <div class="card panel">
      <div class="panel-head"><h2><i class="fa-solid fa-chart-pie"></i> Status mix</h2></div>
      <?php foreach (['Pending','In Progress','Resolved','Closed'] as $s): $v = $counts[$s]; $pct = $counts['total'] ? round($v/$counts['total']*100) : 0; ?>
        <div class="bar-row"><div class="bar-top"><span><span class="badge <?= e(status_badge_class($s)) ?>"><?= e($s) ?></span></span><strong><?= (int)$v ?> · <?= (int)$pct ?>%</strong></div><div class="bar"><div style="width:<?= (int)$pct ?>%"></div></div></div>
      <?php endforeach; ?>
    </div>
    <div class="card panel">
      <div class="panel-head"><h2><i class="fa-solid fa-tags"></i> Top categories</h2></div>
      <?php if (!$catDist): ?><p class="muted">No data yet.</p>
      <?php else: foreach ($catDist as $c): $pct = $maxCat ? round($c['c']/$maxCat*100) : 0; ?>
        <div class="bar-row"><div class="bar-top"><span><?= e($c['category']) ?></span><strong><?= (int)$c['c'] ?></strong></div><div class="bar"><div style="width:<?= (int)$pct ?>%"></div></div></div>
      <?php endforeach; endif; ?>
    </div>
    <div class="card panel">
      <div class="panel-head"><h2><i class="fa-solid fa-timeline"></i> Recent activity</h2></div>
      <?php if (!$activity): ?><p class="muted">No status changes recorded yet.</p>
      <?php else: ?><ul class="timeline">
        <?php foreach ($activity as $a): ?>
        <li><span class="t-dot"></span><div>
          <strong><?= e(complaint_ref($a['complaint_id'])) ?>: <?= e(($a['old_status'] ?: '—') . ' → ' . $a['new_status']) ?></strong>
          <small>by <?= e($a['admin_name'] ?? 'Admin') ?> · <?= e(time_ago($a['created_at'])) ?></small>
          <?php if (!empty($a['comment'])): ?><p><?= e(mb_strimwidth($a['comment'], 0, 120, '…')) ?></p><?php endif; ?>
        </div></li>
        <?php endforeach; ?>
      </ul><?php endif; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
