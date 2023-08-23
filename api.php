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

    // Vendégek adatainak inicializálása üres tömbbel, beleértve a létrehozót is
    $guests = [];

    // Firebase-ba való adatmentés, beleértve a vendégek adatait is
    $data = [
        "roomCode" => $roomCode,
        "createdTimestamp" => $createdTimestamp,
        "guests" => $guests,
        // Módosítva: inicializálva a vendégekkel
        "roomCreator" => $sessionId
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

// Vendégek lekérése egy szobából
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "getGuests" && isset($_GET["roomCode"])) {
    $roomCode = $_GET["roomCode"];

    $firebaseReference = $firebase_url . "rooms/" . $roomCode . "/guests.json"; // A vendégek helye a Firebase-ben
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $guests = json_decode($response, true);

    // Ellenőrizd, hogy van-e vendég, majd küldd vissza a választ
    if ($guests !== null) {
        echo json_encode($guests);
    } else {
        echo json_encode([]);
    }
    exit;
}
// Vendégek frissítése egy szobában
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "updateGuests" && isset($_POST["roomCode"]) && isset($_POST["guests"])) {
    $roomCode = $_POST["roomCode"];
    $guests = json_decode($_POST["guests"], true);

    // Ellenőrizd, hogy van-e már vendég a szobában, és ha van, akkor fűzd hozzá az új vendégeket
    $existingGuests = getExistingGuests($roomCode); // Lekéri a már meglévő vendégeket
    if ($existingGuests !== null) {
        $guests = array_merge($existingGuests, $guests);
    }

    // Ha van már létrehozó, hagyd benne
    if (array_key_exists("Létrehozó", $guests)) {
        $guests["Létrehozó"] = "Létrehozó";
    }

    // Frissítsd a vendéglistát a Firebase adatbázisban
    $firebaseReference = $firebase_url . "rooms/" . $roomCode . "/guests.json";
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($guests));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(["success" => true]);
    exit;
}

// Már meglévő vendégek lekérése
function getExistingGuests($roomCode)
{
    $firebaseReference = $GLOBALS["firebase_url"] . "rooms/" . $roomCode . "/guests.json"; // A vendégek helye a Firebase-ben
    $ch = curl_init($firebaseReference);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $guests = json_decode($response, true);

    return $guests;
}

// Vendég eltávolítása a szobából
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "removeGuestFromRoom" && isset($_POST["roomCode"])) {
    $roomCode = $_POST["roomCode"];
    $sessionId = session_id(); // Az aktuális session azonosító

    $existingGuests = getExistingGuests($roomCode); // Lekéri a már meglévő vendégeket
    if ($existingGuests !== null) {
        // Távolítsd el az aktuális sessionID-t a vendéglistából
        unset($existingGuests[$sessionId]);
    }
    echo json_encode(["success" => true]);
    exit;
}


// SSE formátumban küldi a vendéglistát
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "streamGuests" && isset($_GET["roomCode"])) {
    $roomCode = $_GET["roomCode"];

    header("Content-Type: text/event-stream");
    header("Cache-Control: no-cache");

    while (true) {
        // Firebase-ból való adatlekérés
        $firebaseReference = $firebase_url . "rooms/" . $roomCode . "/guests.json";
        $ch = curl_init($firebaseReference);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $guests = json_decode($response, true);

        echo "data: " . json_encode($guests) . "\n\n";
        flush();

        // Várunk egy rövid ideig, majd frissítjük újra az adatokat
        sleep(2);
    }
}

// Egyéb műveletek kezelése
echo json_encode(["error" => "Invalid request"]);
?>