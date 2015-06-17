<?php

if(isset($_POST['botonBorrarNoticia'])){
    $req = new comun();
    $notic = $req->mostrarNoticias();
    ORM::for_table('noticia')
        ->find_one($_POST['botonBorrarNoticia'])->delete();

    $app->redirect($app->router()->urlFor('listaNoticias'));
    die();
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

        $app->render('admin/listadoNoticias.html.twig',array('inicio' => $notic,'mensajeOk' => 'Noticia agregada con éxito'));
    }


}