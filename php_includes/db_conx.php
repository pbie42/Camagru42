<?php
if (ROOT == "/nfs/2015/p/pbie/http/MyWebSite/camagru42"){
    $DB_DSN = "mysql:host=localhost;";
    $DB_USER = "root";
    $DB_PASSWORD = "root";
    $DB_NAME = "camagru";
    $DB_DSN_MADE = "mysql:dbname=camagru;host=localhost;";
} else {
    $DB_DSN = "mysql:host=localhost;";
    $DB_USER = "root";
    $DB_PASSWORD = "root";
    $DB_NAME = "camagru_test";
    $DB_DSN_MADE = "mysql:dbname=camagru;host=localhost;";
}
try {
    $db_conx2 = new PDO($DB_DSN_MADE, $DB_USER, $DB_PASSWORD);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    echo "    this it?";
}
 ?>
