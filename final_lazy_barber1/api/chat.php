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

require_once '../config/database.php';

// Dynamically fetch platform context to "train" the AI
$shopsContext = "Available Shops:\n";
$shopStmt = $pdo->query("SELECT name, suburb FROM SHOPS LIMIT 10");
while($s = $shopStmt->fetch()) {
    $shopsContext .= "- " . $s['name'] . " in " . $s['suburb'] . "\n";
}

$servicesContext = "Popular Services:\n";
$svcStmt = $pdo->query("SELECT name, price_aud FROM SERVICES LIMIT 5");
while($svc = $svcStmt->fetch()) {
    $servicesContext .= "- " . $svc['name'] . " ($" . $svc['price_aud'] . ")\n";
}

$system_prompt = "You are the Lazy Barber AI assistant. You help customers navigate our multi-shop barber booking platform based in Sydney. 
Here is your live platform knowledge:
$shopsContext
$servicesContext

Instructions for Booking: Tell the user to create an account, go to the 'Shops' page, choose a shop, select their preferred barber, and pick an available time slot.
Rules: Be extremely polite, concise (max 2-3 sentences per reply), and only answer questions related to haircuts, barbers, or using this booking app.";

// Prepare the payload for OpenRouter
$payload = [
    "model" => "openai/gpt-oss-120b:free",
    "messages" => [
        [
            "role" => "system",
            "content" => $system_prompt
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

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Bypass SSL verification for local XAMPP
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($httpcode !== 200 || $response === false) {
    echo json_encode(["error" => "Failed to communicate with AI.", "details" => $response, "curl_error" => $curl_error]);
    exit;
}

$data = json_decode($response, true);
$ai_message = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not understand that.';

echo json_encode(["reply" => $ai_message]);
?>
