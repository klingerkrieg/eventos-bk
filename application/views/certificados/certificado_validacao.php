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
    <a id="sobre">Validação de certificado</a>
  </h2>

    <div class="ui text container">

      <?php if ($dados == null): ?>

        <center>
        <p>
          A URL digitada não se refere à um certificado válido.
        </p>
        
          <i class="massive thumbs down outline icon"></i>
        </center>
        <br/>

      
        <?php else: ?>

      <center>
      <p>
        O certificado verificado foi encontrado e é válido.
      </p>
      
      <br/>
      
        <iframe src='<?=base_url("./certificados/$file_name.pdf")?>' ></iframe>
      </center>


        <?php endif; ?>
    </div>

  <?php include 'application/views/area/bottom.php'; ?>