<?php

// 🔄 Enable CORS and set proper headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🧹 Handle admin wipe request
if (isset($_GET['wipe']) && $_GET['wipe'] == '1') {
    file_put_contents('log.csv', '');
    file_put_contents('redirect_log.csv', '');
    echo json_encode(['status' => 'wiped']);
    exit;
}

// 📱 Telegram notification function
function sendTelegramAlert($data, $intel) {
    $token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? '';
    $chatId = $_ENV['TELEGRAM_CHAT_ID'] ?? '';
    
    if (empty($token) || empty($chatId)) {
        return; // Skip if not configured
    }
    
    $message = "🚨 New Visitor Alert!\n\n";
    $message .= "🔍 Fingerprint: {$data['fingerprint']}\n";
    $message .= "🌍 IP: {$data['ip']} ({$intel['country']}, {$intel['city']})\n";
    $message .= "🖥️ Screen: {$data['screen']}\n";
    $message .= "🌐 Language: {$data['lang']}\n";
    $message .= "🏢 ISP: {$intel['org']}\n";
    $message .= "⏰ Time: {$data['logged_at']}";
    
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    $postData = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($postData)
        ]
    ];
    
    @file_get_contents($url, false, stream_context_create($options));
}

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
try {
    $raw_input = file_get_contents('php://input');
    
    if (empty($raw_input)) {
        echo json_encode(['status' => 'error', 'message' => 'No data received']);
        exit;
    }
    
    $data = json_decode($raw_input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON: ' . json_last_error_msg()]);
        exit;
    }
    
    if (!$data || !isset($data['fingerprint'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }
    
    // Enrich with server data
    $data['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $data['logged_at'] = date("Y-m-d H:i:s");
    $redirected = $data['redirected'] ?? false;
    
    // Get IP intelligence
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

// 📱 Send Telegram alert for new visitors
sendTelegramAlert($data, $intel);

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

    echo json_encode([
        'status' => 'success',
        'message' => 'Data logged successfully',
        'fingerprint' => $data['fingerprint'],
        'ip' => $data['ip'],
        'timestamp' => $data['logged_at']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
