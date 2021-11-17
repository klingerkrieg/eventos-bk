<?php include 'head.php'; ?>

<h2 class="ui horizontal divider header">Recuperar senha</h2>
<div class="ui text container">
  <div class="ui message">

    Enviaremos um e-mail com um link que você precisará acessar para redefinir sua senha.
    <br/>
    Não possui cadastro? <a href="<?=site_url('home/registrar')?>">
        Inscreva-se</a> ao invés disso.
  </div>

  
  <form novalidate class="ui large form" method="post" action="<?=site_url('home/enviar_email_rec')?>">

  <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    
    <div class="field required">  
        <label>E-mail</label>
        <input type="text" name="email" id="email" autofocus maxlength="250" />
        <?=form_error('email')?>
    </div>

    <div class="field required">  
        <label>CPF</label>
        <input type="text" class='cpf' name="cpf" id="cpf" maxlength="14" />
        <?=form_error('cpf')?>
    </div>

    <?php include 'captcha.php'; ?>

    <button type="submit" class="ui fluid large green button">Solicitar e-mail de recuperação</button>
  </form>
  
</div>

</div>

<?php include 'bottom.php'; ?>