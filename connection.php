<?php
function CONNECTIVITY() {
    $servername = "sql210.infinityfree.com"; 
    $username = "if0_39703877"; 
    $password = "lAVNKIaYL4S"; 
    $dbname = "if0_39703877_inventory_system"; 

    
    $connection = new mysqli($servername, $username, $password, $dbname);

    if ($connection->connect_error) {
        die("Connection failure " . $connection->connect_error);
    }
    return $connection;
}

function DISCONNECTIVITY($connection) {
    $connection->close();
}

?>