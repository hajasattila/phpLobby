<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["roomCode"]) && isset($_GET["createdTimestamp"])) {
    $roomCode = $_GET["roomCode"];
    $createdTimestamp = $_GET["createdTimestamp"];
    $creatorSessionId = session_id();

    // Az időbélyeg dátummá alakítása PHP-ben
    $createdDate = date("Y-m-d H:i:s", strtotime($createdTimestamp));

    // Firebase Realtime Database URL
    $firebaseUrl = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";

    // Az adatbázisban az elérési út létrehozása
    $path = "rooms/" . $roomCode;

    // A Firebase adatokat JSON formátumba konvertálása
    $data = json_encode([
        "roomCode" => $roomCode,
        "creatorSessionId" => $creatorSessionId,
        "createdDate" => $createdDate
    ]);

    // Firebase-hez való kapcsolódás
    $ch = curl_init($firebaseUrl . $path . ".json");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        echo "Hiba történt a szoba mentése során.";
    } else {
        echo "Szoba sikeresen létrehozva és mentve!";
    }
} else {
    echo "Helytelen kérés vagy hiányzó adat.";
}
?>