<?php include 'head.php' ?>


<h2 class="ui horizontal divider header">Submeter trabalho</h2>
<div class="ui text container">

  

<form action="<?=site_url('trabalhos/submeter') ?>" novalidate class="ui form" method="post" enctype="multipart/form-data">

    
    <input type="hidden" name="id" value="<?=_v($dados,'id')?>" />

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field required">
      <label for="titulo">Título</label>
      <input type="text" name="titulo" id="titulo" maxlength="250" value="<?=_v($dados,'titulo')?>" />
      <?=form_error('titulo') ?>
    </div>

    <div class="field">
      <label for="titulo">Autor:</label>
      <input value="<?=$_SESSION["user"]['nome_completo']?>" readonly>
    </div>

    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Coautores
    </h4>
    <div id="coautor_area">
    <?php
    if (!isset($dados["coautores"])){
      $dados["coautores"] = [];
    }
    ?>
    <?php for ($i = 0; $i < $evento['limite_coautores']; $i++) {
      print coautorHTMLField($i,"coautor", _v($dados["coautores"],$i));
    } ?>
    </div>
    <?=form_error('nome_coautor[]') ?>
    <?=form_error('email_coautor[]') ?>
    <button id="addCoautores" type="button" class="ui green button" onclick="addCoautor(0)">
      <i class="add icon"></i>
      Adicionar coautor
    </button>

    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Orientadores
    </h4>
    <div id="orientador_area">
    <?php
    if (!isset($dados["orientadores"])){
      $dados["orientadores"] = [];
    }?>
    <?php for ($i = 0; $i < $evento['limite_orientadores']; $i++) {
      print coautorHTMLField($i,"orientador", _v($dados["orientadores"],$i));
    } ?>
    </div>
    <?=form_error('nome_orientador[]') ?>
    <?=form_error('email_coautor[]') ?>
    <button id="addOrientadores" type="button" class="ui green button" onclick="addCoautor(1)">
      <i class="add icon"></i>
      Adicionar orientador
    </button>
    
    <br><br>

    <div class="field required">
      <label for="idarea">Área</label>
      <select name="idarea" id="idarea" required class="ui search dropdown category">
            <option value=''>Selecione a área</option>
            <?php
              $garea = null;
              foreach($areas as $item){
                  $selected = "";
                  if (_v($dados,"idarea") == $item['id']){
                    $selected = "selected";
                  }
                  
                  if ($garea != $item['grande_area']){
                    if ($garea != null){
                      print "</optgroup>";
                    }
                    print "<optgroup label='{$item['grande_area']}'>$garea";
                    $garea = $item['grande_area'];
                  }
                  print "<option value='{$item['id']}' $selected >{$item['area']}</option>";
                  
              }
              print "</optgroup>";
            ?>
      </select>
      
      <?=form_error('idarea') ?>
    </div>


    <div class="field required">
      <label for="idgt">Grupo de trabalho</label>
      <select name="idgt" id="idtipo_trabalho" required class="ui search dropdown">
            <option value=''>Selecione o grupo</option>
            <?php
              foreach($gts as $item){
                  if ($item['id'] == _v($dados,"idgt")){
                      $selected = "selected";
                  } else {
                      $selected = "";
                  }
                  print "<option $selected value='{$item['id']}'>{$item['gt']}</option>";
              }
            ?>
      </select>
      <?=form_error('idgt') ?>
    </div>

    <div class="field required">
      <label for="idtrilha">Trilha</label>
      <select name="idtrilha" id="trilha" required class="ui search dropdown">
            <option value=''>Selecione a trilha</option>
            <?php
                foreach($trilhas as $k=>$v){
                  if ($k == _v($dados,"idtrilha")){
                      $selected = "selected";
                  } else {
                      $selected = "";
                  }
                  print "<option $selected value='$k'>$v</option>";
              }
            ?>
      </select>
      <?=form_error('idtrilha') ?>
    </div>

    <div class="field">
      <label for="url">URL do vídeo no Youtube</label>
      <input type='url' name="url" id="url" >
      <?=form_error('url') ?>
      <div class="ui pointing label">Você poderá atualizar a URL depois.</div>
    </div>

    <?php if (_v($dados,'status') != PENDENTE && _v($dados,'observacao') != ""): ?>

      <div class="ui yellow message">
        <label>Observações:</label>
        <?=_v($dados,'observacao')?>
      </div>

    <?php endif; ?>

    

    <div class="field">
  
      <label>Arquivo: </label>
      <?php if (_v($dados,'id') != ""): ?>
        <a class="ui green button" href="<?=base_url()?>uploads/trabalhos/<?=_v($dados,'arquivo')?>" target="_BLANK">
          <i class="download icon"></i>
          <?=limitar(_v($dados,'arquivo'),40)?>
        </a>
        <span class="date">
          <?=date("d/m/Y H:i:s", strtotime($dados["submissao"]))?>
        </span>
      <?php endif ?>

      <input type="file" name="arquivo" required class="clearable file input" id="arquivo" />
      <div class="ui pointing label">Somente .pdf com tamanho máximo de 10.00 MB</div>
      <?=form_error('arquivo') ?>
    </div>

    
    <?php if ($evento['aceitando_submissoes']): ?>
      <?php if (_v($dados,"id") == "" || _v($dados,"id") != "" && _v($dados,"idusuario") == $_SESSION["user"]["id"]): ?>
        <button type="submit" class="ui green button">
          <i class="upload icon"></i>
          Submeter trabalho
        </button>
      <?php endif; ?>
    <?php endif; ?>
  </form>
  
</div>


<?php include 'bottom.php'; ?>
