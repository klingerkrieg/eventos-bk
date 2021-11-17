<?php
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=planilha.csv");
header("Pragma: no-cache");
header("Expires: 0");

print utf8_decode("Título;Trilha;Área;Autor;");

for($i = 0; $i < $evento["limite_coautores"]; $i++){
    print "Coautor " . ($i+1) .";";
}

for($i = 0; $i < $evento["limite_orientadores"]; $i++){
    print "Orientador " . ($i+1) .";";
}

for($i = 0; $i < $evento["limite_avaliadores_trabalhos"]; $i++){
    print "Avaliador " . ($i+1) .";";
}

print "\r\n";


foreach($lista["dados"] as $ln){
    print utf8_decode(html_entity_decode($ln["titulo"])).";";
    print utf8_decode(html_entity_decode($ln["nome_autor"])).";";
    print utf8_decode(html_entity_decode($trilhas[$ln["idtrilha"]])).";";
    print utf8_decode(html_entity_decode($ln["area"])).";";

    for($i = 0; $i < $evento["limite_coautores"]; $i++){
        if (isset($ln["coautores"][$i])){
            print utf8_decode(html_entity_decode($ln["coautores"][$i]["nome_completo"])).";";
        } else {
            print ";";
        }
    }

    for($i = 0; $i < $evento["limite_orientadores"]; $i++){
        if (isset($ln["orientadores"][$i])){
            print utf8_decode(html_entity_decode($ln["orientadores"][$i]["nome_completo"])).";";
        } else {
            print ";";
        }
    }

    for($i = 0; $i < $evento["limite_avaliadores_trabalhos"]; $i++){
        if (isset($ln["avaliadores"][$i])){
            print utf8_decode(html_entity_decode($ln["avaliadores"][$i]["nome_completo"])).";";
        } else {
            print ";";
        }
    }
    print "\r\n";
}