<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaWeb-Home</title>
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
                <a href="#" class="NavActive">Home</a>
                <a href="catalogo.php" class="NavnoActive">Catálogo</a>
                <a href="seguimiento.php" class="NavnoActive">Seguimiento</a>
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
    <div class="FondoCompleto">
        <div class="ladoA_texto">
            <h1>Consigue la mejor agua de la zona Metropolitana</h1>
            <h2>Contamos con:</h2>
        </div>
        <div class="ladoB_bidones">
            <img src="./images/bidones.webp" alt="Bidones Inicio">
        </div>
    </div>
    <div class="Botones_verdes">
        <div class="separacion">
            <div class="CuadradoDespacho">
                <div class="cabecera">
                    <img src="./images/camion.png" alt="svgCamion">
                    <h3>Despacho veloz y seguro</h3>
                </div>
                <h4>Despachamos a la brevedad, dentro de nuestras factibilidades logísticas en Santiago.</h4>
            </div>
        </div>
        <div class="separacion">
            <div class="CuadradoDespacho">
                <div class="cabecera">
                    <img src="./images/fiabilidad.png" alt="svgcalidad">
                    <h3>Calidad y servicio</h3>
                </div>
                <h4>Nuestra agua mantiene un PH neutro, almacenada en envases libres de BPA.</h4>
            </div>
        </div>
        
    </div>
    
    <footer>

    </footer>
</body>
</html>