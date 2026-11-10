<?php
require 'conexion.php';

$query = "SELECT id, nombre FROM t_estado ORDER BY nombre";
$resultado = $mysqli->query($query);

if (!$resultado) {
    die('No se pudieron cargar los estados. Revisa la consulta SQL: ' . htmlspecialchars($mysqli->error, ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Glam Beauty | Registrar clienta</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <style>
        :root {
            --pink: #e7a8bf;
            --pink-dark: #b96588;
            --pink-light: #fff0f5;
            --blue: #dcecf9;
            --blue-dark: #5d8bb6;
            --blue-line: #cfe2f2;
            --text: #594653;
            --muted: #8e7c87;
            --line: #f0dce5;
            --background: #fff9fb;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(ellipse at top left, rgba(231,168,191,.18), transparent 34%),
                radial-gradient(ellipse at bottom right, rgba(169,201,232,.22), transparent 34%),
                var(--background);
            color: var(--text);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 14px;
        }

        .topbar {
            background: rgba(255,255,255,.94);
            border-bottom: 1px solid var(--line);
            box-shadow: 0 4px 18px rgba(111,74,94,.04);
        }

        .topbar-inner {
            max-width: 1120px;
            margin: auto;
            padding: 16px 22px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: var(--pink-light);
            color: var(--pink-dark);
            font-size: 23px;
        }

        .brand-name {
            margin: 0;
            color: #795368;
            font: 700 25px Georgia, "Times New Roman", serif;
        }

        .brand-tagline {
            margin: 3px 0 0;
            color: #a18d99;
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .page {
            max-width: 920px;
            margin: 36px auto;
            padding: 0 18px;
        }

        .intro {
            margin-bottom: 20px;
            text-align: center;
        }

        .intro-kicker {
            display: inline-block;
            margin-bottom: 8px;
            color: var(--pink-dark);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .intro h1 {
            margin: 0 0 9px;
            color: #795368;
            font: 700 32px Georgia, "Times New Roman", serif;
        }

        .intro p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .form-card {
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            box-shadow: 0 14px 38px rgba(126,83,104,.08);
        }

        .card-top {
            height: 7px;
            background: linear-gradient(90deg, #e7a8bf 0%, #f7dbe6 48%, #dcecf9 100%);
        }

        .form-card-inner { padding: 30px 34px 32px; }

        .form-note {
            margin-bottom: 25px;
            padding: 13px 15px;
            color: #6b8297;
            background: #f1f7fc;
            border: 1px solid #deebf6;
            border-radius: 12px;
        }

        .form-horizontal .form-group {
            display: flex;
            align-items: flex-start;
            margin: 0 0 20px;
        }

        .form-horizontal .control-label {
            width: 175px;
            flex: 0 0 175px;
            padding: 10px 18px 0 0;
            color: #735b6a;
            text-align: left;
            font-weight: 600;
        }

        .field {
            min-width: 0;
            flex: 1;
        }

        .form-control {
            height: 43px;
            padding: 10px 13px;
            color: var(--text);
            background-color: #fff;
            border: 1px solid #ead9e2;
            border-radius: 10px;
            box-shadow: none;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .form-control:focus {
            border-color: var(--blue-dark);
            box-shadow: 0 0 0 3px rgba(169,201,232,.28);
        }

        input[type="file"].form-control {
            height: auto;
            min-height: 43px;
            padding: 7px 10px;
            background: #fbfdff;
        }

        .choice-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            min-height: 40px;
            padding-top: 2px;
        }

        .choice {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 0;
            padding: 9px 13px;
            color: #6f6270;
            background: #fff8fb;
            border: 1px solid #f0dce5;
            border-radius: 9px;
            font-weight: 500;
            cursor: pointer;
        }

        .choice:nth-child(even) {
            background: #f3f8fd;
            border-color: #dceaf6;
        }

        .choice input {
            margin: 0;
            accent-color: var(--pink-dark);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 7px;
            margin-left: 175px;
        }

        .btn {
            min-height: 41px;
            padding: 10px 18px;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease;
        }

        .btn:hover { transform: translateY(-1px); }

        .btn-back {
            color: #5e7d9a;
            background: var(--blue);
            border: 1px solid var(--blue-line);
        }

        .btn-back:hover, .btn-back:focus {
            color: #456b8e;
            background: #cfe4f5;
        }

        .btn-save {
            color: #fff;
            background: var(--pink-dark);
            border: 1px solid var(--pink-dark);
        }

        .btn-save:hover, .btn-save:focus {
            color: #fff;
            background: #a95578;
        }

        .footer {
            padding: 22px 0 10px;
            color: #b09da8;
            text-align: center;
            font-size: 12px;
        }

        @media (max-width: 640px) {
            .page { margin: 24px auto; padding: 0 12px; }
            .intro h1 { font-size: 27px; }
            .form-card-inner { padding: 22px 17px; }
            .form-horizontal .form-group { display: block; margin-bottom: 18px; }
            .form-horizontal .control-label {
                display: block;
                width: auto;
                padding: 0 0 8px;
            }
            .field { width: 100%; }
            .form-actions { margin-left: 0; justify-content: stretch; }
            .form-actions .btn { flex: 1; }
            .choice-row { gap: 7px; }
            .choice { padding: 8px 10px; }
        }
    </style>

    <script>
        $(document).ready(function () {
            $("#id_estado").on("change", function () {
                var id_estado = $(this).val();

                $("#id_municipio").html('<option value="">Cargando municipios...</option>');

                $.post("combobox/getMunicipio.php", { id_estado: id_estado }, function (data) {
                    $("#id_municipio").html(data);
                }).fail(function () {
                    $("#id_municipio").html('<option value="">No se pudieron cargar los municipios</option>');
                });
            });
        });
    </script>
</head>

<body>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand-icon" aria-hidden="true">✿</div>
        <div>
            <p class="brand-name">Glam Beauty</p>
            <p class="brand-tagline">Tu belleza, tu estilo</p>
        </div>
    </div>
</header>

<main class="page">
    <div class="intro">
        <span class="intro-kicker">Un toque de belleza</span>
        <h1>Registrar clienta</h1>
        <p>Guarda los datos de tus clientas y sus preferencias de belleza.</p>
    </div>

    <section class="form-card">
        <div class="card-top"></div>
        <div class="form-card-inner">
            <div class="form-note">♡ Registra la información de la clienta para ofrecerle una atención más personalizada.</div>

            <form class="form-horizontal" method="POST" action="guardar.php" enctype="multipart/form-data" autocomplete="off">
                <div class="form-group">
                    <label for="nombre" class="control-label">Nombre de la clienta</label>
                    <div class="field">
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej. María García" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="control-label">Correo electrónico</label>
                    <div class="field">
                        <input type="email" class="form-control" id="email" name="email" placeholder="clienta@correo.com" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono" class="control-label">Teléfono de contacto</label>
                    <div class="field">
                        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="Ej. 809-000-0000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="producto_favorito" class="control-label">Producto favorito</label>
                    <div class="field">
                        <select class="form-control" id="producto_favorito" name="estado_civil">
                            <option value="Maquillaje para labios">Maquillaje para labios</option>
                            <option value="Bases y correctores">Bases y correctores</option>
                            <option value="Sombras y delineadores">Sombras y delineadores</option>
                            <option value="Rubor e iluminador">Rubor e iluminador</option>
                            <option value="Cuidado de la piel">Cuidado de la piel</option>
                            <option value="Accesorios de belleza">Accesorios de belleza</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <span class="control-label">¿Qué busca hoy?</span>
                    <div class="field choice-row">
                        <label class="choice"><input type="radio" name="hijos" value="Maquillaje" checked> Maquillaje</label>
                        <label class="choice"><input type="radio" name="hijos" value="Cuidado facial"> Cuidado facial</label>
                        <label class="choice"><input type="radio" name="hijos" value="Accesorios"> Accesorios</label>
                    </div>
                </div>

                <div class="form-group">
                    <span class="control-label">Preferencias de belleza</span>
                    <div class="field choice-row">
                        <label class="choice"><input type="checkbox" name="intereses[]" value="Labiales"> Labiales</label>
                        <label class="choice"><input type="checkbox" name="intereses[]" value="Pestañina"> Pestañina</label>
                        <label class="choice"><input type="checkbox" name="intereses[]" value="Paletas de sombras"> Paletas de sombras</label>
                        <label class="choice"><input type="checkbox" name="intereses[]" value="Skincare"> Skincare</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="id_estado" class="control-label">Provincia</label>
                    <div class="field">
                        <select class="form-control" id="id_estado" name="id_estado" required>
                            <option value="">Seleccionar provincia</option>
                            <?php while ($row = $resultado->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars((string)$row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string)$row['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="Archivo" class="control-label">Foto de perfil (opcional)</label>
                    <div class="field">
                        <input type="file" class="form-control" id="Archivo" name="Archivo" accept="image/*">
                        <p style="margin:7px 0 0;color:#9b8994;font-size:12px;">Puedes adjuntar una imagen para identificar el registro.</p>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-back">← Regresar</a>
                    <button type="submit" class="btn btn-save">♡ Guardar registro</button>
                </div>
            </form>
        </div>
    </section>

    <footer class="footer">Glam Beauty · Belleza en cada detalle</footer>
</main>
</body>
</html>
