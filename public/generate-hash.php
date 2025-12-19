<?php
/**
 * Password Hash Generator
 * Visit this file once to generate hashes, then delete it
 */

$passwords = [
    'Admin@123' => 'Admin password',
    'User@123' => 'User password',
];

echo "<h1>Password Hash Generator</h1>";
echo "<p>Use these hashes in your seed.sql file:</p>";
echo "<pre style='background: #f0f0f0; padding: 20px; border-radius: 5px;'>";

foreach ($passwords as $password => $label) {
    $hash = password_hash($password, PASSWORD_ARGON2ID);
    echo "Password: <strong>$password</strong> ($label)\n";
    echo "Hash: <strong>$hash</strong>\n\n";
}

echo "</pre>";
echo "<p style='color: red;'><strong>IMPORTANT: Delete this file after using it!</strong></p>";
echo "<p>File location: <code>c:/xampp/htdocs/jira_clone_system/public/generate-hash.php</code></p>";
