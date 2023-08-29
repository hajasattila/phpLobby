# Lobby (DineDemocracy demo)

## Használt technológiák:
* PHP
* VANILLA JS
* AJAX
* Firebase Realtime DB
* Jelenleg még szükséges a [Xamp](https://www.apachefriends.org/hu/index.html) a futtatáshoz!

## A projekt fő témája egy szavazós játék lenne.
- Egy webes alkalmazás, ami természetesen telefonbarát is egyben, ahol minden féléről dönthetünk a barátainkkal.
- Lehet regisztráció nélkül (guest) és regisztrációval is használni az oldalt.
- Ha csak guestként használjuk az oldalt, meg kell először adnunk egy nevet, majd egy szobát tudunk generálni, ahová linkkel, vagy kóddal lehet belépni másoknak, illetve ha mi kapunk kódot, akkor mi tudunk belépni egy szobába.
- Ha regisztráltként használjuk az oldalt, akkor egy barátlistát tudunk készíteni, és automatikusan el van mentve az adatunk, illetve régebbi szavazásokat vissza tudunk nézni.
- Amint a szoba létrejött és elindul a szavazás, akkor mindenki egy témakörön belül megadhat egy bizonyos dolgot, és akkor arról szavaznak az emberek.

*PL: Egy baráti társaság nem tudja eldönteni, hogy mit egyenek. Mindenki beír egy ételt, ami tetszik neki, és akkor szavaznak róla, de játékosan.
Bal és Jobb oldalon lennének az ételek, és arra kell kattintani (telefonon nyomni) ami jobban tetszik. 
Amire kattintottunk, mindig marad az eredeti oldalon, és amit "leszavaztunk" annak a helyére jön egy új dolog.*

### Ezután 2 opció lenne:
- Amint a szavazásnak vége van, akkor diagramot tekinthetnek meg az userek a szavazás eredményéről, mire mennyien nyomtak stb.
- Egy valamilyen GeoLocation segítségével a felhasználó jelenlegi helyzetét lekérjük, és a Google alapján a választott ételek nevére való kereséssel opciókat mutatunk, hogy a nyertes ételeket hol találhatják meg a legközelebb hozzájuk.

### 2023.08.30. - *Megvalósított dolgok eddig:*
* Index.php indításakor egy sessionID generálódik, ez a localstorageban tárolódik.
* Szobát létre lehet hozni, a "Szoba létrehozása gombbal" ez a szoba megjelenik a Firebase DB-ben.
* Szobát lehet törölni akkor, ha rákattintunk a "Kilépés a szobára", vagy 2percig csak 1 room guest van!
* Ha a szoba létrehozója elhagyja a szobát, mindenkit visszairányít az index-re, illetbe törlődik az adatbázisból a szoba.
* Ha az lép ki csak csatlakozott, akkor őt kiveszi a guests listából az adatbázis, illetve vissza irányítódik az indexre.
* Mindenki kékkel látja a saját azonosítóját, a többiekét pirossal.
* Ha nem jó szobakódot adunk meg a linkben, akkor egy oldalra irányít át, hogy nem létezik ez a kód.
