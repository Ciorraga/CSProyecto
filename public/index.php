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

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $app->render('inicio.html.twig',array('noticias' => $notic));
    } else { //Si es un usaurio registrado
        $req = new comun();
        $notic = $req->mostrarNoticias();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'nuevaSolicitud' => $_SESSION['solicitudes'],'noticias' => $notic,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'registrado' => 'env'));
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
        $app->render('miCuenta.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    }else{
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
})->name('miCuenta');

//Sección bandeja de entrada de MENSAJES de cada usuario
$app->get('/entrada', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
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
    $app->render('mensajesEntradaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
})->name('entrada');

//Sección bandeja de salida de MENSAJES de cada usuario
$app->get('/salida', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
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
    $app->render('mensajesSalidaUsuario.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajes' => $mensajes,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
})->name('salida');

$app->get('/nuevoMensaje', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $usuarios = ORM::for_table('usuario')
        ->select('user')
        ->select('id')
        ->find_many();
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $app->render('mensajeNuevo.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'usuarios' => $usuarios,'nuevaSolicitud' => $_SESSION['solicitudes']));
})->name('nuevoMensaje');

//Cuando el usuario pulsa en "Equipos"
$app->get('/equipos', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    //Si el usuario NO tiene equipo
    if($_SESSION['usuarioLogin']['equipo_id']==null){
        $equipo = null;
        $req = new comun();
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'miEquipo' => $miEquipo,'nuevaSolicitud' => $_SESSION['solicitudes']));
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

$app->get('/buscarUsuario/:id', function ($id) {
    $usuario = ORM::for_table('usuario')
        ->where_like('user', '%'. $id .'%')
        ->find_many();

    foreach ($usuario as $valor){
        echo "<a href='/mensajeNuevo/".$valor['id']."'>". $valor['user'] ."</a><br/>" ;
    };
})->name('buscarUsuario');

$app->get('/mensajeNuevo/:id', function ($id) use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }

    $cons = ORM::for_table('usuario')
        ->where('id', $id)
        ->find_one();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('mensajeNuevo.html.twig',array('usuarioMensaje' => $cons ,'imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
});

$app->get('/equipos/:equipo', function ($equipo) use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
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
    $imagenUser = ".".$_SESSION['usuarioLogin']['imagen'];

    $app->render('equipos.html.twig',array('imagenUser'=>$imagenUser,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'botonSolicitud' => $botonSolicitud,'nuevaSolicitud' => $_SESSION['solicitudes']));
});

$app->get('/solicitudes', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $compCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_SESSION['usuarioLogin']['id'])
        ->find_one();

    if(!$compCapitan){
        $app->redirect($app->router()->urlFor('equipos'));
        die();
    }else{
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

        $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes));
    }
})->name('solicitudes');

$app->get('/retos', function() use ($app) {
    $clasRetos = ORM::for_table('reto')
        ->join('equipo', array('reto.ganador', '=', 'equipo.id'))
        ->select_expr('COUNT(*)', 'total')
        ->select('reto.ganador')
        ->select('equipo.nombre')
        ->group_by('ganador')
        ->order_by_desc('total')
        ->find_many();

    var_dump($clasRetos);die();
    $totalJugadoEquipo = ORM::for_table('reto')
        ->select_expr('count(*)','total_partidos')
        ->where_raw('(`retador_id` = ? OR `retado_id` = ?)', array(26, 26))
        ->find_many();






    foreach ($clasRetos as $item) {
        echo $item['nombre'];
        echo " - ";
        echo $item['ganador'];
        echo " - ";
        echo $item['total'];
        echo "|||";
    }
    die();

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('retos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes']));

})->name('retos');

$app->get('/administracion', function() use ($app) {
    if(!isset($_SESSION['usuarioLogin'])){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    if($_SESSION['usuarioLogin']['es_admin']==0){
        $app->redirect($app->router()->urlFor('inicio'));
        die();
    }
    $app->render('administracion.html.twig');
    die();
});

//------------------------------------------------------------------------POSTS-------------------------------------------------------------------------

$app->post('/registro', function() use ($app) {
    if(!$_POST['user'] || !$_POST['pass1'] || !$_POST['pass2'] || !$_POST['email'] || !$_POST['steam'] || !$_POST['nombre'] || !$_POST['edad']){
        $app->render('nuevoUser.html.twig', array('mensajeError' => "Debes rellenar como mínimo los campos marcados"));
        die();
    }else{
        if ($_POST['pass1'] == $_POST['pass2']) {
            if($_FILES["imagenUser"]['name'] != null){
                $comp = 1;
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

            $notic = $req = new comun();
            $req->mostrarNoticias();

            $nuevoUser->save();
            $app->render('inicio.html.twig', array('mensajeOk' => "Usuario creado con éxito!!Prueba a ingresar!",'noticias' => $notic));
            die();
        } else {
            $app->render('nuevoUser.html.twig', array('mensajeError' => "Las contraseñas no coinciden"));
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
        $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'msgCuenta' => array("danger","Debes rellenar todos los campos obligatorios"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }else{
        if ($_POST['pass1'] == $_POST['pass2']) {
            if($_FILES["imagen"]['name'] != null){
                //Subida de la imagen
                $num = 0;
                $dir = "./imagenes/usuarios/";
                $file = basename($_FILES["imagen"]["name"]);

                // Comprueba si es una imagen o no
                $check = getimagesize($_FILES["imagen"]["tmp_name"]);
                if($check == false) {
                    //Lanzar alerta de que no es una imagen
                    $mensajeError = "El archivo que ha seleccionado NO es una imagen";
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
                    die();
                }

                // Comprobamos el tamaño de la imagen
                if ($_FILES["imagen"]["size"] > 300000) {
                    $mensajeError = "El archivo es demasiado grande";
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $dirFile)) {
                    //Lanzar alerta Ok
                    $mensajeOk = "El archivo ". basename( $_FILES["imagen"]["name"]). " ha sido subido con éxito";
                } else {
                    $mensajeError = "El archivo no pudo ser subido";
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
                    die();
                }
            }
            $userAModificar = ORM::for_table('usuario')->find_one($_SESSION['usuarioLogin']['id']);
            $userAModificar->user = $_POST['user'];
            $userAModificar->password = $_POST['pass1'];
            $userAModificar->nombre = $_POST['nombre'];
            $userAModificar->apellidos = $_POST['apellidos'];
            $userAModificar->email = $_POST['email'];
            $userAModificar->steam = $_POST['steam'];
            $userAModificar->edad = $_POST['edad'];
            if($_FILES["imagen"]['name'] == null){
                $userAModificar->imagen = "/imagenes/interrogacion.jpg";
            }else{
                $userAModificar->imagen = $dirFile;
                $_SESSION['usuarioLogin']['imagen'] = $dirFile;
            }
            $userAModificar->save();

            $usuario = ORM::for_table('usuario')
                ->where('id', $_SESSION['usuarioLogin']['id'])
                ->find_one();

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'msgCuenta' => array("success","Cambios realizados con éxito"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }else{
            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'msgCuenta' => array("danger","Las contraseñas no son iguales"),'numMensajes' => $_SESSION['numMensajes'],"usuarioLogin" => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }
    }
});



$app->post('/', function() use ($app) {
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
            $app->render('inicio.html.twig',array('noticias' => $notic ,'usuarioLoginError' => '1'));
            die();
        }
    }

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

        $app->render('mensajesEntradaUsuario.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeOk' => 'Mensaje enviado con éxito','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
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

        $app->render('mensajeEntrada.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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

        $app->render('mensajeSalida.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensaje' => $mensaje[0],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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
        $id_usuario= htmlentities($_POST['enviarNuevoMensaje']);
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

        $app->render('mensajesEntradaUsuario.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeOk' => 'Mensaje enviado!','numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajes' => $mensajes,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonCreaEquipo'])){
        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonFormNuevoEquipo'])){
        $mensajeOk = "";
        $nombre = htmlentities($_POST['nombreEquipo']);
        $urlSteam = htmlentities($_POST['urlSteam']);
        $fecha_actual=date("Y/m/d");

        if($_FILES["logoEquipo"]['name'] != null){
            //Subida de la imagen
            $num = 0;
            $dir = "./imagenes/equipos/";
            $file = basename($_FILES["logoEquipo"]["name"]);

            // Comprueba si es una imagen o no
            $check = getimagesize($_FILES["logoEquipo"]["tmp_name"]);
            if($check == false) {
                //Lanzar alerta de que no es una imagen
                $mensajeError = "El archivo que ha seleccionado NO es una imagen";
                $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
                die();
            }

            // Comprobamos el tamaño de la imagen
            if ($_FILES["logoEquipo"]["size"] > 300000) {
                $mensajeError = "El archivo es demasiado grande";
                $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
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
            if (move_uploaded_file($_FILES["logoEquipo"]["tmp_name"], $dirFile)) {
                //Lanzar alerta Ok
                $mensajeOk = "El archivo ". basename( $_FILES["logoEquipo"]["name"]). " ha sido subido con éxito";
            } else {
                $mensajeError = "El archivo no pudo ser subido";
                $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
                die();
            }
        }

        //Guardamos el equipo en la BBDD
        $nuevoEquipo = ORM::for_table('equipo')->create();
        $nuevoEquipo->nombre = $nombre;
        $nuevoEquipo->grupo_steam = $urlSteam;
        $nuevoEquipo->capitan_id = $_SESSION['usuarioLogin']['id'];
        $nuevoEquipo->fecha_creacion = $fecha_actual;
        if(isset($_POST['webEquipo'])){
            $nuevoEquipo->web = htmlentities($_POST['webEquipo']);
        }
        if($_FILES["logoEquipo"]['name'] == null){
            $nuevoEquipo->logo = "/imagenes/interrogacion.jpg";
        }else{
            $nuevoEquipo->logo = $dirFile;
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

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeOk' => $mensajeOk,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

    if(isset($_POST['botonSolicitud'])){
        $nuevaSolicitud = ORM::for_table('equipo_usuario')->create();
        $nuevaSolicitud->equipo_id = $_POST['botonSolicitud'];
        $nuevaSolicitud->usuario_id = $_SESSION['usuarioLogin']['id'];
        $nuevaSolicitud->save();

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'noticias' => $notic,'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajeOk' => 'Solicitud enviada con éxito', 'numMensajes' => $_SESSION['numMensajes'], 'nuevaSolicitud' => $_SESSION['solicitudes']));
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

            $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'aprobada' => 'ok','mensajeOk' => "Solicitud aprobada con éxito"));
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
            $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'tieneEquipo' => 'ok'));
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

        $app->render('solicitudes.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'solicitudes' => $solicitudes,'aprobada' => 'notOk','mensajeError' => 'Solicitud denegada con éxito'));
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

        $req = new comun();
        $notic = $req->mostrarNoticias();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'nuevaSolicitud' => $_SESSION['solicitudes'],'noticias' => $notic,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'mensajeOk' => 'Comentario enviado con éxito','registrado' => 'env'));
    }

});




$app->run();

