<?php require_once 'head.php'; ?>



<div class="five wide column">


<h1>Login</h1>

<form class="ui form" action="<?=site_url('admin/login')?>" method="post">

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field">
        <label for="email"> E-mail ou matrícula do SUAP </label>
        <input type="text" id="email" name="email">
        <?=form_error('email')?>
    </div>
    <div class="field">

        <label for="senha"> Senha </label>
        <input type="password" id="senha" name="senha">
        <?=form_error('senha')?>
    </div>
    <div class="field">
        <button class="ui positive  button">Entrar</button>
        <a href='<?=site_url('home')?>' class="ui primary button">Visitar a página do evento</a>
    </div>

</form>
</div>


<?php require_once 'bottom.php'; ?>