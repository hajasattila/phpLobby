<?php
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["roomCode"])) {
    $roomCode = $_GET["roomCode"];

    // Firebase Realtime Database URL
    $firebaseUrl = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

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
    echo "Helytelen kérés vagy hiányzó adat.";
}
?>