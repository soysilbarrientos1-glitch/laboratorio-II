<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('No autorizado');
}

include '../includes/db.php';

$especialista_id = filter_input(INPUT_GET, 'especialista', FILTER_VALIDATE_INT);
$fecha = $_GET['fecha'] ?? '';

if (!$especialista_id || empty($fecha) || !strtotime($fecha)) {
    echo json_encode([]);
    exit;
}

// Obtener día de la semana (1=lunes, 7=domingo)
$dia_semana = date('N', strtotime($fecha));

// Paso 1: Obtener horario laboral del especialista (si usas la tabla horarios_disponibles)
$horas_disponibles = [];

// Si usas la tabla horarios_disponibles:
$stmt = $conn->prepare("SELECT hora_inicio, hora_fin FROM horarios_disponibles WHERE id_especialista = ? AND dia_semana = ?");
$stmt->bind_param("ii", $especialista_id, $dia_semana);
$stmt->execute();
$horario = $stmt->get_result()->fetch_assoc();

if ($horario) {
    // Generar bloques de 60 minutos
    $inicio = strtotime($horario['hora_inicio']);
    $fin = strtotime($horario['hora_fin']);
    
    while ($inicio < $fin) {
        $hora = date('H:i', $inicio);
        $horas_disponibles[] = $hora;
        $inicio += 60 * 60; // +1 hora
    }
} else {
    // Alternativa: usar horario fijo (10:00 a 17:00)
    $horas_disponibles = ['10:00', '11:00', '12:00', '14:00', '15:00', '16:00'];
}

// Paso 2: Obtener horas ya ocupadas
$stmt = $conn->prepare("SELECT hora FROM citas WHERE id_empleado = ? AND fecha = ? AND estado != 'cancelada'");
$stmt->bind_param("is", $especialista_id, $fecha);
$stmt->execute();
$result = $stmt->get_result();

$ocupadas = [];
while ($row = $result->fetch_assoc()) {
    $ocupadas[] = $row['hora'];
}

// Paso 3: Filtrar horas disponibles
$horas_libres = array_diff($horas_disponibles, $ocupadas);
sort($horas_libres);

echo json_encode(array_values($horas_libres));
?>