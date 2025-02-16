<?php
header('Content-Type: application/json');

$logFile = 'motion_log.txt';
$logData = [];

if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match('/^(.*?) - (Y|N)$/', $line, $matches)) {
            $logData[] = [
                'timestamp' => $matches[1],
                'movement' => $matches[2]
            ];
        }
    }
}

echo json_encode(['log' => $logData]);
?>
