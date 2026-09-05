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

$groq_api_key = getenv('GROQ_API_KEY');

if (!$groq_api_key) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured. Please add GROQ_API_KEY to .env file']);
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
    
    // Groq API endpoint
    $groq_api_url = "https://api.groq.com/openai/v1/chat/completions";
    
    $headers = [
        "Authorization: Bearer $groq_api_key",
        "Content-Type: application/json"
    ];
    
    // Prepare the request data
    $data = json_encode([
        'model' => 'mixtral-8x7b-32768',
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are Colli, a helpful AI assistant for coding questions and programming. Provide clear, concise, and practical answers. Help with debugging, code explanations, best practices, and programming concepts.'
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 1024
    ]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $groq_api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    // Log errors for debugging
    if ($http_code !== 200) {
        error_log("Groq API Error - Code: $http_code, Response: $response, cURL Error: $curl_error");
    }
    
    if ($http_code === 429) {
        http_response_code(429);
        echo json_encode(['error' => 'Rate limited. Please try again in a moment.']);
        exit();
    }
    
    if ($http_code === 401) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid API key. Please check your GROQ_API_KEY in .env file']);
        exit();
    }
    
    if ($http_code !== 200) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get response from AI. Error: ' . $http_code . '. Please try again later.']);
        exit();
    }
    
    $result = json_decode($response, true);
    
    // Extract the AI response
    if (isset($result['choices'][0]['message']['content'])) {
        $ai_response = $result['choices'][0]['message']['content'];
    } else {
        $ai_response = 'Sorry, I could not generate a response. Please try again.';
    }
    
    if (empty($ai_response)) {
        $ai_response = "I understood your question but need a moment to generate a response. Please try again.";
    }
    
    echo json_encode(['response' => $ai_response]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
