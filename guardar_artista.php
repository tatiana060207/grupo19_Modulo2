<?php
include 'conexion.php';

$nomArt = $_POST['nomArt'];
$apeArt = $_POST['apeArt'];
$genero = $_POST['genero'];
$ciudadOrig = $_POST['ciudadOrig'];
$idEvento = $_POST['idEvento'];

$sql = "INSERT INTO artistas (nomArt, apeArt, genero, ciudadOrig, idEvento) VALUES ('$nomArt', '$apeArt', '$genero', '$ciudadOrig', '$idEvento')";

if (mysqli_query($con, $sql)) {
    echo "Artista registrado correctamente";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($con);
}

mysqli_close($con);
?>