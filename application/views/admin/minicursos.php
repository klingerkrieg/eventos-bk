<?php require_once 'head.php'; ?>

<div class="nine wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/minicursos/') ?>">Minicursos</a>
</h2>



<?php #echo validation_errors(); ?>

<?php if (isset($dados)) : ?>




<form class="ui form" method="post" action="<?=site_url('painel/minicursos/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    

    <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
    <div class="field">
        <b>Autor:</b> 
        <a href='<?=site_url("painel/usuarios/index/"._v($dados,"id"))?>'><?=_v($dados,"nome_autor")?></a>

        <?=_v($dados,"telefone")?>

        <?php
        $certificado = "";
        if (_v($dados,"certificado") != ""){
            $certificado = " - <a href='".base_url()."certificados/minicursos/{$dados['certificado']}.pdf' target='_BLANK'>{$dados['certificado']}.pdf</a>"
                        . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_data"]))."</span>";
        }
        ?>
        <?=$certificado?>  
    </div>

    <div class="field">
        <b>Currículo:</b> 
        <?=_v($dados,"curriculo")?>
    </div>

    <div class="field">
        <b>Lattes:</b> 
        <?=_v($dados,"lattes")?>
    </div>

    <div class="field">
        <label>Coautores/Registrou ciência:</label>
        <ul>
        <?php foreach($dados["coautores"] as $usr){
            print "<li><a href='".site_url("painel/usuarios/index/"._v($usr,"idusuario"))."'>{$usr['nome_completo']}</a>";

            if ($usr['ciente'] != ""){
                print '<i class="icon checkmark green" ></i>';
            } else {
                print '<i class="icon close red"></i>';
            }
            

            if ($usr["certificado"] != ""){
                print " - <a href='".base_url()."certificados/minicursos/{$usr['certificado']}.pdf' target='_BLANK'>{$usr['certificado']}.pdf</a>"
                        . " <span class='date'>". date("d/m/Y H:i:s", strtotime($usr["certificado_data"]))."</span>";
            }

            print "</li>";

        }?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="field">
        <b>Título:</b><?=_v($dados,"titulo")?>
    </div>

    <div class="field">
        <label>Objetivo</label>
        <textarea readonly><?=_v($dados,"objetivo")?></textarea>
    </div>

    <div class="field">
        <label>Resumo</label>
        <textarea readonly><?=_v($dados,"resumo")?></textarea>
    </div>
    

    <div class="field required">
      <label for="idarea">Área</label>
      <select name="idarea" id="idarea" required class="ui search dropdown category">
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

    <div class="field">
        <label>Informações adicionais</label>
        <textarea readonly><?=_v($dados,"informacoes_adicionais")?></textarea>
    </div>

    

    <div class="field required">
      <label for="vagas">Vagas</label>
      <select name="vagas" id="vagas" >
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
      <label for="ch">Carga horária</label>
      <select name="ch" id="ch" >
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
            $checked = "";
            if (in_array($value,$dados["horarios_preferenciais"])){
                $checked = "checked";
            }
            print "<td><label class='full'><input onclick='return false' $checked class='ui fitted checkbox' type='checkbox' >{$matrixHoras[$j][$y]}</label></td>";
          }
        } else {
          print "<td></td>";
        }
      }
      print "</tr>";
    }
    ?>
    </table>
    </div>

    <div class='field'>

        <label>Horários escolhidos para a realização</label>

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
                $checked = "";
                if (in_array($value,$dados["horarios_escolhidos"])){
                    $checked = "checked";
                }
                print "<td><label class='full'><input name='horarios_escolhidos[]' $checked class='ui fitted checkbox' type='checkbox' value='$value'>{$matrixHoras[$j][$y]}</label></td>";
            }
            } else {
            print "<td></td>";
            }
        }
        print "</tr>";
        }
        ?>
        </table>
        <?=form_error('horarios_escolhidos[]')?>
    </div>


    <div class="field">
        <label>Submissão: 
            <a href="<?=base_url()?>uploads/<?=_v($dados,'arquivo')?>" target="_BLANK">
                <?=_v($dados,'arquivo')?></a>

                <span class='date'><?= date("d/m/Y H:i:s", strtotime(_v($dados, "submissao")))?></span>
        </label>        
    </div>


    

    <div class="field">
        <label for="status">Status</label>
        <select name="status" id="status">
            <?php
                foreach($status as $k=>$v){
                    if ($k == _v($dados,"status")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$k'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('status')?>
    </div>

    <div class="field">
      <label for="url">URL</label>
      <input type='text' id='url' name='url' value='<?=_v($dados,"url")?>'>
      <?=form_error('url') ?>
      <div class="ui pointing label">URL que será usada por todos do minicurso.</div>
    </div>

    <div class="field">
        <label for="descricao">Descrição</label>
        <textarea name="descricao" maxlength="2000"><?=_v($dados,"descricao")?></textarea>
        <?=form_error('descricao')?>
        <div class="ui pointing label">Descrição do minicurso para quem irá se matricular (Divulgação).</div>
    </div>

    <div class="field">
        <?php if (_v($dados,"matricula_disponivel")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="matricula_disponivel" value="0"/>
        <label for="matricula_disponivel"> 
            <input type="checkbox" id="matricula_disponivel" name="matricula_disponivel" value="1" <?=$checked?> > Disponível para matrícula
        </label>
        <?=form_error('matricula_disponivel')?>
    </div>

    <div class="field">
        <label for="observacao">Observação</label>
        <textarea name="observacao" maxlength="8000"><?=_v($dados,"observacao")?></textarea>
        <?=form_error('observacao')?>
        <div class="ui pointing label">Observação que será apresentada para os ministrantes.</div>
    </div>

        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar Minicurso
    </button>

    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && isset($dados["id"])): ?>
        <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/minicursos/deletar/{$dados['id']}")?>')">
            <i class="trash alternate icon"></i>
            Deletar
        </button>

        <a href='<?=site_url("painel/minicursos/clonar/{$dados["id"]}")?>' class="ui orange  button" type="submit">
            <i class="copy outline icon"></i>
            Clonar
        </a>
    <?php else: ?>
        <button class="ui negative disabled button" type="button">
            <i class="trash alternate icon"></i>
            Deletar
        </button>
    <?php endif; ?>

    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && (_v($dados,"status") == APROVADO)): ?>
        <button type="button" class="ui primary button" onclick="$('#confirm_certificado_unico').modal('show')">
            <i class="certificate icon"></i>
            Gerar certificado para ministrantes
        </button>


        <div id="confirm_certificado_unico" class="ui modal">
            <i class="close icon"></i>
            <div class="header">
                Confirmação de geração de certificado
            </div>
                <div class="description content">
                    <p>Você tem certeza que deseja gerar o certificado deste minicurso?
                    Isso acarretará em:
                    <ul>
                        <li>Geração do certificado se o minicurso estiver 'Aprovado'.</li>
                        <li>O certificado só será gerado se o autor principal tiver realizado o pagamento da inscrição.</li>
                        <li>Envio de e-mail para todos os autores do minicurso avisando da disponibilidade do certificado.</li>
                    </ul>
                    </p>
                </div>
            <div class="actions">
                <div class="ui black deny button">
                Cancelar
                </div>
                <a class="ui positive right labeled icon button" href='<?=site_url("painel/minicursos/gerar_certificado/{$dados['id']}")?>'>
                Sim, tenho certeza
                <i class="checkmark icon"></i>
                </a>
            </div>
        </div>
    <?php else: ?>
        <a class="ui primary disabled button">
            <i class="certificate icon"></i>
            Gerar certificado para ministrantes
        </a>


        
    <?php endif; ?>


</form>

<h2 class="ui horizontal divider header">Alunos matriculados</h2>

    <form method='post' action='<?=site_url('painel/minicursos/salvar_diario')?>'>

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    
    <input type='hidden' name='id' value='<?=_v($dados,'id')?>'>
    <table class="ui compact table">
        <thead>
        <tr>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Presenças</th>
        <th>Aprovado</th>
        <th>Certificado</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach( $dados['matriculados'] as $matr):?>
        <tr>
            <td><?=$matr['nome_completo']?></td>
            <td><?=$matr['email']?></td>
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
            <td>
                <?php
                if ($matr['certificado'] != ""){
                    print "<a target='_BLANK' href='".base_url("certificados/matriculados/{$matr['certificado']}.pdf")."'>{$matr['certificado']}.pdf</a>";
                }
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="6">Total de matriculados:<?=count($dados['matriculados'])?></th>
        </tr>
        </tfoot>
    </table>
  
  
    <button class='ui button green' type='submit'>
        <i class="save icon"></i>
        Salvar diário
    </button>
    <a class='ui button primary' target="_BLANK" href='<?=site_url("painel/minicursos/gerar_lista/{$dados['id']}")?>'>
        <i class="save icon"></i>
        Gerar lista de frequência
    </a>


    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && (_v($dados,"status") == APROVADO)): ?>
        <button type="button" class="ui primary button" onclick="$('#confirm_certificado_aprovados').modal('show')">
            <i class="certificate icon"></i>
            Gerar certificado para aprovados
        </button>


        <div id="confirm_certificado_aprovados" class="ui modal">
            <i class="close icon"></i>
            <div class="header">
                Confirmação de geração de certificado
            </div>
                <div class="description content">
                    <p>Você tem certeza que deseja gerar o certificado para os discentes deste minicurso?
                    Isso acarretará em:
                    <ul>
                        <li>Geração do certificado se o discente estiver 'Aprovado'.</li>
                        <li>Envio de e-mail para todos os discentes aprovados do minicurso avisando da disponibilidade do certificado.</li>
                    </ul>
                    </p>
                </div>
            <div class="actions">
                <div class="ui black deny button">
                Cancelar
                </div>
                <a class="ui positive right labeled icon button" href='<?=site_url("painel/minicursos/gerar_certificado_matriculados/{$dados['id']}")?>'>
                Sim, tenho certeza
                <i class="checkmark icon"></i>
                </a>
            </div>
        </div>
    <?php else: ?>
        <button type="button" class="ui primary disabled button">
            <i class="certificate icon"></i>
            Gerar certificados para aprovados
        </button>
    <?php endif; ?>
  </form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>



<div class="fourteen wide column">

<form class="ui form" method="get" action="<?=site_url('painel/minicursos/index/') ?>"> 

    <div class="fields">
        <div class="field">
            <input type="text" id="filtro" name="filtro" maxlength="250" value="<?=_v($_GET,"filtro")?>" placeholder="Filtro">
            </label>
        </div>

        <div class="field">
            <select name="status" id="status">
                <option value=""> - Filtrar por status - </option>
                <?php
                    foreach($status as $k=>$v){
                        if ($k == _v($_GET,"status")){
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        print "<option $selected value='$k'>$v</option>";
                    }
                ?>
            </select>
            </label>
        </div>

        <div class="field">
            <button class="ui positive  button" type="submit">
                <i class="search icon"></i>
                Filtrar
            </button>

            <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN): ?>
            <button type="button" class="ui primary button" onclick="$('#confirm_certificados').modal('show')" data-inverted="" data-tooltip="Esta opção só irá gerar os certificados dos minicursos 'Aprovados'." data-position="bottom left">
                <i class="certificate icon"></i>
                Gerar certificados para ministrantes/matriculados aprovados
            </button>

            <div id="confirm_certificados" class="ui modal">
                <i class="close icon"></i>
                <div class="header">
                    Confirmação de geração de certificados
                </div>
                    <div class="description content">
                        <p>Você tem certeza que deseja gerar os certificados para todos os minicursos (Ministrantes e Discentes)?
                        Isso acarretará em:
                        <ul>
                            <li>Geração do certificado para autores de cada minicurso que esteja 'Aprovado'.</li>
                            <li>Geração do certificado para inscritos de cada minicurso que foram 'Aprovados'.</li>
                            <li>O certificado só será gerado se o autor principal tiver realizado o pagamento da inscrição.</li>
                            <li>Envio de e-mail para todos os autores dos minicursos avisando da disponibilidade do certificado.</li>
                        </ul>
                        </p>
                    </div>
                <div class="actions">
                    <div class="ui black deny button">
                    Cancelar
                    </div>
                    <a class="ui positive right labeled icon button" href='<?=site_url("painel/minicursos/gerar_certificados")?>'>
                    Sim, tenho certeza
                    <i class="checkmark icon"></i>
                    </a>
                </div>
                </div>
            <?php else: ?>
                <button type="button" class="ui primary disabled button">
                    <i class="certificate icon"></i>
                    Gerar certificados para ministrantes
                </button>
            <?php endif; ?>
        </div>
    </div>
</form>




<table class="ui celled table">
  <thead>
    <tr><th>Editar</th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "titulo")?>'>Título<?=order_by_img("titulo")?></a></th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "idarea")?>'>Área<?=order_by_img("idarea")?></a></th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "ch")?>'>CH<?=order_by_img("ch")?></a></th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "vagas")?>'>Vagas<?=order_by_img("vagas")?></a></th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "matricula_disponivel")?>'>Matrículas abertas<?=order_by_img("matricula_disponivel")?></a></th>
    <th><a href='<?=order_by_link("painel/minicursos/index", "status")?>'>Status<?=order_by_img("status")?></a></th>
    <?php /* <th><a href='<?=order_by_link("painel/minicursos/index", "autor.pago")?>'>Pagamento<?=order_by_img("autor.pago")?></a></th> */ ?>
    <th><a href='<?=order_by_link("painel/minicursos/index", "certificado")?>'>Certificado Ministrantes<?=order_by_img("certificado")?></a></th>
    <th><a href='#'>Certificado Discentes</a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/minicursos/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Título"><?=$ln['titulo']?></td>
            <td data-label="idarea"><?=$ln['area']?></td>
            <td data-label="ch"><?=$ln['ch']?></td>
            <td data-label="vagas"><?=$ln['qtdMatriculados']."/".$ln['vagas']?></td>
            <td data-label="matricula_disponivel" class='<?=($ln['matricula_disponivel'] == 1) ? "positive" : "negative"?>'>
            <?php
            if ($ln['matricula_disponivel']){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td>
            <td data-label="status"><?=statusColor($ln["status"],$status)?></td>
            <?php /*<td data-label="Pagamento" class='<?=($ln['pago'] == 1) ? "positive" : "negative"?>'>
            <?php
            if ($ln['pago']){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td> */ ?>
            <td data-label="Certificado" class='<?=($ln['certificado'] != "") ? "positive" : "negative"?>'>
            <?php
            if ($ln['certificado'] != ""){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td>
            <td class='<?=($ln['aprovadosComCertificado'] != "") ? "positive" : "negative"?>'>
            <?php
            if ($ln['qtdMatriculados'] > 0){
                if ($ln['aprovadosComCertificado'] != ""){
                    print '<i class="icon checkmark"></i>';
                } else {
                    print '<i class="icon close"></i>';
                }
            }
            ?>
            </td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <?php
    $config['base_url'] = site_url().'/painel/minicursos';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    
    print "<tfoot><tr><th colspan='10'>Total: {$lista['total_rows']}<div class='ui right floated pagination menu'>$links</div></th></tr></tfoot>";
    
    ?>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

