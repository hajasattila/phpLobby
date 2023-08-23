<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SZOBA -
        <?php echo $_GET['roomCode']; ?>
    </title>
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            text-align: center;
        }

        .creator {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>SZOBA -
            <?php echo $_GET['roomCode']; ?>
        </h1>
        <!-- Kilépés gomb -->
        <button class="button" onclick="confirmExitRoom()">Kilépés a szobából</button>
        <!-- Vendég hozzáadás form -->
        <!-- Vendégek megjelenítése -->
        <h2>Vendégek:</h2>
        <ul id="guestList"></ul>
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
            var xhr = new XMLHttpRequest();
            xhr.open("GET", `api.php?action=getGuests&roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var guests = JSON.parse(xhr.responseText);
                    displayGuests(guests);
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
                if (sessionId === roomCreatorId) {
                    formattedGuestName += "<span class='creator'>" + guestName + "</span>";
                } else {
                    formattedGuestName += guestName;
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
            fetchGuests();
            setupGuestListUpdater();
            startInterval(); // Indítsuk el az időzített feladatot
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




    </script>
</body>

</html>