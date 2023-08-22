<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SZOBA -
        <?php echo $_GET['roomCode']; ?>
    </title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>SZOBA -
            <?php echo $_GET['roomCode']; ?>
        </h1>
        <!-- Kilépés gomb -->
        <button class="button" onclick="confirmExitRoom()">Kilépés a szobából</button>
        <!-- Vendégek megjelenítése -->
        <h2>Vendégek: </h2>
        <ul id="guestList"></ul>
    </div>
    <script>
        // Vendégek megjelenítése
        function displayGuests(guests) {
            var guestList = document.getElementById("guestList");
            guestList.innerHTML = ""; // Töröljük a jelenlegi tartalmat

            for (var sessionId in guests) {
                var listItem = document.createElement("li");
                listItem.textContent = guests[sessionId];
                guestList.appendChild(listItem);
            }
        }

        function exitRoom() {
            var roomCode = "<?php echo $_GET['roomCode']; ?>";

            var xhr = new XMLHttpRequest();
            xhr.open("GET", `/javajatek/deleteRoom.php?roomCode=${roomCode}`, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = "/javajatek/index.php";
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
    </script>
</body>

</html>