<?php
session_start();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar-Estado-AquaWeb</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body id="cuerpo">
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
                echo '<a href="perfilrepartidor.php#Actualizar_Estado" class="NavActive">' . $_SESSION['usuario'] . '</a>';
            } else {
                echo '<a href="perfilrepartidor.php#Actualizar_Estado" class="NavActive">Tu perfil</a>';
            }
            ?>
        </nav>
        </div>
    </header>
    <div class="todo">
        <div class="Menu_Administrador">
            <nav>
                <a href="#Actualizar_Estado">Actualizar Estado</a>
                <a href="#Historial_Compras">Historial de Compras</a>
                <a href="#Reporte_Compras">Reporte Compras</a>
                <a href="#Cerrar_Session">Cerrar Sesión</a>
            </nav>
        </div>
    
        <div class="Cuadrado_variable">
            <div id="Actualizar_Estado" class="tab-content2">
                <h1>Actualizar Estado</h1>
                <div class="diseno_contenedor_table">
                    <h2>Pedidos para despachar</h2>
                    <?php
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
                        $sql = "SELECT COM_id, COM_numprod, DIR_CALLE, DIR_NUM, DIR_COMUNA FROM compra, usuario, direccion WHERE compra.US_id = usuario.US_id AND usuario.DIR_id = direccion.DIR_ID AND COM_estado = 'compra validada' ORDER BY COM_id ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $prevCOM_id = null;
                        if ($resultado->num_rows > 0) {
                            echo '<table border="1">';
                            echo '<tr><th>Orden de Compras</th><th>Cantidad de Bidones</th><th>Direccion</th><th>Número de casa</th><th>Comuna</th><th>Despachar</th></tr>';
                        
                            while ($fila = $resultado->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $fila['COM_id'] . '</td>';
                                echo '<td>' . $fila['COM_numprod'] . '</td>';
                                echo '<td>' . $fila['DIR_CALLE'] . '</td>';
                                echo '<td>' . $fila['DIR_NUM'] . '</td>';
                                echo '<td>' . $fila['DIR_COMUNA'] . '</td>';
                        
                                // Agregar botón solo para la primera aparición de COM_id
                                if ($fila['COM_id'] != $prevCOM_id) {
                                    echo "<form action='backend.php' method='post'>";
                                    echo '<input type="hidden" name="ID_compra" value="' . $fila['COM_id'] . '" />'; 
                                    echo '<input type="hidden" name="despachado" value="despachado" />';
                                    echo '<td><button name="btn_despachado" type="submit">Despacho</button></td>';
                                    echo "</form>";
                                } else {
                                    echo '<td></td>';
                                }
                        
                                echo '</tr>';
                        
                                $prevCOM_id = $fila['COM_id'];
                            }
                        
                            echo '</table>';
                        } else {
                            echo 'No se encontraron resultados';
                        }
                        
                        $stmt->close();
                        $conn->close();
                    ?>
                    <h2>Pedidos por entregar</h2>
                    <?php
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
                        $sql = "SELECT COM_id, COM_numprod, DIR_CALLE, DIR_NUM, DIR_COMUNA FROM compra, usuario, direccion WHERE compra.US_id = usuario.US_id AND usuario.DIR_id = direccion.DIR_ID AND COM_estado = 'despachado' ORDER BY COM_id ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $resultado = $stmt->get_result();
                        $prevCOM_id = null;
                        if ($resultado->num_rows > 0) {
                            echo '<table border="1">';
                            echo '<tr><th>Orden de Compras</th><th>Cantidad de Bidones</th><th>Direccion</th><th>Número de casa</th><th>Comuna</th><th>Despachar</th></tr>';
                        
                            while ($fila = $resultado->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $fila['COM_id'] . '</td>';
                                echo '<td>' . $fila['COM_numprod'] . '</td>';
                                echo '<td>' . $fila['DIR_CALLE'] . '</td>';
                                echo '<td>' . $fila['DIR_NUM'] . '</td>';
                                echo '<td>' . $fila['DIR_COMUNA'] . '</td>';
                        
                                // Agregar botón solo para la primera aparición de COM_id
                                if ($fila['COM_id'] != $prevCOM_id) {
                                    echo "<form action='backend.php' method='post'>";
                                    echo '<input type="hidden" name="ID_compra" value="' . $fila['COM_id'] . '" />'; 
                                    echo '<input type="hidden" name="entregado" value="entregado" />';
                                    echo '<td><button name="btn_entregado" type="submit">Entregado</button></td>';
                                    echo "</form>";
                                } else {
                                    echo '<td></td>';
                                }
                        
                                echo '</tr>';
                        
                                $prevCOM_id = $fila['COM_id'];
                            }
                        
                            echo '</table>';
                        } else {
                            echo 'No se encontraron resultados';
                        }
                        
                        $stmt->close();
                        $conn->close();
                    ?>
                </div>
            </div>
            <div id="Historial_Compras" class="tab-content2">
                <h1>Historial de Compras</h1>
                <?php
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
                    $US_id = $_SESSION['US_id'];
                    $sql = "SELECT * FROM `compra` WHERE `US_id` = ? ORDER BY COM_id DESC";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $US_id);
                    $stmt->execute();
                    if ($stmt->error) {
                        die("Error al ejecutar la consulta: " . $stmt->error);
                    }
                    $resultado = $stmt->get_result();

                    if ($resultado->num_rows > 0) {
                            echo '<div>';
                            echo '<table border="1">';
                            echo '<tr><th>Orden de compra</th><th>Estado</th><th>Número de productos</th><th>fecha</th><th>Total</th></tr>';
                        while ($fila = $resultado->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $fila['COM_id'] . '</td>';
                            echo '<td>' . $fila['COM_estado'] . '</td>';
                            echo '<td>' . $fila['COM_numprod'] . '</td>';
                            echo '<td>' . $fila['COM_fecha'] . '</td>';
                            echo '<td>' . $fila['COM_preciototal'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                        echo '</div>';
                    } else {
                        echo 'No se ha registrado ninguna compra aun.';
                    }

                    $stmt->close();
                    $conn->close();
                    ?>
            </div>
            <div id="Reporte_Compras" class="tab-content2">
                <h1>Reporte Compras</h1>
                <div class="diseno_contenedor_table1">
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
                    ?>
                    <div class="btn_cambiorango">
                        <form action="perfilrepartidor.php#Reporte_Compras" method="post">
                            <p><button type="submit" name="options" value="DAY"></button>Últimas 24 Horas</p>
                            <p><button type="submit" name="options" value="WEEK"></button>Última semana</p>
                            <p><button type="submit" name="options" value="MONTH"></button>Último mes</p>
                            <p><button type="submit" name="options" value="YEAR"></button>Último año</p>
                            <p>
                                <label for="fecha_inicio">Fecha inicio:</label>
                                <input type="date" name="fecha_inicio">
                            </p>
                            <p>
                                    <label for="fecha_fin">Fecha fin:</label>
                                <input type="date" name="fecha_fin">
                            </p>
                            <p>
                                <button type="submit" name="options" value="CUSTOM"></button>
                                Buscar Rango
                            </p>
                        </form>
                    </div>
                    <?php
                    $rango = isset($_POST["options"]) ? $_POST["options"] : "WEEK";
                    $US_id = $_SESSION['US_id'];
                    if ($rango === "CUSTOM") {
                        $fecha_inicio = isset($_POST["fecha_inicio"]) ? $_POST["fecha_inicio"] : "";
                        $fecha_fin = isset($_POST["fecha_fin"]) ? $_POST["fecha_fin"] : "";
                        $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio));
                        $fecha_fin = date("Y-m-d", strtotime($fecha_fin));
                        $sql = "SELECT COM_fecha, SUM(COM_numprod) AS NUMP, SUM(`COM_preciototal`) AS TOTAL FROM `compra` WHERE `US_id` = ? AND COM_fecha BETWEEN ? AND ? GROUP BY COM_fecha;";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("iss", $US_id, $fecha_inicio, $fecha_fin);
                        
                    }
                    else {
                        $sql = "SELECT COM_fecha, SUM(COM_numprod) AS NUMP, SUM(`COM_preciototal`) AS TOTAL FROM `compra` WHERE COM_fecha >= DATE_SUB(CURDATE(), INTERVAL 1 $rango) AND `US_id` = ? GROUP BY COM_fecha;";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $US_id);
                    }
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    if ($resultado->num_rows > 0) {
                        echo "<div class= 'tablabalances'>";
                        echo '<table border="1">';
                        echo '<tr><th>Fecha de Compra</th><th>Cantidad  de Productos</th><th>Total</th></tr>';
                        $SumaTotal = 0;
                        while ($fila = $resultado->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . $fila['COM_fecha'] . '</td>';
                            echo '<td>' . $fila['NUMP'] . '</td>';
                            echo '<td>$' . number_format($fila['TOTAL'], 0) . '</td>';
                            $SumaTotal = $SumaTotal + $fila['TOTAL'];
                            echo '</tr>';
                        }
                        echo '<tr><th></th><th>Suma total</th><td>$'. number_format($SumaTotal, 0) . '</td></tr>';
                        echo '</table>';
                        echo "</div>";
                    } else {
                        echo 'No se encontraron resultados';
                    }
                    
                    $stmt->close();
                    $conn->close();
                    ?>
                </div>
            </div>
            <div id="Cerrar_Session" class="tab-content">
                <h1>Seguro que Desea Cerrar Sessión</h1>
                <div class=" btns_yes_no">
                    <form action="backend.php" method="post">
                        <button type="submit" name="cerrar_session">Si</button>
                    </form>
                    <form action="perfilrepartidor.php#Actualizar_Estado" method="post">
                        <button >No</button>
                    </form>
                </div>
                

            </div>
        </div>
    </div>
    <footer>

    </footer>
               
</body>
</html>