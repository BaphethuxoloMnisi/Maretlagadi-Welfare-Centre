<?php
//session_start();
include 'database/create_database.php';

if (empty($_SESSION['is_admin'])) {
    header('Location: admin_login.php');
    exit;
}

// Optional: auto-expire the session after 30 minutes of inactivity
$timeout = 30 * 60;
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > $timeout) {
    session_unset();
    session_destroy();
    header('Location: admin_login.php?timeout=1');
    exit;
}
$_SESSION['admin_login_time'] = time();

include '../database/create_database.php'; // Database connection

// ===== Filters =====
$status_filter = $_GET['status'] ?? 'all';
$allowed_statuses = ['all', 'pending', 'complete', 'failed', 'cancelled'];
if (!in_array($status_filter, $allowed_statuses, true)) {
    $status_filter = 'all';
}

$where = '';
if ($status_filter !== 'all') {
    $status_escaped = mysqli_real_escape_string($conn, $status_filter);
    $where = "WHERE payment_status = '$status_escaped'";
}

$query = "SELECT * FROM donations $where ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// ===== Summary stats (always across all donations, regardless of filter) =====
$stats_query = "SELECT
    COUNT(*) AS total_count,
    SUM(CASE WHEN payment_status = 'complete' THEN amount ELSE 0 END) AS total_raised,
    SUM(CASE WHEN payment_status = 'complete' THEN 1 ELSE 0 END) AS complete_count,
    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
    SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) AS failed_count
    FROM donations";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result) ?: [
    'total_count' => 0, 'total_raised' => 0, 'complete_count' => 0, 'pending_count' => 0, 'failed_count' => 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Donations</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f5;
            font-family: Arial, sans-serif;
        }

        .admin-topbar {
            background-color: #198754;
            color: #fff;
            padding: 18px 0;
        }

        .admin-topbar h4 {
            margin: 0;
            font-weight: 700;
        }

        .btn-logout {
            border-radius: 30px;
            padding: 6px 20px;
            font-weight: 600;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            padding: 22px;
            height: 100%;
        }

        .stat-card h3 {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stat-card p {
            color: #777;
            margin: 0;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .stat-card.total h3 { color: #198754; }
        .stat-card.pending h3 { color: #b8860b; }
        .stat-card.complete h3 { color: #198754; }
        .stat-card.failed h3 { color: #c0392b; }

        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-complete { background-color: #d1e7dd; color: #0f5132; }
        .status-pending { background-color: #fff3cd; color: #664d03; }
        .status-failed { background-color: #f8d7da; color: #842029; }
        .status-cancelled { background-color: #e2e3e5; color: #41464b; }

        .filter-pills .btn {
            border-radius: 30px;
            margin-right: 6px;
            margin-bottom: 6px;
        }
    </style>
</head>

<body>

<div class="admin-topbar">
    <div class="container d-flex justify-content-between align-items-center">
        <h4>Donations Dashboard</h4>
        <a href="admin_logout.php" class="btn btn-light btn-logout">Log Out</a>
    </div>
</div>

<div class="container py-4">

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card total">
                <h3>R<?php echo number_format((float)($stats['total_raised'] ?? 0), 2); ?></h3>
                <p>Total Raised</p>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card complete">
                <h3><?php echo (int)($stats['complete_count'] ?? 0); ?></h3>
                <p>Completed</p>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card pending">
                <h3><?php echo (int)($stats['pending_count'] ?? 0); ?></h3>
                <p>Pending</p>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="stat-card failed">
                <h3><?php echo (int)($stats['failed_count'] ?? 0); ?></h3>
                <p>Failed</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-pills mb-3">
        <a href="?status=all" class="btn btn-sm <?php echo $status_filter === 'all' ? 'btn-success' : 'btn-outline-success'; ?>">All</a>
        <a href="?status=pending" class="btn btn-sm <?php echo $status_filter === 'pending' ? 'btn-success' : 'btn-outline-success'; ?>">Pending</a>
        <a href="?status=complete" class="btn btn-sm <?php echo $status_filter === 'complete' ? 'btn-success' : 'btn-outline-success'; ?>">Complete</a>
        <a href="?status=failed" class="btn btn-sm <?php echo $status_filter === 'failed' ? 'btn-success' : 'btn-outline-success'; ?>">Failed</a>
        <a href="?status=cancelled" class="btn btn-sm <?php echo $status_filter === 'cancelled' ? 'btn-success' : 'btn-outline-success'; ?>">Cancelled</a>
    </div>

    <!-- Table -->
    <div class="card table-card">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Date</th>
                            <th>Donor</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="ps-3"><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone'] ?: '-'); ?></td>
                                    <td>R<?php echo number_format((float)$row['amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo htmlspecialchars($row['payment_status']); ?>">
                                            <?php echo htmlspecialchars($row['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['m_payment_id']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No donations found for this filter.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

</body>
</html>