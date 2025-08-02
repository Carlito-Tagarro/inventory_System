<?php
function CONNECTIVITY() {
    $servername = "localhost"; 
    $username = "root"; 
<<<<<<< HEAD
    $password = ""; 
=======
    $password = "SANTANDER13"; 
>>>>>>> 3156dcf (Add your commit message here)
    $dbname = "inventory_system"; 

    
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