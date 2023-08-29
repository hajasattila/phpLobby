/*guests.js*/

function createGuest(roomCode, sessionId) {
  var guests = {}; // Üres objektum a vendégeknek
  guests[sessionId] = sessionId; // Hozzáadja az új vendéget a listához

  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./API/api.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.success) {
        console.log("Vendég hozzáadva: GUEST " + sessionId);
      } else {
        console.log("Hiba történt a vendég hozzáadása során.");
      }
    }
  };
  xhr.send(
    `action=updateGuests&roomCode=${roomCode}&guests=${encodeURIComponent(
      JSON.stringify(guests)
    )}`
  );
}

function updateGuestsList(roomCode, guests) {
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "./API/api.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      var response = JSON.parse(xhr.responseText);
      if (response.success) {
        console.log("Vendég hozzáadva: GUEST " + sessionId);
      } else {
        console.log("Hiba történt a vendég hozzáadása során.");
      }
    }
  };
  xhr.send(
    `action=updateGuests&roomCode=${roomCode}&guests=${encodeURIComponent(
      JSON.stringify(guests)
    )}`
  );
}
