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
                <a href="catalogo.php" class="items_Carrito"><img src="images/carrovacio.png" alt="carrito" width=45px><?php echo $total_cantidad; ?></a>
                <a href="index.php" class="NavnoActive">Home</a>
                <a href="#" class="NavActive">Catálogo</a>
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
        <h1>Catálogo de Productos</h1>
    </div>
    
    
    <div class="todo">
        <div class="Panel-derecha">
            <!--Filtros-->
            <div class="filtros">
                <h2>Filtrado de productos</h2>
                <div class="buscador_de_contenedores">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <input type="text" name="searchTerm" placeholder="Buscar...">
                        <button type="submit" name="buscar">Buscar</button>
                    </form>
                </div>
                <div class="filtros-eleccion">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                        <div class="custom-dropdown">
                            <ul class="options-list">
                                <!-- Agrega value="mas_vendido" a cada botón -->
                                <li><button type="submit" name="orden" value="mas_vendido">Más vendido</button></li>
                                <li><button type="submit" name="orden" value="mejor_valorado">Mejor Valorado</button></li>
                                <li><button type="submit" name="orden" value="precio_ascendente">Precio Ascendente</button></li>
                                <li><button type="submit" name="orden" value="precio_descendente">Precio Descendente</button></li>
                                <li><button type="submit" name="orden" value="nombre_ascendente">Nombre Ascendente</button></li>
                                <li><button type="submit" name="orden" value="nombre_descendente">Nombre Descendente</button></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Carrito -->
            <div id="Carrito" class="figura-carrito">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-titulo">Carrito de Compras</h2>
                    </div>
                    <div id="Contenido-carrito"class="modal-body">
                        <div>
                            <div class="p-2">
                                <ul class="lista-grupo">
                                    <?php
                                    if (isset($_SESSION['carrito'])) {
                                        $total = 0;
                                        foreach ($_SESSION['carrito'] as $item) {
                                            // Verificar si el ítem no es NULL y si tiene la clave 'cantidad'
                                            if ($item != NULL && isset($item['cantidad'])) {
                                                ?>
                                                <li class="lista-grupo-item">
                                                    <div class="fila">
                                                        <div class="columnas">
                                                            <h4 class="my">Cantidad: <?php echo $item['cantidad']; ?> = <?php echo $item['nombre']; ?></h4>
                                                            <h5>CLP =  $<?php echo $item['precio'] * $item['cantidad']; ?></h5>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                                $total = $total + ($item['precio'] * $item['cantidad']);
                                            }
                                        }
                                    }
                                    ?>
                                    <li class="lista-gupo-item">
                                        <span>Total (pesos) =</span>
                                        <strong>$ <?php echo $total; ?></strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" >
                        <?php if ($total_cantidad > 0) { ?>
                            <form action="backend.php" method="post">
                                <input type="hidden" name="cantidad" value="<?php echo $total_cantidad ?>">
                                <input type="hidden" name="precio_total" value="<?php echo $total ?>">
                                <button type="submit" name="agregar_ordencompra">Solicitar Pedido</button>
                            </form>
                            <form action="borrarcarrito.php" method="post">
                                <button type="submit">Vaciar carrito</button>
                            </form>
                        <?php } else { ?>
                            <button>Agrega algo al carrito</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!--Listado de Productos-->
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
                $searchTerm = isset($_POST["searchTerm"]) ? htmlspecialchars($_POST["searchTerm"]) : "";
                $searchTermINT = isset($_POST["searchTerm"]) ? htmlspecialchars($_POST["searchTerm"]) : "";
                // Realizar la búsqueda en la base de datos
                $sql = "SELECT * FROM bidon WHERE BID_nom LIKE ? OR BID_precio = ? OR BID_litros = ?";

                
                // Verifica si se ha seleccionado una opción de orden
                if (isset($_POST["orden"])) {
                    $opcionOrden = $_POST["orden"];
                
                    // Aplica la lógica de ordenación según la opción seleccionada
                    switch ($opcionOrden) {
                        case "mas_vendido":
                            // Lógica de ordenación para "Más vendido"
                            $orderBy = " ORDER BY BID_cantVAL DESC"; // Reemplaza "ventas_columna" con el nombre real de la columna de ventas en tu base de datos
                            break;
                        case "mejor_valorado":
                            // Lógica de ordenación para "Mejor Valorado"
                            $orderBy = " ORDER BY BID_val DESC"; // Reemplaza "valoracion_columna" con el nombre real de la columna de valoración en tu base de datos
                            break;
                        case "precio_ascendente":
                            // Lógica de ordenación para "Precio Ascendente"
                            $orderBy = " ORDER BY BID_precio ASC";
                            break;
                        case "precio_descendente":
                            // Lógica de ordenación para "Precio Descendente"
                            $orderBy = " ORDER BY BID_precio DESC";
                            break;
                        case "nombre_ascendente":
                            // Lógica de ordenación para "Nombre Ascendente"
                            $orderBy = " ORDER BY BID_nom ASC";
                            break;
                        case "nombre_descendente":
                            // Lógica de ordenación para "Nombre Descendente"
                            $orderBy = " ORDER BY BID_nom DESC";
                            break;
                        default:
                            // Opción de orden desconocida
                            $orderBy = ""; // Si no hay una opción válida, la cadena de ordenación es vacía
                            break;
                    }
                
                    $sql .= $orderBy;
                }
                $stmt = $conn->prepare($sql);
                
                // Concatenar '%' al término de búsqueda para buscar coincidencias parciales
                $searchTerm = "%" . $searchTerm . "%";

                $stmt->bind_param("sii", $searchTerm, $searchTermINT , $searchTermINT );
                $stmt->execute();
                $result = $stmt->get_result();
                
                // Mostrar resultados de la búsqueda
                if ($result->num_rows > 0) {
                    
                    while ($row = $result->fetch_assoc()) {
                        echo "<form action='carrito.php' method='post'>";
                        echo "<div class='contenedores'>";
                        echo "<p>";
                        echo "<img src='images/" . $row['BID_imagen_url'] . "' alt='Imagen del bidón'>";

                        echo "<p><h4>" . $row['BID_nom'] . "</h4><br>";
                        echo "Precio: $" . $row['BID_precio'] . "<br>";
                        echo "Litros: " . $row['BID_litros'] . "<br>";
                        echo "Stock: " . $row['BID_stock'] . "</p>";
                        echo "<div class='estrellas'>";
                        $numeroEstrellas = 5; // Total de estrellas
                        $valorEstrellas = $row['BID_val'];

                        for ($i = 1; $i <= $numeroEstrellas; $i++) {
                            $tipoEstrella = ($i <= $valorEstrellas) ? 'llena' : 'vacia';
                            echo "<img src='images/estrella_$tipoEstrella.png' alt='estrella $tipoEstrella'>";
                        }
                        echo "</div>";
                        echo "</p>";
                            // Agregamos campos de entrada ocultos
                        echo "<input type='hidden' name='fechaprice' value='" . $row['BID_fechaprice'] . "'>";
                        echo "<input type='hidden' name='nombre' value='" . $row['BID_nom'] . "'>";
                        echo "<input type='hidden' name='precio' value='" . $row['BID_precio'] . "'>";
                        echo "<input type='hidden' name='BID_ID' value='" . $row['BID_ID'] . "'>";
                        echo "<input type='hidden' name='cantidad' value='1'>";
                        echo "<button class='anadir-carrito' name='agregarcarrito' type='submit'> Añadir al carrito</button>";
                        echo "</div>";
                        echo "</form>";
                    }
                } else {
                    echo "<p>No se encontraron resultados.</p>";
                }
                $conn->close();
            ?>
        </div>
    </div>
    <footer>

    </footer>
</body>
</html>