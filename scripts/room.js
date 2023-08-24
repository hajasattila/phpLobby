/*room.js*/

// JavaScript kód az AJAX kéréshez
function createRoom() {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "api.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  var sessionId = getSessionId(); // Lekéri a jelenlegi session azonosítót

  var data = `action=createRoom&sessionId=${sessionId}`;

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.success) {
        console.log("Szoba kód: " + response.roomCode);
        console.log("Létrehozó: " + response.roomCreator);
        console.log("Vendégek: " + JSON.stringify(response.guests));

        // Vendég hozzáadása a szobához
        createGuest(response.roomCode, sessionId);

        window.location.href = `szoba.php?roomCode=${response.roomCode}`;
      } else {
        console.log("Hiba történt a szoba létrehozása során.");
      }
    }
  };
  xhr.send(data);
}

function fetchRooms() {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "api.php?action=getRooms", true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var rooms = JSON.parse(xhr.responseText);
      displayRooms(rooms); // Frissített: lekért szobák megjelenítése
    }
  };
  xhr.send();
}

function displayRooms(rooms) {
  var guestList = document.getElementById("guestList"); // Itt a guestList elemre hivatkozunk
  guestList.innerHTML = ""; // Töröljük a jelenlegi tartalmat

  for (var sessionId in rooms) {
    var listItem = document.createElement("li");
    listItem.textContent = rooms[sessionId];
    guestList.appendChild(listItem);
  }
}

function deleteRoom(roomCode) {
  var xhr = new XMLHttpRequest();
  xhr.open("GET", `deleteRoom.php?roomCode=${roomCode}`, true);
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
  var sessionId = getSessionId(); // Lekéri a jelenlegi session azonosítót

  // Ellenőrizzük, hogy van-e olyan szoba a Firebase-ban
  var xhr = new XMLHttpRequest();
  xhr.open("GET", `checkRoom.php?roomCode=${roomCodeInput}`, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.exists) {
        // Ha létezik ilyen szoba, átirányítunk a "szoba.php" oldalra és átadjuk a "roomCode"-ot
        window.location.href = `szoba.php?roomCode=${roomCodeInput}`;
        // Vendég hozzáadása a szobához
        createGuest(roomCodeInput, getSessionId());
      } else {
        // Ha nincs ilyen szoba, kiírjuk a hibaüzenetet
        alert("Nincs ilyen szoba.");
      }
    }
  };
  xhr.send();
}
