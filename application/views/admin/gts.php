<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/gts/') ?>">GTS</a>
    /
    <a href="<?=site_url("painel/gts/index/form")?>">Novo GT</a>
</h2>

<?php #echo validation_errors(); ?>

<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/gts/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field required">
        <label for="gt">Nome do GT</label>
        <input type="text" id="gt" name="gt" maxlength="250" value="<?=_v($dados,"gt")?>" required>
        <?=form_error('gt')?>
    </div>
  
        
    <button class="ui positive  button" type="submit">
        <i class="trash alternate icon"></i>
        Salvar
    </button>
    
    <?php if (isset($dados["id"])): ?>
    <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/gts/deletar/{$dados['id']}")?>')">
        <img src="<?=base_url()?>static/admin/img/delete.png" class="adminicon"/>
        Deletar
    </button>
    <?php endif; ?>

</form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>

<div class="fourteen wide column">

<form class="ui form" method="get" action="<?=site_url('painel/gts/index/') ?>"> 

    <div class="three fields">
        <div class="field">
            <input type="text" id="filtro" name="filtro" maxlength="250" value="<?=_v($_GET,"filtro")?>" placeholder="Filtro">
            </label>
        </div>

        <div class="field">
            <button class="ui positive  button" type="submit">
                <i class="search icon"></i>
                Filtrar
            </button>
        </div>
    </div>
</form>

<table class="ui celled table">
  <thead>
    <tr><th>Editar</th>
    <th><a href='<?=order_by_link("painel/gts/index", "gt")?>'>Nome do GT <?=order_by_img("gt")?></a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/gts/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Nome"><?=$ln['gt']?></td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <?php
    $config['base_url'] = site_url().'/painel/gts';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    if ($links != ""){
        print "<tfoot><tr><th colspan='5'><div class='ui right floated pagination menu'>$links</div></th></tr></tfoot>";
    }
    ?>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

