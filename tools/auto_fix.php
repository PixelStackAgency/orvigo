<?php
// --- tools/auto_fix.php ---
// Heuristic static scan & lightweight fixes for PHP files.
// - Removes BOM
// - Replaces PHP short open tags '<? ' with '<?php ' (but preserves '<?=')
// - Removes closing '?>' tag at EOF for pure-PHP files
// - Normalizes line endings to LF
// Usage: php tools/auto_fix.php

$root = realpath(__DIR__ . '/../');
if (!$root) {
    echo "Cannot determine project root\n";
    exit(1);
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$phpFiles = [];
foreach ($iterator as $file) {
    if (!$file->isFile()) continue;
    $path = $file->getPathname();
    if (stripos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) continue;
    if (substr($path, -4) === '.php') $phpFiles[] = $path;
}

if (empty($phpFiles)) {
    echo "No PHP files found.\n";
    exit(0);
}

$report = [];
foreach ($phpFiles as $f) {
    $orig = file_get_contents($f);
    $fixed = $orig;
    $changed = false;

    // Remove BOM
    if (substr($fixed, 0, 3) === "\xEF\xBB\xBF") {
        $fixed = substr($fixed, 3);
        $changed = true;
        $report[] = "BOM removed: $f";
    }

    // Normalize line endings to LF
    $norm = str_replace(["\r\n", "\r"], "\n", $fixed);
    if ($norm !== $fixed) { $fixed = $norm; $changed = true; $report[] = "Normalized EOL: $f"; }

    // Replace '<? ' that are not '<?=' and not '<?php'
    $fixedNew = preg_replace_callback('/<\?(?!php|=)(\s)/i', function($m){ return '<?php' . $m[1]; }, $fixed);
    if ($fixedNew !== $fixed) { $fixed = $fixedNew; $changed = true; $report[] = "Replaced short open tag: $f"; }

    // If file starts with <?php and does not contain closing '?>' elsewhere but ends with '?>', remove trailing closing tag
    $startsPhp = preg_match('/^\s*<\?(php)?/i', $fixed) === 1;
    $containsHtml = preg_match('/<html|<body|<div|<script/i', $fixed) === 1;
    if ($startsPhp && !$containsHtml) {
        // Remove final ?> if it's the only closing tag at EOF
        $trimmed = rtrim($fixed);
        if (substr($trimmed, -2) === '?>') {
            $fixed = rtrim(substr($trimmed, 0, -2)) . "\n";
            $changed = true;
            $report[] = "Removed trailing closing tag: $f";
        }
    }

    if ($changed) {
        // backup
        $bak = $f . '.bak';
        if (!file_exists($bak)) file_put_contents($bak, $orig);
        file_put_contents($f, $fixed);
    }
}

echo "Auto-fix complete. Summary:\n";
foreach ($report as $r) echo " - $r\n";
echo "\nBackups created with .bak extension for files changed.\n";
echo "Run php -l on files to validate syntax after these fixes.\n";
