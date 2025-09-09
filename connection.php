<?php
// Prevent direct access
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    exit('Access denied.');
}

function CONNECTIVITY() {
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "inventory_system"; 

    
    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        error_log('Database connection error: ' . $connection->connect_error);
        exit('Database connection failed. Please try again later.');
    }
    return $connection;
}

function DISCONNECTIVITY($connection) {
    $connection->close();
}

?>