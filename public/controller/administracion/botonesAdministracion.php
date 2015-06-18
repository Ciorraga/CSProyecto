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

if(isset($_POST['fijaResultadoReto'])){
    $reto = ORM::for_table('reto')
        ->where('id',$_POST['fijaResultadoReto'])
        ->find_one();
    //var_dump($reto);die();

    $reto->res_eq_retador = $_POST['resRetador'];
    $reto->res_eq_retado = $_POST['resRetado'];
    if($_POST['resRetador']>$_POST['resRetado']){
        $reto->ganador = $reto['retador_id'];
    }else{
        $reto->ganador = $reto['retado_id'];
    }
    $reto->save();

    $retosEq = ORM::for_table('reto')
        ->raw_query('select eq1.nombre as nombreEq1,eq2.nombre as nombreEq2,eq1.logo as eq1Imagen,eq2.logo as eq2Imagen,reto.id,reto.fecha,reto.mapa,reto.res_eq_retador as resEq1,reto.res_eq_retado as resEq2 from reto join equipo as eq1 on reto.retador_id=eq1.id join equipo as eq2 on reto.retado_id=eq2.id where reto.ganador IS null AND reto.aceptado IS NOT false ORDER BY reto.fecha ASC limit 10')
        ->find_many();
    $retos1vs1 = 23;

    $app->render('admin/listaRetos.html.twig',array('retosEq' => $retosEq, 'mensajeOk' => 'Reto actualizado'));
    die();
}

