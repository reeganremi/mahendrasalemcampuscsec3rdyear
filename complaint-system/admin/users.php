<?php
// Admin: manage users — list students + complaint counts, activate/deactivate.
// Never displays password hashes.
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin_auth.php';

// ---- Handle activate/deactivate (POST + CSRF) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        set_flash('Security check failed.', 'error');
    } else {
        $targetId = (int)($_POST['user_id'] ?? 0);
        $action = $_POST['toggle'] ?? '';
        if ($targetId > 0 && in_array($action, ['activate', 'deactivate'], true)) {
            // Admins cannot deactivate themselves or other admins here.
            $stmt = $conn->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
            $stmt->bind_param('i', $targetId);
            $stmt->execute();
            $t = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if (!$t) {
                set_flash('User not found.', 'error');
            } elseif ($t['role'] === 'admin') {
                set_flash('Admin accounts cannot be deactivated from this screen.', 'error');
            } elseif ($targetId === (int)$_SESSION['user_id']) {
                set_flash('You cannot deactivate your own account.', 'error');
            } else {
                $newVal = $action === 'activate' ? 1 : 0;
                $stmt = $conn->prepare('UPDATE users SET is_active = ? WHERE id = ?');
                $stmt->bind_param('ii', $newVal, $targetId);
                $stmt->execute();
                $stmt->close();
                set_flash($action === 'activate' ? 'Account reactivated.' : 'Account deactivated — user can no longer log in.', 'success');
            }
        }
    }
    redirect(BASE_URL . '/admin/users.php');
}

// ---- List students with complaint counts ----
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.is_active, u.created_at,
                                   COUNT(c.id) AS total,
                                   SUM(c.status = 'Pending') AS pending,
                                   SUM(c.status = 'Resolved') AS resolved
                            FROM users u LEFT JOIN complaints c ON c.user_id = u.id AND c.is_deleted = 0
                            WHERE u.role = 'student' AND (u.name LIKE ? OR u.email LIKE ?)
                            GROUP BY u.id ORDER BY u.created_at DESC");
    $like = '%' . $search . '%';
    $stmt->bind_param('ss', $like, $like);
} else {
    $stmt = $conn->prepare("SELECT u.id, u.name, u.email, u.is_active, u.created_at,
                                   COUNT(c.id) AS total,
                                   SUM(c.status = 'Pending') AS pending,
                                   SUM(c.status = 'Resolved') AS resolved
                            FROM users u LEFT JOIN complaints c ON c.user_id = u.id AND c.is_deleted = 0
                            WHERE u.role = 'student'
                            GROUP BY u.id ORDER BY u.created_at DESC");
}
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$page_title = 'Manage Users';
$page_subtitle = count($users) . ' student account' . (count($users) === 1 ? '' : 's') . '.';
$active = 'admin-users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card toolbar">
  <form class="toolbar-row" method="get" action="<?= BASE_URL ?>/admin/users.php">
    <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="q" placeholder="Search name or email…" value="<?= e($search) ?>"></div>
    <button class="btn btn-primary" type="submit">Search</button>
    <?php if ($search !== ''): ?><a class="btn btn-ghost" href="<?= BASE_URL ?>/admin/users.php">Clear</a><?php endif; ?>
  </form>
</div>

<div class="card panel">
<?php if (!$users): ?>
  <div class="empty"><div class="empty-icon"><i class="fa-solid fa-users"></i></div><h3>No students found</h3><p>Registrations will appear here automatically.</p></div>
<?php else: ?>
  <div class="table-wrap"><table class="table">
    <thead><tr><th>Student</th><th>Complaints</th><th>Pending</th><th>Status</th><th>Joined</th><th class="th-right">Actions</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><div class="user-line"><div class="avatar"><?= e(strtoupper(mb_substr($u['name'], 0, 1, 'UTF-8'))) ?></div><div><strong><?= e($u['name']) ?></strong><br><small class="muted"><?= e($u['email']) ?></small></div></div></td>
        <td><strong><?= (int)$u['total'] ?></strong></td>
        <td><?= (int)$u['pending'] ?></td>
        <td><?= (int)$u['is_active'] === 1 ? '<span class="badge badge-resolved">Active</span>' : '<span class="badge badge-closed">Inactive</span>' ?></td>
        <td class="muted nowrap"><?= e(date('M j, Y', strtotime($u['created_at']))) ?></td>
        <td class="row-actions">
          <form method="post" action="<?= BASE_URL ?>/admin/users.php" class="inline-form" <?= (int)$u['is_active'] === 1 ? 'data-confirm="Deactivate ' . e($u['email']) . '? They will be logged out and blocked."' : '' ?>>
            <?= csrf_field() ?>
            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
            <?php if ((int)$u['is_active'] === 1): ?>
              <button class="btn btn-ghost btn-sm" type="submit" name="toggle" value="deactivate"><i class="fa-solid fa-ban"></i> Deactivate</button>
            <?php else: ?>
              <button class="btn btn-ghost btn-sm" type="submit" name="toggle" value="activate"><i class="fa-solid fa-check"></i> Activate</button>
            <?php endif; ?>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
<?php endif; ?>
<p class="hint" style="margin-top:12px"><i class="fa-solid fa-lock"></i> Passwords are never shown — only bcrypt hashes exist in the database and they are never rendered.</p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
