<?php
session_start();
include 'admin_config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_login_time'] = time();
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'Incorrect password. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Maretlagadi Welfare Centre</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 380px;
            border: none;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            background-color: #198754;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
            border-radius: 14px 14px 0 0;
        }

        .login-header h4 {
            font-weight: 700;
            margin: 0;
        }

        .btn-login {
            background-color: #198754;
            border-color: #198754;
            border-radius: 30px;
            padding: 10px;
            font-weight: 600;
        }

        .btn-login:hover {
            background-color: #146c43;
            border-color: #146c43;
        }
    </style>
</head>

<body>

    <div class="card login-card">
        <div class="login-header">
            <h4>Admin Dashboard</h4>
            <small>Maretlagadi Welfare Centre</small>
        </div>

        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="admin_login.php">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control" name="password" required autofocus>
                </div>

                <button type="submit" class="btn btn-login btn-success w-100 text-white">
                    Log In
                </button>
            </form>

        </div>
    </div>

</body>
</html>