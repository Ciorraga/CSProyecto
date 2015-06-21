<?php

//Al pulsar el responder en los comentarios de las inicio
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
    $_SESSION['retos1vs1'] = $req->compruebaRetosUsuario();

    $app->render('inicio.html.twig', array('imagenUser'=>$_SESSION['usuarioLogin']['imagen'],
        'nuevaSolicitud' => $_SESSION['solicitudes'],
        'noticias' => $notic,
        'numMensajes' => $_SESSION['numMensajes'],
        'retos1vs1' => $_SESSION['retos1vs1'],
        'usuarioLogin' => $_SESSION['usuarioLogin'],
        'mensajeOk' => 'Comentario enviado con éxito',
        'registrado' => 'env'));
}