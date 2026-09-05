<?php
// Usage: php database/audit_schema.php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDB();
} catch (Exception $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}

$sqlFile = __DIR__ . '/eoffice.sql';
if (!file_exists($sqlFile)) {
    fwrite(STDERR, "Reference SQL file not found: $sqlFile\n");
    exit(1);
}

$content = file_get_contents($sqlFile);
preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=/s', $content, $matches, PREG_SET_ORDER);

$issues = [];
foreach ($matches as $m) {
    $table = $m[1];
    echo "Checking table: $table\n";
    // check if table exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    if ((int)$stmt->fetchColumn() === 0) {
        $issues[] = "Missing table: $table";
        echo "  -> MISSING\n";
        continue;
    }

    // parse expected columns: only lines that start with a column definition (start with `column_name`)
    $colsRaw = $m[2];
    $expectedCols = [];
    $lines = preg_split('/\r?\n/', $colsRaw);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '`')) {
            // column definition line
            if (preg_match('/^`([^`]+)`\s+[A-Za-z0-9\(\)]+/', $line, $cm)) {
                $expectedCols[] = $cm[1];
            }
        }
    }

    $stmt = $pdo->prepare("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $missingCols = array_diff($expectedCols, $existing);
    if ($missingCols) {
        $issues[] = "Table $table missing columns: " . implode(', ', $missingCols);
        echo "  -> Missing columns: " . implode(', ', $missingCols) . "\n";
    } else {
        echo "  -> OK\n";
    }
}

if (empty($issues)) {
    echo "\nSchema audit OK: all expected tables/columns found.\n";
} else {
    echo "\nSchema issues found:\n";
    foreach ($issues as $i) echo " - $i\n";
}
