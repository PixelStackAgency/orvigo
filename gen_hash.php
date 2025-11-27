<?php
// gen_hash.php
// Usage: php gen_hash.php "YourAdminPasswordHere"
if ($argc < 2) {
    fwrite(STDERR, "Usage: php gen_hash.php \"YourPasswordHere\"\n");
    exit(1);
}
$pass = $argv[1];
echo password_hash($pass, PASSWORD_DEFAULT) . PHP_EOL;
