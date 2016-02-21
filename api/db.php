<?php
function getDB() {
    $dbhost = "localhost";
    $dbuser = "staging_tours";
    $dbpass = "zGkp949?";
    $dbname = "staging_tourscsm";
    $dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $dbConnection;
}
?>