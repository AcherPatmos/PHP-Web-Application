<?php
$host = "localhost";
$db   = "hero_db";
$user = "root";
$psw  = "";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $psw, $db);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die("could not connect to the database: " . $e->getMessage() .
        "<br> check that MYSQL is running in Xampp and that you imported the database file");
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

?>