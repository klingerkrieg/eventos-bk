<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title><?=nome_evento();?></title>
  
  <link rel="shortcut icon" href="<?=base_url()?>static/img/favicon.ico" type="image/x-icon"/>
  <script src="<?=base_url()?>static/js/jquery.js"></script>
  <!-- page scroll -->
  <script src="<?=base_url()?>static/js/jquery-ui.min.js"></script>

  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/semantic-ui/semantic.min.css">
  <script src="<?=base_url()?>static/semantic-ui/semantic.min.js"></script>

  <link rel="stylesheet" type="text/css" href="<?=base_url()?>static/site.css">
  
  <script src="<?=base_url()?>/static/js/changeEmailToPerfil.js"></script>
  <script src="<?=base_url()?>static/js/main.js"></script>
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
    });
  </script>

</head>
<body>



  <?php include'index_includes/menu_geral.php' ?>

    
    <div class="ui text container logo_space">
      <img class="ui" src="<?=base_url()?>static/img/logo-verde-vii.svg"/>
    </div>
  </div>

    
  <?php include'index_includes/sobre.php' ?>

  <?php include'index_includes/modelos.php' ?>

  <?php include'index_includes/programacao.php' ?>

  <?php #include'index_includes/inscricoes.php' ?>
  
  <?php #include'index_includes/grupos-de-trabalho.php' ?>

  <?php include'index_includes/palestrantes.php' ?>

  <?php #include'index_includes/a-cidade.php' ?>

  <?php #include'index_includes/apoio.php' ?>
  
  <?php include'index_includes/organizacao.php' ?>



  <?php include 'area/bottom.php'; ?>