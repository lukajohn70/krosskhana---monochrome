<?php
// Test the campaign API call directly
$url = 'http://localhost/krosskhana/admin/api/campaigns.php';
$data = [
    'action' => 'send',
    'id' => 1
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => [
            'Content-Type: application/json',
            'Cookie: PHPSESSID=' . session_id()
        ],
        'content' => json_encode($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "<h2>API Test Result</h2>";
echo "<p><strong>Request:</strong></p>";
echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";

echo "<p><strong>Response:</strong></p>";
echo "<pre>" . $result . "</pre>";

// Also test with curl
echo "<h2>CURL Test</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=' . session_id());

$curlResult = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> {$httpCode}</p>";
echo "<p><strong>CURL Response:</strong></p>";
echo "<pre>" . $curlResult . "</pre>";
?>
