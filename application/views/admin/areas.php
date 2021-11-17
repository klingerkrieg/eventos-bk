<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/areas/') ?>">Áreas</a>
    /
    <a href="<?=site_url("painel/areas/index/form")?>">Nova área</a>
</h2>

<?php #echo validation_errors(); ?>

<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/areas/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />


    <div class="field required">
        <label for="area">Nome da área</label>
        <input type="text" id="area" name="area" maxlength="250" value="<?=_v($dados,"area")?>" required>
        <?=form_error('area')?>
    </div>

    <div class="field">
        <label for="idgrandearea">Grande área</label>
        <select name="idgrandearea" id="idgrandearea">
            <option value=""> - </option>
            <?php foreach($grandes_areas as $item){
                if ($item['id'] == _v($dados,"idgrandearea")){
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value='{$item['id']}' $selected>{$item['grande_area']}</option>";
            } ?>
        </select>
        <?=form_error('idgrandearea')?>
    </div>
  
        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>
    
    <?php if (isset($dados["id"])): ?>
    <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/areas/deletar/{$dados['id']}")?>')">
        <i class="trash alternate icon"></i>
        Deletar
    </button>
    <?php endif; ?>

</form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>

<div class="fourteen wide column">

<form class="ui form" method="get" action="<?=site_url('painel/areas/index/') ?>"> 

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
    <th><a href='<?=order_by_link("painel/areas/index", "area")?>'>Nome da área <?=order_by_img("area")?></a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/areas/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Nome da área"><?=$ln['area']?></td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <?php
    $config['base_url'] = site_url().'/painel/areas';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    print "<tfoot><tr><th colspan='10'>Total: {$lista['total_rows']}<div class='ui right floated pagination menu'>$links</div></th></tr></tfoot>";
    
    ?>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

