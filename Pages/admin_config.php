<?php
// Shared admin password for the dashboard.
// IMPORTANT: Change this password, then regenerate the hash below and
// replace ADMIN_PASSWORD_HASH with the new value. Never store the plain
// password in code.
//
// To generate a new hash, run this once (e.g. in a throwaway PHP file or CLI):
//   echo password_hash('your-new-password', PASSWORD_DEFAULT);

// Default password for this hash is: changeme123
// CHANGE THIS before deploying — see instructions above.
define('ADMIN_PASSWORD_HASH', '$2b$10$6tpnz5Wcum4uBi30iUJ6f.sngs3epakQwoiGZ5jYRzOxK.5/LI3oW');