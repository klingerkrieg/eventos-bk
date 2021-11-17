<?php
require_once 'head.php';

function countHorarios($lista){
    $horarios_preferenciais = [];
    $horarios_escolhidos = [];
    foreach($lista as $dados){
        foreach($dados["horarios_preferenciais"] as $hora){
            if (isset($horarios_preferenciais[$hora])){
                array_push($horarios_preferenciais[$hora], ['id'=>$dados['id'],'titulo'=>$dados['titulo'],'ch'=>$dados['ch']]);
            } else {
                $horarios_preferenciais[$hora] = [['id'=>$dados['id'],'titulo'=>$dados['titulo'],'ch'=>$dados['ch']]];
            }
        }

        foreach($dados["horarios_escolhidos"] as $hora){
            if (isset($horarios_escolhidos[$hora])){
                array_push($horarios_escolhidos[$hora], ['id'=>$dados['id'],'titulo'=>$dados['titulo'],'ch'=>$dados['ch']]);
            } else {
                $horarios_escolhidos[$hora] = [['id'=>$dados['id'],'titulo'=>$dados['titulo'],'ch'=>$dados['ch']]];
            }
        }
    }

    return [$horarios_preferenciais, $horarios_escolhidos];
}


function printDistribuicao($datasHorarios, $horariosDefinidos){
    print "<table class='ui celled table' style='width:90%;padding: 0;'>";

    $datas = [];
    $max = 0;
    print "<thead><tr>";
    foreach($datasHorarios as $data=>$horarios){
        print "<th>". date("d/m/Y",strtotime($data)) ."</th>";

        $max = max($max, count($horarios));
        array_push($datas,$data);
    }
    print "</tr></thead><tbody>";

    for ($i = 0; $i < $max; $i++){
        print "<tr>";
        foreach($datas as $data){
            $horarios = $datasHorarios[$data];
            
            if (isset($horarios[$i])){
                $hora = $horarios[$i];
                $key = $data." ".$hora;
                $cursos = "<ul>";
                if (isset($horariosDefinidos[$key])){
                    $qtd = count($horariosDefinidos[$key]) ." minicursos";
                    foreach($horariosDefinidos[$key] as $curso){
                        $cursos .= "<li><a href='".site_url("painel/minicursos/index/{$curso['id']}")."'>({$curso['id']}) {$curso['titulo']} ({$curso['ch']}h)</a></li>";
                    }
                } else {
                    $qtd = "";
                }
                $cursos .= "</ul>";

                print "<td>$hora $qtd $cursos</td>";
            } else {
                print "<td></td>";
            }
            /*foreach($horarios as $hora){
                
                print "<div class='item'>$hora $qtd &nbsp; $cursos</div>";
            }*/

        }
        print "</tr>";
    }

    print "</tbody></table>";

}

list($horarios_preferenciais, $horarios_escolhidos) = countHorarios($lista);


print '<div class="row"><h2 class="ui header">Horários preferenciais</h2></div>';
printDistribuicao($datasHorarios, $horarios_preferenciais);

print '<div class="row"><h2 class="ui header">Horários escolhidos</h2></div>';
printDistribuicao($datasHorarios, $horarios_escolhidos);

require_once 'bottom.php';