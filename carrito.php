<?php
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $carrito_user = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $cantidad = $_POST['cantidad'];
        $bid_id = $_POST['BID_ID'];
        $fechaprice = $_POST['fechaprice'];
        // Verificar si el producto ya está en el carrito
        $producto_encontrado = false;
        foreach ($carrito_user as &$item) {
            if ($item != null && isset($item['nombre']) && $item['nombre'] === $nombre) {
                $item['cantidad'] += $cantidad;
                $producto_encontrado = true;
                break;
            }
        }

        // Si el producto no está en el carrito, agregarlo
        if (!$producto_encontrado) {
            $carrito_user[] = array("nombre" => $nombre, "precio" => $precio, "cantidad" => $cantidad,"ID"=> $bid_id,"fechaprice"=> $fechaprice);
        }

        $_SESSION['carrito'] = $carrito_user;
    }

    // Redireccionar al usuario a la página anterior
    header('Location: ' . $_SERVER['HTTP_REFERER']);
?>