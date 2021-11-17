<?php include'application/views/area/head.php'; ?>

<style>
  #page-header {
    min-height:65px;
    padding-bottom: 0px;
  }
  iframe{
    width:700px;
    height:500px;
  }

  .texto{

  }
</style>


  <div class='area_interna'>
  <h2 class="ui stripe horizontal divider header">
    <a id="sobre">Registrar ciência de minicurso com seu nome</a>
  </h2>

    <div class="ui text container">

      <?php if ($dados == null): ?>

        <center>
        <p>
          A URL digitada não se refere a um minicurso válido.
        </p>
        
          <i class="massive thumbs down outline icon"></i>
        </center>
        <br/>

      
        <?php else: ?>

      <center>

      <p>
        O minicurso entitulado <b><?=$minicurso["titulo"]?></b> foi submetido, e o 
        seu nome consta como coautor.
      </p>
      <?php if ($dados["ciente"]): ?>
        <p>Você já registrou ciência deste trabalho.</p>
        <?php else: ?>
      <p>
          Caso queira confirmar a submissão, faça o registro da ciência do minicurso:
          <a class='ui button green' href='<?=site_url("ciente/registrar_ciencia/$hash")?>'>Registrar ciência</a>
      </p>
      <?php endif; ?>
      </center>
      <br/>

      <div style='text-align:justify;'>
        <div><b>Título:</b><?=$minicurso["titulo"]?></div>
        <div><b>Objetivo:</b><?=$minicurso["objetivo"]?></div>
        <div><b>Resumo:</b><?=$minicurso["resumo"]?></div>
        <div><b>Área:</b><?=$minicurso["area"]?></div>
        <div><b>Vagas:</b><?=$minicurso["vagas"]?></div>
        <div><b>CH:</b><?=$minicurso["ch"]?></div>
        
        
        <?php if (count($minicurso["horarios_escolhidos"]) == 0):?>
          <b>Data/Hora preferencial:</b>
          <div class='horarios'>
            <?=exibirHorarios($minicurso["horarios_preferenciais"])?>
          </div>
        <?php else: ?>
          <b>Data/Hora:</b>
          <div class='horarios'>
          <?=exibirHorarios($minicurso["horarios_escolhidos"])?>
          </div>
          <?php endif; ?>
      </div>
        
      


        <?php endif; ?>
    </div>

  <?php include 'application/views/area/bottom.php'; ?>