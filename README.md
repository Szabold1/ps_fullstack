# Regisztráció és Bejelentkezés Projekt

## Projekt Leírása

Ez a projekt egy egyszerű regisztrációs és bejelentkezési rendszert valósít meg native PHP-ben. Az adatok tárolása MySQL adatbázisban és fájlban történik. A felhasználók a bejelentkezés után módosíthatják profil adataikat, valamint kijelentkezhetnek.

## Fejlesztési Környezet

- PHP 8.4 vagy újabb
- MySQL 8.0 vagy újabb

## Telepítési Útmutató

1. **Szükséges szoftverek**: Győződj meg róla, hogy a következő szoftverek telepítve vannak:

   - PHP (8.4 vagy újabb)
   - MySQL (8.0 vagy újabb)
   - Composer

2. **Adatbázis Beállítások**:

   - Importáld a `ps_fullstack.sql` fájlt a MySQL adatbázisodba:
     ```bash
     mysql -u felhasznalo -p adatbazis_nev < ps_fullstack.sql
     ```

3. **`.env` Fájl**:

   - Másold a `.env.example` fájlt `.env` néven:

   ```bash
   cp .env.example .env
   ```

   - Töltsd ki a .env fájlt a saját környezetednek megfelelően:

   ```bash
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=adatbazis_nev
   DB_USER=felhasznalo
   DB_PASS=jelszo
   ```

4. Futtasd a következő pár sort a terminálban:

```shell
composer install
php -S localhost:8000
```

6. Látogasd meg a böngészőben a http://localhost:8000/-ot. Ha a 8000-es port foglalt, válassz másik portot.
