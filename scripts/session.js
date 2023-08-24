/*session.js */

// Session azonosító létrehozása vagy lekérése
function getSessionId() {
  var sessionIdElement = document.getElementById("sessionId");
  var sessionId = sessionIdElement.getAttribute("data-session-id");
  return sessionId;
}

// Véletlenszerű session azonosító generálása
function generateSessionId() {
  var characters =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  var sessionId = "";
  for (var i = 0; i < 10; i++) {
    sessionId += characters.charAt(
      Math.floor(Math.random() * characters.length)
    );
  }
  return sessionId;
}

// Aktuális session ID lekérése a localStorage-ból
function getCurrentSessionId() {
  return localStorage.getItem("currentSessionId");
}
