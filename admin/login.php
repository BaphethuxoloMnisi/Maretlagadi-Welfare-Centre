<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';

$pageTitle = "Admin Login";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter your email and password.";
    } else {

        $stmt = $pdo->prepare("
            SELECT 
                u.user_id,
                u.name,
                u.email,
                u.password,
                a.admin_id
            FROM `user` u
            INNER JOIN admin a ON u.user_id = a.user_id
            WHERE u.email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {

            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['user_id'] = $admin['user_id'];

            header("Location: dashboard.php");
            exit;

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login - Maretlagadi Welfare Centre</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center"
     style="min-height:100vh;">

    <div class="card p-4 shadow-sm"
         style="max-width:380px; width:100%;">

        <h4 class="fw-bold text-center mb-1">
            Admin Portal
        </h4>

        <p class="text-secondary text-center small mb-3">
            Maretlagadi Welfare Centre
        </p>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Email</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    required
                >
            </div>

            <button
                type="submit"
                class="btn btn-brand w-100 rounded-pill py-2"
            >
                Log In
            </button>

        </form>

    </div>

</div>

</body>
</html>