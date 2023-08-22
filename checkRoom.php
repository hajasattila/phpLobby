<?php
header("Content-Type: application/json");

// Firebase Realtime Database konfiguráció
$firebase_url = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["roomCode"])) {
    $roomCode = $_GET["roomCode"];

    $firebaseReference = $firebase_url . "rooms/" . $roomCode . ".json";
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $roomData = json_decode($response, true);
    $exists = $roomData !== null;

    echo json_encode(["exists" => $exists]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
