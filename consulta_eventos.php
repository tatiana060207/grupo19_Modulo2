<?php
include 'conexion.php';

// Obtener filtros
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';
$departamento = isset($_GET['departamento']) ? $_GET['departamento'] : '';
$municipio = isset($_GET['municipio']) ? $_GET['municipio'] : '';

// Obtener departamentos para el filtro
$sql_deptos = "SELECT * FROM departamentos ORDER BY nombre";
$result_deptos = mysqli_query($con, $sql_deptos);
$departamentos = [];
while ($row = mysqli_fetch_assoc($result_deptos)) {
    $departamentos[] = $row;
}

// Construir consulta base
$sql = "SELECT e.*, m.nombre as municipio, d.nombre as departamento
        FROM eventos e
        LEFT JOIN municipios m ON e.id_municipio = m.id
        LEFT JOIN departamentos d ON m.id_departamento = d.id
        WHERE 1=1";

$params = [];
$types = "";

// Agregar filtros
if (!empty($fecha)) {
    $sql .= " AND DATE(e.fechaIni) = ?";
    $params[] = $fecha;
    $types .= "s";
}

if (!empty($departamento)) {
    $sql .= " AND d.id = ?";
    $params[] = $departamento;
    $types .= "i";
}

if (!empty($municipio)) {
    $sql .= " AND m.id = ?";
    $params[] = $municipio;
    $types .= "i";
}

$sql .= " ORDER BY e.fechaIni ASC";

$stmt = mysqli_prepare($con, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$eventos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $eventos[] = $row;
}

// Para cada evento, obtener artistas y boletas
foreach ($eventos as &$evento) {
    // Obtener artistas
    $sql_artistas = "SELECT * FROM artistas WHERE idEvento = ?";
    $stmt_artistas = mysqli_prepare($con, $sql_artistas);
    mysqli_stmt_bind_param($stmt_artistas, "i", $evento['id']);
    mysqli_stmt_execute($stmt_artistas);
    $result_artistas = mysqli_stmt_get_result($stmt_artistas);
    $evento['artistas'] = [];
    while ($artista = mysqli_fetch_assoc($result_artistas)) {
        $evento['artistas'][] = $artista;
    }

    // Obtener boletas
    $sql_boletas = "SELECT * FROM boletas WHERE evento_id = ?";
    $stmt_boletas = mysqli_prepare($con, $sql_boletas);
    mysqli_stmt_bind_param($stmt_boletas, "i", $evento['id']);
    mysqli_stmt_execute($stmt_boletas);
    $result_boletas = mysqli_stmt_get_result($stmt_boletas);
    $evento['boletas'] = [];
    while ($boleta = mysqli_fetch_assoc($result_boletas)) {
        $evento['boletas'][] = $boleta;
    }
}

mysqli_close($con);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Eventos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Consulta de Eventos</h1>

        <!-- Filtros -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <h3>Filtros de Búsqueda</h3>
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha; ?>">
                </div>
                <div class="col-md-4">
                    <label for="departamento" class="form-label">Departamento:</label>
                    <select id="departamento" name="departamento" class="form-select" onchange="cargarMunicipios()">
                        <option value="">Todos los departamentos</option>
                        <?php foreach ($departamentos as $depto): ?>
                            <option value="<?php echo $depto['id']; ?>" <?php echo ($departamento == $depto['id']) ? 'selected' : ''; ?>><?php echo $depto['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="municipio" class="form-label">Municipio:</label>
                    <select id="municipio" name="municipio" class="form-select">
                        <option value="">Todos los municipios</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="consulta_eventos.php" class="btn btn-secondary">Limpiar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Resultados -->
        <div class="row">
            <?php if (empty($eventos)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No se encontraron eventos con los criterios de búsqueda.</div>
                </div>
            <?php else: ?>
                <?php foreach ($eventos as $evento): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($evento['nombre']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($evento['descripcion']); ?></p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <strong>Ubicación:</strong> <?php echo htmlspecialchars($evento['municipio'] . ', ' . $evento['departamento']); ?><br>
                                        <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($evento['fechaIni'])); ?><br>
                                        <strong>Hora:</strong> <?php echo date('H:i', strtotime($evento['horaIni'])); ?> - <?php echo date('H:i', strtotime($evento['horaFin'])); ?>
                                    </small>
                                </p>

                                <?php if (!empty($evento['artistas'])): ?>
                                    <h6>Artistas:</h6>
                                    <ul class="list-unstyled">
                                        <?php foreach ($evento['artistas'] as $artista): ?>
                                            <li><small><?php echo htmlspecialchars($artista['nomArt'] . ' ' . $artista['apeArt']); ?> (<?php echo htmlspecialchars($artista['genero']); ?>)</small></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if (!empty($evento['boletas'])): ?>
                                    <h6>Boletas Disponibles:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Localidad</th>
                                                    <th>Valor</th>
                                                    <th>Disponibles</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($evento['boletas'] as $boleta): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($boleta['localidad']); ?></td>
                                                        <td>$<?php echo number_format($boleta['valor'], 0, ',', '.'); ?></td>
                                                        <td><?php echo $boleta['cantidad_disponible']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarMunicipios() {
            const deptoId = document.getElementById('departamento').value;
            const municipioSelect = document.getElementById('municipio');

            if (deptoId) {
                fetch(`obtener_municipios.php?departamento=${deptoId}`)
                    .then(response => response.json())
                    .then(data => {
                        municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';
                        data.forEach(municipio => {
                            municipioSelect.innerHTML += `<option value="${municipio.id}">${municipio.nombre}</option>`;
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                municipioSelect.innerHTML = '<option value="">Todos los municipios</option>';
            }
        }

        // Cargar municipios si hay un departamento seleccionado
        document.addEventListener('DOMContentLoaded', function() {
            const deptoSelect = document.getElementById('departamento');
            if (deptoSelect.value) {
                cargarMunicipios();
            }
        });
    </script>
</body>
</html>