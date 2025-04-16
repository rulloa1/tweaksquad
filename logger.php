<?php

function fetchIntel($ip) {
    $res = @file_get_contents("https://ipapi.co/{$ip}/json/");
    if (!$res) return ['country' => 'Unknown', 'org' => 'N/A', 'city' => 'N/A'];
    $data = json_decode($res, true);
    return [
        'country' => $data['country_name'] ?? 'Unknown',
        'city'    => $data['city'] ?? 'N/A',
        'org'     => $data['org'] ?? 'N/A',
        'timezone'=> $data['timezone'] ?? 'N/A'
    ];
}

// 🧠 Collect & enrich data
$data = json_decode(file_get_contents('php://input'), true);
$data['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$data['logged_at'] = date("Y-m-d H:i:s");
$redirected = $data['redirected'] ?? false;
$intel = fetchIntel($data['ip']);

// 💾 Log everything to log.csv
$f1 = fopen("log.csv", "a");
fputcsv($f1, [
    $data['fingerprint'],
    $data['ip'],
    $data['agent'],
    $data['screen'],
    $data['lang'],
    $data['timezone'],
    $data['referrer'],
    $intel['country'],
    $intel['city'],
    $intel['org'],
    $intel['timezone'],
    $data['logged_at']
]);
fclose($f1);

// 🌀 If redirected, log to separate file
if ($redirected) {
    $f2 = fopen("redirect_log.csv", "a");
    fputcsv($f2, [
        $data['fingerprint'],
        $data['ip'],
        $data['screen'],
        $data['lang'],
        $data['referrer'],
        $data['timezone'],
        $data['logged_at']
    ]);
    fclose($f2);
}

echo json_encode(['status' => 'logged']);
