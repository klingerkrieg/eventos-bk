<?php include 'head.php'; ?>

<h2 class="ui horizontal divider header">Entrar</h2>
<div class="ui text container">

  <?php if (!evento_encerrado()): ?>
    <div class="ui message">
      Não possui cadastro? <a href="<?=site_url('home/registrar')?>">
          Inscrever-se</a> ao invés disso.
    </div>
  <?php endif; ?>


  

  <form novalidate class="ui large form" method="post" action="<?=site_url('home/logar')?>">

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
     
    <div class="field">  
        <label for="email">E-mail ou Matrícula do SUAP</label>
        <div class="ui labeled input">
          
          <input type="text" name="email" class='emailSuap' id="email" autofocus required maxlength="254" />
        </div>
        <?=form_error("email");?>
    </div>

    <div class="field">  
        <label for="password">Senha</label>
        <input type="password" name="password" id="password" />
        <?=form_error("password");?>
    </div>
      <button type="submit" class="ui fluid large green button">Entrar</button>
  </form>
  <div class="ui basic segment">
    <a href="<?=site_url('home/recuperar_senha')?>">Recuperar senha</a>
  </div>
  
</div>

</div>


<?php include 'bottom.php'; ?>