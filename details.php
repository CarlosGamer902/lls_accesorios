<?php

require 'config/config.php';
require 'config/conexion.php';
$db = new Database();
$con = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if($id == '' || $token == ''){
    echo 'Error al procesar la peticion';
    exit;
}else {
    $token_tmp = hash_hmac('sha1',$id,KEY_TOKEN);

    if ($token == $token_tmp) {
        
        $sql = $con->prepare("SELECT count(id) FROM productos WHERE id=? AND activo=1");
        $sql->execute([$id]);
        if ($sql->fetchColumn() > 0) {
            
            $sql = $con->prepare("SELECT id, nombre, descripcion, precio, descuento FROM productos WHERE id=? AND activo=1 LIMIT 1");
            $sql->execute([$id]);
            $row = $sql->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $nombre = $row['nombre'];
            $descripcion = $row['descripcion'];
            $precio = $row['precio'];
            $descuento = $row['descuento'];
            $precio_desc = $precio - (($precio * $descuento) / 100);
            $dir_images = 'images/productos/'. $id . '/';

            $rutaImg = $dir_images . 'principal.png';

            if (!file_exists($rutaImg)) {
                $rutaImg = 'images/no_foto.jpg';
            }

            $imagenes = array();
            $dir = dir($dir_images);
            while (($archivo = $dir->read()) != false) {
                if($archivo != 'principal.png' && (strpos($archivo, 'png') || strpos($archivo, 'jpg'))){
                    $imagenes[] =  $dir_images . $archivo;
                }
            }
            $dir->close();
        }

    }else {
        echo 'Error al procesar la peticion';
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LLS Accesorios | Tienda Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
    
</head>

<header>
  <div class="collapse bg-dark" id="navbarHeader">
    
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
      <a href="index.php" class="navbar-brand d-flex align-items-center">
        <strong>LLS Accesorios</strong>
      </a>
      <button type="button" class="btn btn-success" onclick="location.href='carrito.php'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
        <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0l3-3z"/>
        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1H.5zm3.915 10L3.102 4h10.796l-1.313 7h-8.17zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
        </svg>
    </div>
  </div>
</header>
<body>
    
<main class="album py-5 bg-light">
    <div class="container"> 
        <div class="row">
            <div class="col-md-6 order-md-1">

                <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                    <img src="<?php echo $rutaImg; ?>" class="d-block w-100">
                    </div>

                    <?php foreach($imagenes as $img) { ?>
                        <div class="carousel-item">
                            <img src="<?php echo $img; ?>" class="d-block w-100">
                        
                        </div>
                    <?php } ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
                </div>


            </div>
            <div class="col-md-6 order-md-2">
                <h2><?php echo $nombre; ?></h2>

                <?php if ($descuento > 0) { ?>
                    <p><del>$ <?php echo number_format($precio, 2 ,'.',','); ?></del></p>
                    <h2>$ <?php echo number_format($precio_desc, 2 ,'.',','); ?> 
                    <small class="text-success"><?php echo $descuento; ?>% de descuento</small>
                    </h2>
                    <?php } else{ ?>
                        <h2>$ <?php echo number_format($precio, 2 ,'.',','); ?></h2>
                    <?php } ?>
                <p class="lead">
                    <?php echo $descripcion; ?>
                </p>

                <div class="d-grid gap-3 col-10 mx-auto">
                    <button class="btn btn-outline-success" type="button">Comprar ahora</button>
                    <button class="btn btn-outline-primary" type="button">Agregar al carrito</button>
                </div>

            </div>

        </div>
    </div>
</main>

</body>
</html>