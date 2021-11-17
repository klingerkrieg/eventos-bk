<?php include 'head.php' ?>



<h2 class="ui horizontal divider header">Minicursos disponíveis</h2>
<div class="ui text container">

<?php if ($_SESSION['user']['pago'] == false): ?>
    <div class='ui message yellow'>
      Você só poderá se matricular em um minicurso após a realização do pagamento da inscrição.
    </div>
<?php endif; ?>

  <?php if (count($minicursos) == 0): ?>
    <div class="ui message">
        Ainda não temos nenhum minicurso disponível.
    </div>
  <?php endif; ?>

  <?php foreach($minicursos as $minicurso): ?>

  <a name="curso<?=$minicurso['id']?>"></a>
  <div class="ui card minicurso_card">
    <div class="content">
      <div class="header">
        <?=$minicurso['titulo']?>
      </div>
      
      <div class="description">
        <p><?=$minicurso['descricao']?></p>
        <?php

        //só mostra a URL se houver URL definida 
        //se estiver inscrito
        //e se estiver na data do minicurso
        if ($minicurso['url'] != "" && $minicurso['minhaMatricula'] != false){
          $mostrarUrl = false;
          foreach($minicurso["horarios_escolhidos"] as $horario){
            $data = date('d/m/Y', strtotime($horario));
            if ($data == date('d/m/Y')){
              $mostrarUrl = true;
              break;
            }
          }
          if ($mostrarUrl){
            print "<p>URL:<a target='_blank' href='{$minicurso['url']}'>{$minicurso['url']}</a></p>";
          }
        }
        ?>
      </div>
    </div>
    <div class="content">
      <div class="right floated author">
          
            <?php
            if ($minicurso['ch'] != ""){
              print "Carga horária:".$chs[$minicurso['ch']]."<br/>";
            }
            if ($minicurso['vagas'] != ""){
              print "Vagas:{$minicurso['vagas']}<br/>";
            }
            print "Matriculados:{$minicurso['qtdMatriculados']}";
            ?>

        </div>

        <div class="left floated author">
        
          Data/Hora:
          <div class='horarios'>
        
          <?php
            $data_ant = null;
            $qtdHorarios = count($minicurso["horarios_escolhidos"]);
            

            for ($i = 0; $i < $qtdHorarios; $i++){
              $horario = $minicurso["horarios_escolhidos"][$i];
              $data = date('d/m/Y', strtotime($horario));
              $hora_de = date('H:i', strtotime($horario));
              $hora_ate = date('H:i', strtotime($horario ." +1 hours +30 minutes"));
              $data_hora_ate = date('Y-m-d H:i', strtotime($horario ." +1 hours +30 minutes"));

              if ($i+1 < $qtdHorarios) {
                $next_datahora_de = date('Y-m-d H:i', strtotime($minicurso["horarios_escolhidos"][$i+1]));
                while ($i+1 < $qtdHorarios && $next_datahora_de == $data_hora_ate){
                  $hora_ate = date('H:i', strtotime($minicurso["horarios_escolhidos"][$i+1]." +1 hours +30 minutes"));
                  $i++;
                  if ($i+1 == $qtdHorarios){
                    break;
                  }
                  $next_datahora_de = date('Y-m-d H:i', strtotime($minicurso["horarios_escolhidos"][$i+1]));
                  $data_hora_ate = date('Y-m-d H:i', strtotime($horario ." +1 hours +30 minutes"));
                }
              }


              if ($data_ant != $data){
                if ($data_ant != null){
                  print "</div>";
                }
                print "<div><b>$data</b><br>";
                $data_ant = $data;
              }
              print "$hora_de - $hora_ate<br>";
            }
            print "</div>";
          ?>
          </div>
        
        </div>
    </div>
    <div class="content">
      <div class="left floated author">
        Ministrantes
          <ul>
            <?php
            if ($minicurso['lattes'] == "" || strstr($minicurso['lattes'], "lattes") == false){ 
                $nome = ucwords( strtolower($minicurso['nome_autor']) );
            } else {
                $nome = "<a target='_BLANK' href='{$minicurso['lattes']}'>".ucwords( strtolower($minicurso['nome_autor']) )."</a>";
            }

            print "<li>".$nome."</li>";
            foreach($minicurso['coautores'] as $coautor){

              if ($coautor['lattes'] == "" || strstr($coautor['lattes'], "lattes") == false){ 
                  $nome = ucwords( strtolower($coautor['nome_completo']) );
              } else {
                  $nome = "<a target='_BLANK' href='{$coautor['lattes']}'>".ucwords( strtolower($coautor['nome_completo']) )."</a>";
              }

              print "<li>".$nome."</li>";
            }
            ?>
          </ul>
      </div>
      
      <div class="right floated author">
        <?php if ($minicurso['minhaMatricula']["certificado"] != ""):?>
          <a class='ui button primary' target='_BLANK' href='<?=base_url("certificados/matriculados/{$minicurso['minhaMatricula']["certificado"]}.pdf")?>'>
            <i class="certificate icon"></i>
            Download do certificado
          </a>
        <?php elseif (evento_encerrado() || matriculas_encerradas()):?>
            <div class="ui message">Não está disponível para matrícula</div>
        <?php elseif ($minicurso['minhaMatricula'] != false):?>
          <a class='ui button red' onclick='showConf("<?=site_url("minicursos/cancelar_matricula/{$minicurso['id']}")?>","<?=$minicurso['titulo']?>")'>
            <i class="eraser icon"></i>
            Cancelar matrícula
          </a>
        <?php elseif ($minicurso['qtdMatriculados'] == $minicurso['vagas'] && $minicurso['vagas'] > 0): ?>
          <div class="ui message">Vagas esgotadas</div>
        <?php elseif ($_SESSION['user']['pago']): ?>
          <a class='ui button green' href='<?=site_url("minicursos/matricular/{$minicurso['id']}")?>'>
            <i class="pencil alternate icon"></i>
            Matricular-se
          </a>
        <?php else: ?>
          <a class='ui button disabled green' >
            <i class="pencil alternate icon"></i>
            Matricular-se
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php endforeach; ?>

  <div id="confirmOrCancel" class="ui mini modal">
  <div class="header">Confirmação</div>
  <div class="content">
    <p>Tem certeza que deseja cancelar sua matrícula no minicurso <b id="trabalhoSendoDeletado"></b>?</p>
  </div>
  <div class="actions">
    <div class="ui positive right labeled icon button" onclick="confirmDelete();">
        Sim
        <i class="checkmark icon"></i>
    </div>
    <div class="ui cancel red button">Cancelar</div>
  </div>
</div>

</div>

    



<?php include 'bottom.php' ?>