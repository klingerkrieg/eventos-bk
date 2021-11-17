<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title><?=nome_evento();?></title>
  
  <link rel="shortcut icon" href="<?=base_url()?>/static/img/favicon.ico" type="image/x-icon"/>
  
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>/static/semantic-ui/semantic.min.css">
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>/static/site.css">
  <link rel="stylesheet" type="text/css" href="<?=base_url()?>/static/site-interno.css">
  
  
  
  <script src="<?=base_url()?>/static/js/jquery.js"></script>
  <script src="<?=base_url()?>/static/js/jquery-ui.min.js"></script>
  <script src="<?=base_url()?>/static/semantic-ui/semantic.min.js"></script>
  <script src="<?=base_url()?>/static/js/main.js"></script>
  <script src="<?=base_url()?>/static/js/jquery.mask.min.js"></script>
  <script src="<?=base_url()?>/static/js/mask-validate.js"></script>
  <script src="<?=base_url()?>/static/js/cepWs.js"></script>
  <script src="<?=base_url()?>/static/js/confirm_delete.js"></script>
  <script src="<?=base_url()?>/static/js/changeEmailToPerfil.js"></script>
  <script src="<?=base_url()?>/static/js/coautores.js"></script>
  <script src="<?=base_url()?>/static/js/outra_instituicao.js"></script>
  <script src="<?=base_url()?>/static/js/dropdowns.js"></script>
  <script src="<?=base_url()?>/static/js/textarea.js"></script>
  <script src="<?=base_url()?>/static/js/lattes.js"></script>
  <script>
  $(document)
    .ready(function() {
      // fix menu when passed
      $('.masthead').visibility({
          once: false,
          onBottomPassed: function() {
            $('.fixed.menu').removeClass("hidden");
            $('.fixed.menu').css('display','block');
          },
          onBottomPassedReverse: function() {
            $('.fixed.menu').addClass("hidden");
            $('.fixed.menu').css('display','none');
            
          }
        }); 
        
      // create sidebar and attach to menu open
      $('.ui.sidebar').sidebar('attach events', '.toc.item');

      $('input, textarea').first().focus();
    });


    var site_url = "<?=site_url()?>";
  </script>
  

<style type="text/css">
body > .grid {
  height: 100%;
}
.column {
  max-width: 450px;
}
</style>
    

</head>
<body>
  <?php include view('./index_includes/menu_geral.php') ?>
</div>


  
<div class="area_interna">

<div class="ui text container">

<?php if (isset($_SESSION['user'])): ?>

  <?php /*if ($_SESSION['user']['pago'] == 0): ?>
    <div class="ui yellow message">
      Para as inscrições do IV SEDOC será cobrada uma taxa de inscrição de R$ 10,00 para alunos de graduação e R$ 20,00 para alunos de pós e docentes.<br/>
      O depósito ou transferência deve ser feito na conta da FUNCERN:<br/>
      Banco 104 - Caixa Econômica Federal<br/>
      Agência: 1406 - Operação: 003 - Conta corrent: 329-2<br/>

      O comprovante e o nome do inscrito deve ser enviado para: sedoc@ifrn.edu.br
    </div>
  <?php else: ?>
  <div class="ui positive message">
      O pagamento da sua inscrição foi confirmado.
  </div>
  <?php endif;*/ ?>

<?php endif; ?>


<div class="row">
  <?php if ($this->session->flashdata('warning')): ?>
      <div class="ui yellow message">
          <?=$this->session->flashdata('warning'); ?>
      </div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('success')): ?>
      <div class="ui positive message">
          <?=$this->session->flashdata('success'); ?>
      </div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('error')): ?>
      <div class="ui negative message">
          <?=$this->session->flashdata('error'); ?>
      </div>
  <?php endif; ?>
</div>

</div>