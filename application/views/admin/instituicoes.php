<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/instituicoes/') ?>">Instituições</a>
    /
    <a href="<?=site_url("painel/instituicoes/index/form")?>">Nova instituição</a>
</h2>

<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/instituicoes/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field required">
        <label for="instituicao">Nome da instituição</label>
        <input type="text" id="instituicao" name="instituicao" maxlength="250" value="<?=_v($dados,"instituicao")?>" >
        <?=form_error('instituicao')?>
    </div>
  

    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>
    
    <?php if (isset($dados["id"])): ?>
    <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/instituicoes/deletar/{$dados['id']}")?>')">
    <i class="trash alternate icon"></i>
        Deletar
    </button>

    
    <?php endif; ?>

</form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>

<div class="fourteen wide column">


<form class="ui form" method="get" action="<?=site_url('painel/instituicoes/index/') ?>"> 

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
    <th><a href='<?=order_by_link("painel/instituicoes/index", "instituicao")?>'>Nome da instituicao <?=order_by_img("instituicao")?></a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/instituicoes/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Nome do instituicao"><?=$ln['instituicao']?></td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <tfoot>
  <?php
    $config['base_url'] = site_url().'/painel/instituicoes';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    
    print "<tfoot><tr><th colspan='10'>Total: {$lista['total_rows']}<div class='ui right floated pagination menu'>$links</div></th></tr>";
    
    ?>
    </tfoot>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

