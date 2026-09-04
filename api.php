<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load environment variables from .env file
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        return;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            putenv("$key=$value");
        }
    }
}

// Load .env file
loadEnv(__DIR__ . '/.env');

$hf_api_key = getenv('HF_API_KEY');

if (!$hf_api_key) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
    
    if (empty($message)) {
        http_response_code(400);
        echo json_encode(['error' => 'No message provided']);
        exit();
    }
    
    // Call Hugging Face API
    $hf_api_url = "https://api-inference.huggingface.co/models/zai-org/GLM-5.3";
    
    $headers = [
        "Authorization: Bearer $hf_api_key",
        "Content-Type: application/json"
    ];
    
    $data = json_encode(['inputs' => $message]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $hf_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code !== 200) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get response from AI']);
        exit();
    }
    
    $result = json_decode($response, true);
    
    // Extract the generated text
    if (is_array($result) && count($result) > 0) {
        $ai_response = $result[0]['generated_text'] ?? 'No response generated';
    } else {
        $ai_response = $result['generated_text'] ?? 'No response generated';
    }
    
    echo json_encode(['response' => $ai_response]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
