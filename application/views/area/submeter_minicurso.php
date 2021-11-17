<?php include 'head.php' ?>


<h2 class="ui horizontal divider header">Submeter minicurso</h2>
<div class="ui text container">

  
  <form action="<?=site_url('minicursos/submeter') ?>" novalidate class="ui form" method="post" enctype="multipart/form-data">
    
    <input type="hidden" name="id" value="<?=_v($dados,'id')?>" />

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    

    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Autores
    </h4>

    <div class="field">
      <label for="titulo">Autor:</label>
      <input value="<?=$_SESSION["user"]['nome_completo']?>" readonly>
    </div>

    <div class="field required">
      <label for="telefone">Telefone:</label>
      <input id="telefone" name="telefone" class="telefone" value="<?=_v($user,"telefone")?>">
    </div>

    <div class="fields ">
          <div class='field required'><label>Apresentação pessoal</label></div>
            <label><input type="radio" name="lattesC" value="lattes" id="radioLattes" hide="curriculoDiv" show="lattesDiv">Lattes</label>
            <label><input type="radio" name="lattesC" value="curriculo" id="radioCurriculo" hide="lattesDiv" show="curriculoDiv">Currículo</label>
    </div>
    
    <div class="field required" id='lattesDiv'>
        <label for="lattes" >Link do lattes</label>
        <input type="text" name="lattes" id="lattes" maxlength="250" value="<?=_v($user,'lattes')?>" />
        <?=form_error('lattes')?>
        <div class="ui pointing label">
          Cole aqui a URL do seu lattes, para que nós possamos divulgar o minicurso com a sua apresentação pessoal.
        </div>
    </div>

    <div class="field required" id='curriculoDiv'>
        <label for="curriculo" >Currículo</label>
        <textarea name="curriculo" id="curriculo" maxlength="1000"><?=_v($user,'curriculo')?></textarea>
        <?=form_error('curriculo')?>
        <div class="ui pointing label">
          Caso você prefira, pode escrever a sua formação acadêmica para fazermos a divulgação do minicurso com a sua apresentação pessoal.
        </div>
    </div>

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
    <br/><br/>

    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Minicurso
    </h4>

    <div class="field required">
      <label for="titulo">Título</label>
      <input type="text" name="titulo" id="titulo" maxlength="250" value="<?=_v($dados,'titulo')?>" />
      <?=form_error('titulo') ?>
    </div>

    <div class="field required">
      <label for="resumo">Resumo</label>
      <textarea name="resumo" maxlength="2000"><?=_v($dados,"resumo")?></textarea>
      <?=form_error('resumo') ?>
    </div>

    <div class="field required">
      <label for="objetivo">Objetivo</label>
      <textarea name="objetivo" maxlength="500"><?=_v($dados,"objetivo")?></textarea>
      <?=form_error('objetivo') ?>
    </div>

    <div class="field required">
      <label for="idarea">Área</label>
      <select name="idarea" id="idarea" required class="ui search dropdown category">
            <option value=''>Selecione a área do minicurso</option>
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
                    print "<optgroup label='{$item['grande_area']}'>$opt";
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
      <label for="vagas">Vagas sugeridas</label>
      <select name="vagas" id="vagas" required class="ui search dropdown">
            <option value=''>Selecione a quantidade de vagas</option>
            <?php
                foreach($vagas as $v){
                  if ($v == _v($dados,"vagas")){
                      $selected = "selected";
                  } else {
                      $selected = "";
                  }
                  print "<option $selected value='$v'>$v</option>";
              }
            ?>
      </select>
      <?=form_error('vagas') ?>
    </div>

    

    <div class="field required">
      <label for="ch">Carga horária sugerida</label>
      <select name="ch" id="ch" required class="ui search dropdown">
            <option value=''>Selecione a carga horária</option>
            <?php
                foreach($chs as $k=>$v){
                  if ($k == _v($dados,"ch")){
                      $selected = "selected";
                  } else {
                      $selected = "";
                  }
                  print "<option $selected value='$k'>$v</option>";
              }
            ?>
      </select>
      <?=form_error('ch') ?>
    </div>

    <?php if (_v($dados,"id") != ""): ?>
      <div class="field">
        <label>Status:</label>
        <?php if ($dados["status"] == PENDENTE): ?>
          <div class="ui grey label"  style="text-align:center;">
        <?php elseif ($dados["status"] == APROVADO): ?>
          <div class="ui green label"  style="text-align:center;">
        <?php elseif ($dados["status"] == APROVADO_CORRECOES_PENDENTES): ?>
          <div class="ui yellow label" style="text-align:center;">
        <?php elseif ($dados["status"] == APROVADO_CORRECOES): ?>
          <div class="ui green label" style="text-align:center;">
        <?php else: ?>
          <div class="ui red label"  style="text-align:center;">
        <?php endif; ?>
        <?=$status[$dados["status"]]?>
        </div>
        </label>
      </div>
    <?php endif; ?>


    <div class='field'>

    <label>Horários preferenciais para a realização</label>

    <table class="ui celled table">
    <?php
    $horariosMaximo = 0;
    $matrixHoras = [];
    $vetDatas = [];
    print "<thead><tr>";
    foreach($datasHorarios as $data=>$horarios){
      print "<th>".date("d/m/Y",strtotime($data))."</th>";

      array_push($vetDatas,$data);
      array_push($matrixHoras,$horarios);

      if (count($horarios) > $horariosMaximo){
        $horariosMaximo = count($horarios);
      }
    }
    print "</tr></thead>";

    
    for ($y = 0; $y < $horariosMaximo; $y++){
      print "<tr>";
      for ($j = 0; $j < count($matrixHoras); $j++){
        
        if (isset($matrixHoras[$j][$y])){
          if ($matrixHoras[$j][$y] == null){
            print "<td></td>";
          } else {
            $value = $vetDatas[$j]." ".$matrixHoras[$j][$y];
            print "<td><label class='full'><input class='ui fitted checkbox' name='horarios_preferenciais[]' type='checkbox' value='$value'>{$matrixHoras[$j][$y]}</label></td>";
          }
        } else {
          print "<td></td>";
        }
      }
      print "</tr>";
    }
    

    ?>
    </table>
    <?=form_error('horarios_preferenciais[]')?>
    </div>

    <?php /*div class="field required">
      <label for="descricao">Descrição do minicurso</label>
      <textarea name="descricao" maxlength="800"><?=_v($dados,"descricao")?></textarea>
      <?=form_error('descricao') ?>
      <div class="ui pointing label">
        Neste campo você deve inserir um resumo para as pessoas que irão se matricular.<br/>
        É importante que você deixe claro quais são os requisitos, tanto de software como de conhecimentos exigidos.
      </div>
    </div*/ ?>

    <div class="field">
      <label for="informacoes_adicionais">Informações adicionais</label>
      <textarea name="informacoes_adicionais" maxlength="800"><?=_v($dados,"informacoes_adicionais")?></textarea>
      <?=form_error('informacoes_adicionais') ?>
      <div class="ui pointing label">Neste campo você deve inserir qualquer informação que seja 
                                      útil para que o minicurso possa ser realizado, como por exemplo, 
                                      softwares que precisam ser instalados no laboratório.</div>
    </div>

    <?php if (_v($dados,'status') != PENDENTE && _v($dados,'observacao') != ""): ?>

      <div class="ui yellow message">
        <label>Observações:</label>
        <?=_v($dados,'observacao')?>
      </div>

    <?php endif; ?>

    

    <?php /*div class="field">
  
      <label>Arquivo: </label>
      <?php if (_v($dados,'id') != ""): ?>
        <a class="ui green button" href="<?=base_url()?>uploads/minicursos/<?=_v($dados,'arquivo')?>" target="_BLANK">
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
    </div*/ ?>

    <?php if ($evento['aceitando_submissoes_minicursos']): ?>
      <button type="submit" class="ui green button">
        <i class="upload icon"></i>
        Submeter minicurso
      </button>
    <?php endif; ?>
  </form>
  
</div>


<?php include 'bottom.php'; ?>
