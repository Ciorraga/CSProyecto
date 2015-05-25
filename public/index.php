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
        $app->render('inicio.html.twig', array('numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin']));
        die();
    }
})->name('inicio');

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

//Sección mi cuenta. Sólo puede acceder a ella un usuario previamente logueado
$app->get('/miCuenta', function() use ($app) {
    if(isset($_SESSION['usuarioLogin'])){
        $app->render('miCuenta.html.twig',array('numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
    }else{
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
})->name('miCuenta');

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/entrada', function() use ($app) {
    $mensajes = ORM::for_table('mensaje')->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))->where('usuario_id',$_SESSION['usuarioLogin']['id'])->find_many();
    //var_dump($mensajes);die();
    $app->render('mensajesEntradaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes']));
    die();
})->name('entrada');

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/salida', function() use ($app) {
    $mensajes = ORM::for_table('mensaje')->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))->where('remitente_id',$_SESSION['usuarioLogin']['id'])->find_many();
    $app->render('mensajesSalidaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes']));
    die();
})->name('salida');

//------------------------------------------------------------------------POSTS--------
//Cuando pulsamos en el boton de ACEPTAR en el login
$app->post('/login', function() use ($app) {
    $usuario = ORM::for_table('usuario')->where('user', $_POST['username'])->where('password', $_POST['password'])->find_one();
    if($usuario){
        $_SESSION['usuarioLogin'] = $usuario;
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')->where('usuario_id', $_SESSION['usuarioLogin']['id'])->where('leido',0)->count();

        $app->render('inicio.html.twig',array('numMensajes' => $_SESSION['numMensajes'] , 'usuarioLogin' => $usuario));
        die();
    }
    else{
        $app->render('inicio.html.twig', array('usuarioLoginError' => '1'));
        die();
    }
});

//Cuando pulsamos en el boton de CREAR en el registro de usuario
$app->post('/', function() use ($app) {
   if(isset($_POST['botonRespondeMensaje'])){
       $fecha_actual=date("Y/m/d");
       $titulo = "Re:".$_POST['titulo'];
       $texto = htmlentities($_POST['textoMensaje']);
       $destinatario = htmlentities($_POST['botonRespondeMensaje']);
       $remitente = $_SESSION['usuarioLogin']['id'];

       $nuevoMensaje = ORM::for_table('mensaje')->create();
       $nuevoMensaje->usuario_id = $destinatario;
       $nuevoMensaje->remitente_id = $remitente;
       $nuevoMensaje->asunto = $titulo;
       $nuevoMensaje->mensaje = $texto;
       $nuevoMensaje->fecha = $fecha_actual;
       $nuevoMensaje->save();
       $mensajes = ORM::for_table('mensaje')->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))->where('usuario_id',$_SESSION['usuarioLogin']['id'])->find_many();
       $app->render('mensajesEntradaUsuario.html.twig', array('mensajeRespEnviado' => 'ok','usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes));
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

//Cuando pulsamos el botón actualizar en la sección "Mi cuenta"
$app->post('/actualizaUsuario', function() use ($app) {
    if(!$_POST['user'] || !$_POST['pass1'] || !$_POST['pass2'] || !$_POST['email'] || !$_POST['steam'] || !$_POST['nombre'] || !$_POST['edad']){
        $app->render('miCuenta.html.twig', array('msgCuenta' => array("danger","Debes rellenar todos los campos obligatorios"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
        die();
    }else{
        if ($_POST['pass1'] == $_POST['pass2']) {
            $userAModificar = ORM::for_table('usuario')->find_one($_SESSION['usuarioLogin']['id']);
            $userAModificar->user = $_POST['user'];
            $userAModificar->password = $_POST['pass1'];
            $userAModificar->nombre = $_POST['nombre'];
            $userAModificar->apellidos = $_POST['apellidos'];
            $userAModificar->email = $_POST['email'];
            $userAModificar->steam = $_POST['steam'];
            $userAModificar->edad = $_POST['edad'];
            $userAModificar->save();

            $usuario = ORM::for_table('usuario')->where('id', $_SESSION['usuarioLogin']['id'])->find_one();
            if($usuario){
                $_SESSION['numMensajes'] = ORM::for_table('mensaje')->where('usuario_id', $_SESSION['usuarioLogin']['id'])->where('leido',0)->count();
                $_SESSION['usuarioLogin'] = $usuario;
            }

            $app->render('miCuenta.html.twig', array('msgCuenta' => array("success","Cambios realizados con éxito"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
            die();
        }else{
            $app->render('miCuenta.html.twig', array('msgCuenta' => array("danger","Las contraseñas no son iguales"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
            die();
        }
    }
});

$app->post('/muestraMensaje', function() use ($app) {
    
    $mensaje = ORM::for_table('mensaje')
        ->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))
        ->where('mensaje.id', $_POST['botonMostrar'])
        ->find_one();
    //$mensaje = ORM::for_table('mensaje')->where('id', $_POST['botonMostrar']) ->find_one();

    //$mensajeLeido = ORM::for_table('mensaje')->find_one($_POST['botonMostrar']);
    //$mensajeLeido -> leido = 1;
    //$mensajeLeido->save();

    //$_SESSION['numMensajes'] = ORM::for_table('mensaje')->where('usuario_id', $_SESSION['usuarioLogin']['id'])->where('leido',0)->count(); ,'numMensajes' => $_SESSION['numMensajes']

    $app->render('mensajeEntrada.html.twig', array('mensaje' => $mensaje,'usuarioLogin'=>$_SESSION['usuarioLogin']));
    die();
});


$app->run();

