<?php
/**
 * Reusable app layout — header (sidebar + topbar + <main> open tag).
 * Expects (optionally): $page_title, $page_subtitle, $active, $breadcrumb
 * Requires: $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role'], $conn
 */
if (!isset($page_title))  $page_title = 'Dashboard';
if (!isset($page_subtitle)) $page_subtitle = '';
if (!isset($active)) $active = '';
$role = $_SESSION['user_role'] ?? 'student';
$userName = $_SESSION['user_name'] ?? 'User';
$userInitial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8'));

// Pending badge for topbar bell (best-effort, never fatal).
$pendingCount = 0;
if (isset($conn) && $conn instanceof mysqli) {
    try {
        if ($role === 'admin') {
            $r = $conn->query("SELECT COUNT(*) AS c FROM complaints WHERE status='Pending' AND is_deleted=0");
            $pendingCount = (int)($r->fetch_assoc()['c'] ?? 0);
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS c FROM complaints WHERE user_id=? AND status='Pending' AND is_deleted=0");
            $stmt->bind_param('i', $_SESSION['user_id']);
            $stmt->execute();
            $pendingCount = (int)($stmt->get_result()->fetch_assoc()['c'] ?? 0);
            $stmt->close();
        }
    } catch (Throwable $t) { $pendingCount = 0; }
}

function nav_active($key, $active) { return $key === $active ? ' active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title) ?> · ResolveDesk</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/responsive.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='22' fill='%234f46e5'/><text x='50' y='68' font-size='52' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'>R</text></svg>">
</head>
<body class="app-body">
<div class="app-shell">

  <!-- Mobile overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <div class="brand-mark"><i class="fa-solid fa-comments"></i></div>
      <div class="brand-text">
        <span class="brand-name">ResolveDesk</span>
        <span class="brand-sub">College Complaints</span>
      </div>
      <button class="icon-btn sidebar-close" id="sidebarClose" aria-label="Close menu"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="role-pill <?= $role === 'admin' ? 'role-admin' : 'role-student' ?>">
      <i class="fa-solid <?= $role === 'admin' ? 'fa-shield-halved' : 'fa-graduation-cap' ?>"></i>
      <?= $role === 'admin' ? 'Administrator' : 'Student Portal' ?>
    </div>

    <?php if ($role === 'admin'): ?>
    <nav class="side-nav" aria-label="Admin navigation">
      <p class="nav-label">Overview</p>
      <a class="nav-link<?= nav_active('admin-dashboard', $active) ?>" href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <a class="nav-link<?= nav_active('admin-complaints', $active) ?>" href="<?= BASE_URL ?>/admin/complaints.php"><i class="fa-solid fa-inbox"></i> Manage Complaints
        <?php if ($pendingCount > 0): ?><span class="nav-count"><?= (int)$pendingCount ?></span><?php endif; ?>
      </a>
      <a class="nav-link<?= nav_active('admin-users', $active) ?>" href="<?= BASE_URL ?>/admin/users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
      <p class="nav-label">Account</p>
      <a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-globe"></i> View Site</a>
      <a class="nav-link" href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
    <?php else: ?>
    <nav class="side-nav" aria-label="Student navigation">
      <p class="nav-label">Menu</p>
      <a class="nav-link<?= nav_active('dashboard', $active) ?>" href="<?= BASE_URL ?>/dashboard.php"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
      <a class="nav-link<?= nav_active('submit', $active) ?>" href="<?= BASE_URL ?>/submit_complaint.php"><i class="fa-solid fa-pen-to-square"></i> Submit Complaint</a>
      <a class="nav-link<?= nav_active('my', $active) ?>" href="<?= BASE_URL ?>/my_complaints.php"><i class="fa-solid fa-list-check"></i> My Complaints</a>
      <p class="nav-label">Account</p>
      <a class="nav-link" href="<?= BASE_URL ?>/index.php"><i class="fa-solid fa-globe"></i> Home</a>
      <a class="nav-link" href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </nav>
    <?php endif; ?>

    <div class="sidebar-card">
      <div class="sidebar-card-icon"><i class="fa-solid fa-circle-question"></i></div>
      <p class="sidebar-card-title">Need help?</p>
      <p class="sidebar-card-text">Track status anytime from My Complaints. Admins reply within 2–3 working days.</p>
    </div>
  </aside>

  <!-- ============ MAIN COLUMN ============ -->
  <div class="main-col">
    <header class="topbar">
      <div class="topbar-left">
        <button class="icon-btn hamburger" id="sidebarToggle" aria-label="Open menu"><i class="fa-solid fa-bars"></i></button>
        <div>
          <div class="breadcrumb">ResolveDesk <span>/</span> <?= e($page_title) ?></div>
          <h1 class="page-title"><?= e($page_title) ?></h1>
          <?php if ($page_subtitle): ?><p class="page-subtitle"><?= e($page_subtitle) ?></p><?php endif; ?>
        </div>
      </div>
      <div class="topbar-right">
        <a class="icon-btn bell" href="<?= $role === 'admin' ? BASE_URL . '/admin/complaints.php?status=Pending' : BASE_URL . '/my_complaints.php?status=Pending' ?>" title="Pending complaints" aria-label="Pending complaints">
          <i class="fa-regular fa-bell"></i>
          <?php if ($pendingCount > 0): ?><span class="bell-dot"><?= (int)min($pendingCount, 99) ?></span><?php endif; ?>
        </a>
        <div class="user-chip">
          <div class="avatar"><?= e($userInitial) ?></div>
          <div class="user-meta">
            <span class="user-name"><?= e(mb_strimwidth($userName, 0, 20, '…', 'UTF-8')) ?></span>
            <span class="user-role"><?= e(ucfirst($role)) ?></span>
          </div>
        </div>
      </div>
    </header>

    <main class="content">
