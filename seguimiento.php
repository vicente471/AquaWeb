<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento-AquaWeb</title>
    <link rel="stylesheet" href="styles.css">
    
</head>
<body>
    <header>
        <div class="logo">
            <img src="./images/Logo.jpg" alt="AquaWeb Logo">
            <h1>AquaWeb</h1>
        </div>
        <div class="Espacio_nav">
            <nav>
                <a href="index.php" class="NavnoActive">Home</a>
                <a href="catalogo.php" class="NavnoActive">Catálogo</a>
                <a href="" class="NavActive">Seguimiento</a>
                <?php
                session_start();
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
    <?php 
        $pedido_estado="";
        if (isset($_SESSION["estado"]) && !empty($_SESSION["estado"])) {
            $pedido_estado = $_SESSION["estado"]; // Asume que el estado del pedido se obtiene de la base de datos
            $estilo = "";
            $estilo2 = "";
            $estilo3 = "";
            $estilo4 = "";
            $estilo5 = "";
            switch ($pedido_estado) {
                case "Pendiente":
                    $estilo = "contenedor_estado1_pendiente";
                    break;
                case "compra validada":
                    $estilo2 = "contenedor_estado1_confirmado";
                    break;
                case "despachado":
                    $estilo3 = "contenedor_estado1_despachado";
                    break;
                case "entregado":
                    $estilo4 = "contenedor_estado1_entregado";
                    break;
                case "anulado":
                    $estilo5 = "contenedor_estado1_anulado";
                    break;    
            }
            unset($_SESSION["estado"]);
        }
    ?>
    <div class="seguimiento">
        <h1>Revisa el seguimiento de tu pedido</h1>
        <div class="seguimiento_estado_pedido">
            <h2>El estado del pedido es: </h2>
            <?php if ($pedido_estado == "anulado"): ?>
                <!-- Mostrar solo si el estado es "Anulado" -->
                <div id="pedido_rechazado" class="pedido_rechazado">
                    <div class="contenedor_estadocancelado <?php echo $estilo5; ?>"><img src="images/cerrar.png" alt="tarea-cancelada"><h4>Pedido Anulado</h4></div>
                    <h3>El pedido ha sido anulado por falta de stock</h3>
                </div>
            <?php else: ?>
                <!-- Mostrar si el estado no es "Anulado" -->
                <div id="pedido_confirmado" class="pedido_confirmado">
                    <div class="contenedor_estado <?php echo $estilo; ?>"><img src="images/tareas-pendientes.png" alt="tarea-pendientes"><h4>Pendiente</h4></div>
                    <div class="contenedor_estado <?php echo $estilo2; ?>"><img src="images/carrito_confirmado.png" alt="tarea-confirmada"><h4>Confirmado</h4></div>
                    <div class="contenedor_estado <?php echo $estilo3; ?>"><img src="images/camion.png" alt="tarea-despachada"><h4>Despachado</h4></div>
                    <div class="contenedor_estado <?php echo $estilo4; ?>"><img src="images/linea_de_meta.png" alt="tarea-entregada"><h4>Entregado</h4></div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="orden_seguimiento">
            <h3>Ingrese su orden de compra:</h3> 
            <form action="backend.php" method="post">
                <div class="orden_seguimiento_ingreso">
                    <input type="text" id="orden_compra" name="orden_compra" required >
                    <button type="submit" name="buscar_pedido">Revisar estado</button>
                </div>
            </form>
        </div>
    </div>
    
    <footer>

    </footer>
</body>
</html>