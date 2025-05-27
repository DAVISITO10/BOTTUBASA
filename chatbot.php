<?php
// chatbot.php

header("Access-Control-Allow-Origin: *"); // Solo para pruebas
header("Content-Type: application/json");

// ✅ Usar variable de entorno de forma segura
$apiKey = getenv("OPENAI_API_KEY");

if (!$apiKey) {
    http_response_code(500);
    echo json_encode(["error" => "API Key no configurada."]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$mensaje = $input['mensaje'] ?? '';

if (!$mensaje) {
    echo json_encode(["error" => "Mensaje vacío"]);
    exit;
}

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.openai.com/v1/chat/completions",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "user", "content" => $mensaje]
        ],
        "temperature" => 0.7
    ])
]);

$response = curl_exec($curl);
$err = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Log opcional para depuración
file_put_contents("log.txt", json_encode([
    "mensaje" => $mensaje,
    "httpCode" => $httpCode,
    "error" => $err,
    "respuesta" => $response
], JSON_PRETTY_PRINT));

if ($err) {
    echo json_encode(["error" => "cURL: $err"]);
} elseif ($httpCode >= 400) {
    echo json_encode(["error" => "Error HTTP $httpCode: " . $response]);
} else {
    $data = json_decode($response, true);
    $reply = $data["choices"][0]["message"]["content"] ?? "Sin respuesta de OpenAI.";
    echo json_encode(["respuesta" => $reply]);
}
?>
