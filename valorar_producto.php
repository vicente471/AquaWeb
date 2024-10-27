<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo-AquaWeb</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
        session_start();

        $carrito_user = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
        $_SESSION['carrito'] = $carrito_user;

        if (isset($_SESSION['carrito'])) {
            $total_cantidad = 0;

            foreach ($carrito_user as $item) {
                // Verificar si el ítem no es NULL y si tiene la clave 'cantidad'
                if ($item != NULL && isset($item['cantidad'])) {
                    $total_cantidad += $item['cantidad'];
                }
            }
        }
    ?>
    <header>
        <div class="logo">
            <img src="./images/Logo.jpg" alt="AquaWeb Logo">
            <h1>AquaWeb</h1>
        </div>
        <div class="Espacio_nav">
            <nav>
                <a href="index.php" class="NavnoActive">Home</a>
                <a href="catalogo.php" class="NavnoActive">Catálogo</a>
                <a href="seguimiento.php" class="NavnoActive">Seguimiento</a>
                <?php
                if (isset($_SESSION['usuario'])) {
                    if (isset($_SESSION['tipousuario']) && $_SESSION["tipousuario"] == 0){
                        echo '<a href="balancesadm.php#Reporte_Balances" class="NavnoActive">Tu perfil</a>';
                    }
                    elseif ($_SESSION['tipousuario'] == 1){
                        echo '<a href="perfilcliente.php#Historial_Compras" class="NavnoActive">Tu perfil</a>';
                    }
                    elseif ($_SESSION['tipousuario'] == 2){
                        echo '<a href="perfilrepartidor.php#Actualizar_Estado" class="NavnoActive">Tu perfil</a>';
                    }
                    else {
                        echo '<a href="ingreso.html" class="NavnoActive">Ingreso</a>';
                    }
                } else {
                    echo '<a href="ingreso.html" class="NavnoActive">Ingreso</a>';
                }
                ?>
            </nav>
        </div>
    </header>
    <div class="titulo">
        <h1>Valorar Producto</h1>
    </div>
    <div class="results-container">
        <?php
            // Configuración de la base de datos
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "myweb";
                
            // Crear conexión
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            // Verificar la conexión
            if ($conn->connect_error) {
                die("La conexión a la base de datos falló: " . $conn->connect_error);
            }
            // Realizar la búsqueda en la base de datos
            $compra_id = isset($_GET['compra_id']) ? $_GET['compra_id'] : null;
            $sql = "SELECT * FROM com_bid, bidon WHERE COM_BIDID = BID_ID AND COM_COMID = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $compra_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Mostrar resultados de la búsqueda
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bidID = $row['BID_ID'];
                    $botonPresionado = isset($_SESSION['boton_presionado'][$bidID]) && $_SESSION['boton_presionado'][$bidID];
                
                    echo "<form action='backend.php' method='post'>";
                    echo "<div class='contenedores'>";
                    echo "<p>";
                    echo "<img src='images/" . $row['BID_imagen_url'] . "' alt='Imagen del bidón'>";
                    echo "<p><h4>" . $row['BID_nom'] . "</h4><br>";
                    echo "Precio: $" . $row['BID_precio'] . "<br>";
                    echo "Litros: " . $row['BID_litros'] . "<br>";
                    echo "</p>";
                
                    echo '<label for="puntuacion">Puntuación:</label>';
                    echo '<input type="hidden" id="id_bidon" name="id_bidon" value="'. $row['BID_ID'] . '" required>';
                    echo '<select name="puntuacion" id="puntuacion">';
                    echo '<option value="1">1</option>';
                    echo '<option value="2">2</option>';
                    echo '<option value="3">3</option>';
                    echo '<option value="4">4</option>';
                    echo '<option value="5">5</option>';
                    echo '</select>';
                
                    // Utiliza el atributo name para identificar el botón presionado
                    echo '<button type="submit" name="enviar_valoracion">Añadir valoración</button>';
                    echo "</div>";
                    echo "</form>";
                
                    // Marcar el botón como presionado solo si el botón específico ha sido presionado
                    if (!$botonPresionado && isset($_POST['enviar_valoracion_'.$bidID])) {
                        $_SESSION['boton_presionado'][$bidID] = true;
                    }
                }
            } else {
                echo "<p>No se encontraron resultados.</p>";
            }
            $conn->close();
        ?>
    </div>
    <div class="formulario_valoracion">
        <form action="backend.php" method="post">
            <button type="submit" name="anadir_valoracion">Finalizar Valoración</button>
        </form>
    </div>
</body>
</html>
