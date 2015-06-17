<?php

if(isset($_POST['botonBorrarNoticia'])){
    $req = new comun();
    $notic = $req->mostrarNoticias();
    ORM::for_table('noticia')
        ->find_one($_POST['botonBorrarNoticia'])->delete();

    $app->redirect($app->router()->urlFor('listaNoticias'));
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
        $req = new comun();
        $notic = $req->mostrarNoticias();
        $app->render('inicio.html.twig',array('noticias' => $notic ,'usuarioLoginError' => '1'));
        die();
    }
}

if(isset($_POST['botonEnviaNoticia'])){
    if($_POST['tituloNoticia']=="" || $_POST['textoNoticia']==""){
        $app->render('admin/nuevaNoticia.html.twig',array('mensajeError' => 'Tienes que rellenar todos los campos'));
    }else{
        $fecha=date("Y/m/d");
        $nuevaNoticia = ORM::for_table('noticia')
            ->create();
        $nuevaNoticia->titulo = $_POST['tituloNoticia'];
        $nuevaNoticia->texto = $_POST['textoNoticia'];
        $nuevaNoticia->fecha = $fecha;
        $nuevaNoticia->usuario_id = $_SESSION['usuarioLogin']['id'];
        $nuevaNoticia->save();

        $req = new comun();
        $notic = $req->mostrarNoticias();

        $app->render('admin/listadoNoticias.html.twig',array('noticias' => $notic,'mensajeOk' => 'Noticia agregada con éxito'));
    }


}