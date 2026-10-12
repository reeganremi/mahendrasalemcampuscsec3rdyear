<?php
// Public landing page — no private data shown here.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
define('BASE_URL', '.');
$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['user_role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ResolveDesk · College Complaint Management System</title>
<meta name="description" content="Submit, track and resolve college complaints transparently — academics, hostel, labs, transport and more.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="./css/style.css">
<link rel="stylesheet" href="./css/responsive.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='22' fill='%234f46e5'/><text x='50' y='68' font-size='52' text-anchor='middle' fill='white' font-family='Arial' font-weight='bold'>R</text></svg>">
</head>
<body class="landing">
<nav class="landing-nav">
  <div class="wrap nav-inner">
    <a class="brand" href="<?= BASE_URL ?>/index.php">
      <span class="brand-mark"><i class="fa-solid fa-comments"></i></span>
      <span class="brand-text"><span class="brand-name">ResolveDesk</span><span class="brand-sub">College Complaints</span></span>
    </a>
    <div class="nav-links">
      <a href="#features">Features</a>
      <a href="#process">How it works</a>
      <a href="#categories">Categories</a>
    </div>
    <div class="nav-actions">
      <?php if ($isLoggedIn): ?>
        <a class="btn btn-ghost" href="<?= $role === 'admin' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/dashboard.php' ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-ghost" href="<?= BASE_URL ?>/login.php">Login</a>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Register <i class="fa-solid fa-arrow-right"></i></a>
      <?php endif; ?>
    </div>
    <button class="icon-btn nav-burger" id="landingBurger" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
  </div>
  <div class="wrap mobile-menu" id="landingMenu">
    <a href="#features">Features</a>
    <a href="#process">How it works</a>
    <a href="#categories">Categories</a>
    <div class="mobile-actions">
      <?php if ($isLoggedIn): ?>
        <a class="btn btn-ghost" href="<?= $role === 'admin' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/dashboard.php' ?>">Dashboard</a>
      <?php else: ?>
        <a class="btn btn-ghost" href="<?= BASE_URL ?>/login.php">Login</a>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<header class="hero wrap">
  <div class="hero-copy">
    <span class="eyebrow"><i class="fa-solid fa-bolt"></i> Transparent campus grievance portal</span>
    <h1>Your Voice.<br><span class="grad">Our Responsibility.</span></h1>
    <p class="lede">Report academic, hostel, lab, transport or fee issues in under a minute — then track every update from submission to resolution. No queues, no lost paperwork.</p>
    <div class="hero-cta">
      <a class="btn btn-primary btn-lg" href="<?= $isLoggedIn && $role !== 'admin' ? BASE_URL . '/submit_complaint.php' : BASE_URL . '/register.php' ?>"><i class="fa-solid fa-pen-to-square"></i> Submit a Complaint</a>
      <a class="btn btn-outline btn-lg" href="<?= $isLoggedIn ? ($role === 'admin' ? BASE_URL . '/admin/dashboard.php' : BASE_URL . '/dashboard.php') : BASE_URL . '/login.php' ?>"><i class="fa-solid fa-magnifying-glass"></i> Track Your Complaint</a>
    </div>
    <div class="hero-meta">
      <div><strong>~2 min</strong><span>to file a complaint</span></div>
      <div><strong>10</strong><span>support categories</span></div>
      <div><strong>100%</strong><span>trackable status</span></div>
    </div>
  </div>
  <div class="hero-card" aria-hidden="true">
    <div class="mock-window">
      <div class="mock-bar"><span></span><span></span><span></span></div>
      <div class="mock-body">
        <div class="mock-row"><span class="mock-badge b-pending">Pending</span><strong>Hostel water supply — Block B</strong><small>CMP-000142 · Hostel · 2 hrs ago</small></div>
        <div class="mock-row"><span class="mock-badge b-progress">In Progress</span><strong>Lab computers not booting</strong><small>CMP-000139 · Laboratory · Yesterday</small></div>
        <div class="mock-row"><span class="mock-badge b-resolved">Resolved</span><strong>Library book renewal issue</strong><small>CMP-000131 · Library · 3 days ago</small></div>
        <div class="mock-progress"><div style="width:72%"></div></div>
        <p class="mock-note"><i class="fa-solid fa-check-circle"></i> Admin responded: “Technician assigned, ETA tomorrow.”</p>
      </div>
    </div>
  </div>
</header>

<section class="section wrap" id="features">
  <h2>Everything you need, nothing you don’t</h2>
  <p class="sub">A calm, focused workflow for students — and a powerful queue for administrators.</p>
  <div class="grid-3">
    <div class="card feature"><div class="f-icon fi-1"><i class="fa-solid fa-bolt"></i></div><h3>Easy submission</h3><p>Categorised form with subject, detail and location. Validation on client and server, confirmation in seconds.</p></div>
    <div class="card feature"><div class="f-icon fi-2"><i class="fa-solid fa-eye"></i></div><h3>Transparent tracking</h3><p>Live badges — Pending → In Progress → Resolved / Closed — plus a step timeline and admin responses.</p></div>
    <div class="card feature"><div class="f-icon fi-3"><i class="fa-solid fa-shield-halved"></i></div><h3>Admin resolution</h3><p>Admins triage by category, reply, change status and keep a full audit history of every action.</p></div>
  </div>
</section>

<section class="section wrap" id="process">
  <h2>From complaint to closure in 3 steps</h2>
  <p class="sub">Designed with real college workflows in mind.</p>
  <div class="steps">
    <div class="card step"><span class="step-n">1</span><h3>Submit</h3><p>Pick a category, describe the issue, add the block or department. Your complaint is timestamped instantly.</p></div>
    <div class="step-arrow"><i class="fa-solid fa-arrow-right"></i></div>
    <div class="card step"><span class="step-n">2</span><h3>Review</h3><p>The admin team reviews the queue, prioritises urgent cases and posts an official response.</p></div>
    <div class="step-arrow"><i class="fa-solid fa-arrow-right"></i></div>
    <div class="card step"><span class="step-n">3</span><h3>Resolve</h3><p>Status moves to Resolved or Closed. Full history stays on your record for accountability.</p></div>
  </div>
</section>

<section class="section wrap" id="categories">
  <h2>We handle all campus concerns</h2>
  <p class="sub">Ten categories covering academic and campus life.</p>
  <div class="chips">
    <?php foreach (['Academics','Faculty','Infrastructure','Laboratory','Hostel','Transportation','Library','Examination','Fees and Administration','Other'] as $c): ?>
      <span class="chip"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($c) ?></span>
    <?php endforeach; ?>
  </div>
  <div class="cta-band card">
    <div><h3>Facing an issue right now?</h3><p>Create a free student account and file your first complaint in about two minutes.</p></div>
    <div class="cta-actions">
      <a class="btn btn-primary" href="<?= BASE_URL ?>/register.php">Get started</a>
      <a class="btn btn-outline" href="<?= BASE_URL ?>/login.php">I have an account</a>
    </div>
  </div>
</section>

<footer class="landing-footer">
  <div class="wrap foot-inner">
    <div class="brand"><span class="brand-mark sm"><i class="fa-solid fa-comments"></i></span><span><strong>ResolveDesk</strong><br><small>College Complaint Management System</small></span></div>
    <p class="foot-note">Built with PHP · MySQLi · Custom CSS · For campus demonstration use.</p>
    <div class="foot-links"><a href="<?= BASE_URL ?>/login.php">Login</a><a href="<?= BASE_URL ?>/register.php">Register</a><a href="#process">Process</a></div>
  </div>
</footer>

<script src="<?= BASE_URL ?>/js/app.js"></script>
</body>
</html>
