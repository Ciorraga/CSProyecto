<?php

include "../vendor/autoload.php";
require_once "../config.php";
include "comun.php";


$app = new \Slim\Slim(
        array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => '../templates',
    'debug' => true
        )
);

$view = $app->view();
$view->parserOptions = array(
    'charset' => 'utf-8',
    'debug' => true,
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);

# Add extensions.

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
    new \Twig_Extension_Debug()
);

session_cache_limiter(false);
session_start();

include "controller/administracion/administracion.php";
include "controller/inicio/inicio.php";
include "controller/equipos/equipos.php";
include "controller/miCuenta/miCuenta.php";
include "controller/mensajes/mensajes.php";
include "controller/solicitudes/solicitudes.php";
include "controller/retos/retos.php";
include "controller/clasificacion/clasificacionEquipos.php";


//Cuando pulsamos en Salir para cerrar nuestra sesión
$app->get('/cerrarSesion', function() use ($app) {
    session_destroy();
    $app->redirect($app->router()->urlFor('inicio'));
    die();
})->name('cerrarSesion');

//Sección de registro de un nuevo usuario
$app->get('/registro', function() use ($app) {
    $app->render('nuevoUser.html.twig');
})->name('registro');







//------------------------------------------------------------------------POSTS-------------------------------------------------------------------------

$app->post('/registro', function() use ($app) {
    if(!$_POST['user'] || !$_POST['pass1'] || !$_POST['pass2'] || !$_POST['email'] || !$_POST['steam'] || !$_POST['nombre'] || !$_POST['edad']){
        $app->render('nuevoUser.html.twig', array('mensajeError' => "Debes rellenar como mínimo los campos marcados"));
        die();
    }else{
        if ($_POST['pass1'] == $_POST['pass2']) {
            $req = new comun();
            $compUser = $req->compruebaNombres('usuario','user',$_POST['user']);
            $compMail = $req->compruebaNombres('usuario','email',$_POST['email']);
            if($compUser){
                $app->render('nuevoUser.html.twig', array('mensajeError' => "El nombre de usuario ya exite. Por favor, elije otro"));
                die();
            }
            if($compMail){
                $app->render('nuevoUser.html.twig', array('mensajeError' => "El e-mail ya existe. Por favor elije  otro"));
                die();
            }
                if($_FILES["imagenUser"]['name'] != null){
                    //Subida de la imagen
                    $num = 0;
                    $dir = "./imagenes/usuarios/";
                    $file = basename($_FILES["imagenUser"]["name"]);

                    // Comprueba si es una imagen o no
                    $check = getimagesize($_FILES["imagenUser"]["tmp_name"]);
                    if($check == false) {
                        //Lanzar alerta de que no es una imagen
                        $mensajeError = "El archivo que ha seleccionado NO es una imagen";
                        $app->render('nuevoUser.html.twig', array('mensajeError' => $mensajeError));
                        die();
                    }

                    // Comprobamos el tamaño de la imagen
                    if ($_FILES["imagenUser"]["size"] > 300000) {
                        $mensajeError = "El archivo es demasiado grande";
                        $app->render('nuevoUser.html.twig',array('mensajeError' => $mensajeError));
                        die();
                    }

                    //Comprobación de que si el fichero existe, se le añade un número
                    $fileN = explode(".",$file);
                    while(file_exists($dir . $file)){
                        $num++;
                        $file = $fileN[0]."".$num.".".$fileN[1];
                    }
                    $dirFile = $dir ."". $file;

                    // Subimos la imagen
                    if (move_uploaded_file($_FILES["imagenUser"]["tmp_name"], $dirFile)) {
                        $mensajeOk = "El archivo ". basename( $_FILES["imagenUser"]["name"]). " ha sido subido con éxito";
                    } else {
                        $mensajeError = "El archivo no pudo ser subido";
                        $app->render('nuevoUser.html.twig', array('mensajeError' => $mensajeError));
                        die();
                    }
                }
                $nuevoUser = ORM::for_table('usuario')->create();
                $nuevoUser->user = $_POST['user'];
                $nuevoUser->password = $_POST['pass1'];
                $nuevoUser->nombre = $_POST['nombre'];
                if(!$_POST['apellidos']){
                    $nuevoUser->apellidos = " ";
                }else{
                    $nuevoUser->apellidos = $_POST['apellidos'];
                }
                $nuevoUser->email = $_POST['email'];
                $nuevoUser->steam = $_POST['steam'];
                $nuevoUser->edad = $_POST['edad'];
                if($_FILES["imagenUser"]['name'] == null){
                    $nuevoUser->imagen = "/imagenes/interrogacion.jpg";
                }else{
                    $nuevoUser->imagen = $dirFile;
                }
                $nuevoUser->save();

                $notic = $req->mostrarNoticias();
                $app->render('inicio.html.twig', array('mensajeOk' => "Usuario creado con éxito!!Prueba a ingresar!",'noticias' => $notic));
                die();

        } else {
            $app->render('nuevoUser.html.twig', array('mensajeError' => "Las contraseñas no coinciden"));
            die();
        }
    }
});





$app->post('/', function() use ($app) {
    include "controller/inicio/botonesInicio.php";
    include "controller/administracion/botonesAdministracion.php";
    include "controller/equipos/botonesEquipo.php";
    include "controller/mensajes/botonesMensaje.php";
    include "controller/solicitudes/botonesSolicitudes.php";
    include "controller/retos/botonesReto.php";

    if(isset($_POST['loginUsuario'])){
        $usuario = ORM::for_table('usuario')->where('user', $_POST['username'])->where('password', $_POST['password'])->find_one();
        if($usuario){
            $_SESSION['solicitudes'] = 0;
            $_SESSION['usuarioLogin'] = $usuario;
            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
            $app->redirect($app->router()->urlFor('inicio'));
            die();
        }
        else{
            $req = new comun();
            $notic = $req->mostrarNoticias();
            $app->render('inicio.html.twig',array('usuarioLoginError' => '1','noticias' => $notic));
        }
    }


});




$app->run();

