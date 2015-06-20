<?php

if(isset($_POST['buscarUsuario'])){
    $usuarios = ORM::for_table('usuario')
        ->select('usuario.id')
        ->select('usuario.imagen')
        ->select('usuario.user')
        ->select('usuario.edad')
        ->select('usuario.steam')
        ->where_like('usuario.user', '%'. $_POST['nombreUser'] .'%')
        ->find_many();

    $ultUsuarios = ORM::for_table('usuario')
        ->select('usuario.id')
        ->select('usuario.imagen')
        ->select('usuario.user')
        ->select('usuario.edad')
        ->select('usuario.steam')
        ->limit(10)
        ->order_by_desc('id')
        ->find_many();

    if($usuarios == null){
        $usuarios="Vacio";
    }

    $req = new comun();
    $req->mostrarSolicitudes($_SESSION['usuarioLogin']['id']);
    $req->mostrarMensajes($_SESSION['usuarioLogin']['id']);

    $app->render('usuarios.html.twig',array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'ultimosUsuarios' => $ultUsuarios,
        'usuarios' => $usuarios));
    die();
}