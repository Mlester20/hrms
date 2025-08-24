<?php
header('Content-Type: application/json');
require_once '../components/config.php';

// Your OpenRouter API key
$OPENROUTER_API_KEY = "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";
$SITE_URL = "http://localhost/hrms";
$SITE_NAME = "Hotel Management System";

// Rate limiting configuration
$RATE_LIMIT_FILE = '../temp/rate_limit.json';
$MAX_REQUESTS_PER_MINUTE = 10; // Adjust based on your API limits
$MAX_REQUESTS_PER_HOUR = 200;  // Adjust based on your API limits

function checkRateLimit() {
    global $RATE_LIMIT_FILE, $MAX_REQUESTS_PER_MINUTE, $MAX_REQUESTS_PER_HOUR;
    
    // Create temp directory if it doesn't exist
    $tempDir = dirname($RATE_LIMIT_FILE);
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    
    $now = time();
    $rateData = [];
    
    // Load existing rate limit data
    if (file_exists($RATE_LIMIT_FILE)) {
        $rateData = json_decode(file_get_contents($RATE_LIMIT_FILE), true) ?: [];
    }
    
    // Clean old entries (older than 1 hour)
    $rateData = array_filter($rateData, function($timestamp) use ($now) {
        return ($now - $timestamp) < 3600; // 1 hour
    });
    
    // Count recent requests
    $recentRequests = array_filter($rateData, function($timestamp) use ($now) {
        return ($now - $timestamp) < 60; // 1 minute
    });
    
    // Check limits
    if (count($recentRequests) >= $MAX_REQUESTS_PER_MINUTE) {
        throw new Exception('Rate limit exceeded: too many requests per minute. Please wait a moment.');
    }
    
    if (count($rateData) >= $MAX_REQUESTS_PER_HOUR) {
        throw new Exception('Rate limit exceeded: too many requests per hour. Please try again later.');
    }
    
    // Add current request
    $rateData[] = $now;
    
    // Save updated rate data
    file_put_contents($RATE_LIMIT_FILE, json_encode($rateData));
    
    return true;
}

function makeAPIRequest($messages, $retryCount = 0) {
    global $OPENROUTER_API_KEY, $SITE_URL, $SITE_NAME;
    
    // Alternative models to try if the primary fails
    $models = [
        'deepseek/deepseek-r1-0528:free',
        'meta-llama/llama-3.2-3b-instruct:free',
        'microsoft/phi-3-mini-128k-instruct:free',
        'google/gemma-2-9b-it:free'
    ];
    
    $currentModel = $models[$retryCount % count($models)];
    
    // Initialize cURL session
    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    
    // Set cURL options with longer timeout
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 30, // 30 seconds timeout
        CURLOPT_CONNECTTIMEOUT => 10, // 10 seconds connection timeout
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $OPENROUTER_API_KEY,
            'HTTP-Referer: ' . $SITE_URL,
            'X-Title: ' . $SITE_NAME
        ],
        CURLOPT_POSTFIELDS => json_encode([
            'model' => $currentModel,
            'messages' => $messages,
            'max_tokens' => 500, // Limit response length
            'temperature' => 0.7
        ])
    ]);
    
    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    
    curl_close($ch);
    
    // Handle cURL errors
    if ($curlError) {
        throw new Exception('Connection error: ' . $curlError);
    }
    
    // Handle HTTP errors with retry logic
    if ($httpCode === 429) {
        if ($retryCount < 3) {
            // Wait before retry (exponential backoff)
            sleep(pow(2, $retryCount));
            return makeAPIRequest($messages, $retryCount + 1);
        } else {
            throw new Exception('Service temporarily unavailable due to high demand. Please try again in a few minutes.');
        }
    }
    
    if ($httpCode !== 200) {
        // Try next model if available
        if ($retryCount < count($models) - 1) {
            return makeAPIRequest($messages, $retryCount + 1);
        }
        throw new Exception('Service temporarily unavailable. Please try again later.');
    }
    
    $result = json_decode($response, true);
    
    if (!$result || !isset($result['choices'][0]['message']['content'])) {
        if ($retryCount < count($models) - 1) {
            return makeAPIRequest($messages, $retryCount + 1);
        }
        throw new Exception('Invalid response from service');
    }
    
    return $result;
}

function getFallbackResponse($userMessage) {
    // Simple keyword-based fallback responses
    $message = strtolower($userMessage);
    
    if (strpos($message, 'booking') !== false || strpos($message, 'reservation') !== false) {
        return "I'd be happy to help with your booking! For room reservations, you can check our availability on our main booking page or call our front desk. Our standard check-in is at 3:00 PM and check-out is at 11:00 AM.";
    }
    
    if (strpos($message, 'amenities') !== false || strpos($message, 'services') !== false) {
        return "Our hotel offers various amenities including: free WiFi, fitness center, restaurant, room service, laundry service, and 24/7 front desk assistance. Is there a specific service you'd like to know more about?";
    }
    
    if (strpos($message, 'payment') !== false || strpos($message, 'credit card') !== false) {
        return "We accept all major credit cards (Visa, Mastercard, American Express), debit cards, and cash payments. Payment is typically processed at check-in or check-out.";
    }
    
    if (strpos($message, 'check-in') !== false || strpos($message, 'check in') !== false) {
        return "Our standard check-in time is 3:00 PM. Early check-in may be available depending on room availability. Please contact our front desk for arrangements.";
    }
    
    if (strpos($message, 'check-out') !== false || strpos($message, 'check out') !== false) {
        return "Check-out time is 11:00 AM. Late check-out may be available upon request and may incur additional charges.";
    }
    
    return "Thank you for contacting us! I'm currently experiencing high demand, but I'm here to help. For immediate assistance with bookings, services, or any hotel-related questions, please feel free to call our front desk or visit our main booking page.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $userMessage = $data['message'] ?? '';
        $history = $data['history'] ?? [];

        if (empty($userMessage)) {
            throw new Exception('Message is required');
        }

        // Check rate limits
        checkRateLimit();

        // Prepare messages for API
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a helpful hotel customer service assistant. Provide friendly, professional, and concise responses about hotel services, bookings, amenities, and policies. Keep responses under 200 words and focus on being helpful while maintaining a courteous tone.'
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ];

        // Make API request
        $result = makeAPIRequest($messages);
        
        echo json_encode([
            'success' => true,
            'message' => $result['choices'][0]['message']['content']
        ]);

    } catch (Exception $e) {
        // Use fallback response for rate limiting or API errors
        $fallbackResponse = getFallbackResponse($data['message'] ?? '');
        
        echo json_encode([
            'success' => true,
            'message' => $fallbackResponse,
            'fallback' => true,
            'note' => 'Response generated using fallback system due to high demand.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}