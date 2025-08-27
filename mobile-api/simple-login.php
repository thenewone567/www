<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'login') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit();
    }
    
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $user_type = $data['user_type'] ?? '';
    
    // Real test credentials for Home Hardware
    $valid_customers = [
        ['username' => 'customer1', 'password' => 'pass123', 'name' => 'John Smith', 'email' => 'john@email.com'],
        ['username' => 'mary', 'password' => 'mary2024', 'name' => 'Mary Johnson', 'email' => 'mary@email.com']
    ];
    
    $valid_contractors = [
        ['username' => 'contractor1', 'password' => 'build123', 'name' => 'ABC Construction', 'email' => 'abc@construction.com'],
        ['username' => 'mike', 'password' => 'mike2024', 'name' => 'Mike Builder Co', 'email' => 'mike@builder.com']
    ];
    
    $found_user = null;
    
    if ($user_type === 'customer') {
        foreach ($valid_customers as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $found_user = $user;
                break;
            }
        }
    } else if ($user_type === 'contractor') {
        foreach ($valid_contractors as $user) {
            if ($user['username'] === $username && $user['password'] === $password) {
                $found_user = $user;
                break;
            }
        }
    }
    
    if ($found_user) {
        echo json_encode([
            'success' => true,
            'token' => 'real_token_' . time() . '_' . rand(1000, 9999),
            'user' => [
                'id' => (string)rand(1, 1000),
                'username' => $found_user['username'],
                'email' => $found_user['email'],
                'user_type' => $user_type,
                'full_name' => $found_user['name']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?>
