<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOBBY</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>LOBBY</h1>
        <button class="button" onclick="createRoom()">Szoba létrehozása</button>
        <div class="input-field">
            <input id="roomCodeInput" type="text" placeholder="Szoba kódja">
            <button class="button">Csatlakozás</button>
        </div>
    </div>

    <script>
        function createRoom() {
            var roomCode = Math.floor(100000 + Math.random() * 900000);
            var createdTimestamp = Date.now(); // Az aktuális időbélyeg (ezredmásodpercekben)

            // Az időbélyeg dátummá alakítása JavaScript-ben
            var createdDate = new Date(createdTimestamp).toISOString();

            // A szoba kód, időbélyeg és session ID mentése a Firebase adatbázisba
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `/javajatek/saveRoom.php?roomCode=${roomCode}&createdTimestamp=${createdDate}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Sikeres válasz esetén itt tudsz további műveleteket végezni
                    window.location.href = `/javajatek/szoba.php?roomCode=${roomCode}`;
                } else if (xhr.readyState === 4) {
                    // Hiba esetén itt tudsz hibakezelést végezni
                    console.log("Hiba történt a szoba mentése során.");
                }
            };
            xhr.send();
        }


        // Az oldal betöltésekor elindítja az ellenőrzést az inaktív szobákra
        window.onload = function () {
            checkInactiveRooms();
            // Az inaktív szobák ellenőrzése minden 5 percben
            setInterval(checkInactiveRooms, 5 * 60 * 1000); // 5 perc * 60 másodperc * 1000 ezredmásodperc
        };

        // Ellenőrzi az inaktív szobákat és törli őket a Firebase-ból
        function checkInactiveRooms() {
            // Itt megvalósítod a Firebase lekérdezést az inaktív szobákra
            // Például lekérdezhetsz minden szobát, ellenőrizheted az időbélyeget,
            // és ha egy szoba inaktív, akkor töröld azt a Firebase-ből
            // Az alábbiak csak egy egyszerű példa:

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "/javajatek/getInactiveRooms.php", true); // Cseréld le a megfelelő elérési útra
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var inactiveRooms = JSON.parse(xhr.responseText);
                    var currentTime = Date.now();

                    inactiveRooms.forEach(function (room) {
                        if (currentTime - room.createdTimestamp >= 30 * 60 * 1000) { // 30 perc inaktivitás
                            deleteRoom(room.roomCode);
                        }
                    });
                }
            };
            xhr.send();
        }

        // Törli a megadott szobát a Firebase-ból
        function deleteRoom(roomCode) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `/javajatek/deleteRoom.php?roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(`Szoba (${roomCode}) sikeresen törölve.`);
                } else if (xhr.readyState === 4) {
                    console.log(`Hiba történt a szoba (${roomCode}) törlése során.`);
                }
            };
            xhr.send();
        }

        // A "Csatlakozás" gomb kattintásának kezelése
        var joinButton = document.querySelector(".input-field button");
        joinButton.addEventListener("click", function () {
            joinRoom();
        });

        function joinRoom() {
            var roomCodeInput = document.getElementById("roomCodeInput").value;

            // Ellenőrizzük, hogy van-e olyan szoba a Firebase-ban
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `/javajatek/checkRoom.php?roomCode=${roomCodeInput}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.exists) {
                        // Ha létezik ilyen szoba, átirányítás a szoba oldalra
                        window.location.href = `/javajatek/szoba.php?roomCode=${roomCodeInput}`;
                    } else {
                        // Ha nincs ilyen szoba, kiírjuk a hibaüzenetet
                        alert("Nincs ilyen szoba.");
                    }
                }
            };
            xhr.send();
        }

        document.title = "LOBBY"; // Az index oldal címe


    </script>
</body>

</html>