<?php include 'head.php' ?>


<?php

if ($_SESSION["user"]["certificado_participante"] != ""){
  $url = base_url("certificados/participacao/{$_SESSION["user"]["certificado_participante"]}.pdf");
  print <<<HTML
  <div class='ui text container'>
    <div class='ui message'>Seu certificado de participação já está disponível: 
      <a target="_BLANK" href='$url'>
        {$_SESSION["user"]["certificado_participante"]}.pdf</a>
    </div>
  </div>
  HTML;
}

?>


<h2 class="ui horizontal divider header">Submissões</h2>
<div class="ui text container">







    <div class="ui vertical segment">

      <table class="ui compact table">
          <thead>
          <tr>
            <th>Titulo</th>
            <th>Situação</th>
            <th class='lastColumnSubmissoes'></th>
          </tr>
          <tr>
            <th colspan="3">Trabalhos</th>
          </tr>
          </thead>
          <tbody>
      <?php if (count($trabalhos) == 0): ?>
      <tr>
        <td colspan='3'>
        <div class="ui message">
          <?php if (evento_encerrado()): ?>
            Submissões encerradas
          <?php elseif ($evento['aceitando_submissoes'] == 0): ?>
            Ainda não estamos aceitando submissões de trabalhos.
          <?php else: ?>
            Você ainda não realizou nenhuma submissão de trabalhos.
          <?php endif; ?>
        </div>
        </td>
      </tr>
      <?php endif; ?>

    <?php
    $trabalhos_meus = 0;
    foreach($trabalhos as $trab): ?>

          <tr>
          <td>
          
          <a href="<?=site_url('trabalhos/form/' . $trab['id'])?>">
          <?=limitar($trab['titulo'],100)?>
          </a><br/>

          <?=_v($trilhas,$trab['idtrilha'])?>

          </td>
          <td>
            <?php if ($trab['status'] == PENDENTE): ?>
              <div class="ui grey label"  style="text-align:center;">
            <?php elseif ($trab['status'] == APROVADO): ?>
              <div class="ui green label"  style="text-align:center;">
            <?php elseif ($trab['status'] == APROVADO_CORRECOES_PENDENTES): ?>
              <div class="ui yellow label" style="text-align:center;">
            <?php elseif ($trab['status'] == APROVADO_CORRECOES): ?>
              <div class="ui green label" style="text-align:center;">
            <?php else: ?>
              <div class="ui red label"  style="text-align:center;">
            <?php endif; ?>
              <?=$status_trabalhos[$trab['status']]?>
            </div>
          </td>
          <td class='lastColumnSubmissoes'>
            

            <?php 
            #caso seja um trabalho onde eu sou o autor principal (quem submeteu)
            if ($trab["idusuario"] == $_SESSION["user"]["id"]): ?>

              <?php if ($trab['status'] == APROVADO_CORRECOES_PENDENTES): ?>
                <?php if ($evento['aceitando_correcoes'] == 1): ?>
                <a href="<?=site_url('trabalhos/form/' . $trab['id'])?>" class="ui green button">
                  <i class="upload icon"></i>
                  Enviar versão corrigida
                </a>
                <?php endif; ?>
              <?php elseif ($trab['certificado'] != ""): ?>
                <a href="<?=site_url('trabalhos/form/' . $trab['id'])?>" class="ui green label"  style="text-align:center;">
                  Os certificados estão disponíveis.
                </a>
              <?php elseif ($trab['status'] == PENDENTE): ?>
                <button onclick="showConf('<?=site_url('trabalhos/cancelar/' . $trab['id'])?>','<?=$trab['titulo']?>')" class="ui red button">
                  <i class="trash icon"></i>
                  Cancelar
                </button>
                <?php endif; ?>
            <?php 

            $trabalhos_meus++;

            #caso seja um trabalho onde fui incluído como coautor ou orientador
            else: ?>

            <?php
            $ciente = null;
            $hash = null;
            foreach(array_merge($trab["orientadores"],$trab["coautores"]) as $user){
              if ($user["idusuario"] == $_SESSION['user']['id']){
                $ciente = $user["ciente"];
                $hash = $user["ciente_hash"];
                break;
              }
            }
            
            if ($ciente == false){
              print "<a class='ui button green' href='".site_url("ciente/registrar_ciencia/$hash")."'>Registrar ciência</a>";
            } else {
              print "<i class='icon checkmark green' ></i>Você já registrou ciência.";
            }
            ?>      

            <?php if ($trab['certificado'] != ""): ?>
              <a href="<?=site_url('trabalhos/form/' . $trab['id'])?>" class="ui green label"  style="text-align:center;">
                Os certificados estão disponíveis.
              </a>
            <?php endif; ?>      

            <?php endif; ?>

          </td>
        </tr>
        <?php if ($trab['status'] != PENDENTE && $trab['observacao']): ?>
        <tr>
          <td colspan="3" style="border-top:0px none;">
            <b>Observações:</b>
            <?=limitar($trab['observacao'],250)?>
          </td>
        </tr>
        <?php endif; ?>
	
    <?php endforeach; ?>

        <thead>
        <tr>
          <th colspan="3">Minicursos</th>
        </tr>
        </thead>

        <?php if (count($minicursos) == 0): ?>
          <tr>
            <td colspan='3'>
            <div class="ui message">
              <?php if (evento_encerrado()): ?>
                Submissões encerradas
              <?php elseif ($evento['aceitando_submissoes_minicursos'] == 0): ?>
                Ainda não estamos aceitando submissões de minicursos.
              <?php else: ?>
                Você ainda não realizou nenhuma submissão de minicurso.
              <?php endif; ?>
            </div>
            </td>
          </tr>
        <?php endif; ?>

        <?php
        $minicursos_meus = 0;
        foreach($minicursos as $curso): ?>
          <tr>
          <td>
          <a href="<?=site_url('minicursos/form/' . $curso['id'])?>">
            <?=limitar($curso['titulo'],100)?>
          </a><br/>
          <?=$curso['area']?>

          </td>
          <td>
            <?php if ($curso['status'] == PENDENTE): ?>
              <div class="ui grey label"  style="text-align:center;">
            <?php elseif ($curso['status'] == APROVADO): ?>
              <div class="ui green label"  style="text-align:center;">
            <?php elseif ($curso['status'] == APROVADO_CORRECOES_PENDENTES): ?>
              <div class="ui yellow label" style="text-align:center;">
            <?php elseif ($curso['status'] == APROVADO_CORRECOES): ?>
              <div class="ui green label" style="text-align:center;">
            <?php else: ?>
              <div class="ui red label"  style="text-align:center;">
            <?php endif; ?>
            
            <?=$status_minicursos[$curso['status']]?>
            
            </div>
          </td>
          <td class='lastColumnSubmissoes'>
            

          <?php 
            #caso seja um trabalho onde eu sou o autor principal (quem submeteu)
            if ($curso["idusuario"] == $_SESSION["user"]["id"]): ?>

            <?php if ($curso['certificado'] != ""): ?>
              <a href="<?=site_url('minicursos/form/' . $curso['id'])?>" class="ui green label"  style="text-align:center;">
                Os certificados estão disponíveis.
              </a>
            <?php elseif ($curso['status'] == PENDENTE): ?>
              <button onclick="showConf('<?=site_url('minicursos/cancelar/' . $curso['id'])?>','<?=$curso['titulo']?>')" class="ui red button">
                <i class="trash icon"></i>
                Cancelar
              </button>
            <?php endif; ?>

             

            <?php
          $minicursos_meus++;
          #caso eu tenha sido inserido como coautor em um minicurso
          else: ?>

            <?php
            $ciente = null;
            $hash = null;
            foreach($curso["coautores"] as $user){
              if ($user["idusuario"] == $_SESSION['user']['id']){
                $ciente = $user["ciente"];
                $hash = $user["ciente_hash"];
                break;
              }
            }
            
            if ($ciente == false){
              print "<a class='ui button green' href='".site_url("ciente/registrar_ciencia/$hash")."'>Registrar ciência</a>";
            } else {
              print "<i class='icon checkmark green' ></i>Você já registrou ciência.";
            }
            ?> 

            <?php if ($curso['certificado'] != ""): ?>
              <a href="<?=site_url('minicursos/form/' . $curso['id'])?>" class="ui green label"  style="text-align:center;">
                Os certificados estão disponíveis.
              </a>
            <?php endif; ?>

            <?php endif; ?>

          </td>
          </tr>
          <?php if ($curso['status'] != PENDENTE && $curso['observacao']): ?>
          <tr>
          <td colspan="3" style="border-top:0px none;">
            <b>Observações:</b>
            <?=limitar($curso['observacao'],250)?>
          </td>
          </tr>
          <?php endif; ?>

          <?php endforeach; ?>
      </tbody>
    </table>  

    
    <div align="right">
    <?php if ($evento['aceitando_submissoes'] == 1): ?>

      <?php if ($trabalhos_meus < $evento['limite_submissoes']): ?>
        <a href="<?=site_url('trabalhos/form')?>" class="ui green button">
          <i class="upload icon"></i>
          Submeter trabalho
        </a>
      <?php else: ?>
        <div class="ui message">
          Você já enviou a quantidade máxima de trabalhos.
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <?php if ($evento['aceitando_submissoes_minicursos'] == 1): ?>
      <?php if ($minicursos_meus < $evento['limite_submissoes_minicursos']): ?>
        <a href="<?=site_url('minicursos/form')?>" class="ui green button">
          <i class="upload icon"></i>
          Submeter minicurso
        </a>
      <?php else: ?>
        <div class="ui message">
          Você já enviou a quantidade máxima de minicursos.
        </div>
      <?php endif; ?>
    <?php endif; ?>
    </div>

    </div>
</div>


<div id="confirmOrCancel" class="ui mini modal">
  <div class="header">Confirmação</div>
  <div class="content">
    <p>Tem certeza que deseja cancelar o trabalho/minicurso entitulado <b id="trabalhoSendoDeletado"></b>?</p>
  </div>
  <div class="actions">
    <div class="ui positive right labeled icon button" onclick="confirmDelete();">
        Sim
        <i class="checkmark icon"></i>
    </div>
    <div class="ui cancel red button">Cancelar</div>
  </div>
</div>



<?php include 'bottom.php' ?>
