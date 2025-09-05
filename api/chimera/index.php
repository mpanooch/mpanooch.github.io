<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuration
$data_file = 'chimera_data.json';
$log_file = 'chimera_log.txt';

// Function to log requests
function logRequest($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Function to save data
function saveData($data) {
    global $data_file;
    $data['last_updated'] = date('c');
    return file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

// Function to load data
function loadData() {
    global $data_file;
    if (file_exists($data_file)) {
        $content = file_get_contents($data_file);
        return json_decode($content, true);
    }
    return null;
}

// Simple routing based on query parameter
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update') {
    // Handle data updates from CHIMERA system
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data) {
        if (saveData($data)) {
            logRequest("Data updated successfully");
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Data updated']);
        } else {
            logRequest("Failed to save data");
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to save data']);
        }
    } else {
        logRequest("Invalid JSON data received");
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'stats') {
    // Handle data requests from dashboard
    $data = loadData();
    
    if ($data) {
        // Check if data is recent (within last 30 seconds)
        $last_updated = strtotime($data['last_updated']);
        $now = time();
        
        if (($now - $last_updated) > 30) {
            $data['status'] = 'stale';
            $data['message'] = 'Data is older than 30 seconds';
        } else {
            $data['status'] = 'active';
        }
        
        logRequest("Stats requested - data age: " . ($now - $last_updated) . " seconds");
        http_response_code(200);
        echo json_encode($data);
    } else {
        logRequest("No data available");
        http_response_code(404);
        echo json_encode([
            'status' => 'no_data',
            'message' => 'No CHIMERA data available',
            'performance' => [
                'total_pnl' => 0,
                'pnl_percent' => 0,
                'win_rate' => 0,
                'total_trades' => 0,
                'balance' => 10000
            ],
            'regime' => [
                'current' => 'UNKNOWN',
                'confidence' => 0
            ],
            'market' => [
                'BTCUSDT' => 0,
                'ETHUSDT' => 0,
                'SOLUSDT' => 0
            ],
            'system' => [
                'processing_speed' => 0,
                'total_ticks' => 0,
                'signals_generated' => 0,
                'model_accuracy' => 0
            ],
            'recent_trades' => []
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'health') {
    // Health check endpoint
    http_response_code(200);
    echo json_encode([
        'status' => 'healthy',
        'timestamp' => date('c'),
        'endpoints' => [
            'POST ?action=update' => 'Receive data from CHIMERA system',
            'GET ?action=stats' => 'Get current trading stats',
            'GET ?action=health' => 'Health check'
        ]
    ]);
    
} else {
    // Unknown endpoint or method
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
}
?>
