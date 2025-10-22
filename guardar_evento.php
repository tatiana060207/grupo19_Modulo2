<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$fechaIni = $_POST['fechaIni'];
$horaIni = $_POST['horaIni'];
$fechaFin = $_POST['fechaFin'];
$horaFin = $_POST['horaFin'];
$municipio = $_POST['municipio'];

$sql = "INSERT INTO eventos (nombre, descripcion, fechaIni, horaIni, fechaFin, horaFin, id_municipio) VALUES ('$nombre', '$descripcion', '$fechaIni', '$horaIni', '$fechaFin', '$horaFin', '$municipio')";

if (mysqli_query($con, $sql)) {
    echo "Evento registrado correctamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
}

mysqli_close($con);
?>