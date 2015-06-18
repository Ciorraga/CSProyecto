<?php

//Cuando el usuario sin equipo pulsa el boton de enviar solicitud de equipo
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

//Cuando el usuario sin equipo pulsa el boton de crear equipo
if(isset($_POST['botonCreaEquipo'])){
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
    die();
}

//Cuando el usuario con equipo pulsa el botón de abandonar equipo
if(isset($_POST['botonDejarEquipo'])){
    $consEqId = ORM::for_table('usuario')
        ->where('id',$_POST['botonDejarEquipo'])
        ->find_one();

    $equipoCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_POST['botonDejarEquipo'])
        ->find_one();

    if($equipoCapitan){
        $consIntegrantes = ORM::for_table('usuario')
            ->where('equipo_id',$consEqId['equipo_id'])
            ->find_many();

        $equipoCapitan->capitan_id = $consIntegrantes[1]['id'];
        $equipoCapitan->save();
    }


    $userAModificar = ORM::for_table('usuario')->find_one($_POST['botonDejarEquipo']);
    $userAModificar->equipo_id = null;
    $userAModificar->save();

    $usuario = ORM::for_table('usuario')->where('id', $_POST['botonDejarEquipo'])
        ->find_one();
    $_SESSION['usuarioLogin'] = $usuario;

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'mensajeOk' => 'Has abandonado el equipo'));
}

//Cuando el usuario capitán del equipo quiere borrar el equipo. Entra aquí cuando pulsa el botón eliminar equipo
if(isset($_POST['botonDestruirEquipo'])){
    $usuarios = ORM::for_table('usuario')
        ->where('equipo_id',$_POST['botonDestruirEquipo'])
        ->find_many();
    foreach ($usuarios as $item) {
        $item->equipo_id = null;
        $item->save();
    }


    $capitan = ORM::for_table('equipo')
        ->where('id',$_POST['botonDestruirEquipo'])
        ->find_one();
    $capitan->capitan_id = null;
    $capitan->save();

    $eq = ORM::for_table('equipo')
        ->find_one($_POST['botonDestruirEquipo']);
    $eq->nombre = "Eq. eliminado";
    $eq->save();

    $usuario = ORM::for_table('usuario')->where('id', $_SESSION['usuarioLogin']['id'])
        ->find_one();
    $_SESSION['usuarioLogin'] = $usuario;

    $req = new comun();
    $notic = $req->mostrarNoticias();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('inicio.html.twig',array('noticias' => $notic,'imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'nuevaSolicitud' => $_SESSION['solicitudes'],'mensajeOk' => 'Equipo eliminado con éxito'));
}

if(isset($_POST['botonRetarEquipo'])){
    echo "va";die();
}

if(isset($_POST['botonFormNuevoEquipo'])){
    $mensajeOk = "";
    $nombre = htmlentities($_POST['nombreEquipo']);
    $urlSteam = htmlentities($_POST['urlSteam']);
    $fecha_actual=date("Y/m/d");
    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $compEquipo = $req->compruebaNombres('equipo','nombre',$nombre);

    if($compEquipo){
        $mensajeError = "El nombre de equipo ya existe. Pruebe con otro";
        $app->render('nuevoEquipo.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeError' => $mensajeError,'numMensajes' => $_SESSION['numMensajes'],'usuarioLogin' => $_SESSION['usuarioLogin'],'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }else{
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

        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],'mensajeOk' => $mensajeOk,'usuarioLogin'=>$_SESSION['usuarioLogin'],'numMensajes' => $_SESSION['numMensajes'],'equipo' => $equipo,'usuarios' => $usuarios,'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }

}
