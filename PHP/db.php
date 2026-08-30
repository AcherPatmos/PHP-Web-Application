<?php
// db.php is the single place where the database connection is opened.
// Every page does require_once __DIR__ . '/db.php'; and then uses $conn.

// Pulling in auth.php gives a page the connection,
// the session, and the helper functions all at once.
require_once __DIR__ . '/auth.php';

// XAMPP defaults
$host = 'localhost';
$db   = 'hero_db';
$user = 'root';
$psw  = '';

// Make mysqli throw an exception on failure instead of quietly returning false.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $psw, $db);

    // utf8mb4 so names with accents and apostrophes store correctly.
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    die('Could not connect to the database: ' . $ex->getMessage() .
        '<br>Check that MySQL is running in XAMPP and that you imported heroes.sql.');
}