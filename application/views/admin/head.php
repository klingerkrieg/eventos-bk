<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?=nome_evento();?> - Admin Panel</title>
        <link rel="shortcut icon" href="<?=base_url()?>static/img/favicon.ico" type="image/x-icon"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        
        <link href="<?=base_url()?>static/semantic-ui/semantic.min.css" rel="stylesheet"/>
        <script src="<?=base_url()?>static/semantic-ui/semantic.min.js"></script>
        <link href="<?=base_url()?>static/admin/css/admin.css" rel="stylesheet"/>
        <script src="<?=base_url()?>static/admin/js/admin.js"></script>
        <script src="<?=base_url()?>static/js/jquery.mask.min.js"></script>
        <script src="<?=base_url()?>static/js/mask-validate.js"></script>
        <script src="<?=base_url()?>static/js/cepWs.js"></script>
        <script src="<?=base_url()?>static/js/confirm_delete.js"></script>
        <script src="<?=base_url()?>/static/js/dropdowns.js"></script>
        <script src="<?=base_url()?>/static/js/textarea.js"></script>
        <script>
        $(function(){
            $('input, textarea').first().focus();
        });
        </script>
        
    </head>
<body>

<!-- Menu MOBILE -->
<div class="ui vertical sidebar menu">
    <?php if (isset($_SESSION['admin_user'])): ?>
        <a class="item" href="#"><?=$_SESSION['admin_user']['email']?></a>

        <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>

            <a class="item" href="<?=site_url("painel/usuarios")?>">Usuários</a>
            <a class="item" href="<?=site_url("painel/instituicoes")?>">Instituições</a>
            <a class="item" href="<?=site_url("painel/cursos")?>">Cursos</a>
            <div class='item'>
            <div class="header">Minicursos</div>
                <a class="item" href="<?=site_url("painel/minicursos")?>">Submissões</a>
                <a class="item" href="<?=site_url("painel/minicursos/distribuicao")?>">Distribuição de horários</a>
                <a class="item" href="<?=site_url("painel/grandesareas")?>">Grandes áreas</a>
                <a class="item" href="<?=site_url("painel/areas")?>">Áreas</a>
            </div>
        <?php endif; ?>

        <div class='item'>
            <div class="header">Trabalhos</div>
            <a class="item" href="<?=site_url("painel/trabalhos")?>">Submissões</a>
            <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
                <a class="item" href="<?=site_url("painel/gts")?>">Grupos de trabalho</a>
            <?php endif; ?>
        </div>

        <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
            <a class="item" href="<?=site_url("painel/evento")?>">Evento</a>
        <?php endif; ?>

        <a class="item" href="<?=site_url("admin/logout")?>" class="item">Sair</a>
    <?php endif; ?>
</div>


<!-- Page Contents -->
<div class="pusher">


    <div class="ui inverted vertical masthead center aligned segment" >

        <!-- Top Menu -->
        <div class="ui container">
            <div class="ui large secondary top menu">

                <?php if (isset($_SESSION['admin_user'])): ?>
                    <a class="toc item">
                        <i class="sidebar icon"></i>
                    </a>
                <?php endif; ?>
                
                <div class='left item'>
                    <a class="navbar-brand" href="<?=site_url('admin/painel')?>"><?=$this->config->item('nome_evento')?> Admin Panel </a>
                </div>
                
                <div class="right item">
                    <?php include 'menu.php'; ?>
                </div>
            </div>
        </div>
    </div>



<div class="ui middle stackable centered grid">

<div class="row">
<div class="eight wide column">
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
</div></div>