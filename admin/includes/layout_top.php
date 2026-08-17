<?php
// Shared admin layout - sidebar + topbar. Expects $pageTitle and $activeNav to be set before including.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?> - Maretlagadi Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-body">

<div class="admin-wrapper">
  <aside class="admin-sidebar">
    <div class="admin-brand"><span class="brand-mark">MWC</span> Admin</div>
    <nav class="admin-nav">
      <a href="dashboard.php" class="admin-nav-link <?php echo $activeNav === 'dashboard' ? 'active' : ''; ?>">
        <i class="bi bi-grid-1x2-fill"></i> Dashboard
      </a>
      <a href="announcements.php" class="admin-nav-link <?php echo $activeNav === 'announcements' ? 'active' : ''; ?>">
        <i class="bi bi-megaphone-fill"></i> Announcements
      </a>
      <a href="volunteers.php" class="admin-nav-link <?php echo $activeNav === 'volunteers' ? 'active' : ''; ?>">
        <i class="bi bi-people-fill"></i> Volunteers
      </a>
      <a href="donations.php" class="admin-nav-link <?php echo $activeNav === 'donations' ? 'active' : ''; ?>">
        <i class="bi bi-heart-fill"></i> Donations
      </a>
      <a href="messages.php" class="admin-nav-link <?php echo $activeNav === 'messages' ? 'active' : ''; ?>">
        <i class="bi bi-envelope-fill"></i> Messages
      </a>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <h5 class="mb-0 fw-semibold"><?php echo htmlspecialchars($pageTitle); ?></h5>
      <div class="d-flex align-items-center gap-3">
        <span class="text-secondary small">Hi, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-dark rounded-pill">Log Out</a>
      </div>
    </header>
    <main class="admin-content">