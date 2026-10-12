<?php
// Manage complaints — search (ID / name / subject / email), filters, sort, pagination.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

$search   = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$status   = trim($_GET['status'] ?? '');
$sort     = ($_GET['sort'] ?? 'newest') === 'oldest' ? 'oldest' : 'newest';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 10;
$offset   = ($page - 1) * $perPage;

$where = ['c.is_deleted = 0'];
$types = '';
$params = [];

if ($search !== '') {
    $where[] = '(CAST(c.id AS CHAR) LIKE ? OR c.subject LIKE ? OR c.description LIKE ? OR u.name LIKE ? OR u.email LIKE ?)';
    $types .= 'sssss';
    $like = '%' . $search . '%';
    array_push($params, $like, $like, $like, $like, $like);
}
if ($category !== '' && is_valid_category($category)) { $where[] = 'c.category = ?'; $types .= 's'; $params[] = $category; }
if ($status !== '' && is_valid_status($status))       { $where[] = 'c.status = ?';   $types .= 's'; $params[] = $status; }

$whereSql = implode(' AND ', $where);
$orderSql = $sort === 'oldest' ? 'c.created_at ASC' : 'c.created_at DESC';

// Count.
$sql = "SELECT COUNT(*) AS c FROM complaints c JOIN users u ON u.id = c.user_id WHERE $whereSql";
$stmt = $conn->prepare($sql);
if ($types !== '') stmt_bind($stmt, $types, $params);
$stmt->execute();
$total = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
$stmt->close();
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

// Rows.
$sql = "SELECT c.id, c.category, c.subject, c.status, c.created_at, u.name AS student_name, u.email AS student_email
        FROM complaints c JOIN users u ON u.id = c.user_id
        WHERE $whereSql ORDER BY $orderSql LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$types2 = $types . 'ii';
$params2 = array_merge($params, [$perPage, $offset]);
stmt_bind($stmt, $types2, $params2);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function qsa($overrides = []) {
    $q = array_merge($_GET, $overrides);
    foreach ($q as $k => $v) { if ($v === '' || $v === null) unset($q[$k]); }
    return '?' . http_build_query($q);
}

$page_title = 'Manage Complaints';
$page_subtitle = $total . ' record' . ($total === 1 ? '' : 's') . ' found.';
$active = 'admin-complaints';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card toolbar">
  <form class="toolbar-row" method="get" action="<?= BASE_URL ?>/admin/complaints.php">
    <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" placeholder="Search ID, student, subject, email…" value="<?= e($search) ?>"></div>
    <select name="category"><option value="">All categories</option><?php foreach (complaint_categories() as $cat): ?><option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select>
    <select name="status"><option value="">All statuses</option><?php foreach (complaint_statuses() as $s): ?><option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
    <select name="sort"><option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest first</option><option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest first</option></select>
    <button class="btn btn-primary" type="submit">Apply</button>
    <?php if ($search !== '' || $category !== '' || $status !== '' || $sort !== 'newest'): ?><a class="btn btn-ghost" href="<?= BASE_URL ?>/admin/complaints.php">Clear</a><?php endif; ?>
  </form>
</div>

<div class="card panel">
<?php if (!$rows): ?>
  <div class="empty"><div class="empty-icon"><i class="fa-regular fa-folder-open"></i></div><h3>No complaints match</h3><p>Try a different search term or clear the filters.</p></div>
<?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Ref</th><th>Subject</th><th>Student</th><th>Category</th><th>Status</th><th>Date</th><th class="th-right">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $c): ?>
      <tr>
        <td class="mono"><?= e(complaint_ref($c['id'])) ?></td>
        <td><strong><?= e(mb_strimwidth($c['subject'], 0, 44, '…')) ?></strong></td>
        <td><?= e($c['student_name']) ?><br><small class="muted"><?= e($c['student_email']) ?></small></td>
        <td><span class="cat-pill"><?= e($c['category']) ?></span></td>
        <td><span class="badge <?= e(status_badge_class($c['status'])) ?>"><?= e($c['status']) ?></span></td>
        <td class="muted nowrap"><?= e(date('M j, Y', strtotime($c['created_at']))) ?></td>
        <td class="row-actions">
          <a class="btn btn-ghost btn-sm" href="<?= BASE_URL ?>/admin/view_complaint.php?id=<?= (int)$c['id'] ?>"><i class="fa-regular fa-eye"></i> View</a>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?><a class="page-btn" href="<?= e(qsa(['page' => $page - 1])) ?>"><i class="fa-solid fa-chevron-left"></i> Prev</a><?php endif; ?>
    <?php for ($p = 1; $p <= $totalPages; $p++): if ($totalPages > 7 && abs($p - $page) > 2 && $p !== 1 && $p !== $totalPages) continue; ?>
      <a class="page-btn<?= $p === $page ? ' current' : '' ?>" href="<?= e(qsa(['page' => $p])) ?>"><?= $p ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?><a class="page-btn" href="<?= e(qsa(['page' => $page + 1])) ?>">Next <i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
  </div>
  <?php endif; ?>
<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
