<?php
include 'conexion.php';

// Función para agregar departamento
if (isset($_POST['agregar_departamento'])) {
    $nombre_depto = mysqli_real_escape_string($con, $_POST['nombre_departamento']);
    $sql = "INSERT INTO departamentos (nombre) VALUES ('$nombre_depto')";
    if (mysqli_query($con, $sql)) {
        echo "<div class='alert alert-success'>Departamento agregado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($con) . "</div>";
    }
}

// Función para agregar municipio
if (isset($_POST['agregar_municipio'])) {
    $nombre_municipio = mysqli_real_escape_string($con, $_POST['nombre_municipio']);
    $id_departamento = $_POST['id_departamento'];
    $sql = "INSERT INTO municipios (nombre, id_departamento) VALUES ('$nombre_municipio', '$id_departamento')";
    if (mysqli_query($con, $sql)) {
        echo "<div class='alert alert-success'>Municipio agregado correctamente.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($con) . "</div>";
    }
}

// Obtener departamentos para el dropdown
$sql_deptos = "SELECT * FROM departamentos ORDER BY nombre";
$result_deptos = mysqli_query($con, $sql_deptos);
$departamentos = [];
while ($row = mysqli_fetch_assoc($result_deptos)) {
    $departamentos[] = $row;
}

// Obtener municipios con sus departamentos
$sql_municipios = "SELECT m.*, d.nombre as departamento FROM municipios m JOIN departamentos d ON m.id_departamento = d.id ORDER BY d.nombre, m.nombre";
$result_municipios = mysqli_query($con, $sql_municipios);
$municipios = [];
while ($row = mysqli_fetch_assoc($result_municipios)) {
    $municipios[] = $row;
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ubicaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Gestión de Ubicaciones</h1>

        <div class="row">
            <!-- Agregar Departamento -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h3>Agregar Departamento</h3>
                    <form method="post">
                        <div class="mb-3">
                            <label for="nombre_departamento" class="form-label">Nombre del Departamento:</label>
                            <input type="text" id="nombre_departamento" name="nombre_departamento" class="form-control" required>
                        </div>
                        <button type="submit" name="agregar_departamento" class="btn btn-primary">Agregar Departamento</button>
                    </form>
                </div>
            </div>

            <!-- Agregar Municipio -->
            <div class="col-md-6">
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h3>Agregar Municipio</h3>
                    <form method="post">
                        <div class="mb-3">
                            <label for="id_departamento" class="form-label">Seleccionar Departamento:</label>
                            <select id="id_departamento" name="id_departamento" class="form-select" required>
                                <option value="">Seleccione un departamento</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?php echo $depto['id']; ?>"><?php echo $depto['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_municipio" class="form-label">Nombre del Municipio:</label>
                            <input type="text" id="nombre_municipio" name="nombre_municipio" class="form-control" required>
                        </div>
                        <button type="submit" name="agregar_municipio" class="btn btn-primary">Agregar Municipio</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Municipios -->
        <div class="bg-white p-4 rounded shadow">
            <h3>Municipios Registrados</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Departamento</th>
                            <th>Municipio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($municipios as $municipio): ?>
                            <tr>
                                <td><?php echo $municipio['departamento']; ?></td>
                                <td><?php echo $municipio['nombre']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>