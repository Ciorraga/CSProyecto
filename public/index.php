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

//Página de inicio de la aplicación
$app->get('/', function() use ($app) {
    //Si es un usaurio NO registrado
    if (!isset($_SESSION['usuarioLogin'])) {
        unset($_SESSION);
        session_destroy();

        $noticias = ORM::for_table('noticia')
            ->select('noticia.id')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();

        $miArray = [];
        $i = 0;
        foreach($noticias as $item){
            $comentarios = ORM::for_table('comentario')
                ->order_by_asc('usuario.user')
                ->select('comentario.texto')
                ->select('comentario.fecha')
                ->select('usuario.imagen')
                ->select('usuario.user')
                ->join('usuario', array('comentario.usuario_id', '=', 'usuario.id'))
                ->where('noticia_id',$item['id'])
                ->find_many();

            $miArray[$i]['id'] = $item['id'];
            $miArray[$i]['titulo'] = $item['titulo'];
            $miArray[$i]['texto'] = $item['texto'];
            $miArray[$i]['fecha'] = $item['fecha'];
            $miArray[$i]['user'] = $item['user'];
            $miArray[$i]['comentarios'] = $comentarios;
            $i++;
        }

        session_start();
        $_SESSION['not']=$noticias;
        $app->render('inicio.html.twig',array('noticias' => $miArray));
    } else { //Si es un usaurio registrado
        $noticias = ORM::for_table('noticia')
            ->select('noticia.id')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();

        $miArray = [];
        $i = 0;
        foreach($noticias as $item){
            $comentarios = ORM::for_table('comentario')
                ->select('comentario.texto')
                ->select('comentario.fecha')
                ->select('usuario.imagen')
                ->select('usuario.user')
                ->join('usuario', array('comentario.usuario_id', '=', 'usuario.id'))
                ->where('noticia_id',$item['id'])
                ->order_by_desc('comentario.fecha')
                ->find_many();

            $miArray[$i]['id'] = $item['id'];
            $miArray[$i]['titulo'] = $item['titulo'];
            $miArray[$i]['texto'] = $item['texto'];
            $miArray[$i]['fecha'] = $item['fecha'];
            $miArray[$i]['user'] = $item['user'];
            $miArray[$i]['comentarios'] = $comentarios;
            $i++;
        }

        $_SESSION['not']=$noticias;
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('inicio.html.twig', array('nuevaSolicitud' => $_SESSION['solicitudes'],'noticias' => $miArray,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'registrado' => 'env'));
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
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('miCuenta.html.twig',array('numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    //var_dump($mensajes);die();
    $app->render('mensajesEntradaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $app->render('mensajesSalidaUsuario.html.twig',array('mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
})->name('salida');

$app->get('/nuevoMensaje', function() use ($app) {
    $usuarios = ORM::for_table('usuario')
        ->select('user')
        ->select('id')
        ->find_many();
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $app->render('mensajeNuevo.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'usuarios' => $usuarios,'nuevaSolicitud' => $_SESSION['solicitudes']));
})->name('nuevoMensaje');

//Cuando el usuario pulsa en "Equipos"
$app->get('/equipos', function() use ($app) {
    //Si el usuario NO tiene equipo
    if($_SESSION['usuarioLogin']['equipo_id']==null){
        $equipo = null;
        $req = new comun();
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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

        if($_SESSION['usuarioLogin']['id']==$equipo[0]['id']){
            $miEquipo = false;
        }else{
            $miEquipo = true;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'miEquipo' => $miEquipo,'nuevaSolicitud' => $_SESSION['solicitudes']));
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

    if(!$_SESSION['usuarioLogin']['equipo_id']){
        $botonSolicitud = [true,$equipo[0]['id']];
    }else{
        $botonSolicitud = false;
    }

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('equipos.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'botonSolicitud' => $botonSolicitud,'nuevaSolicitud' => $_SESSION['solicitudes']));
});

$app->get('/solicitudes', function() use ($app) {
    $solicitudes = ORM::for_table('equipo_usuario')
        ->select('equipo_usuario.id')
        ->select('usuario.user')
        ->select('usuario.imagen')
        ->select('usuario.nombre')
        ->select('usuario.steam')
        ->select('equipo_usuario.estado')
        ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
        ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
        ->find_many();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('solicitudes.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes));

})->name('solicitudes');

//------------------------------------------------------------------------POSTS-------------------------------------------------------------------------

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
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('miCuenta.html.twig', array('msgCuenta' => array("danger","Debes rellenar todos los campos obligatorios"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

            $app->render('miCuenta.html.twig', array('msgCuenta' => array("success","Cambios realizados con éxito"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }else{
            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

            $app->render('miCuenta.html.twig', array('msgCuenta' => array("danger","Las contraseñas no son iguales"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }
    }
});

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

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('mensajesEntradaUsuario.html.twig', array('mensajeRespEnviado' => 'ok','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

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

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('mensajeEntrada.html.twig', array('mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonBorrar'])){
        ORM::for_table('mensaje')
            ->find_one($_POST['botonBorrar'])->delete();
        $es_capitan = ORM::for_table('equipo')->where('capitan_id', $_SESSION['usuarioLogin']['id'])->find_one();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

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

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('mensajeSalida.html.twig', array('mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonBorrar_Msg_Salida'])){
        ORM::for_table('mensaje')
            ->find_one($_POST['botonBorrar_Msg_Salida'])->delete();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

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

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('mensajesEntradaUsuario.html.twig', array('mensajeRespEnviado' => 'ok','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonCreaEquipo'])){
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('nuevoEquipo.html.twig', array('numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonFormNuevoEquipo'])){
        $nombre = htmlentities($_POST['nombreEquipo']);
        $urlSteam = htmlentities($_POST['urlSteam']);
        $fecha_actual=date("Y/m/d");

        //Guardamos el equipo en la BBDD
        $nuevoEquipo = ORM::for_table('equipo')->create();
        $nuevoEquipo->nombre = $nombre;
        $nuevoEquipo->grupo_steam = $urlSteam;
        $nuevoEquipo->capitan_id = $_SESSION['usuarioLogin']['id'];
        $nuevoEquipo->fecha_creacion = $fecha_actual;
        if(isset($_POST['webEquipo'])){
            $nuevoEquipo->web = htmlentities($_POST['webEquipo']);
        }
        if($_POST['logoEquipo']==""){
            $nuevoEquipo->logo = "/imagenes/interrogacion.jpg";
        }else{
            $nuevoEquipo->logo = $_POST['logoEquipo'];
        }
        $nuevoEquipo->save();

        //Extraemos el id del equipo que se acaba de crear
        $equipoId = ORM::for_table('equipo')
            ->select('id')
            ->where('nombre',$nombre)
            ->find_one();

        //Agregamos el campo "equipo_id"con el equipo que esté usuario creó anteriormente
        $userAModificar = ORM::for_table('usuario')->find_one($_SESSION['usuarioLogin']['id']);
        $userAModificar->equipo_id = $equipoId['id'];
        $userAModificar->save();

        //Volvemos a grabar la sesion con los nuevos datos
        $_SESSION['usuarioLogin'] = ORM::for_table('usuario')
            ->where('id', $_SESSION['usuarioLogin']['id'])
            ->find_one();
        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('equipos.html.twig',array('mensajeNuevoEquipo' => 'ok','usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonSolicitud'])){
        $nuevaSolicitud = ORM::for_table('equipo_usuario')->create();
        $nuevaSolicitud->equipo_id = $_POST['botonSolicitud'];
        $nuevaSolicitud->usuario_id = $_SESSION['usuarioLogin']['id'];
        $nuevaSolicitud->save();

        $noticias = ORM::for_table('noticia')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('inicio.html.twig', array('noticias' => $noticias,'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajeNuevaSolicitud' => 'ok', 'numMensajes' => $_SESSION['numMensajes'], 'nuevaSolicitud' => $_SESSION['solicitudes']));
    }

    //Al pulsar el boton aceptar del menú solicitudes del capitán de equipo
    if(isset($_POST['botonAceptarSol'])){
        //var_dump($_POST['botonAceptarSol']);die();
        $usuario = ORM::for_table('usuario')
            ->select('usuario.equipo_id')
            ->join('equipo_usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.id',$_POST['botonAceptarSol'])
            ->find_one();

        if($usuario['equipo_id']==""){
            $us = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonAceptarSol'])
            ->find_one();

            $userAModificar = ORM::for_table('usuario')
                ->where('id',$us['usuario_id'])
                ->find_one();
            $userAModificar->equipo_id = $_SESSION['usuarioLogin']['equipo_id'];
            $userAModificar->save();

           $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
                ->where('id',$_POST['botonAceptarSol'])
                ->find_one();
            $modificaEstadoPeticion->estado = 'aprobada';
            $modificaEstadoPeticion->save();


            $solicitudes = ORM::for_table('equipo_usuario')
                ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
                ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
                ->where('equipo_usuario.estado','pendiente')
                ->find_many();

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

            $solicitudes = ORM::for_table('equipo_usuario')
                ->select('equipo_usuario.id')
                ->select('usuario.user')
                ->select('usuario.imagen')
                ->select('usuario.nombre')
                ->select('usuario.steam')
                ->select('equipo_usuario.estado')
                ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
                ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
                ->find_many();

            $app->render('solicitudes.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'aprobada' => 'ok'));
        }else{

            $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
                ->where('id',$_POST['botonAceptarSol'])
                ->find_one();
            $modificaEstadoPeticion->estado = 'denegada';
            $modificaEstadoPeticion->save();

            $solicitudes = ORM::for_table('equipo_usuario')
                ->select('equipo_usuario.id')
                ->select('usuario.user')
                ->select('usuario.imagen')
                ->select('usuario.nombre')
                ->select('usuario.steam')
                ->select('equipo_usuario.estado')
                ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
                ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
                ->find_many();

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
            $app->render('solicitudes.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'tieneEquipo' => 'ok'));
        }
    }

    //Al pulsar el boton denegar del menú solicitudes del capitán de equipo
    if(isset($_POST['botonDenegarSol'])){
        $us = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonDenegarSol'])
            ->find_one();

        $modificaEstadoPeticion = ORM::for_table('equipo_usuario')
            ->where('id',$_POST['botonDenegarSol'])
            ->find_one();
        $modificaEstadoPeticion->estado = 'denegada';
        $modificaEstadoPeticion->save();


        $solicitudes = ORM::for_table('equipo_usuario')
            ->select('equipo_usuario.id')
            ->select('usuario.user')
            ->select('usuario.imagen')
            ->select('usuario.nombre')
            ->select('usuario.steam')
            ->select('equipo_usuario.estado')
            ->join('usuario', array('equipo_usuario.usuario_id', '=', 'usuario.id'))
            ->where('equipo_usuario.equipo_id', $_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('solicitudes.html.twig',array('usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'aprobada' => 'notOk'));
    }

    //Al pulsar el responder en los comentarios de las noticias
    if(isset($_POST['botonResponderNoticia'])){
        $texto = htmlentities($_POST['textoComentario']);
        $fechaYHora = date("Y-m-d H:i:s");

        $nuevoComentario = ORM::for_table('comentario')->create();
        $nuevoComentario->texto = $texto;
        $nuevoComentario->fecha = $fechaYHora;
        $nuevoComentario->usuario_id = $_SESSION['usuarioLogin']['id'];
        $nuevoComentario->noticia_id = $_POST['botonResponderNoticia'];
        $nuevoComentario->save();

        $noticias = ORM::for_table('noticia')
            ->select('noticia.id')
            ->select('noticia.titulo')
            ->select('noticia.texto')
            ->select('noticia.fecha')
            ->select('usuario.user')
            ->join('usuario', array('noticia.usuario_id', '=', 'usuario.id'))
            ->order_by_desc('noticia.fecha')
            ->find_array();

        $miArray = [];
        $i = 0;
        foreach($noticias as $item){
            $comentarios = ORM::for_table('comentario')
                ->select('comentario.texto')
                ->select('comentario.fecha')
                ->select('usuario.imagen')
                ->select('usuario.user')
                ->join('usuario', array('comentario.usuario_id', '=', 'usuario.id'))
                ->where('noticia_id',$item['id'])
                ->order_by_desc('comentario.fecha')
                ->find_many();

            $miArray[$i]['id'] = $item['id'];
            $miArray[$i]['titulo'] = $item['titulo'];
            $miArray[$i]['texto'] = $item['texto'];
            $miArray[$i]['fecha'] = $item['fecha'];
            $miArray[$i]['user'] = $item['user'];
            $miArray[$i]['comentarios'] = $comentarios;
            $i++;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('inicio.html.twig', array('nuevaSolicitud' => $_SESSION['solicitudes'],'noticias' => $miArray,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'comentarioEnv' => 'ok','registrado' => 'env'));
    }

});




$app->run();

