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

        $app->render('admin/listadoNoticias.html.twig',array('noticias' => $notic,'mensajeOk' => 'Noticia agregada con éxito'));
    }
}

if(isset($_POST['botonAdminBorrarUsuario'])){
    $consEqId = ORM::for_table('usuario')
        ->where('id',$_POST['botonAdminBorrarUsuario'])
        ->find_one();

    $equipoCapitan = ORM::for_table('equipo')
        ->where('capitan_id',$_POST['botonAdminBorrarUsuario'])
        ->find_one();

    if($equipoCapitan) {
        $consIntegrantes = ORM::for_table('usuario')
            ->where('equipo_id', $consEqId['equipo_id'])
            ->find_many();

        if (count($consIntegrantes) == 1) {
            $equipoCapitan->nombre="Eq. eliminado";
            $equipoCapitan->capitan_id=null;
            $equipoCapitan->save();
        } else {
            $equipoCambiaCap = ORM::for_table('usuario')
                ->where('equipo_id', $equipoCapitan['id'])
                ->where_not_equal('id', $_POST['botonAdminBorrarUsuario'])
                ->find_many();

            $equipoCapitan->capitan_id = $consIntegrantes[1]['id'];
            $equipoCapitan->save();
        }
    }
    $borrar = ORM::for_table('usuario')
        ->find_one($_POST['botonAdminBorrarUsuario']);

    $borrar->delete();

    $usuarios = ORM::for_table('usuario')
        ->select('usuario.id')
        ->select('usuario.imagen')
        ->select('usuario.user')
        ->select('usuario.nombre')
        ->select('usuario.email')
        ->select('usuario.edad')
        ->find_many();

    $app->render('admin/listaUsuarios.html.twig', array('mensajeOk' => 'Usuario eliminado con éxito','usuarios' => $usuarios));

}
