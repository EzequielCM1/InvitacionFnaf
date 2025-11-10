<?php
session_start();
$mensaje = "";
$errores = [];
$ruta = "lista.csv";
$mensajeExito = "";
$logeado = false;
if (isset($_SESSION['usuario'])) {
    $logeado = true;
    $nombre = $_SESSION['usuario'];
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nombre = htmlspecialchars(trim($_POST['name'] ?? ''));
    $mensaje = htmlentities(trim($_POST['mensaje'] ?? ''));


    if (empty($nombre)) {
        $errores['usuario'] = "Introduce el nombre cohones";
    }
    if (empty($mensaje)) {
        $errores['mensaje'] = "Di lo que sea ";
    }

    if (empty($errores)) {
        /* Guarda */
        $archivo = fopen($ruta, "a+");
        fputcsv($archivo, [$nombre, $mensaje]);
        rewind($archivo);
        fclose($archivo);

        /* Guardar sesion */
        $_SESSION['usuario'] = $nombre;
        $_SESSION['logeadoPrimeraVez'] = true;
        $logeado = true;

        /* Mostrar mensaje de exito */
        $mensajeExito = "$nombre Te has unido correctamente a la tertulia";
    }
}

function lista($ruta){
    $hayRegistros = false;
    if (!file_exists($ruta)) {
        echo "<p>No hay registros aún</p>";
        return;
    }
    $archivo = fopen($ruta, "a+");
    if (!$archivo) {
        echo "<p>Error al abrir el archivo.</p>";
        fclose($archivo);
        return;
    }
    echo "<h2>Lista de personas registradas: </h2>";
    echo "<ul>";
    while (($fila = fgetcsv($archivo)) !== false) {
        $hayRegistros = true;
        $nombre = htmlspecialchars($fila[0]);
        $mensaje = htmlspecialchars($fila[1]);
        echo "<li><strong>$nombre</strong>: $mensaje</li>";
    }
    if (!$hayRegistros) {
        echo "<p style='color: #ff4d4d; font-weight: bold;'>Aún no se ha registrado nadie</p>";
    }
    echo "</ul>";
    fclose($archivo);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title></title>
    <link rel="stylesheet" href="./styles.css">
</head>

<body>
    <header>
        <h1>Te invito a ver Fnaf 2 con nosotros !</h1>
    </header>

    <main>

        <?php if ($logeado == false) : ?>
            <form action="" method="POST">
                <p>Te quieres unir ?</p>
                <label for="name">Nombre:</label>
                <input type="text" id="name" name="name" />
                <?php if (!empty($errores['usuario'])) : ?>
                <span class="error"><?= $errores['usuario'] ?></span>
                <?php endif; ?>

                <label for="mensaje">Mensaje:</label>
                <textarea id="mensaje" name="mensaje" rows="4"></textarea>
                <?php if (!empty($errores['mensaje'])) : ?>
                <span class="error"><?= $errores['mensaje'] ?></span>
                <?php endif; ?><br>

                <button type="submit">Enviar</button>
            </form>
        <?php endif; ?>
        <div>
            <?php
            if (isset($_SESSION['logeadoPrimeraVez']) && $_SESSION['logeadoPrimeraVez'] === true) {
                echo "<p>$mensajeExito</p>";
                unset($_SESSION['logeadoPrimeraVez']);
            } ?>
            <?php lista($ruta); ?>
        </div>

    </main>

    <footer>
        <p>Zequi</p>
    </footer>
</body>

</html>