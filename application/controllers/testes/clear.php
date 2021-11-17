<?php

function clear($class){
    $sql = "delete from minicursos_coautores where id in(select minicursos_coautores.id from minicursos_coautores

        inner join minicursos ON 
        minicursos_coautores.idminicurso = minicursos.id 

        inner join usuarios on
        minicursos.idusuario = usuarios.id

        where usuarios.email = 'teste01@gmail.com')";
    $class->db->query($sql);

    $sql = "delete from minicursos where id in(select minicursos.id from minicursos
    
        inner join usuarios on
        minicursos.idusuario = usuarios.id

        where usuarios.email = 'teste01@gmail.com')";
    $class->db->query($sql);

    $sql = "delete from log where id in(select log.id from log

        inner join usuarios on
        log.iduser = usuarios.id

        where usuarios.email = 'teste01@gmail.com')";
    $class->db->query($sql);

    $class->db->delete("usuarios",["email"=>"teste01@gmail.com"]);
}