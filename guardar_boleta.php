<?php
include 'conexion.php';

$evento = $_POST['evento'];
$localidad = $_POST['localidad'];
$valor = $_POST['valor'];
$cantidad = $_POST['cantidad'];

$sql = "INSERT INTO boletas (evento_id, localidad, valor, cantidad_disponible) VALUES ('$evento', '$localidad', '$valor', '$cantidad')";

if (mysqli_query($con, $sql)) {
    echo "Boleta guardada correctamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
}

mysqli_close($con);
?>