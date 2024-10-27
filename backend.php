<?php
//inicia session
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
function obtenerIdUsuarioDesdeLaSesion() {
    if (isset($_SESSION["US_id"])) {
        return $_SESSION["US_id"];
    } else {
        header("Location: ingreso.html");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["registro"])) {
        // Recuperar datos del formulario
        $nombre = htmlspecialchars($_POST["nombre"]);
        $apellido = htmlspecialchars($_POST["apellido"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $email = htmlspecialchars($_POST["email"]);
        $contrasena = htmlspecialchars($_POST["contrasena"]);
        $tipo = 1;
        $calle = htmlspecialchars($_POST["calle"]);
        $callenum = htmlspecialchars($_POST["direccionnum"]);
        $comuna = htmlspecialchars($_POST["comuna"]);
        $US_id = rand(1000, 999999);
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            // Consulta SQL preparada para evitar inyección de SQL - Tabla 2 (direccion)
            $sqlDIR = "INSERT INTO direccion(DIR_CALLE,DIR_NUM,DIR_COMUNA) VALUES (?, ?, ?);";

            // Preparar la declaración
            $stmtDIR = $conn->prepare($sqlDIR);

            // Vincular parámetros (ajusta según tus campos)
            $stmtDIR->bind_param("sss", $calle, $callenum, $comuna);

            // Ejecutar la declaración para la tabla 2
            $stmtDIR->execute();

            // Obtener el DIR_id recién insertado
            $DIR_id = $stmtDIR->insert_id;

            // Cerrar la declaración para la tabla 2
            $stmtDIR->close();

            // Consulta SQL preparada para evitar inyección de SQL - Tabla 1 (usuario)
            $sqlUsuario = "INSERT INTO usuario (US_id, US_nombre, US_apellido, US_fono, US_mail, US_pass, TIPOUS_ID, DIR_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";

            // Preparar la declaración
            $stmtUsuario = $conn->prepare($sqlUsuario);

            // Vincular parámetros
            $stmtUsuario->bind_param("issssssi", $US_id, $nombre, $apellido, $telefono, $email, $contrasena, $tipo, $DIR_id);

            // Ejecutar la declaración para la tabla 1
            $stmtUsuario->execute();
            $stmtUsuario->close();

            // Confirmar la transacción
            $conn->commit();

            // Redireccionar a la página de ingreso
            header("Location: ingreso.html");
            exit();
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $conn->rollback();
            echo "Error en el registro: " . $e->getMessage();
        }
    
    }// Funcion que realiza el login 
    elseif (isset($_POST["login"])) {
        $email = htmlspecialchars($_POST["email"]);
        $contrasena = htmlspecialchars($_POST["contrasena"]);
        // Realizar consulta SQL
        $sql = "SELECT US_id, US_nombre, US_pass, US_mail, TIPOUS_ID FROM usuario WHERE US_mail = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si el usuario existe y las credenciales son correctas
        if ($row = $result->fetch_assoc()) {
            if ($contrasena == $row['US_pass']) {
                // Credenciales válidas, iniciar sesión
                $_SESSION["usuario"] = $row['US_nombre'];
                $_SESSION["US_id"] = $row['US_id'];
                $_SESSION["tipousuario"] = $row['TIPOUS_ID'];
                if($row['TIPOUS_ID'] == 0){
                    header("Location: balancesadm.php#Reporte_Balances");
                }else if($row['TIPOUS_ID'] == 1){
                    header("Location: perfilcliente.php#Historial_Compras");
                }else if($row['TIPOUS_ID'] == 2){
                    header("Location: perfilrepartidor.php#Actualizar_Estado");
                }
                exit();
            } else {
                include("ingreso.html");
                ?>
                <div class="AlertaINGRESO" role="alert">
                    <h1>ERROR AL INICIAR SESSIÓN</h1>
                    <p><strong>Email o Contraseña incorrecta.</strong></p>
                </div>
                <?php
            }
        } else {
            include("ingreso.html");
                ?>
                <div class="AlertaINGRESO" role="alert">
                    <h1>ERROR AL INICIAR SESSIÓN</h1>
                    <p><strong>Email o Contraseña incorrecta.</strong></p>
                </div>
                <?php
        }
        $stmt->close();
    }
    //Funcion para agregar Bidones
    elseif (isset($_POST["agregar_bidon"])) {

        $nombrebidon = htmlspecialchars($_POST["nombre_bidon"]);
        $preciobidon = htmlspecialchars($_POST["precio_bidon"]);
        $litrosbidon = htmlspecialchars($_POST["litros_bidon"]);
        $stockbidon = htmlspecialchars($_POST["stock_bidon"]);
        $tipo_bidon = htmlspecialchars($_POST["tipo_bidon"]);
        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] == UPLOAD_ERR_OK) {
            // Ruta de destino para la imagen (en este caso, la carpeta "images" en el mismo directorio que este script)
            $ruta_destino = "images/" . basename($_FILES["imagen"]["name"]);
    
            // Mueve el archivo a la carpeta de destino
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
                echo "La imagen se ha subido correctamente.";
            } else {
                echo "Hubo un error al subir la imagen.";
            }
        } else {
            echo "Error: No se ha seleccionado ninguna imagen.";
        }
        $imagenurl = basename($_FILES["imagen"]["name"]);
        // Realizar consulta SQL
        $sqlBidon = "INSERT INTO bidon (BID_fechaprice, BID_nom, BID_precio, TIPOBID_id, BID_litros, BID_stock, BID_imagen_url) VALUES (CURDATE(), ?, ?, ?, ?, ?, ?)";

        $stmtBidon = $conn->prepare($sqlBidon);

        $stmtBidon->bind_param("siiiis", $nombrebidon, $preciobidon, $tipo_bidon, $litrosbidon, $stockbidon,$imagenurl);
        $stmtBidon->execute();
        $stmtBidon->close();

        $conn->commit();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["modificar_bidon"])) {
        $ID_bidon = htmlspecialchars($_POST["ID_bidon"]);
        $nombre_bidon = htmlspecialchars($_POST["nombre_bidon"]);
        $precio_bidon = htmlspecialchars($_POST["precio_bidon"]);
        $litros_bidon = htmlspecialchars($_POST["litros_bidon"]);
        $stock_bidon = htmlspecialchars($_POST["stock_bidon"]);

        $Modificarbidon = "UPDATE bidon SET BID_nom=?, BID_precio=?, BID_litros=?, BID_stock=? WHERE BID_ID=?";
        $stmt = $conn->prepare($Modificarbidon);

        $stmt->bind_param("siiii", $nombre_bidon, $precio_bidon, $litros_bidon, $stock_bidon, $id_a_modificar);
        $stmt->execute();
        $stmt->close();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["eliminar_bidon"])) {
        $ID_bidon = htmlspecialchars($_POST["ID_bidon"]);
        $Eliminarfila = "DELETE FROM bidon WHERE BID_ID = ?";
        
        $stmtBidon = $conn->prepare($Eliminarfila);
        $stmtBidon->bind_param("i",  $ID_bidon);
        $stmtBidon->execute();
        $stmtBidon->close();
        header("Location: balancesadm.php#div5");
        $conn->commit();
        exit();
    }
    elseif (isset($_POST["agregar_ordencompra"])) {
    
        // Obtén el US_id del usuario de la sesión
        $US_id = obtenerIdUsuarioDesdeLaSesion();
        $Preciototal = htmlspecialchars($_POST["precio_total"]);
        $Cantidad = htmlspecialchars($_POST["cantidad"]);
        $EstadoCompra = 'Pendiente';
        $AgregarOrden = "INSERT INTO compra (COM_estado, US_id, COM_numprod,COM_fecha, COM_preciototal) VALUES (?, ?, ?,CURDATE(), ?)";
    
        $stmtInsertarOrden = $conn->prepare($AgregarOrden);
        $stmtInsertarOrden->bind_param('siii', $EstadoCompra, $US_id, $Cantidad, $Preciototal);
        $stmtInsertarOrden->execute();
        $stmtInsertarOrden->close();

        // Guardamos los detalles de la orden en una tabla intermedia
        $ultimo_id = $conn->insert_id;
        foreach ($_SESSION['carrito'] as $item) {
            $Cantidad = $item['cantidad'];
            $bidon_id = $item['ID'];
            $Fecha= date("Y-m-d", strtotime($item['fechaprice']));
            if($bidon_id != NULL){
                $AgregarDatosOrden = "INSERT INTO com_bid (COM_COMID, COM_BIDFECHA, COM_BIDID, COM_CANTIDAD) VALUES (?, ?, ?, ?)";
                $stmtInsertarDatosOrden = $conn->prepare($AgregarDatosOrden);
                $stmtInsertarDatosOrden->bind_param('isii', $ultimo_id, $Fecha,$bidon_id, $Cantidad);
                $stmtInsertarDatosOrden->execute();
                $stmtInsertarDatosOrden->close();
        
            }
        }
        $conn->commit();
        unset($_SESSION['carrito']);
        if (isset($_SESSION['tipousuario']) && $_SESSION["tipousuario"] == 0){
            header("Location: balancesadm.php#Reporte_Balances");
        }else if($_SESSION['tipousuario'] == 1){
            header("Location: perfilcliente.php#Historial_Compras");
        }else if($_SESSION['tipousuario'] == 2){
            header("Location: perfilrepartidor.php#Historial_Compras");
        }
        exit();
    }
    //Aceptar o Rechazar ordenes de compras
    elseif (isset($_POST["btn_aceptar"])){
        $ID_compra = htmlspecialchars($_POST["ID_compra"]);
        $estado = htmlspecialchars($_POST["compra-validada"]);
        $BID_ID = htmlspecialchars($_POST["BID_ID"]);
        $BID_stock = htmlspecialchars($_POST["Bid_stock"]);
        $Cantidad_solicitada = htmlspecialchars($_POST["COM_Cantidad"]);
        $BID_stock = $BID_stock - $Cantidad_solicitada;
        $ActualizarEstado = "UPDATE compra SET COM_estado = ? WHERE COM_id = ?";

        $stmtActualizarEstado = $conn->prepare($ActualizarEstado);
        $stmtActualizarEstado-> bind_param('si', $estado,$ID_compra);
        $stmtActualizarEstado-> execute();
        $stmtActualizarEstado-> close();

        $ActualizarStock = "UPDATE bidon SET BID_stock = ? WHERE BID_ID = ?";

        $stmtActualizarEstado = $conn->prepare($ActualizarStock);
        $stmtActualizarEstado-> bind_param('si', $BID_stock,$BID_ID);
        $stmtActualizarEstado-> execute();
        $stmtActualizarEstado-> close();
        header("Location: balancesadm.php#Validar_Compra");
        exit();
    }
    elseif (isset($_POST["btn_rechazar"])){
        $ID_compra = htmlspecialchars($_POST["ID_compra"]);
        $estado =htmlspecialchars($_POST["compra-rechazada"]);

        $ActualizarReferencias = "UPDATE compra SET COM_estado = ? WHERE COM_id = ?";
        $stmtActualizarReferencias = $conn->prepare($ActualizarReferencias);
        $stmtActualizarReferencias->bind_param('si', $estado, $ID_compra);
        $stmtActualizarReferencias->execute();
        $stmtActualizarReferencias->close();

        $conn->commit();
        header("Location: balancesadm.php#Validar_Compra");
        exit();
    }
    //Despachar o Entregar pedido
    elseif (isset($_POST["btn_despachado"])){
        $ID_compra = htmlspecialchars($_POST["ID_compra"]);
        $estado =htmlspecialchars($_POST["despachado"]);
        
        $ActualizarEstado = "UPDATE compra SET COM_estado = ? WHERE COM_id = ?";

        $stmtActualizarEstado = $conn->prepare($ActualizarEstado);
        $stmtActualizarEstado-> bind_param('si', $estado,$ID_compra);
        $stmtActualizarEstado-> execute();
        $stmtActualizarEstado-> close();
        header("Location: perfilrepartidor.php#Actualizar_Estado");
        exit();
    }
    elseif (isset($_POST["btn_entregado"])){
        $ID_compra = htmlspecialchars($_POST["ID_compra"]);
        $estado =htmlspecialchars($_POST["entregado"]);

        $ActualizarEstado = "UPDATE compra SET COM_estado = ? WHERE COM_id = ?";

        $stmtActualizarEstado = $conn->prepare($ActualizarEstado);
        $stmtActualizarEstado-> bind_param('si', $estado,$ID_compra);
        $stmtActualizarEstado-> execute();
        $stmtActualizarEstado-> close();
        header("Location: perfilrepartidor.php#Actualizar_Estado");
        exit();
    }
    //Agregar TIPO USUARIO
    elseif (isset($_POST["agregar_TipoUsuario"])) {

        $nombre_tipousuario = htmlspecialchars($_POST["nombre_TipoUsuario"]);
        $sqlTipousario = "INSERT INTO tipousuario (TIPOUS_DESC) VALUES (?)";

        $stmtTipousario = $conn->prepare($sqlTipousario);

        $stmtTipousario->bind_param("s", $nombre_tipousuario);
        $stmtTipousario->execute(); 
        $stmtTipousario->close();

        $conn->commit();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["modificar_TipoUsuario"])) {
        $ID_tipousuario = htmlspecialchars($_POST["ID_TipoUsuario"]);
        $nombre_tipousuario = htmlspecialchars($_POST["nombre_TipoUsuario"]);

        $ModificarTipousario = "UPDATE tipousuario SET TIPOUS_DESC=? WHERE TIPOUS_ID=?";
        $stmt = $conn->prepare($ModificarTipousario);

        $stmt->bind_param("si", $nombre_tipousuario, $ID_tipousuario);
        $stmt->execute();
        $stmt->close();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["eliminar_TipoUsuario"])) {
        $ID_tipousuario  = htmlspecialchars($_POST["ID_TipoUsuario"]);
        $Eliminarfila = "DELETE FROM tipousuario WHERE TIPOUS_ID = ?";
        
        $stmtTipousario = $conn->prepare($Eliminarfila);
        $stmtTipousario->bind_param("i",  $ID_tipousuario);
        $stmtTipousario->execute();
        $stmtTipousario->close();
        header("Location: balancesadm.php#div5");
        $conn->commit();
        exit();
    }
    //Agregar TIPO BIDON
    elseif (isset($_POST["agregar_Tipobidon"])) {

        $nombre_tipobidon = htmlspecialchars($_POST["nombre_tipobidon"]);
        $sqlTipobidon = "INSERT INTO tipo_bidon (TIPOBID_desc) VALUES (?)";

        $stmt = $conn->prepare($sqlTipobidon);

        $stmt->bind_param("s", $nombre_tipobidon);
        $stmt->execute(); 
        $stmt->close();

        $conn->commit();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["modificar_Tipobidon"])) {
        $ID_tipobidon = htmlspecialchars($_POST["ID_tipobidon"]);
        $nombre_tipobidon = htmlspecialchars($_POST["nombre_tipobidon"]);

        $Modificartipobidon = "UPDATE tipo_bidon SET TIPOBID_desc=? WHERE TIPOBID_id=?";
        $stmt = $conn->prepare($Modificartipobidon);

        $stmt->bind_param("si", $nombre_tipobidon, $ID_tipobidon);
        $stmt->execute();
        $stmt->close();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["eliminar_Tipobidon"])) {
        $ID_tipobidon  = htmlspecialchars($_POST["ID_tipobidon"]);
        $Eliminarfila = "DELETE FROM tipo_bidon WHERE TIPOBID_id = ?";
        
        $stmt = $conn->prepare($Eliminarfila);
        $stmt->bind_param("i",  $ID_tipobidon);
        $stmt->execute();
        $stmt->close();
        header("Location: balancesadm.php#div5");
        $conn->commit();
        exit();
    }
    elseif (isset($_POST["buscar_pedido"])) {
        $ID_Pedido = htmlspecialchars($_POST["orden_compra"]);

        // Consulta SQL para obtener el estado del pedido desde la base de datos
        $Rastrear = "SELECT COM_estado FROM compra WHERE COM_id = ?";

        $stmt = $conn->prepare($Rastrear);
        $stmt->bind_param("i", $ID_Pedido);
        $stmt->execute();
        $stmt->bind_result($estado);
        $stmt->fetch();
        $stmt->close();
        $_SESSION["estado"] = $estado;
        header('Location: seguimiento.php');
    }
    elseif (isset($_POST["cerrar_session"])){
        header('Location: index.php');
        unset($_SESSION["usuario"]);
        unset($_SESSION["US_id"]);
    }
    elseif (isset($_POST["agregar_Usuario"])) {
        $nombre = htmlspecialchars($_POST["nombre"]);
        $apellido = htmlspecialchars($_POST["apellido"]);
        $contrasena = htmlspecialchars($_POST["contrasena"]);
        $email = htmlspecialchars($_POST["email"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $tipo = htmlspecialchars($_POST["tipo_us"]);
        $calle = htmlspecialchars($_POST["calle"]);
        $callenum = htmlspecialchars($_POST["direccionnum"]);
        $comuna = htmlspecialchars($_POST["comuna"]);
        $US_id = rand(10, 999999);
        // Iniciar una transacción
        $conn->begin_transaction();

        try {
            $sqlDIR = "INSERT INTO direccion (DIR_CALLE,DIR_NUM,DIR_COMUNA) VALUES (?, ?, ?);";
            $stmtDIR = $conn->prepare($sqlDIR);
            $stmtDIR->bind_param("sss", $calle, $callenum, $comuna);
            $stmtDIR->execute();
            $DIR_id = $stmtDIR->insert_id;
            $stmtDIR->close();
            $sqlUsuario = "INSERT INTO usuario (US_id, US_nombre, US_apellido, US_fono, US_mail, US_pass, TIPOUS_ID, DIR_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
            $stmtUsuario = $conn->prepare($sqlUsuario);

            $stmtUsuario->bind_param("issssssi", $US_id, $nombre, $apellido, $telefono, $email, $contrasena, $tipo, $DIR_id);
            $stmtUsuario->execute();
            $stmtUsuario->close();

            $conn->commit();
            header("Location: balancesadm.php#div5");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error en el registro: " . $e->getMessage();
        }
    }
    elseif (isset($_POST["modificar_Usuario"])) {
        $ID_usuario = htmlspecialchars($_POST["ID_usuario"]);
        $nombre = htmlspecialchars($_POST["nombre"]);
        $apellido = htmlspecialchars($_POST["apellido"]);
        $contrasena = htmlspecialchars($_POST["contrasena"]);
        $email = htmlspecialchars($_POST["email"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $tipo = htmlspecialchars($_POST["tipo_us"]);
        $calle = htmlspecialchars($_POST["calle"]);
        $callenum = htmlspecialchars($_POST["direccionnum"]);
        $comuna = htmlspecialchars($_POST["comuna"]);

        $ModificarDir = "INSERT INTO direccion (DIR_CALLE,DIR_NUM,DIR_COMUNA) VALUES (?, ?, ?);";
        $stmtDIR = $conn->prepare($ModificarDir);
        $stmtDIR->bind_param("sss", $calle, $callenum, $comuna);
        $stmtDIR->execute();
        $DIR_id = $stmtDIR->insert_id;
        $stmtDIR->close();

        $ModificarUSU = "UPDATE usuario SET US_nombre=?, US_apellido=?, US_pass=?, US_fono=?, US_mail=?,TIPOUS_ID=?, DIR_id=? WHERE US_id=?";
        $stmt = $conn->prepare($ModificarUSU);

        $stmt->bind_param("issssssi", $US_id, $nombre, $apellido, $telefono, $email, $contrasena, $tipo, $DIR_id);
        $stmt->execute();
        $stmt->close();
        
        $conn->commit();
        header("Location: balancesadm.php#div5");
        exit();
    }
    elseif (isset($_POST["eliminar_Usuario"])) {
        $ID_usuario = htmlspecialchars($_POST["ID_usuario"]);
        $EliminarUs_decompra = "UPDATE compra SET US_id = NULL WHERE US_id = ?";
        $stmt = $conn->prepare($EliminarUs_decompra);
        $stmt->bind_param("i",  $ID_usuario);
        $stmt->execute();
        $stmt->close();

        $Eliminarfila = "DELETE FROM usuario WHERE US_id = ?";
        
        $stmt = $conn->prepare($Eliminarfila);
        $stmt->bind_param("i",  $ID_usuario);
        $stmt->execute();
        $stmt->close();

        header("Location: balancesadm.php#div5");
        $conn->commit();
        exit();
    }
    elseif (isset($_POST['enviar_valoracion'])){
        $bidID = htmlspecialchars($_POST["id_bidon"]);
        $Valoracion = htmlspecialchars($_POST["puntuacion"]);
    
        // Verificar si ya hay una valoración existente
        $ObtenerValoracionActual = "SELECT BID_cantVAL, BID_val FROM bidon WHERE BID_ID=?";
        $stmt = $conn->prepare($ObtenerValoracionActual);
        $stmt->bind_param("i", $bidID);
        $stmt->execute();
        $stmt->bind_result($CantValoracionActual, $ValoracionActual);
        $stmt->fetch();
        $stmt->close();
    
        if ($CantValoracionActual > 0) {
            // Actualizar la tabla bidon con la nueva cantidad de valoraciones y la nueva valoración promediada
            $CantValoracionNueva = $CantValoracionActual + 1;
            $ValoracionPromediadaNueva = ($ValoracionActual * $CantValoracionActual + $Valoracion) / $CantValoracionNueva;
    
            $Modificarbidon = "UPDATE bidon SET BID_cantVAL=?, BID_val=? WHERE BID_ID=?";
            $stmt = $conn->prepare($Modificarbidon);
            $stmt->bind_param("idi", $CantValoracionNueva, $ValoracionPromediadaNueva, $bidID);
            $stmt->execute();
            $stmt->close();
        } else {
            // No hay valoración existente, establecer la nueva valoración directamente
            $CantValoracionNueva = 1;
    
            $Modificarbidon = "UPDATE bidon SET BID_cantVAL=?, BID_val=? WHERE BID_ID=?";
            $stmt = $conn->prepare($Modificarbidon);
            $stmt->bind_param("idi", $CantValoracionNueva, $Valoracion, $bidID);
            $stmt->execute();
            $stmt->close();
        }
    
        // Resto de tu código
        $conn->commit();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    elseif (isset($_POST['anadir_valoracion'])){
        unset($_SESSION["boton_presionado"]);
        header('Location: catalogo.php');
    }
}
// Cerrar la conexión
$conn->close();
?>

