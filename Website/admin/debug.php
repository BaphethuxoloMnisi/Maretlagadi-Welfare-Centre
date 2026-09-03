<?php
require_once '../includes/db.php';

$email = 'admin@maretlagadi.org'; // change if yours is different

echo "<h3>1. Checking user table</h3>";
$stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();
echo "<pre>"; print_r($user); echo "</pre>";

echo "<h3>2. Checking admin table link</h3>";
if ($user) {
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $admin = $stmt->fetch();
    echo "<pre>"; print_r($admin); echo "</pre>";
} else {
    echo "No user found with that email — check for typos or extra spaces.";
}

echo "<h3>3. Testing password_verify()</h3>";
if ($user) {
    $testPassword = '12345'; // the password you're typing at login
    $stored = $user['password'];
    echo "Stored hash: " . htmlspecialchars($stored) . "<br>";
    echo "Hash length: " . strlen($stored) . " (should be 60 for bcrypt)<br>";
    var_dump(password_verify($testPassword, $stored));
}