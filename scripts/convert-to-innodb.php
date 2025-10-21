<?php
// scripts/convert-to-innodb.php
// Uso: acceder desde el navegador en http://localhost/semisMarie/scripts/convert-to-innodb.php
// El script crea un backup SQL (en carpeta backups/) y luego ejecuta ALTER TABLE ... ENGINE=InnoDB
// SOLO PARA ENTORNOS LOCALES: comprueba REMOTE_ADDR para evitar ejecución remota.

// Seguridad: permitir solo localhost
$remote = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!in_array($remote, ['127.0.0.1', '::1', '::ffff:127.0.0.1'])) {
    header('HTTP/1.1 403 Forbidden');
    echo "Acceso denegado. Este script sólo puede ejecutarse desde el servidor local.";
    exit();
}

require_once __DIR__ . '/../includes/db.php';

$tables = ['citas', 'notificaciones', 'servicios', 'usuarios'];
$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
$ts = date('Ymd_His');
$backupFile = $backupDir . "/backup_semis_marie_{$ts}.sql";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']) && $_POST['confirm'] === '1') {
    // Crear backup: volcar CREATE TABLE y datos
    $fp = fopen($backupFile, 'w');
    if (!$fp) {
        echo "No se pudo crear el archivo de backup: {$backupFile}";
        exit();
    }

    fwrite($fp, "-- Backup generado por scripts/convert-to-innodb.php\n");
    fwrite($fp, "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n");

    foreach ($tables as $table) {
        // SHOW CREATE TABLE
        $res = $conn->query("SHOW CREATE TABLE `{$table}`");
        if ($res && $row = $res->fetch_assoc()) {
            $create = $row['Create Table'] ?? $row['Create View'] ?? null;
            fwrite($fp, "-- Estructura de la tabla {$table}\n");
            fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n");
            fwrite($fp, $create . ";\n\n");
        } else {
            fwrite($fp, "-- No se pudo obtener CREATE TABLE para {$table}\n");
        }

        // Volcar datos (INSERTs)
        $r2 = $conn->query("SELECT * FROM `{$table}`");
        if ($r2 && $r2->num_rows > 0) {
            fwrite($fp, "-- Datos para {$table}\n");
            while ($row = $r2->fetch_assoc()) {
                $cols = array_map(function($c){ return "`$c`"; }, array_keys($row));
                $vals = array_map(function($v) use ($conn){
                    if (is_null($v)) return 'NULL';
                    return "'" . $conn->real_escape_string($v) . "'";
                }, array_values($row));
                fwrite($fp, "INSERT INTO `{$table}` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n");
            }
            fwrite($fp, "\n");
        }
    }

    fclose($fp);

    // Ejecutar conversion a InnoDB
    $results = [];
    foreach ($tables as $table) {
        $sql = "ALTER TABLE `{$table}` ENGINE=InnoDB";
        if ($conn->query($sql) === TRUE) {
            $results[$table] = 'OK';
        } else {
            $results[$table] = 'ERROR: ' . $conn->error;
        }
    }

    ?>
    <!doctype html>
    <html>
    <head><meta charset="utf-8"><title>Resultado conversión a InnoDB</title></head>
    <body>
      <h1>Conversión a InnoDB finalizada</h1>
      <p>Backup guardado en: <?php echo htmlspecialchars($backupFile); ?></p>
      <h2>Resultados</h2>
      <ul>
      <?php foreach ($results as $t => $r): ?>
        <li><?php echo htmlspecialchars($t); ?>: <?php echo htmlspecialchars($r); ?></li>
      <?php endforeach; ?>
      </ul>
      <p><a href="/semisMarie/">Volver al sitio</a></p>
    </body>
    </html>
    <?php
    exit();
}

// Mostrar página de confirmación antes de ejecutar
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Convertir tablas a InnoDB - backup y conversión</title></head>
<body>
  <h1>Convertir tablas a InnoDB</h1>
  <p>Este script hará lo siguiente:</p>
  <ol>
    <li>Creará un respaldo SQL de las tablas: <?php echo htmlspecialchars(implode(', ', $tables)); ?></li>
    <li>Ejecutará ALTER TABLE ... ENGINE=InnoDB para cada tabla</li>
  </ol>
  <p><strong>IMPORTANTE:</strong> Hacé <em>backup</em> antes de continuar. El script genera un backup automático en la carpeta <code>backups/</code>.</p>
  <form method="post">
    <p>Si estás en el servidor local y querés continuar, presioná el botón de confirmación.</p>
    <button type="submit" name="confirm" value="1">Confirmar y convertir a InnoDB</button>
  </form>
</body>
</html>
