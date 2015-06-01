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

/*function comentarios($id_noticia){
    $noticias = ORM::for_table('comentario')
        ->select('comentario.id','idComentario')
        ->select('comentario.texto')
        ->select('comentario.fecha')
        ->select('usuario.user')
        ->select('usuario.id','idUsuario')
        ->select('usuario.imagen')
        ->join('noticia', array('noticia.id', '=', 'comentario.noticia_id'))
        ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
        ->where('comentario.noticia_id',$id_noticia)
        ->order_by_desc('noticia.fecha')
        ->find_array();
    var_dump($noticias);die();
    return $noticias;
}*/

//Página de inicio de la aplicación
$app->get('/', function() use ($app) {
    if (!isset($_SESSION['usuarioLogin'])) {
        unset($_SESSION);
        session_destroy();
        $noticias = ORM::for_table('noticia')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();
        session_start();
        $_SESSION['not']=$noticias;
        $app->render('inicio.html.twig',array('noticias' => $noticias));
    } else {
        $noticias = ORM::for_table('noticia')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();
        $_SESSION['not']=$noticias;
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();

        $app->render('inicio.html.twig', array('noticias' => $noticias,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin']));
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
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
        $app->render('miCuenta.html.twig',array('numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
    }else{
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
})->name('miCuenta');

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/entrada', function() use ($app) {
    $mensajes = ORM::for_table('mensaje')
        ->select('mensaje.id')
        ->select('mensaje.leido')
        ->select('mensaje.asunto')
        ->select('mensaje.mensaje')
        ->select('mensaje.fecha')
        ->select('usuario.user')
        ->join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
        ->where('mensaje.usuario_id',$_SESSION['usuarioLogin']['id'])
        ->order_by_desc('mensaje.fecha')
        ->find_array();
    $_SESSION['numMensajes'] = ORM::for_table('mensaje')
        ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
        ->where('leido',0)->count();
    //var_dump($mensajes);die();
    $app->render('mensajesEntradaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes']));
    die();
})->name('entrada');

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/salida', function() use ($app) {
    $mensajes = ORM::for_table('mensaje')
        ->select('mensaje.asunto')
        ->select('mensaje.fecha')
        ->select('mensaje.id')
        ->select('usuario.user')
        ->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))
        ->where('remitente_id',$_SESSION['usuarioLogin']['id'])
        ->find_many();
    $app->render('mensajesSalidaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes']));
    die();
})->name('salida');

$app->get('/nuevoMensaje', function() use ($app) {
    $usuarios = ORM::for_table('usuario')
        ->select('user')
        ->select('id')
        ->find_many();

    $app->render('mensajeNuevo.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'usuarios' => $usuarios));
})->name('nuevoMensaje');

//Cuando el usuario pulsa en "Equipos"
$app->get('/equipos', function() use ($app) {
    //Si el usuario NO tiene equipo
    if($_SESSION['usuarioLogin']['equipo_id']==null){
        $equipo = null;
        $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes']));
    }else{
        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();

        $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios));
    }


})->name('equipos');

$app->get('/buscarEquipo/:id', function ($id) {
    $equipo = ORM::for_table('equipo')
        ->where_like('nombre', '%'. $id .'%')
        ->find_many();

    foreach ($equipo as $valor){
      echo "<a href='/equipos/".$valor['nombre']."'>". $valor['nombre'] ."</a><br/>" ;
    };
})->name('buscarEquipo');

$app->get('/equipos/:equipo', function ($equipo) use ($app) {
    $equipo = ORM::for_table('equipo')
        ->where('nombre',$equipo)
        ->find_many();

    $usuarios = ORM::for_table('usuario')
        ->where('equipo_id',$equipo[0]['id'])
        ->find_many();

    $_SESSION['numMensajes'] = ORM::for_table('mensaje')
        ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
        ->where('leido',0)->count();

    $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios));
});

//------------------------------------------------------------------------POSTS--------
//Cuando pulsamos en el boton de ACEPTAR en el login
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
        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
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

            $usuario = ORM::for_table('usuario')
                ->where('id', $_SESSION['usuarioLogin']['id'])
                ->find_one();

            if($usuario){
                $_SESSION['numMensajes'] = ORM::for_table('mensaje')
                    ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
                    ->where('leido',0)->count();
                $_SESSION['usuarioLogin'] = $usuario;
            }

            $app->render('miCuenta.html.twig', array('msgCuenta' => array("success","Cambios realizados con éxito"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
            die();
        }else{
            $_SESSION['numMensajes'] = ORM::for_table('mensaje')
                ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
                ->where('leido',0)->count();
            $app->render('miCuenta.html.twig', array('msgCuenta' => array("danger","Las contraseñas no son iguales"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin']));
            die();
        }
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

        //var_dump($destinatario);die();

        $nuevoMensaje = ORM::for_table('mensaje')->create();
        $nuevoMensaje->usuario_id = $destinatario;
        $nuevoMensaje->remitente_id = $remitente;
        $nuevoMensaje->asunto = $titulo;
        $nuevoMensaje->mensaje = $texto;
        $nuevoMensaje->fecha = $fecha_actual;
        $nuevoMensaje->save();

        $mensajes = ORM::for_table('mensaje')
            ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
            ->where('usuario_id',$_SESSION['usuarioLogin']['id'])
            ->find_many();

        $app->render('mensajesEntradaUsuario.html.twig', array('mensajeRespEnviado' => 'ok','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes));
        die();
    }

    if(isset($_POST['loginUsuario'])){
        $usuario = ORM::for_table('usuario')->where('user', $_POST['username'])->where('password', $_POST['password'])->find_one();
        if($usuario){
            $_SESSION['usuarioLogin'] = $usuario;
            $_SESSION['numMensajes'] = ORM::for_table('mensaje')
                ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
                ->where('leido',0)->count();

            $app->redirect($app->router()->urlFor('inicio'));
            //$app->render('inicio.html.twig',array('numMensajes' => $_SESSION['numMensajes'] , 'usuarioLogin' => $usuario));
            die();
        }
        else{
            //$app->redirect($app->router()->urlFor('inicio',array('usuarioLoginError' => '1')));
            //Necesito saber como pasar parámetros en urlFor
            $app->render('inicio.html.twig',array('noticias' => $_SESSION['not'] ,'usuarioLoginError' => '1'));
            die();
        }
    }

    if(isset($_POST['botonMostrar'])){
        $mensajeLeido = ORM::for_table('mensaje')->find_one($_POST['botonMostrar']);
        $mensajeLeido -> leido = 1;
        $mensajeLeido->save();

        $mensaje = ORM::for_table('mensaje')
            ->select('mensaje.id')
            ->select('mensaje.asunto')
            ->select('mensaje.mensaje')
            ->select('mensaje.fecha')
            ->select('usuario.user')
            ->select('mensaje.remitente_id')
            ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
            ->where('mensaje.id', $_POST['botonMostrar'])
            ->find_array();

        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
        $app->render('mensajeEntrada.html.twig', array('mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin']));
        die();
    }

    if(isset($_POST['botonBorrar'])){
        ORM::for_table('mensaje')
            ->find_one($_POST['botonBorrar'])->delete();

        $app->redirect($app->router()->urlFor('entrada'));
        die();
    }

    if(isset($_POST['botonMostrar_Msg_Salida'])){
        $mensaje = ORM::for_table('mensaje')
            ->select('mensaje.id')
            ->select('mensaje.asunto')
            ->select('mensaje.mensaje')
            ->select('mensaje.fecha')
            ->select('usuario.user')
            ->select('mensaje.remitente_id')
            ->inner_join('usuario', array('mensaje.usuario_id', '=', 'usuario.id'))
            ->where('mensaje.id', $_POST['botonMostrar_Msg_Salida'])
            ->find_array();

        $_SESSION['numMensajes'] = ORM::for_table('mensaje')
            ->where('usuario_id', $_SESSION['usuarioLogin']['id'])
            ->where('leido',0)->count();
        $app->render('mensajeSalida.html.twig', array('mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin']));
        die();
    }

    if(isset($_POST['botonBorrar_Msg_Salida'])){
        ORM::for_table('mensaje')
            ->find_one($_POST['botonBorrar_Msg_Salida'])->delete();

        $app->redirect($app->router()->urlFor('salida'));
        die();
    }

    if(isset($_POST['enviarNuevoMensaje'])){
        $asunto = htmlentities($_POST['asunto']);
        $mensaje = htmlentities($_POST['mensaje']);
        $id_usuario= htmlentities($_POST['id_usuario']);
        $id_remitente = $_SESSION['usuarioLogin']['id'];
        $fecha_actual=date("Y/m/d");


        $nuevoMensaje = ORM::for_table('mensaje')->create();
        $nuevoMensaje->usuario_id = $id_usuario;
        $nuevoMensaje->remitente_id = $id_remitente;
        $nuevoMensaje->asunto = $asunto;
        $nuevoMensaje->mensaje = $mensaje;
        $nuevoMensaje->fecha = $fecha_actual;
        $nuevoMensaje->save();

        $mensajes = ORM::for_table('mensaje')
            ->inner_join('usuario', array('mensaje.remitente_id', '=', 'usuario.id'))
            ->where('usuario_id',$_SESSION['usuarioLogin']['id'])
            ->find_many();

        $app->render('mensajesEntradaUsuario.html.twig', array('mensajeRespEnviado' => 'ok','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes));
        die();
    }

    if(isset($_POST['botonCreaEquipo'])){
        echo "CREAR EQUIPO";
        die();
    }

});




$app->run();

