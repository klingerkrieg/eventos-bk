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
    <a id="sobre">Registrar ciência de trabalho com seu nome</a>
  </h2>

    <div class="ui text container">

      <?php if ($dados == null): ?>

        <center>
        <p>
          A URL digitada não se refere a um trabalho válido.
        </p>
        
          <i class="massive thumbs down outline icon"></i>
        </center>
        <br/>

      
        <?php else: ?>

      <center>
      <p>
        O trabalho entitulado <b><?=$trab["titulo"]?></b> foi submetido, e o 
        seu nome consta como <?=($dados["tipo"] == 0) ? "Coautor" : "Orientador"; ?>.
      </p>
      <?php if ($dados["ciente"]): ?>
        <p>Você já registrou ciência deste trabalho.</p>
        <?php else: ?>
      <p>
          Caso queira confirmar a submissão, faça o registro da ciência do trabalho:
          <a class='ui button green' href='<?=site_url("ciente/registrar_ciencia/$hash")?>'>Registrar ciência</a>
      </p>
      <?php endif; ?>
      
      <br/>
      
        <iframe src='<?=base_url("./uploads/trabalhos/{$trab["arquivo"]}")?>' ></iframe>
      </center>


        <?php endif; ?>
    </div>

  <?php include 'application/views/area/bottom.php'; ?>