<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SZOBA -
        <?php echo $_GET['roomCode']; ?>
    </title>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <div class="container">

        <?php
        // Ellenőrizzük, hogy van-e ilyen szoba a Firebase adatbázisban
        $roomCode = $_GET['roomCode'];
        $firebaseUrl = "https://javajatek-bc965-default-rtdb.europe-west1.firebasedatabase.app/";
        $ch = curl_init($firebaseUrl . "rooms/" . $roomCode . ".json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === 'null') {
            echo "<p>Az adott szobakód nem található.</p>";
            echo "<p><a href='index.php'>Vissza a főoldalra</a></p>";
        } else {
            echo "<h1>SZOBA - $roomCode</h1>";
            echo "<!-- Kilépés gomb -->";
            echo "<button class='button' onclick='confirmExitRoom()'>Kilépés a szobából</button>";
            echo "<!-- Vendégek megjelenítése -->";
            echo "<h2>Vendégek:</h2>";
            echo "<ul id='guestList'></ul>";
        }
        ?>
    </div>
    <script>
        function addGuest() {
            var guestNameInput = document.getElementById("guestNameInput");
            var guestName = guestNameInput.value.trim();

            if (guestName !== "") {
                var roomCode = "<?php echo $_GET['roomCode']; ?>";

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "api.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            guestNameInput.value = ""; // Töröljük a beviteli mező tartalmát
                            fetchGuests(); // Frissítjük a vendéglistát
                        } else {
                            console.log("Hiba történt a vendég hozzáadása során.");
                        }
                    }
                };
                xhr.send(`action=addGuest&roomCode=${roomCode}&guestName=${encodeURIComponent(guestName)}`);
            }
        }

        function fetchGuests() {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";
            var retrievedSessionId = localStorage.getItem("currentSessionId");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `api.php?action=getGuests&roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var guests = JSON.parse(xhr.responseText);
                    displayGuests(guests, retrievedSessionId); // Átadjuk a roomCode-ot is
                }
            };
            xhr.send();
        }


        // Vendégek megjelenítése
        function displayGuests(guests, roomCreatorId) {
            var guestList = document.getElementById("guestList");
            guestList.innerHTML = ""; // Töröljük a jelenlegi tartalmat

            var guestNumber = 1; // Az első vendég sorszáma


            for (var sessionId in guests) {
                var listItem = document.createElement("li");
                var guestName = guests[sessionId];

                // Elnevezés hozzáadása a vendég nevéhez
                var formattedGuestName = "Vendég " + guestNumber + ": ";

                // Ellenőrzés, hogy a vendég a szoba létrehozója-e
                if (guests[sessionId] === roomCreatorId) {
                    formattedGuestName += "<span class='creator'>" + guestName + "</span>";
                } else {
                    formattedGuestName += "<span class='creatorr'>" + guestName + "</span>";
                }

                listItem.innerHTML = formattedGuestName;
                guestList.appendChild(listItem);

                guestNumber++; // Következő vendég sorszáma
            }
        }



        // Frissítse a vendéglistát minden 3 másodpercben
        function setupGuestListUpdater() {
            setInterval(function () {
                fetchGuests();
            }, 500); // 3000 milliszekundum = 3 másodperc
        }

        function exitRoom() {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";

            var xhr = new XMLHttpRequest();
            xhr.open("GET", `deleteRoom.php?roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = "index.php";
                } else if (xhr.readyState === 4) {
                    console.log("Hiba történt a szoba törlése során.");
                }
            };
            xhr.send();
        }

        // A kilépés megerősítését kérdező függvény
        function confirmExitRoom() {
            if (window.confirm("Biztosan kilép a szobából?")) {
                exitRoom(); // Kilépés végrehajtása
            }
        }


        // Az oldal betöltésekor frissítsük a vendéglistát
        window.onload = function () {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";
            fetchGuests();
            setupGuestListUpdater();
            setupRoomCheckInterval();
            startInterval(); // Indítsuk el az időzített feladatot

            // Ellenőrizzük a szobát a Firebase adatbázisban és állítsuk be a roomCreatorId-t
            checkRoomCreator(roomCode);
        }

        // Ellenőrizzük a szobát a Firebase adatbázisban és állítsuk be a roomCreatorId-t
        function checkRoomCreator(roomCode) {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `api.php?action=getRoomCreator&roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var roomCreatorId = xhr.responseText;
                    fetchGuests(roomCreatorId); // Átadjuk a roomCreatorId-t a fetchGuests függvénynek
                }
            };
            xhr.send();
        }

        // Időzített feladat elindítása
        function startInterval() {
            setInterval(function () {
                checkAndUpdateGuests();
            }, 120000); // 120000 ms 2perc
        }

        // Vendégek ellenőrzése és frissítése
        function checkAndUpdateGuests() {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `api.php?action=getGuests&roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var guests = JSON.parse(xhr.responseText);
                    if (guests !== null && Object.keys(guests).length === 1) {
                        // Ellenőrizzük, hogy csak egy vendég van a szobában
                        // Frissítsd a vendéglistát a szerverrel
                        updateGuestsOnServer(guests);
                        alert("A szoba törölve lett. 😞");
                        window.location.href = "index.php"; // Átirányítás az index.php-re
                    }
                }
            };
            xhr.send();
        }

        // Vendégek frissítése a szerverrel
        function updateGuestsOnServer(guests) {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "api.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Sikeresen frissítettük a szerveren a vendéglistát
                        // Ellenőrizzük, hogy csak egy vendég van, és ha igen, jelenítünk egy üzenetet
                        if (Object.keys(guests).length === 1) {
                            alert("A szoba törölve lett. 😞");
                            window.location.href = "index.php"; // Átirányítás az index.php-re
                        }
                    }
                }
            };
            var params = `action=updateGuests&roomCode=${roomCode}&guests=${encodeURIComponent(JSON.stringify(guests))}`;
            xhr.send(params);
        }

        // Az szoba ellenőrzését időzítetten végző függvény
        function setupRoomCheckInterval() {
            setInterval(function () {
                checkRoomExistence();
            }, 5000); // 5000 ms = 5 másodperc
        }

        // Szoba létezésének ellenőrzése
        function checkRoomExistence() {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `api.php?action=checkRoom&roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (!response.exists) {
                        roomDoesNotExist();
                    }
                }
            };
            xhr.send();
        }

        // Ha a szoba nem létezik, jelezzük ezt és irányítsunk vissza az index.php-re
        function roomDoesNotExist() {
            var container = document.querySelector(".container");
            container.innerHTML = "<p>A szoba már nem létezik.</p><p><a href='index.php'>Vissza a főoldalra</a></p>";
        }
    </script>


</body>

</html>