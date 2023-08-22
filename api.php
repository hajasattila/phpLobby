<?php
header("Content-Type: application/json");

// Firebase Realtime Database konfiguráció
$firebase_url = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

// Szoba létrehozása
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "createRoom") {
    session_start(); // Indítsd el a session-t
    $roomCode = uniqid(); // Példa: egyedi szobakód generálása
    $createdTimestamp = date("Y-m-d H:i:s"); // Aktuális dátum és idő formátumban
    $sessionId = session_id(); // Jelenlegi session azonosító

    // Vendégek adatainak inicializálása üres tömbbel
    $guests = [
        $sessionId => "Létrehozó" // Az első vendég neve
    ];

    // Mentsd el a szoba létrehozó nevét a session-ban
    $_SESSION["roomCreatorName"] = "Létrehozó";

    // Firebase-ba való adatmentés, beleértve a vendégek adatait is
    $data = [
        "roomCode" => $roomCode,
        "createdTimestamp" => $createdTimestamp,
        "guests" => $guests,
        "roomCreator" => $sessionId // Hozzáadva a roomCreator
    ];

    // Firebase adatbázis mentése
    $firebaseReference = $firebase_url . "rooms/" . $roomCode . ".json";
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["success" => true, "roomCode" => $roomCode, "createdTimestamp" => $createdTimestamp, "roomCreator" => $sessionId, "guests" => $guests]);
    exit;
}



// Szoba ellenőrzése
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "checkRoom" && isset($_GET["roomCode"])) {
    // Firebase-ból való adatlekérés
    $firebaseReference = $firebase_url . "rooms/" . $_GET["roomCode"] . ".json";
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Ellenőrzés, hogy a szoba létezik-e a Firebase-ban
    $roomData = json_decode($response, true);
    $exists = $roomData !== null;

    echo json_encode(["exists" => $exists]);
    exit;
}

// Egyéb műveletek kezelése
echo json_encode(["error" => "Invalid request"]);
?>