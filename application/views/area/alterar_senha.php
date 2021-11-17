<?php include 'head.php' ?>

<h2 class="ui horizontal divider header">Entrar</h2>
<div class="ui text container">
  <div class="ui message">
    A senha deve conter o mínimo de 6 caracteres.
  </div>

  <form novalidate class="ui large form" method="post" action="<?=site_url('home/salvar_alteracao_senha')?>">

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    
    <div class="field required">  
        <label for="password1">Senha</label>
        <input type="password" name="password1" id="password1" autofocus  />
        <?=form_error('password1')?>
    </div>

    <div class="field required">  
        <label for="password2">Confirmação da senha</label>
        <input type="password" name="password2" id="password2"  />
        <?=form_error('password2')?>
    </div>

      <button type="submit" class="ui fluid large green button">Alterar senha</button>
  </form>
  
</div>

</div>


<?php include 'bottom.php' ?>