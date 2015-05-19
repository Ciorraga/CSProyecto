<?php

include "../vendor/autoload.php";
require_once "../config.php";

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

//Página de inicio de la aplicación
$app->get('/', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        unset($_SESSION);
        session_destroy();
        $app->render('inicio.html.twig');
    } else {
        $app->render('inicio.html.twig', array('usuarioLogin' => $_SESSION['usuarioLogin']));
        die();
    }
})->name('inicio');

//Cuando pulsamos en Salir para cerrar nuestra sesión
$app->get('/cerrarSesion', function() use ($app) {
    session_destroy();
    $app->render('inicio.html.twig');
    die();
})->name('cerrarSesion');

$app->get('/registro', function() use ($app) {
    $app->render('nuevoUser.html.twig');
})->name('registro');

//------------------------------------------------------------------------POSTS--------
//Cuando pulsamos en el boton de ACEPTAR en el login
$app->post('/login', function() use ($app) {
    $usuario = ORM::for_table('usuario')->where('user', $_POST['username'])->where('password', $_POST['password'])->find_one();
    if($usuario){
        $_SESSION['usuarioLogin'] = $usuario;
        $app->render('inicio.html.twig', array('usuarioLogin' => $usuario));
        die();
    }
    else{
        $app->render('inicio.html.twig');
        die();
    }
});

$app->post('/registro', function() use ($app) {
    if(!$_POST['user'] || !$_POST['pass1'] || !$_POST['pass2'] || !$_POST['email'] || !$_POST['steam'] || !$_POST['nombre'] || !$_POST['edad']){
        $app->render('nuevoUser.html.twig', array('alta' => "campos"));
        die();
    }else{
        if ($_POST['pass1'] == $_POST['pass2']) {
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
            if(!isset($_POST['imagen'])){
                $nuevoUser->imagen = "imagenes/interrogacion.jpg";
            }else{
                $nuevoUser->imagen = $_POST['imagen'];
            }
            $nuevoUser->save();
            $app->render('inicio.html.twig', array('alta' => "ok"));
            die();
        } else {
            $app->render('nuevoUser.html.twig', array('alta' => "pass"));
            die();
        }
    }
});




$app->run();

