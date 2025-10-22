<?php
include 'conexion.php';

$departamento_id = $_GET['departamento'];

$sql = "SELECT id, nombre FROM municipios WHERE id_departamento = '$departamento_id' ORDER BY nombre";
$result = mysqli_query($con, $sql);

$municipios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $municipios[] = $row;
}

mysqli_close($con);

header('Content-Type: application/json');
echo json_encode($municipios);
?>