<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["roomCode"])) {
    $roomCode = $_GET["roomCode"];

    // Firebase Realtime Database URL
    $firebaseUrl = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

    // Ellenőrizzük, hogy a jelenlegi session azonosítója megegyezik-e a szoba létrehozójának session azonosítójával
    $ch = curl_init($firebaseUrl . "rooms/" . $roomCode . ".json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $roomData = json_decode($response, true);
    $roomCreator = $roomData["roomCreator"] ?? ""; // Szoba létrehozó session azonosítója

    if ($roomCreator === session_id()) {
        // Az adatbázisból a szoba törlése
        $path = "rooms/" . $roomCode;
        $firebaseUrlWithRoom = $firebaseUrl . $path . ".json";

        $ch = curl_init($firebaseUrlWithRoom);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            echo "Hiba történt a szoba törlése során.";
        } else {
            echo "Szoba sikeresen törölve!";
        }
    } else {
        echo "Nem rendelkezel jogosultsággal a szoba törléséhez.";
    }
} else {
    echo "Helytelen kérés vagy hiányzó adat.";
}
?>