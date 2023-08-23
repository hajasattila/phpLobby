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
        // Az adatbázisból eltávolítjuk az aktuális sessionID-t
        $firebaseReference = $firebaseUrl . "rooms/" . $roomCode . "/guests.json";
        $ch = curl_init($firebaseReference);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $guests = json_decode($response, true);

        // Távolítsuk el az aktuális sessionID-t a vendéglistából
        if ($guests !== null) {
            $sessionId = session_id();
            if (isset($guests[$sessionId])) {
                unset($guests[$sessionId]);

                // Frissítsük a vendéglistát a Firebase adatbázisban
                $ch = curl_init($firebaseReference);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($guests));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
            }
        }

        echo "Kiléptél a szobából. Az adatbázisból eltávolítottuk a sessionID-d.";
    }
} else {
    echo "Helytelen kérés vagy hiányzó adat.";
}
?>