<?php
    // Datos de conexión
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "eventos";

    // Crear conexión
    $con = mysqli_connect($hostname, $username, $password, $database);

    // Verificar la conexión
    if (!$con) {
        die("La conexión falló: " . mysqli_connect_error());
    }
    echo "Conexión exitosa"; // Mensaje de verificación, puedes eliminarlo
?>