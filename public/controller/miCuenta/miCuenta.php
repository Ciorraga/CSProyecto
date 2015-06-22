<?php

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

//Cuando pulsamos el botón actualizar en la sección "Mi cuenta"
$app->post('/actualizaUsuario', function() use ($app) {
    $user = htmlentities($_POST['user']);
    $pass = htmlentities($_POST['pass1']);
    $nombre = htmlentities($_POST['nombre']);
    $apellidos = htmlentities($_POST['apellidos']);
    $email = htmlentities($_POST['email']);
    $steam =htmlentities($_POST['steam']);
    $edad =htmlentities($_POST['edad']);

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
    $compUser = $req->compruebaNombres('usuario','user',$user);
    $compEmail = $req->compruebaNombres('usuario','email',$email);
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
    $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

    if(!$_POST['user'] || !$_POST['pass1'] || !$_POST['pass2'] || !$_POST['email'] || !$_POST['steam'] || !$_POST['nombre'] || !$_POST['edad']){
        $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'msgCuenta' => array("danger","Debes rellenar todos los campos obligatorios"),
            'numMensajes' => $_SESSION['numMensajes'],
            'retos1vs1' => $_SESSION['retos1vs1'],
            'retosEquipo' => $_SESSION['retosEquipo'],
            "usuarioLogin" => $_SESSION['usuarioLogin'],
            'nuevaSolicitud' => $_SESSION['solicitudes']));
        die();
    }else{
        if($compUser && $user != $_SESSION['usuarioLogin']['user']){
            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                'msgCuenta' => array("danger","El nombre de usuario ya existe"),
                'numMensajes' => $_SESSION['numMensajes'],
                'retos1vs1' => $_SESSION['retos1vs1'],
                'retosEquipo' => $_SESSION['retosEquipo'],
                "usuarioLogin" => $_SESSION['usuarioLogin'],
                'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }
        if($compEmail){
            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                'msgCuenta' => array("danger","El e-mail que has introducido ya existe"),
                'numMensajes' => $_SESSION['numMensajes'],
                'retosEquipo' => $_SESSION['retosEquipo'],
                "usuarioLogin" => $_SESSION['usuarioLogin'],
                'retos1vs1' => $_SESSION['retos1vs1'],
                'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }
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
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                        'mensajeError' => $mensajeError,
                        'numMensajes' => $_SESSION['numMensajes'],
                        'retosEquipo' => $_SESSION['retosEquipo'],
                        'retos1vs1' => $_SESSION['retos1vs1'],
                        'usuarioLogin' => $_SESSION['usuarioLogin'],
                        'nuevaSolicitud' => $_SESSION['solicitudes']));
                    die();
                }

                // Comprobamos el tamaño de la imagen
                if ($_FILES["imagen"]["size"] > 300000) {
                    $mensajeError = "El archivo es demasiado grande";
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                        'mensajeError' => $mensajeError,
                        'numMensajes' => $_SESSION['numMensajes'],
                        'retosEquipo' => $_SESSION['retosEquipo'],
                        'usuarioLogin' => $_SESSION['usuarioLogin'],
                        'retos1vs1' => $_SESSION['retos1vs1'],
                        'nuevaSolicitud' => $_SESSION['solicitudes']));
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
                    $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                        'mensajeError' => $mensajeError,
                        'retos1vs1' => $_SESSION['retos1vs1'],
                        'numMensajes' => $_SESSION['numMensajes'],
                        'retosEquipo' => $_SESSION['retosEquipo'],
                        'usuarioLogin' => $_SESSION['usuarioLogin'],
                        'nuevaSolicitud' => $_SESSION['solicitudes']));
                    die();
                }
            }
            $userAModificar = ORM::for_table('usuario')->find_one($_SESSION['usuarioLogin']['id']);
            $userAModificar->user = $user;
            $userAModificar->password = $pass;
            $userAModificar->nombre = $nombre;
            $userAModificar->apellidos = $apellidos;
            $userAModificar->email = $email;
            $userAModificar->steam = $steam;
            $userAModificar->edad = $edad;
            if($_FILES["imagen"]['name'] == null){
                $userAModificar->imagen = "/imagenes/interrogacion.jpg";
            }else{
                $userAModificar->imagen = substr($dirFile,1);
                $_SESSION['usuarioLogin']['imagen'] = $dirFile;
            }
            $userAModificar->save();

            $usuario = ORM::for_table('usuario')
                ->where('id', $_SESSION['usuarioLogin']['id'])
                ->find_one();

            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
            $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
            $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

            $usuario = ORM::for_table('usuario')->where('user', $user)
                ->find_one();
            $_SESSION['usuarioLogin'] = $usuario;

            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                'msgCuenta' => array("success","Cambios realizados con éxito"),
                'numMensajes' => $_SESSION['numMensajes'],
                'retosEquipo' => $_SESSION['retosEquipo'],
                'retos1vs1' => $_SESSION['retos1vs1'],
                "usuarioLogin" => $_SESSION['usuarioLogin'],
                'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }else{
            $req = new comun();
            $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
            $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
            $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();
            $_SESSION['retosEquipo'] = $req->compruebaRetosEquipo();

            $app->render('miCuenta.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
                'msgCuenta' => array("danger","Las contraseñas no son iguales"),
                'numMensajes' => $_SESSION['numMensajes'],
                "usuarioLogin" => $_SESSION['usuarioLogin'],
                'retos1vs1' => $_SESSION['retos1vs1'],
                'retosEquipo' => $_SESSION['retosEquipo'],
                'nuevaSolicitud' => $_SESSION['solicitudes']));
            die();
        }
    }
});