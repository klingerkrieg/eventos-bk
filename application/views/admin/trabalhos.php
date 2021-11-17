<?php require_once 'head.php'; ?>

<?php 
$readOnlyAvaliador = "";
if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR){
    $readOnlyAvaliador = "disabled";
} ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/trabalhos/') ?>">Trabalhos</a>
</h2>



<?php #echo validation_errors(); ?>

<?php if (_v($dados,"id") != "") : ?>



<?php if ($_SESSION["admin_user"] == NIVEL_AVALIADOR):?>
    <form class="ui form" >
<?php else:?>
    <form class="ui form" method="post" action="<?=site_url('painel/trabalhos/salvar/') ?>" enctype="multipart/form-data">
<?php endif; ?>

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />


    <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
    <div class="field">
        <b>Autor:</b> 
        <a href='<?=site_url("painel/usuarios/index/"._v($dados,"idusuario"))?>'><?=_v($dados,"nome_autor")?></a>

        <?=_v($dados,"telefone")?>


        <?php
        $certificado = "";
        if (_v($dados,"certificado") != ""){
            $certificado = " - <a href='".base_url()."certificados/trabalhos/{$dados['certificado']}.pdf' target='_BLANK'>{$dados['certificado']}.pdf</a>"
                        . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_data"]))."</span>";
        }
        ?>
        <?=$certificado?>  
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
                print " - <a href='".base_url()."certificados/trabalhos/{$usr['certificado']}.pdf' target='_BLANK'>{$usr['certificado']}.pdf</a>"
                        . " <span class='date'>". date("d/m/Y H:i:s", strtotime($usr["certificado_data"]))."</span>";
            }

            print "</li>";
        }?>
        </ul>
    </div>

    <div class="field">
        <label>Orientadores/Registrou ciência:</label>
        <ul>
        <?php foreach($dados["orientadores"] as $usr){
            print "<li><a href='".site_url("painel/usuarios/index/"._v($usr,"idusuario"))."'>{$usr['nome_completo']}</a>";

            
            if ($usr['ciente'] != ""){
                print '<i class="icon checkmark green" ></i>';
            } else {
                print '<i class="icon close red"></i>';
            }
            

            if ($usr["certificado"] != ""){
                print " - <a href='".base_url()."certificados/trabalhos/{$usr['certificado']}.pdf' target='_BLANK'>{$usr['certificado']}.pdf</a>"
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
    
    <?php /*div class="field">
        <label>GT: <?=_v($dados,"gt")?></label>
    </div*/ ?>

    <div class="field required">
      
      <?php if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR):?>
        <b for="idarea">Área</b>
        <?=$dados["area"]?>
      <?php else: ?>
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
      <?php endif; ?>
    </div>

    <?php /* div class="field">
        <b>Tipo de trabalho:</b><?=_v($tiposTrabalhos, _v($dados,"idtipo_trabalho"))?>
    </div*/ ?>

    <div class="field">
        <label for="idtrilha">Trilha</label>
        <select name="idtrilha" id="idtrilha" required class="ui search dropdown">
        <?php
        foreach($trilhas as $key=>$trilha){
            $selected = "";
            if (_v($dados,"idtrilha") == $key){
                $selected = "selected";
            }
            print "<option value='{$key}' $selected >{$trilha}</option>";
        }
        ?>
        </select>
        <?=form_error('idtrilha') ?>
    </div>

    <div class="field">
        <label for="idgt">Grupo de Trabalho</label>
        <select name="idgt" id="idgt" required class="ui search dropdown">
        <?php
        foreach($gts as $item){
            $selected = "";
            if (_v($dados,"idtrilha") == $item["id"]){
                $selected = "selected";
            }
            print "<option value='{$item["id"]}' $selected >{$item["gt"]}</option>";
        }
        ?>
        </select>
        <?=form_error('idgt') ?>
    </div>

    <div class="field">
        <b>URL do vídeo no Youtube: </b>
        <a target="_BLANK" href="<?=_v($dados,'url')?>"><?=_v($dados,'url')?></a>
    </div>

    <div class="field">
        <label>Trabalho: 
                <a href="<?=base_url()?>uploads/trabalhos/<?=_v($dados,'arquivo')?>" target="_BLANK">
                    <?=_v($dados,'arquivo')?></a>

                    <span class='date'><?= date("d/m/Y H:i:s", strtotime(_v($dados, "submissao")))?></span>
        </label>        
    </div>

    <div class="field">
    <?php
    $corrigido = "";
    if (_v($dados,"arquivoCorrigido") != ""){
        $corrigido = "<a href='".base_url()."uploads/trabalhos/correcoes/{$dados['arquivoCorrigido']}' target='_BLANK'>{$dados['arquivoCorrigido']}</a>";
        $corrigido .= " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["correcao"]))."</span>";
    }
    ?>
        <label>Trabalho corrigido: <?=$corrigido?> </label>        
    </div>


    <div class="field">
        <?php if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR):?>
            <?=statusColor($dados['status'],$status)?>
        <?php else: ?>
            <label for="status">Status</label>
            <select name="status" id="status" class="ui search dropdown">
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
        <?php endif; ?>
    </div>

    <div class="field">
        <b>Média das notas:</b>
        <?=number_format($dados["nota"],2,",",".") ?>
    </div>

    <div class="field">
        
        <input type='hidden' name='apresentado' value='0'>
        <label>
            <?php
            $checked = "";
            if (_v($dados,"apresentado")){
                $checked = "checked";
            }
            ?>
            <input type='checkbox' name='apresentado' value='1' <?=$checked?> <?=$readOnlyAvaliador?> >
            Apresentado
        </label>
        <?=form_error('apresentado')?>
    </div>

    <div class="field">
        
        <input type='hidden' name='premiado' value='0'>
        <label>
            <?php
            $checked = "";
            if (_v($dados,"premiado")){
                $checked = "checked";
            }
            ?>
            <input type='checkbox' name='premiado' value='1' <?=$checked?> <?=$readOnlyAvaliador?> >
            Trabalho premiado
        </label>
        <?=form_error('premiado')?>
    </div>

    <div class="field">
        <label for="observacao">Observação sobre o resumo</label>
        <textarea id="observacao" name="observacao" maxlength="8000" <?=$readOnlyAvaliador?>><?=_v($dados,"observacao")?></textarea>
        <?=form_error('observacao')?>
        <div class="ui pointing label">O participante poderá ver essas observações.</div>
    </div>




    <div class="ui horizontal divider">Definição dos membros da banca Avaliadora</div>


    <?php
    #se você é da equipe e vai setar o avaliador
    if ($_SESSION["admin_user"]["nivel"] >= NIVEL_EQUIPE): ?>

        <?php for ($i = 0; $i < $evento['limite_avaliadores_trabalhos']; $i++): 
            
            $idAv = null;
            if (isset($dados["avaliadores"]) && count($dados["avaliadores"]) >= ($i+1)){
                $idAv = $dados["avaliadores"][$i]["idusuario"];
            }
            
            ?>

            <div class="field">
                <div class="ui fluid selection search dropdown">
                    <input type="hidden" name="avaliador[]" value='<?=$idAv?>'>
                    <i class="dropdown icon"></i>
                    <div class="default text">Selecione o avaliador</div>
                    <div class="menu">
                        <div class='item' data-value='0'>Remover avaliador</div>
                        <?php 
                        foreach($avaliadores as $item){

                            print "<div class='item' data-value='{$item['id']}'>";
                            if ($item["foto"] != ""){
                                print "<img class='ui mini avatar image' src='{$item["foto"]}'>";
                            }
                            print $item['nome_completo'];

                            if ($item["areas"] != ""){
                                $areas = explode(";",$item["areas"]);
                                print '<div class="ui blue labels areasAvaliacaoInsideMenu">';
                                foreach($areas as $area){
                                    print "<a class='ui label'>$area</a>";
                                }
                                print "</div>";
                            }
                            
                            print "</div>";
                        } ?>
                    </div>
                </div>
            </div>
            <?php if (isset($dados["avaliadores"][$i])):?>
                <div class='field'>
                    <b>Avaliação de:</b>
                    <?=$dados["avaliadores"][$i]["nome_completo"]?>
                    <b>Nota:</b>
                    <?=$dados["avaliadores"][$i]["nota"]?>
                    <b>Observações:</b>
                    <?=$dados["avaliadores"][$i]["observacao"]?>
                </div>
            <?php endif; ?>
            
        <?php endfor; ?>
        <?=form_error('avaliador[]')?>
            

        

    <?php endif; ?>

    
    
    
    



    <?php if ($_SESSION["admin_user"]["nivel"] != NIVEL_AVALIADOR): ?>

        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>

    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && isset($dados["id"])): ?>
        <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/trabalhos/deletar/{$dados['id']}")?>')">
            <i class="trash alternate icon"></i>
            Deletar
        </button>
    <?php else: ?>
        <button class="ui negative disabled button" type="button">
            <i class="trash alternate icon"></i>
            Deletar
        </button>
    <?php endif; ?>

    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && _v($dados,"apresentado") && (_v($dados,"status") == APROVADO || _v($dados,"status") == APROVADO_CORRECOES && _v($dados,"arquivoCorrigido") != "")): ?>
        <button type="button" class="ui primary button" onclick="$('#confirm_certificado_unico').modal('show')">
            <i class="certificate icon"></i>
            Gerar certificado
        </button>


        <div id="confirm_certificado_unico" class="ui modal">
            <i class="close icon"></i>
            <div class="header">
                Confirmação de geração de certificado
            </div>
                <div class="description content">
                    <p>Você tem certeza que deseja gerar o certificado deste trabalho?
                    Isso acarretará em:
                    <ul>
                        <li>Geração do certificado se o trabalho estiver 'Aprovado' ou 'Aprovado com correções'.
                        Caso o trabalho esteja aprovados com correções, ele só receberá certificado se o arquivo 
                        de correção tiver sido submetido pelo autor.</li>
                        <li>O certificado só será gerado se o autor principal tiver realizado o pagamento da inscrição.</li>
                        <li>Envio de e-mail para todos os autores do trabalho avisando da disponibilidade do certificado.</li>
                    </ul>
                    </p>
                </div>
            <div class="actions">
                <div class="ui black deny button">
                Cancelar
                </div>
                <a class="ui positive right labeled icon button" href='<?=site_url("painel/trabalhos/gerar_certificado/{$dados['id']}")?>'>
                Sim, tenho certeza
                <i class="checkmark icon"></i>
                </a>
            </div>
        </div>
    <?php else: ?>
        <a class="ui primary disabled button">
            <i class="certificate icon"></i>
            Gerar certificado
        </a>


        
        <?php endif; ?>

    <?php endif; ?>


</form>



<?php
    #se voce é um avaliador deste trabalho
    foreach($dados['avaliadores'] as $av):
        if ($_SESSION["admin_user"]["id"] == $av["idusuario"] ):?>

            <br/><br/>
            <div class="ui horizontal divider">Formulário para a banca Avaliadora</div>

            <form  class="ui form" method="post" action="<?=site_url('painel/trabalhos/salvar_avaliacao/') ?>">

            <input type='hidden' name='id' value='<?=$av["id"]?>' >
            <input type='hidden' name='idtrabalho' value='<?=$av["idtrabalho"]?>' >
            <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

            <?php /*<div class="field">
                <label for="status">Status</label>
                <select name="status" id="status" class="ui search dropdown">
                    <?php
                        foreach($status as $k=>$v){
                            if ($k == _v($av,"status")){
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            print "<option $selected value='$k'>$v</option>";
                        }
                    ?>
                </select>
                <?=form_error('status')?> 
                <div class="ui pointing label">
                <ul>
                <li>A aprovação do trabalho depende do seu voto, 
                um trabalho só será reprovado se 50% ou mais votarem como 'Reprovado'.</li>
                <li>Caso o trabalho precise melhorar em algum ponto, marque como 'Aprovado com correções pendentes' e informe na observação o que deve ser melhorado. 
                O aluno irá resubmeter o trabalho e caso as observações tenham sido solucionadas marque o trabalho como 'Aprovado com correções finalizadas'.</li>
                <li>O status 'Aprovado' não necessita de observações.</li>
                </ul>
                </div>
            </div>*/?>

                <div class="ui message">
                    <div class='header'>Prezado avaliador</div>
                    Você foi selecionado como avaliador deste trabalho, 
                    então você poderá relatar as suas observações 
                    quanto ao trabalho e atribuir uma nota. A nota final será uma média das notas 
                    atribuídas para este trabalho.
                </div>


            <div class="field">
                <label for="observacao">Suas observações como membro da banca avaliadora</label>
                <textarea id="observacao" name="observacao" maxlength="8000"><?=_v($av,"observacao")?></textarea>
                <?=form_error('observacao')?>
                <div class="ui pointing label">O participante poderá ver essas observações.</div>
            </div>

            <div class="field">
                <label for="nota">Nota do trabalho</label>
                <input type="number" id="nota" name="nota" maxlength="3" value="<?=_v($av,"nota")?>">
                <?=form_error('nota')?>
                <div class="ui pointing label">A nota deve ser de 0 a 100 e só deve ser lançada após a apresentação/arguições.</div>
            </div>

            <button class="ui positive  button" type="submit">
                <i class="save icon"></i>
                Salvar avaliação
            </button>

            </form>

            <?php endif; ?>
    <?php endforeach; ?>

<?php endif; ?>

</div>



<?php
/**
 * LISTAGEM
 */

if (isset($lista)) : ?>



<div class="eleven wide column">

<form id="filtros" class="ui form" method="get" action="<?=site_url('painel/trabalhos/index/') ?>"> 

    <div class="ui horizontal divider">Filtros</div>

    <div class='fields'>

        <div class="ten wide field">
            <label for="filtro">Filtro textual</label>
            <div class="field">
                <input type="text" id="filtro" name="filtro" maxlength="250" value="<?=_v($_GET,"filtro")?>" placeholder="Filtro">
            </div>
        </div>

        <div class="five wide field">
            <br><br>
            <label>
                <input type="checkbox" name="avaliador" value="1" <?php print (_v($_GET,"avaliador") == true) ? "checked" : ""; ?>>
                Onde estou como avaliador</label>
        </div>
    </div>

    <div class='fields'>

        <div class="five wide field">
            <label for="status">Status</label>
            <select name="status" id="status" class="ui search dropdown">
                <option value="0"> Sem filtro </option>
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

        <div class="five wide field">
            <label for="trilha">Trilha</label>
            <select name="idtrilha" id="idtrilha" class="ui search dropdown">
                <option value="0"> Sem filtro </option>
                <?php
                    foreach($trilhas as $k=>$v){
                        if ($k == _v($_GET,"idtrilha")){
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

        <div class="five wide field">
        <label for="idarea">Área</label>
        <select name="idarea" id="idarea" class="ui search dropdown category">
                <optgroup>
                    <option value="0"> Sem filtro </option>
                </optgroup>
                <?php
                $garea = null;
                foreach($areas as $item){
                    $selected = "";
                    if (_v($_GET,"idarea") == $item['id']){
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

    </div>

    <div class="field">
        <button class="ui positive  button" type="submit">
            <i class="search icon"></i>
            Filtrar
        </button>

        <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN): ?>
        <button type="button" class="ui primary button" onclick="$('#confirm_certificados').modal('show')" 
                data-inverted="" 
                data-tooltip="Esta opção só irá gerar os certificados dos trabalhos 'Aprovados' ou 'Aprovados com correções'." 
                data-position="bottom left">
            <i class="certificate icon"></i>
            Gerar certificados
        </button>

            <div id="confirm_certificados" class="ui modal">
                <i class="close icon"></i>
                <div class="header">
                    Confirmação de geração de certificados
                </div>
                <div class="description content">
                    <p>Você tem certeza que deseja gerar os certificados para todos os participantes?
                    Isso acarretará em:
                    <ul>
                        <li>Geração do certificado para cada trabalho que esteja 'Aprovado' ou 'Aprovado com correções'.
                        Os trabalhos que foram aprovados com correções, só receberão certificado se o arquivo de correção 
                        tiver sido submetido pelo autor.</li>
                        <li>O certificado só será gerado se o autor principal tiver realizado o pagamento da inscrição.</li>
                        <li>Envio de e-mail para todos os autores dos trabalhos avisando da disponibilidade do certificado.</li>
                    </ul>
                    </p>
                </div>
                <div class="actions">
                    <div class="ui black deny button">
                        Cancelar
                    </div>
                    <a class="ui positive right labeled icon button" href='<?=site_url("painel/trabalhos/gerar_certificados")?>'>
                        Sim, tenho certeza
                        <i class="checkmark icon"></i>
                    </a>
                </div>
            </div>
            <?php else: ?>
                <button type="button" class="ui primary disabled button">
                    <i class="certificate icon"></i>
                    Gerar certificados
                </button>
            <?php endif; ?>



            <?php if ($_SESSION["admin_user"]["nivel"] > NIVEL_AVALIADOR):?>

                <a href="<?=site_url("painel/trabalhos/distribuir_trabalhos")?>" class="ui primary button"
                data-inverted="" 
                data-html="Esta opção só irá distribuir os trabalhos que ainda não possuem avaliadores.<br>
                            Caso não existam avaliadores na área deste trabalho ele não será enviado para nenhum avaliador.<br>
                            Sua distribuição deverá ser feita manualmente." 
                data-position="bottom left">
                    <i class="paper plane icon"></i>
                    Distribuir os trabalhos para os avaliadores
                </a>

            <?php endif;?>


            <button src="<?=site_url("painel/trabalhos/gerar_planilha")?>" class="ui primary button"
                onclick="gerarPlanilha(this)"
                type='button'>
                    <i class="file excel outline icon"></i>
                    Gerar planilha da pesquisa
            </button>

    </div>

</form>




<table class="ui celled table">
  <thead>
    <tr><th>Editar</th>
    <th><a href='<?=order_by_link("painel/trabalhos/index", "titulo")?>'>Título<?=order_by_img("titulo")?></a></th>
    <!--th><a href='<?=order_by_link("painel/trabalhos/index", "gts.gt")?>'>GT<?=order_by_img("gts.gt")?></a></th-->
    <?php /* th><a href='<?=order_by_link("painel/trabalhos/index", "idtipo_trabalho")?>'>Tipo de trabalho<?=order_by_img("idtipo_trabalho")?></a></th */ ?>
    <th><a href='<?=order_by_link("painel/trabalhos/index", "idtrilha")?>'>Trilha<?=order_by_img("idtrilha")?></a></th>
    
    <th>
        <a href='<?=order_by_link("painel/trabalhos/index", "status")?>'>Status<?=order_by_img("status")?></a>
        <?php if ($_SESSION["admin_user"]["nivel"] != NIVEL_AVALIADOR):?>
            <a href='<?=order_by_link("painel/trabalhos/index", "avaliadores_qtd")?>'>Avaliadores<?=order_by_img("avaliadores_qtd")?></a>
            <a href='<?=order_by_link("painel/trabalhos/index", "nota")?>'>Nota<?=order_by_img("nota")?></a>
        <?php endif; ?>
    </th>
    <?php /*<th><a href='<?=order_by_link("painel/trabalhos/index", "autor.pago")?>'>Pagamento<?=order_by_img("autor.pago")?></a></th>*/ ?>
    <th><a href='<?=order_by_link("painel/trabalhos/index", "apresentado")?>'>Apresentado<?=order_by_img("apresentado")?></a></th>
    <th><a href='<?=order_by_link("painel/trabalhos/index", "certificado")?>'>Certificado<?=order_by_img("certificado")?></a></th>
    
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/trabalhos/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Título"><?=$ln['titulo']?></td>
            <!--td data-label="GT"><?=$ln['gt']?></td-->
            <?php /*td data-label="idtipo_trabalho"><?=_v($tiposTrabalhos,$ln['idtipo_trabalho'])?></td */ ?>
            <td data-label="idtrilha"><?=_v($trilhas,$ln['idtrilha'])?></td>
            
            <td data-label="status">
            
              <?=statusColor($ln["status"],$status)?>

              <?php if ($ln['status'] == APROVADO_CORRECOES_PENDENTES) {
                  if ($ln['arquivoCorrigido'] != ""){
                    print "(Arquivo reenviado)";
                  } else {
                    print "(Arquivo pendente)";
                  }
              }?>


              <div class='statusInsideTable'>
              <?php
                if ($_SESSION["admin_user"]["nivel"] == NIVEL_AVALIADOR){
                    #visao do avaliador
                    foreach($ln["avaliadores"] as $av){

                        if ($av["nota"] == 0){
                            $st = statusColor(PENDENTE,$status);
                        } else {
                            $st = "";
                        }
                        

                        if ($av["idusuario"] == $_SESSION["admin_user"]["id"]){
                            print "<div>Meu voto:".$st." Nota:".number_format($av["nota"],0,",",".")."</div>";
                        } else {
                            print "<div>Outros:".$st."</div>";
                        }
                    }
                } else {

                    if ($ln["nota"] == 0){
                        $st = statusColor(PENDENTE,$status);
                    } else {
                        $st = "";
                    }

                    #visao da equipe
                    print "Média das notas:" . number_format(_v($ln,'nota'),2,",",".");

                    foreach($ln["avaliadores"] as $k=>$av){
                        print "<div>Avaliador ".($k+1).":".$st." Nota:".number_format($av["nota"],0,",",".")."</div>";
                    }
                }
              ?>
              </div>

            </td>
            
            </td>
            <?php /*<td data-label="Pagamento" class='<?=($ln['pago'] == 1) ? "positive" : "negative"?>'>
            <?php
            if ($ln['pago']){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td>*/ ?>
            <td data-label="Apresentado" class='<?=($ln['apresentado'] != "0") ? "positive" : "negative"?>'>
            <?php
            if ($ln['apresentado'] != "0"){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td>
            <td data-label="Certificado" class='<?=($ln['certificado'] != "") ? "positive" : "negative"?>'>
            <?php
            if ($ln['certificado'] != ""){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td>
            
        </tr>

    <?php endforeach; ?>
  </tbody>

  <?php
    $config['base_url'] = site_url().'/painel/trabalhos';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    
    print "<tfoot><tr><th colspan='10'>Total: {$lista['total_rows']}<div class='ui right floated pagination menu'>$links</div></th></tr></tfoot>";
    
    ?>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

