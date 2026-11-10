<?php
session_start();

// Si el usuario ya está autenticado, redirigir al panel principal
if (isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$email_ingresado = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $email_ingresado = $email;

    // Reutiliza la conexión existente de conexion.php.
    require_once __DIR__ . '/conexion.php';

    if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
        die('No se encontró la conexión $mysqli. Revisa el nombre de la variable en conexion.php.');
    }

    // Cambiamos 'email' por 'correo' para que coincida con tu base de datos
    $stmt = $mysqli->prepare("SELECT id, nombre, password FROM usuarios WHERE correo = ?");
    
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            if (password_verify($password, $row['password']) || $password === $row['password']) {
                $_SESSION['usuario'] = $row['nombre'];
                $_SESSION['id_usuario'] = $row['id'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta. Por favor, verifica tus datos.";
            }
        } else {
            $error = "El correo electrónico ingresado no está registrado en la base de datos.";
        }
        $stmt->close();
    } else {
        $error = "Error al preparar la consulta SQL: " . $mysqli->error;
    }
    
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Glam Beauty | Iniciar Sesión</title>
<style>
:root {
    --pink: #e7a8bf;
    --pink-dark: #b96588;
    --pink-pale: #fff0f5;
    --blue: #dcecf9;
    --blue-dark: #5d8bb6;
    --ink: #594653;
    --muted: #8e7c87;
    --line: #f0dce5;
    --white: #fff;
    --bg: #fff9fb;
}
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
body {
    background: var(--bg);
    color: var(--ink);
    font-family: "Segoe UI", Arial, sans-serif;
    font-size: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.login-container {
    background: var(--white);
    border: 1px solid var(--line);
    border-radius: 18px;
    padding: 36px 30px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 8px 28px #7e53680d;
}
.header-login {
    text-align: center;
    margin-bottom: 28px;
}
.logo-login {
    width: 50px;
    height: 50px;
    display: inline-grid;
    place-items: center;
    background: var(--pink-pale);
    color: var(--pink-dark);
    border-radius: 14px;
    font-size: 26px;
    margin-bottom: 12px;
}
.header-login h1 {
    margin: 0;
    color: #795368;
    font: 700 26px Georgia, "Times New Roman", serif;
}
.header-login p {
    margin: 4px 0 0;
    color: #a18d99;
    font-size: 10px;
    letter-spacing: 2px;
    text-transform: uppercase;
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    margin-bottom: 7px;
    color: var(--ink);
    font-weight: 600;
    font-size: 13px;
}
.form-group input {
    width: 100%;
    height: 42px;
    padding: 0 12px;
    border: 1px solid #e8d7e0;
    border-radius: 9px;
    font: inherit;
    outline: none;
    transition: border-color .18s, box-shadow .18s;
}
.form-group input:focus {
    border-color: var(--pink);
    box-shadow: 0 0 0 3px #e7a8bf30;
}
.btn-login {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 42px;
    background: var(--blue-dark);
    color: var(--white);
    border: 1px solid transparent;
    border-radius: 9px;
    font: 600 14px "Segoe UI", Arial, sans-serif;
    cursor: pointer;
    transition: .18s;
    margin-top: 10px;
}
.btn-login:hover {
    filter: brightness(.95);
    transform: translateY(-1px);
}
.error-msg {
    padding: 12px 14px;
    border: 1px solid #f1d2dc;
    border-radius: 10px;
    background: #fff3f6;
    color: #9a4d68;
    font-size: 13px;
    margin-bottom: 18px;
    text-align: center;
}
</style>
</head>
<body>

    <div class="login-container">
        <div class="header-login">
            <div class="logo-login" aria-hidden="true">✿</div>
            <h1>Glam Beauty</h1>
            <p>Panel de Administración</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_ingresado, ENT_QUOTES, 'UTF-8'); ?>" placeholder="admin@glambeauty.com" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-login">Ingresar al Sistema</button>
        </form>
    </div>

</body>
</html>