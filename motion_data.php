<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = trim(file_get_contents("php://input"));

    if ($data === 'Y' || $data === 'N') {
        $logFile = 'motion_log.txt';
        $lastTimestamp = null;

        // Read the last entry to check the timestamp difference
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $count = count($lines);

            if ($count > 0) {
                $lastEntry = explode(' - ', $lines[$count - 1]);
                if (isset($lastEntry[0])) {
                    $lastTimestamp = strtotime($lastEntry[0]);
                }
            }
        }

        $currentTimestamp = time();
        if ($lastTimestamp === null || ($currentTimestamp - $lastTimestamp) > 10) {
            sendAlertEmail();
        }

        // Append new data to motion_log.txt
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $data . "\n", FILE_APPEND);
        http_response_code(200);
        echo "Motion data recorded successfully.";
    } else {
        http_response_code(400);
        echo "Invalid data received.";
    }
} else {
    http_response_code(405);
    echo "Method not allowed.";
}

function sendAlertEmail() {
    $to = "charlie.huang@berkeley.edu";
    $subject = "Motion Alert!";
    $message = "Motion detected by the sensor! See https://char787.com/technology/swiper-no-swiping/ for more info.";
    $headers = "From: charliehuang@char787.com\r\n" .
               "Reply-To: charliehuang@char787.com\r\n" .
               "X-Mailer: PHP/" . phpversion();

    mail($to, $subject, $message, $headers);
}
