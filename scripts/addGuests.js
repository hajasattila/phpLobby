function addGuest() {
  var guestNameInput = document.getElementById("guestNameInput");
  var guestName = guestNameInput.value.trim();

  if (guestName !== "") {
    var roomCode = "<?php echo $_GET['roomCode']; ?>";

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "./API/api.php", true);
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
    xhr.send(
      `action=addGuest&roomCode=${roomCode}&guestName=${encodeURIComponent(
        guestName
      )}`
    );
  }
}
