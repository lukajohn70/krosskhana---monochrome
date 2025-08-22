<?php
require_once '../includes/config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    echo "Not logged in";
    exit;
}

$pdo = getDBConnection();

if (!$pdo) {
    echo "Database connection failed";
    exit;
}

echo "<h1>Campaigns API Test</h1>";

// Test 1: Check if campaigns table exists
echo "<h2>Test 1: Check if campaigns table exists</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'campaigns'");
    $tableExists = $stmt->rowCount() > 0;
    echo "Campaigns table exists: " . ($tableExists ? "YES" : "NO") . "<br>";
} catch (PDOException $e) {
    echo "Error checking table: " . $e->getMessage() . "<br>";
}

// Test 2: Try to query campaigns table
echo "<h2>Test 2: Try to query campaigns table</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM campaigns");
    $result = $stmt->fetch();
    echo "Total campaigns: " . $result['total'] . "<br>";
} catch (PDOException $e) {
    echo "Error querying campaigns: " . $e->getMessage() . "<br>";
}

// Test 3: Check all tables
echo "<h2>Test 3: All tables in database</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Available tables:<br>";
    foreach ($tables as $table) {
        echo "- $table<br>";
    }
} catch (PDOException $e) {
    echo "Error listing tables: " . $e->getMessage() . "<br>";
}

// Test 4: Test the actual API endpoint
echo "<h2>Test 4: Test API endpoint</h2>";
echo "<p>Testing campaigns API...</p>";

// Simulate the API call
$page = 1;
$limit = 10;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM campaigns";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute();
    $total = $countStmt->fetch()['total'];
    
    // Get campaigns data
    $sql = "SELECT * FROM campaigns ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$limit, $offset]);
    $data = $stmt->fetchAll();
    
    // Calculate pagination
    $totalPages = ceil($total / $limit);
    $from = $offset + 1;
    $to = min($offset + $limit, $total);
    
    $pagination = [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total' => $total,
        'from' => $from,
        'to' => $to,
        'per_page' => $limit
    ];
    
    $response = [
        'status' => 'success',
        'data' => $data,
        'pagination' => $pagination
    ];
    
    echo "API Response:<br>";
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
    
} catch (PDOException $e) {
    echo "API Error: " . $e->getMessage() . "<br>";
    
    // Return empty data as fallback
    $response = [
        'status' => 'success',
        'data' => [],
        'pagination' => [
            'current_page' => 1,
            'total_pages' => 0,
            'total' => 0,
            'from' => 0,
            'to' => 0,
            'per_page' => $limit
        ]
    ];
    
    echo "Fallback Response:<br>";
    echo "<pre>" . json_encode($response, JSON_PRETTY_PRINT) . "</pre>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If the campaigns table doesn't exist, run the setup script:</p>";
echo "<a href='../setup-campaign-system.php' style='background: #f97316; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Setup Script</a>";
?>





