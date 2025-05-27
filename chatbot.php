<?php
// chatbot.php

header("Access-Control-Allow-Origin: *"); // Solo para pruebas
header("Content-Type: application/json");

// ✅ Usar variable de entorno (NO pongas la clave directamente aquí)
$apiKey = getenv("OPENAI_API_KEY");

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
curl_close($curl);

if ($err) {
    echo json_encode(["error" => $err]);
} else {
    $data = json_decode($response, true);
    $reply = $data["choices"][0]["message"]["content"] ?? "Sin respuesta.";
    echo json_encode(["respuesta" => $reply]);
}
?>
