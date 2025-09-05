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

// Get the request path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Route handling
if (strpos($path, '/api/chimera/update') !== false) {
    // Handle data updates from CHIMERA system
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
    
} elseif (strpos($path, '/api/chimera/stats') !== false) {
    // Handle data requests from dashboard
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
    } else {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
    
} elseif (strpos($path, '/api/chimera/health') !== false) {
    // Health check endpoint
    http_response_code(200);
    echo json_encode([
        'status' => 'healthy',
        'timestamp' => date('c'),
        'endpoints' => [
            'POST /api/chimera/update' => 'Receive data from CHIMERA system',
            'GET /api/chimera/stats' => 'Get current trading stats',
            'GET /api/chimera/health' => 'Health check'
        ]
    ]);
    
} else {
    // Unknown endpoint
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Endpoint not found']);
}
?>