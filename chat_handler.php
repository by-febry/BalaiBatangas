<?php
session_start();
require_once 'connection.php';
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userMessage = $data['message'];

    // Gemini API endpoint
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . GEMINI_API_KEY;

    // Prepare the request payload with context about Batangas festivals
    $payload = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => "You are CHIP (Cultural Heritage Information Provider), a specialized AI assistant 
                                  focused on Batangas festivals, cultural heritage, and traditions. 
                                  Maintain a friendly, informative, and professional tone.
                                  Provide accurate and engaging responses about Batangas-related topics.
                                  User question: " . $userMessage
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 800,
        ]
    ];

    // Initialize cURL session
    $ch = curl_init($url);
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // Execute cURL request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Process the response
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        $aiResponse = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'I apologize, but I could not generate a response.';
        
        echo json_encode([
            'status' => 'success',
            'message' => $aiResponse
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to get response from AI'
        ]);
    }
}
?> 