<?php
header("Content-Type: application/json");

// Firebase Realtime Database konfiguráció
$firebase_url = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["roomCode"]) && isset($_POST["sessionId"]) && isset($_POST["guestName"])) {
    $roomCode = $_POST["roomCode"];
    $sessionId = $_POST["sessionId"];
    $guestName = $_POST["guestName"];

    $firebaseReference = $firebase_url . "rooms/" . $roomCode . "/guests/" . $sessionId . ".json";
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($guestName)); // Módosítva: vendég neve JSON kódolt formában
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Invalid request"]);
}
?>
