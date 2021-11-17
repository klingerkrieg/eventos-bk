<?php $emTestes = false;

$foto = "";
if (isset($_SESSION["user"]) && $_SESSION["user"]["foto"] != ""){
  $foto = "<img class='ui avatar image' src='{$_SESSION["user"]['foto']}'>";
}
?>
<!-- Following Menu: -->
<div class="ui xmassive top fixed menu hidden verde" >
  <div class="ui container">
    <div class="ui secondary top menu">
          <a class="toc item">
            <i class="sidebar icon"></i>
          </a>
        
          <div class='left item logoMenu'>
            <a href='<?=site_url("home")?>'>
              <img src="<?=base_url("./static/img/logo-branca-vii.svg")?>">
            </a>
          </div>
          
          <div class="right menu">

            <?php include 'menu.php' ?>

			
          <?php if (!$emTestes || isset($_SESSION['user'])): ?>
            <?php if (isset($_SESSION["user"])): ?>
            
              <div class="item ui dropdown menu_superior">
                <div class="text">
                  <?=$foto?>
                  <?=$_SESSION["user"]["email"]?>
                </div>
                <div class="menu">
                  <a class="item" href='<?=site_url('perfil/index') ?>'>PERFIL</a>
                  <a class="item" href='<?=site_url('home/logout') ?>'>SAIR</a>
              </div>
            </div>

            <?php else: ?>
              <a class="item" href="<?=site_url('home/login') ?>">Área do participante</a>
            <?php endif; ?>
          <?php endif; ?>
          </div>
    </div>
  </div>
</div>

<!-- Menu MOBILE -->
<div class="ui vertical sidebar menu">
    <a class="item" href="#"> <br/><br/><br/> </a>

    <?php if (isset($_SESSION["user"])): ?>
      <a class="item" href="<?=site_url('perfil/index') ?>"> <?=$_SESSION["user"]["email"]?> </a>
      <?php endif; ?>

      <?php include 'menu.php' ?>
    
	  <?php if (!$emTestes || isset($_SESSION['user'])): ?>
		  <?php if (isset($_SESSION["user"])): ?>
			<a class="item" href="<?=site_url('home/logout') ?>"> SAIR</a>
		  <?php else: ?>
			<a class="item" href="<?=site_url('home/login') ?>">Área do participante</a>
		  <?php endif; ?>
	  <?php endif; ?>
</div>


<!-- Page Contents -->
<div class="pusher">
  <div class="ui vertical masthead center aligned segment" id="page-header">


  
    <!-- Top Menu -->
    <div class="ui container">
      <div class="ui large secondary top menu">
        <a class="toc item">
          <i class="sidebar icon"></i>
        </a>
        
        <div class='left item logoMenu'>
          <a href='<?=site_url("home")?>'>
            <img src="<?=base_url("./static/img/logo-verde-vii.svg")?>">
          </a>
        </div>
        
        <div class="right item">

        <?php include 'menu.php' ?>

		<?php if (!$emTestes || isset($_SESSION['user'])): ?>
			<?php if (isset($_SESSION["user"])): ?>


        <div class="item ui dropdown menu_superior">
            <div class="text">
              <?=$foto?>
              <?=$_SESSION["user"]["email"]?>
            </div>
            <div class="menu">
              <a class="item" href='<?=site_url('perfil/index') ?>'>PERFIL</a>
              <a class="item" href='<?=site_url('home/logout') ?>'>SAIR</a>
          </div>
        </div>

			<?php else: ?>
			  <a class="item" href="<?=site_url('home/login') ?>">Área do participante</a>
			<?php endif; ?>
		<?php endif; ?>
        </div>
      </div>
    </div>