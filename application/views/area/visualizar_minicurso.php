<?php include 'head.php' ?>


<h2 class="ui horizontal divider header">Visualizar minicurso</h2>
<div class="ui text container">

  <form class="ui form" >
  
    
    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Autores
    </h4>


    <?php
    $fields = "";
    $certificado = "";
    if (_v($dados,'certificado') != ""){
      $certificado = "<div class='field'>"
            ."<a class='ui blue button' href='".base_url("./certificados/minicursos/{$dados['certificado']}.pdf")."' target='_BLANK'>"
            ."<i class='download icon'></i>"
            ."Certificado do minicurso</a></div>";
      $fields = "fields";
    } ?>

    <div class="field">
      <label for="autor">Autor:</label>

      <div class="two wide <?=$fields?>">
        <div class="field">
        <input id="autor" value="<?=_v($dados,'nome_autor')?>" readonly>
        </div>
        <?=$certificado?>
      </div>
    </div>

    <div class="field">
      <label for="telefone">Telefone:</label>
      <input id="telefone" readonly value="<?=_v($user,"telefone")?>">
    </div>
    
    <?php if (_v($user,"lattes") != ""):?>
    <div class="field" id='lattesDiv'>
        <label for="lattes" >Lattes</label>
        <input id="lattes" readonly value="<?=_v($user,'lattes')?>" />
    </div>
    <?php endif; ?>

    <?php if (_v($user,"curriculo") != ""):?>
    <div class="field" id='curriculoDiv'>
        <label for="curriculo" >Currículo</label>
        <textarea id="curriculo" readonly><?=_v($user,'curriculo')?></textarea>
    </div>
    <?php endif; ?>

    <?php if (count($dados["coautores"]) > 0):?>

    <div class="field">
      <label>Coautores/Registro de ciência</label>
      <ul id="coautor_area">
      <?php 
      $i = 0;
      foreach( $dados["coautores"] as $coautor) {
        print "<li id='coautor$i'>{$coautor['nome_completo']} &lt;{$coautor['email']}&gt; ";
        $i++;
        
        if ($coautor['ciente'] != ""){
          print '<i class="icon checkmark green" ></i>';
        } else {
            print '<i class="icon close red"></i>';
        }

        if ($coautor["certificado"] != ""){
          print "<a class='ui blue button' href='".base_url("./certificados/minicursos/{$coautor['certificado']}.pdf")."' target='_BLANK'>
                    <i class='download icon'></i>Certificado do minicurso</a>";
        }


        print "</li>";

      }
      ?>
      </ul>
    </div>
    <?php endif; ?>

    <h4 class="ui horizontal divider header">
      <i class="tag icon"></i>
      Minicurso
    </h4>

    <div class="field">
      <label for="titulo">Título:</label>
      <input id="titulo" value="<?=_v($dados,'titulo')?>" readonly>
    </div>

    <div class="field ">
      <label for='resumo'>Resumo</label>
      <textarea id="resumo" readonly><?=_v($dados,"resumo")?></textarea>
    </div>

    <div class="field ">
      <label for='objetivo'>Objetivo</label>
      <textarea id="objetivo" readonly><?=_v($dados,"objetivo")?></textarea>
    </div>


    <div class="field">
      <label for="area">Área: </label>
      <input id="area" value="<?=_v($dados,'grande_area')?> >> <?=_v($dados,'area')?>" readonly>
    </div>

    <div class="field">
      <label for="ch">Carga horária: </label>
      <input id="ch" value="<?=$chs[_v($dados,'ch')]?>" readonly>
    </div>

    <div class="field">
      <label for="vagas">Vagas: </label>
      <input id="vagas" value="<?=_v($dados,'vagas')?>" readonly>
    </div>

    <?php if (count($dados["horarios_escolhidos"]) == 0):?>

    <div class="field">
        
        <label>Data/Hora preferencial:</label>
        <div  id="horario_preferencial" class='horarios'>
        <?=exibirHorarios($dados["horarios_preferenciais"])?>
        </div>
        
    </div>

    <?php else: ?>

    <div class="field">
        
        <label>Data/Hora:</label>
        <div id="horario_escolhido" class='horarios'>
        <?=exibirHorarios($dados["horarios_escolhidos"])?>
        </div>
        
    </div>

    <?php endif; ?>

    <div class="field">
      <label for='url'>URL do minicurso: </label>
      <a id="url" target="_BLANK" href="<?=_v($dados,"url")?>"><?=_v($dados,"url")?></a>
    </div>

    
    <div class="field">
      <label>Status:</label>
      <?php if ($dados["status"] == PENDENTE): ?>
        <div id="status" class="ui grey label"  style="text-align:center;">
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

    

    <div class="field">
      <label for='descricao'>Descrição: </label>
      <textarea id="descricao" readonly><?=_v($dados,'descricao')?></textarea>
    </div>
    

    <div class="field">
      <label for='informacoes_adicionais'>Informações adicionais: </label>
      <textarea id="informacoes_adicionais" readonly><?=_v($dados,'informacoes_adicionais')?></textarea>
    </div>



    

    <?php if (_v($dados,'observacao') != ""): ?>

      <div class="ui message">
        <div class="header">Observações dos revisores</div>
        <?=_v($dados,'observacao')?>
      </div>

    <?php endif; ?>

    

    <?php /*div class="field">
      <label for="arquivo">Arquivo: </label>
      <a class="ui blue button" href="<?=base_url()?>uploads/minicursos/<?=_v($dados,'arquivo')?>" target="_BLANK">
        <i class="download icon"></i>
        <?=limitar(_v($dados,'arquivo'),40)?>
      </a>
      <span class="date">
        <?=date("d/m/Y H:i:s", strtotime($dados["submissao"]))?>
      </span>
    </div*/ ?>

  </form>


<?php if (_v($dados,"status") == APROVADO): ?>

    

  <?php /*
  <div class='ui message'>
    Adicione a URL que você usará para realizar o minicurso.
  </div>


  <form action="<?=site_url('minicursos/setar_url') ?>" novalidate class="ui form" method="post" >

    <input type='hidden' name='id' value='<?=_v($dados,"id")?>'>

    <div class="field">
      <label>URL do minicurso: </label>
      <input type='text' name='url' value='<?=_v($dados,"url")?>' maxlength='2000' placeholder="http://">
      <?=form_error('url') ?>
    </div>
    <button class='ui button green' type='submit'>
      <i class="save icon"></i>
      Salvar URL
    </button>

  </form */ ?>


  <h2 class="ui horizontal divider header">Alunos matriculados</h2>

  <form method='post' action='<?=site_url('minicursos/salvar_diario')?>'>

  <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
  
  <input type='hidden' name='id' value='<?=_v($dados,'id')?>'>
  <table class="ui compact table">
    <thead>
    <tr>
      <th>Nome</th>
      <th>Presenças</th>
      <th>Aprovado</th>
    </tr>
    </thead>
    <tbody>
      <?php foreach( $dados['matriculados'] as $matr):?>
      <tr>
        <td><?=$matr['nome_completo']?></td>
        <td>
          <select name="presenca[<?=$matr['id']?>]">
            <option value='0'>0h</option>
            <?php foreach($chs as $key=>$ch){
              $selected = '';
              if ($key == $matr['presenca']){
                $selected = 'selected';
              }
              print "<option value='$key' $selected>$ch</option>";
            }?>
          </select>
        </td>
        <td>
          <?php
          $checked = '';
          if ($matr['aprovado'] == 1){
            $checked = 'checked';
          }
          ?>
          <input type='hidden' name='aprovado[<?=$matr['id']?>]' value='0' >
          <input type='checkbox' name='aprovado[<?=$matr['id']?>]' value='1' <?=$checked?>>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3">Total de matriculados:<?=count($dados['matriculados'])?></th>
      </tr>
    </tfoot>
  </table>
  
  
  <button class='ui button green' type='submit'>
    <i class="save icon"></i>
    Salvar
  </button>
  <a class='ui button primary' target="_BLANK" href='<?=site_url("minicursos/gerar_lista/{$dados['id']}")?>'>
    <i class="save icon"></i>
    Gerar lista de frequência
  </a>
  </form>
<?php endif; ?>

</div>



<?php include 'bottom.php'; ?>
