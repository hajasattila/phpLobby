<?php
session_start();
$sessionId = session_id(); // Jelenlegi session azonosító
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOBBY - Vote</title>
    <link rel="stylesheet" href="style/style.css">
    <link rel="icon" type="image/x-icon" href="imgs/icon.png">
</head>

<body>
    <div class="container">
        <p>Az aktuális azonosító: <br><span id="sessionId" data-session-id="<?php echo $sessionId; ?>"></span>

        <h1>LOBBY</h1>
        <button class="button" onclick="createRoom()">Szoba létrehozása</button>
        <div class="input-field">
            <input id="roomCodeInput" type="text" placeholder="Szoba kódja">
            <button class="button" onclick="joinRoom()">Csatlakozás</button>
        </div>
    </div>

    <script>
        // Az oldal betöltésekor elindítja az ellenőrzést az inaktív szobákra
        window.onload = function () {
            var sessionIdElement = document.getElementById("sessionId");
            sessionIdElement.textContent = getSessionId();
            localStorage.setItem("currentSessionId", getSessionId()); // Elmentjük a localStorage-ba
            fetchRooms();
        };
    </script>

    <script src="scripts/room.js"></script>
    <script src="scripts/session.js"></script>
    <script src="scripts/guests.js"></script>
</body>

</html>