<?php
header("Content-Type: application/json");

// YOUR OPENROUTER API KEY GOES HERE
$OPENROUTER_API_KEY = "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";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE);

$user_message = $input['message'] ?? '';

if (empty($user_message)) {
    http_response_code(400);
    echo json_encode(["error" => "Message cannot be empty"]);
    exit;
}

// Prepare the payload for OpenRouter
$payload = [
    "model" => "openai/gpt-oss-120b:free",
    "messages" => [
        [
            "role" => "system",
            "content" => "You are the Lazy Barber AI assistant. You help customers find barbers, answer questions about haircuts, and provide polite, concise customer service. Keep answers brief (max 2-3 sentences)."
        ],
        [
            "role" => "user",
            "content" => $user_message
        ]
    ]
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $OPENROUTER_API_KEY,
    "HTTP-Referer: http://localhost", // Optional, for OpenRouter rankings
    "X-Title: Lazy Barber App", // Optional, for OpenRouter rankings
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode !== 200) {
    echo json_encode(["error" => "Failed to communicate with AI.", "details" => $response]);
    exit;
}

$data = json_decode($response, true);
$ai_message = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not understand that.';

echo json_encode(["reply" => $ai_message]);
?>
