<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/cursos/') ?>">Cursos</a>
    /
    <a href="<?=site_url("painel/cursos/index/form")?>">Novo curso</a>
</h2>

<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/cursos/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field required">
        <label for="curso">Nome do curso</label>
        <input type="text" id="curso" name="curso" maxlength="250" value="<?=_v($dados,"curso")?>" required>
        <?=form_error('curso')?>
    </div>
  

        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>
    
    <?php if (isset($dados["id"])): ?>
    <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/cursos/deletar/{$dados['id']}")?>')">
    <i class="trash alternate icon"></i>
        Deletar
    </button>

    
    <?php endif; ?>

</form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>

<div class="fourteen wide column">


<form class="ui form" method="get" action="<?=site_url('painel/cursos/index/') ?>"> 

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
    <tr><th class="one wide">Editar</th>
    <th><a href='<?=order_by_link("painel/cursos/index", "curso")?>'>Nome do curso <?=order_by_img("curso")?></a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar"><a href='<?=site_url().'/painel/cursos/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Nome do curso"><?=$ln['curso']?></td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <tfoot>
  <?php
    $config['base_url'] = site_url().'/painel/cursos';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    
    print "<tfoot><tr><th colspan='10'>Total: {$lista['total_rows']}<div class='ui right floated pagination menu'>$links</div></th></tr>";
    
    ?>
    
    </tfoot>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

