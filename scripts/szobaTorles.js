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
  container.innerHTML =
    "<p>A szoba már nem létezik.</p><p><a href='index.php'>Vissza a főoldalra</a></p>";
}
