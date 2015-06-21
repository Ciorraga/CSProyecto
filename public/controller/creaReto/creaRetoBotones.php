<?php

if(isset($_POST['botonRetarEquipo'])){

}

if(isset($_POST['botonEnviaReto'])){
    if($_POST['fecha']==""){
        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'retos1vs1' => $_SESSION['retos1vs1'],
            'mensajeError' => "Tiene que adjuntar una fecha cuando haga un reto"));
        die();
    }
    $compReto = ORM::for_table('reto1vs1')
        ->where('retador_id',$_SESSION['usuarioLogin']['id'])
        ->where('retado_id',$_POST['botonEnviaReto'])
        ->where('ganador',"0")
        ->find_one();

    if($compReto){
        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'retos1vs1' => $_SESSION['retos1vs1'],
            'mensajeError' => 'El usuario seleccionado y tú aún tenéis un reto activo. Termina el reto, o ponte en contacto con un administrador'));
        die();
    }else{
        $nuevoReto1vs1 = ORM::for_table('reto1vs1')
            ->create();
        $nuevoReto1vs1->retador_id = $_SESSION['usuarioLogin']['id'];
        $nuevoReto1vs1->retado_id = $_POST['botonEnviaReto'];
        $nuevoReto1vs1->fecha = $_POST['fecha'].":00";
        $nuevoReto1vs1->mapa = $_POST['mapa'];
        $nuevoReto1vs1->save();

        $ultUsuarios = ORM::for_table('usuario')
            ->select('usuario.id')
            ->select('usuario.imagen')
            ->select('usuario.user')
            ->select('usuario.edad')
            ->select('usuario.steam')
            ->limit(10)
            ->order_by_desc('id')
            ->find_many();

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin' => $_SESSION['usuarioLogin'],
            'ultimosUsuarios' => $ultUsuarios,
            'retos1vs1' => $_SESSION['retos1vs1'],
            'mensajeOk' => 'Reto enviado!'));

        die();
    }
}

if(isset($_POST['botonEnviaRetoEquipo'])){
    if($_POST['fecha']==""){
        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $ultJugados = ORM::for_table('reto')
            ->raw_query('select reto.ganador,eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where (reto.retador_id='.$_SESSION['usuarioLogin']['equipo_id'].' or reto.retado_id='.$_SESSION['usuarioLogin']['equipo_id'].') and reto.ganador IS NOT NULL  ORDER BY reto.fecha DESC')
            ->find_many();

        if($_SESSION['usuarioLogin']['id']==$equipo[0]['id']){
            $miEquipo = false;
        }else{
            $miEquipo = true;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'equipo' => $equipo,
            'usuarios' => $usuarios,
            'miEquipo' => $miEquipo,
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'mensajeError' => "Tiene que adjuntar una fecha cuando haga un reto",
            'retos1vs1' => $_SESSION['retos1vs1'],
            'misRetos' => $ultJugados));
        die();
    }

    $compReto = ORM::for_table('reto')
        ->where('retador_id',$_SESSION['usuarioLogin']['id'])
        ->where('retado_id',$_POST['botonEnviaRetoEquipo'])
        ->where('ganador',"0")
        ->find_one();

    if($compReto){
        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $ultJugados = ORM::for_table('reto')
            ->raw_query('select reto.ganador,eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where (reto.retador_id='.$_SESSION['usuarioLogin']['equipo_id'].' or reto.retado_id='.$_SESSION['usuarioLogin']['equipo_id'].') and reto.ganador IS NOT NULL  ORDER BY reto.fecha DESC')
            ->find_many();

        if($_SESSION['usuarioLogin']['id']==$equipo[0]['id']){
            $miEquipo = false;
        }else{
            $miEquipo = true;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'equipo' => $equipo,
            'usuarios' => $usuarios,
            'miEquipo' => $miEquipo,
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'mensajeError' => 'El equipo seleccionado y tú aún tenéis un reto activo. Termina el reto, o ponte en contacto con un administrador',
            'retos1vs1' => $_SESSION['retos1vs1'],
            'misRetos' => $ultJugados));
        die();
    }else{
        $nuevoReto = ORM::for_table('reto')
            ->create();
        $nuevoReto->retador_id = $_SESSION['usuarioLogin']['equipo_id'];
        $nuevoReto->retado_id = $_POST['botonEnviaRetoEquipo'];
        $nuevoReto->fecha = $_POST['fecha'].":00";
        $nuevoReto->mapa = $_POST['mapa'];
        $nuevoReto->save();

        //Consulta para extraer los datos del equipo
        $equipo = ORM::for_table('equipo')
            ->where('id',$_SESSION['usuarioLogin']['equipo_id'])
            ->find_many();
        //Consulta para extraer los datos de los miembros del equipo
        $usuarios = ORM::for_table('usuario')
            ->where('equipo_id',$equipo[0]['id'])
            ->find_many();

        $ultJugados = ORM::for_table('reto')
            ->raw_query('select reto.ganador,eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where (reto.retador_id='.$_SESSION['usuarioLogin']['equipo_id'].' or reto.retado_id='.$_SESSION['usuarioLogin']['equipo_id'].') and reto.ganador IS NOT NULL  ORDER BY reto.fecha DESC')
            ->find_many();

        if($_SESSION['usuarioLogin']['id']==$equipo[0]['id']){
            $miEquipo = false;
        }else{
            $miEquipo = true;
        }

        $req = new comun();
        $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
        $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);
        $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

        $app->render('equipos.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
            'usuarioLogin'=>$_SESSION['usuarioLogin'],
            'numMensajes' => $_SESSION['numMensajes'],
            'equipo' => $equipo,
            'usuarios' => $usuarios,
            'miEquipo' => $miEquipo,
            'nuevaSolicitud' => $_SESSION['solicitudes'],
            'mensajeOk' => 'Reto enviado!',
            'retos1vs1' => $_SESSION['retos1vs1'],
            'misRetos' => $ultJugados));
        die();
    }
}