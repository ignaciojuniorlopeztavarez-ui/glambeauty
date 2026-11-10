<?php
// Glam Beauty - Página principal
// Reutiliza la conexión existente de conexion.php.
require_once __DIR__ . '/conexion.php';

// La conexión de tu proyecto normalmente se llama $mysqli.
// Si en conexion.php tiene otro nombre, cambia $mysqli por ese nombre aquí.
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    die('No se encontró la conexión $mysqli. Revisa el nombre de la variable en conexion.php.');
}

$busqueda = trim($_GET['buscar'] ?? '');
$errorConsulta = '';
$resultado = false;

/*
  Estructura asumida según la tabla que aparece en tu captura:
  personas(id, nombre, correo, telefono, id_estado, imagen)
  t_estado(id, nombre)
*/
if ($busqueda !== '') {
    $sql = "SELECT p.*, e.nombre AS estado_nombre
            FROM personas p
            LEFT JOIN t_estado e ON p.id_estado = e.id
            WHERE p.nombre LIKE ?
            ORDER BY p.nombre ASC";
    $stmt = $mysqli->prepare($sql);

    if ($stmt) {
        $termino = '%' . $busqueda . '%';
        $stmt->bind_param('s', $termino);
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
        } else {
            $errorConsulta = 'No se pudo ejecutar la búsqueda: ' . $stmt->error;
        }
    } else {
        $errorConsulta = 'No se pudo preparar la consulta. Verifica los nombres de las tablas y columnas: ' . $mysqli->error;
    }
} else {
    $sql = "SELECT p.*, e.nombre AS estado_nombre
            FROM personas p
            LEFT JOIN t_estado e ON p.id_estado = e.id
            ORDER BY p.nombre ASC";
    $resultado = $mysqli->query($sql);

    if (!$resultado) {
        $errorConsulta = 'No se pudo cargar la tabla. Verifica la estructura de la base de datos: ' . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Glam Beauty | Panel de administración</title>
<style>
:root {
  --pink:#e7a8bf; --pink-dark:#b96588; --pink-pale:#fff0f5;
  --blue:#dcecf9; --blue-dark:#5d8bb6; --ink:#594653;
  --muted:#8e7c87; --line:#f0dce5; --white:#fff; --bg:#fff9fb;
}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font-family:"Segoe UI",Arial,sans-serif;font-size:14px}
header{background:#fff;border-bottom:1px solid var(--line)}
.header-inner{max-width:1450px;margin:auto;padding:17px 22px;display:flex;align-items:center;gap:13px}
.logo{width:45px;height:45px;display:grid;place-items:center;background:var(--pink-pale);color:var(--pink-dark);border-radius:14px;font-size:24px}
.brand{margin:0;color:#795368;font:700 26px Georgia,"Times New Roman",serif}
.tagline{margin:3px 0 0;color:#a18d99;font-size:10px;letter-spacing:2px;text-transform:uppercase}
main{max-width:1450px;margin:28px auto;padding:0 20px}
.app-layout{display:flex;align-items:flex-start;gap:22px}
.sidebar{width:230px;flex:0 0 230px;padding:17px 13px;background:#f0f7fd;border:1px solid #dceaf6;border-radius:16px;transition:width .22s ease,flex-basis .22s ease,padding .22s ease;overflow:hidden}
.sidebar-heading{margin:4px 10px 13px;color:#6687a5;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;white-space:nowrap}
.side-nav{display:flex;flex-direction:column;gap:7px}
.side-link{display:flex;align-items:center;gap:12px;min-height:43px;padding:10px 12px;color:#536f88;background:transparent;border:1px solid transparent;border-radius:10px;text-decoration:none;font-weight:600;white-space:nowrap;transition:background .18s,color .18s}
.side-link:hover,.side-link:focus{background:#dcecf9;border-color:#c9e0f2;color:#3f6f99;text-decoration:none}
.side-icon{display:inline-flex;align-items:center;justify-content:center;width:22px;flex:0 0 22px;font-size:17px}
.side-text{overflow:hidden}
.sidebar-toggle{display:flex;align-items:center;justify-content:center;gap:9px;width:100%;margin-bottom:15px;padding:10px;border:1px solid #cfe2f2;border-radius:10px;background:#dcecf9;color:#527da8;font-weight:700;cursor:pointer}
.sidebar-toggle:hover{background:#cfe4f5}
.content-column{min-width:0;flex:1}
.app-layout.sidebar-collapsed .sidebar{width:68px;flex-basis:68px;padding:17px 8px}
.app-layout.sidebar-collapsed .sidebar-heading,.app-layout.sidebar-collapsed .side-text,.app-layout.sidebar-collapsed .toggle-label{display:none}
.app-layout.sidebar-collapsed .side-link{justify-content:center;padding:10px 7px}
.app-layout.sidebar-collapsed .sidebar-toggle{padding:10px 5px}

.hero{padding:28px 30px;border:1px solid var(--line);border-radius:18px;background:linear-gradient(115deg,#fff0f5,#fff 65%,#edf6ff);margin-bottom:22px}
.eyebrow{color:var(--pink-dark);font-size:11px;font-weight:700;letter-spacing:1.8px;text-transform:uppercase}
h1{margin:9px 0;color:#795368;font:700 31px Georgia,"Times New Roman",serif}
.hero p{margin:0;color:var(--muted)}
.panel{background:#fff;border:1px solid var(--line);border-radius:17px;padding:24px;box-shadow:0 8px 28px #7e53680d}
.actions{display:flex;justify-content:center;flex-wrap:wrap;gap:10px;margin-bottom:24px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:10px 14px;border:1px solid transparent;border-radius:9px;text-decoration:none;font:600 13px "Segoe UI",Arial,sans-serif;cursor:pointer;transition:.18s}
.btn:hover{transform:translateY(-1px);filter:brightness(.98)}
.btn-primary{background:var(--pink-dark);color:white}
.btn-soft{background:#edf5fc;border-color:#d9e9f7;color:#536f88}
.search{display:flex;gap:8px;max-width:460px;margin-bottom:22px}
input[type=search]{min-width:0;flex:1;height:40px;padding:0 12px;border:1px solid #e8d7e0;border-radius:9px;font:inherit;outline:none}
input[type=search]:focus{border-color:var(--pink);box-shadow:0 0 0 3px #e7a8bf30}
.btn-search{background:var(--blue-dark);color:#fff}
.table-wrap{width:100%;overflow-x:auto}
table{width:100%;border-collapse:separate;border-spacing:0;white-space:normal}
thead th{padding:14px 10px;background:#fcecf3;color:#795368;border-top:1px solid var(--line);border-bottom:1px solid var(--line);text-align:center;font-weight:700}
thead th:first-child{border-radius:10px 0 0 10px}
thead th:last-child{border-radius:0 10px 10px 0}
tbody td{padding:12px 10px;border-bottom:1px solid #f2e8ed;vertical-align:middle}
tbody tr:nth-child(odd){background:#fffafd}
tbody tr:hover{background:#f2f8fd}
td.center{text-align:center}
.thumb{width:50px;height:50px;object-fit:cover;border:1px solid var(--line);border-radius:9px;padding:3px;background:#fff}
.muted{color:#b3a4ad}
.btn-edit,.btn-delete{padding:7px 10px;color:#fff}
.btn-edit{background:#8bb9df}
.btn-delete{background:#d995aa}
.notice{padding:13px 15px;border:1px solid #f1d2dc;border-radius:10px;background:#fff3f6;color:#9a4d68;margin-bottom:18px;overflow-wrap:anywhere}
.empty{text-align:center;color:var(--muted);padding:25px}
footer{text-align:center;padding:22px 0 8px;color:#b09da8;font-size:12px}
@media(max-width:800px){
  main{margin-top:18px;padding:0 10px}
  .app-layout{gap:12px}
  .sidebar{width:68px;flex-basis:68px;padding:12px 7px}
  .sidebar-heading,.side-text,.toggle-label{display:none}
  .side-link{justify-content:center;padding:10px 7px}
  .sidebar-toggle{padding:10px 5px}
  .hero{padding:22px 18px}h1{font-size:26px}.panel{padding:14px}
}
@media(max-width:480px){.app-layout{gap:8px}.sidebar{width:54px;flex-basis:54px;padding:10px 4px}.side-link{padding:9px 3px}.side-icon{width:20px;flex-basis:20px}}
</style>
</head>
<body>
<header>
  <div class="header-inner">
    <div class="logo" aria-hidden="true">✿</div>
    <div>
      <p class="brand">Glam Beauty</p>
      <p class="tagline">Tu belleza, tu estilo</p>
    </div>
  </div>
</header>

<main>
  <section class="hero">
    <span class="eyebrow">Panel de administración</span>
    <h1>Bienvenida a Glam Beauty</h1>
    <p>Gestiona tus registros de forma sencilla, organizada y con estilo.</p>
  </section>

  <div class="app-layout" id="appLayout">
    <aside class="sidebar" aria-label="Menú principal">
      <button class="sidebar-toggle" type="button" id="sidebarToggle" aria-expanded="true" aria-controls="sideNav">
        <span aria-hidden="true">☰</span><span class="toggle-label">Ocultar menú</span>
      </button>
      <div class="sidebar-heading">Menú principal</div>
      <nav class="side-nav" id="sideNav">
        <a class="side-link" href="nuevo.php" title="Nuevo registro"><span class="side-icon">＋</span><span class="side-text">Nuevo registro</span></a>
        <a class="side-link" href="pdf/index.php" title="Generar PDF"><span class="side-icon">▤</span><span class="side-text">Generar PDF</span></a>
        <a class="side-link" href="Excel/reporte.php" title="Exportar a Excel"><span class="side-icon">▦</span><span class="side-text">Exportar a Excel</span></a>
        <a class="side-link" href="email/index.php" title="Enviar correo"><span class="side-icon">✉</span><span class="side-text">Enviar correo</span></a>
        <a class="side-link" href="codigos_barras/index.php" title="Código de barras"><span class="side-icon">▥</span><span class="side-text">Código de barras</span></a>
        <a class="side-link" href="codigoqr/index.php" title="Código QR"><span class="side-icon">▦</span><span class="side-text">Código QR</span></a>
        <a class="side-link" href="captcha/validar.php" title="CAPTCHA"><span class="side-icon">✓</span><span class="side-text">CAPTCHA</span></a>
      </nav>
    </aside>

    <div class="content-column">
    <section class="panel">
    <form class="search" method="get" action="">
      <input type="search" name="buscar" placeholder="Buscar por nombre..." value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>">
      <button class="btn btn-search" type="submit">Buscar</button>
      <?php if ($busqueda !== ''): ?><a class="btn btn-soft" href="index.php">Limpiar</a><?php endif; ?>
    </form>

    <?php if ($errorConsulta !== ''): ?>
      <div class="notice"><strong>Hay que revisar la consulta SQL.</strong><br>
        <?php echo htmlspecialchars($errorConsulta, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead><tr>
          <th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Imagen</th><th>Modificar</th><th>Eliminar</th>
        </tr></thead>
        <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
          <?php while ($row = $resultado->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars((string)($row['id'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($row['nombre'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($row['correo'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($row['telefono'] ?? '')); ?></td>
            <td><?php echo htmlspecialchars((string)($row['estado_nombre'] ?? 'Sin asignar')); ?></td>
            <td class="center">
              <?php
                $imagen = (string)($row['imagen'] ?? '');
                $id = (string)($row['id'] ?? '');
                $rutaImagen = 'files/' . rawurlencode($id) . '/' . rawurlencode($imagen);
                $archivoImagen = __DIR__ . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . $imagen;
              ?>
              <?php if ($imagen !== '' && is_file($archivoImagen)): ?>
                <img class="thumb" src="<?php echo htmlspecialchars($rutaImagen, ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen del registro">
              <?php else: ?><span class="muted">Sin imagen</span><?php endif; ?>
            </td>
            <td class="center"><a class="btn btn-edit" href="modificar.php?id=<?php echo urlencode($id); ?>">✎</a></td>
            <td class="center"><a class="btn btn-delete" href="eliminar.php?id=<?php echo urlencode($id); ?>" onclick="return confirm('¿Seguro que deseas eliminar este registro?')">⌫</a></td>
          </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="8" class="empty">No hay registros para mostrar.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
    </section>
    </div>
  </div>
  <footer>Glam Beauty · Belleza en cada detalle</footer>
</main>

<script>
(function () {
  const layout = document.getElementById('appLayout');
  const toggle = document.getElementById('sidebarToggle');
  if (!layout || !toggle) return;

  toggle.addEventListener('click', function () {
    const collapsed = layout.classList.toggle('sidebar-collapsed');
    toggle.setAttribute('aria-expanded', String(!collapsed));
    const label = toggle.querySelector('.toggle-label');
    if (label) label.textContent = collapsed ? 'Mostrar menú' : 'Ocultar menú';
    toggle.title = collapsed ? 'Mostrar menú' : 'Ocultar menú';
  });
})();
</script>
</body>
</html>
