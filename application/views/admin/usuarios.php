<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/usuarios/') ?>">Usuários</a>
    /
    <a href="<?=site_url("painel/usuarios/index/form")?>">Novo usuário</a>
</h2>

<?php #echo validation_errors(); ?>

<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/usuarios/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class='field'>
        <?php
        if (_v($dados,"foto") == ""){
            $foto = base_url("static/img/no-photo.jpg");
        } else {
            $foto = $dados["foto"];
        }
        ?>
        <div class="field">
            <div class="ui gray ribbon label">
                Para alterar a foto o usuário deve realizar o login no sistema em ir em perfil.
            </div>
            <img class="ui bordered small image" src="<?=$foto?>">
        </div>
    </div>

    <div class="field required">
        <label for="nome_completo">Nome completo</label>
        <input type="text" id="nome_completo" name="nome_completo" maxlength="250" value="<?=_v($dados,"nome_completo")?>" required>
        <?=form_error('nome_completo')?>
    </div>
  

    <?php /*div class="field">
        <label for="nome_social">Nome social</label>
        <input type="text" id="nome_social" name="nome_social" maxlength="200" value="<?=_v($dados,"nome_social")?>">
        <?=form_error('nome_social')?>
    </div */ ?>

    <div class="field required">
        <label for="email">E-mail</label>
        <input type="text" id="email" name="email" maxlength="250" value="<?=_v($dados,"email")?>"  required>
        <?=form_error('email')?>
    </div>

    <div class="field">
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" class="cpf" name="cpf" maxlength="11" value="<?=_v($dados,"cpf")?>">
        <?=form_error('cpf')?>
    </div>


    <div class="field">
        <?php if (_v($dados,"pago")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="pago" value="0"/>
        <label for="pago"> <input type="checkbox" id="pago" name="pago" value="1" <?=$checked?> > Inscrição paga?</label>
        <?=form_error('pago')?>
    </div>

        
    <div class="field">
        <label for="tipoInscricao">Tipo de inscrição</label>
        <select name="tipoInscricao" id="tipoInscricao" class="ui search dropdown">
            <?php
                foreach($tiposInscricao as $k=>$v){
                    if ($k == _v($dados,"tipoInscricao")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$k'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('tipoInscricao')?>
    </div>

    
    <div class="field">
        <label for="instituicao">Instituição</label>
        <select name="instituicao" id="instituicao" class="ui search dropdown">
            <option value="outra" <?php if (_v($dados,"outra_instituicao") != "") {print "selected";} ?>>Outra instituição</option>
            <?php foreach($instituicoes as $item){
                if ($item['id'] == _v($dados,"idinstituicao")){
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value='{$item['id']}' $selected>{$item['instituicao']}</option>";
            } ?>
        </select>
        <?=form_error('instituicao')?>
    </div>

    <div class="field">
        <label for="outra_instituicao">Outra instituição</label>
        <input type="text" id="outra_instituicao" name="outra_instituicao" maxlength="250" value="<?=_v($dados,"outra_instituicao")?>">
        <?=form_error('outra_instituicao')?>
    </div>

    <div class="field">
        <label for="curso">Curso</label>
        <select name="curso" id="curso" class="ui search dropdown">
            <option value=""> Selecione o curso </option>
            <?php foreach($cursos as $item){
                if ($item['id'] == _v($dados,"idcurso")){
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value='{$item['id']}' $selected>{$item['curso']}</option>";
            } ?>
        </select>
        <?=form_error('curso')?>
    </div>

    <div class="field">
        <label for="idnivelcurso">Nível do curso</label>
        <select name="idnivelcurso" id="idnivelcurso"  class="ui search dropdown">
            <option value=""> Selecione o nível do curso </option>
            <?php foreach($niveis_cursos as $k=>$item){
                if ($k == _v($dados,"idnivelcurso")){
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value='{$k}' $selected>{$item}</option>";
            } ?>
        </select>
        <?=form_error('idnivelcurso')?>
      </div>

    

        
    <div class="field">
        <label for="nivel">Nível</label>
        <select name="nivel" id="nivel" required class="ui search dropdown">
            <?php
                foreach($niveis as $k=>$v){

                    if ($k > $_SESSION["admin_user"]["nivel"]){
                        continue;
                    }

                    if ($k == _v($dados,"nivel")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value=$k>$v</option>";
                }
            ?>
        </select>
        <?=form_error('nivel')?>
    </div>

    <div class="ui message">
        <div class="header">Níveis de acesso</div>
        <ul class="list">
            <li>O nível <b>Participante</b> não tem acesso à área de administração, apenas à área de submissão de trabalhos.</li>
            <li>O nível <b>Avaliador</b> só poderá acessar os trabalhos e nada mais no sistema, em relação aos trabalhos ele não conseguirá visualizar o autor, coautor nem orientador, somente o título, GT, se a inscrição foi paga ou não e o trabalho em si. Ele poderá dar o seu voto no status do trabalho, inserir observações e dar o seu voto na nota do trabalho.</li>
            <li>O nível <b>Equipe</b> pode fazer tudo exceto deletar trabalhos e gerar certificados.</li>
            <li>O nível <b>Admin</b> pode fazer tudo.</li>
        </ul>
    </div>

    <?php if (_v($dados,"nivel") == NIVEL_AVALIADOR):  ?>
        <div class='field'>
            <label>O avaliador escolheu as seguintes áreas</label>
            <?php if (_v($dados,"nivel") == NIVEL_AVALIADOR && count($dados["areas"]) == 0):  ?>
                <div class="ui label">
                    Nenhuma área escolhida
                </div>
            <?php endif; ?>
            <div class="ui blue labels">
            <?php foreach($dados["areas"] as $area): ?>
                <a class="ui label">
                <?=$area["area"]?>
                </a>
            <?php endforeach; ?>
            </div>
        </div>
    <?php endif;  ?>

    <?php /* <div class="field">
        <label for="cep">CEP</label>
        <input type="text" id="cep" class="cep" name="cep" maxlength="14" value="<?=_v($dados,"cep")?>">
        <?=form_error('cep')?>
    </div>


    <div class="field">
        <div class="two fields">
        <div class="field">
            <label for="logradouro">Logradouro</label>
            <input type="text" id="logradouro" name="logradouro" maxlength="14" value="<?=_v($dados,"logradouro")?>">
            <?=form_error('logradouro')?>
        </div>
        <div class="field">
            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" maxlength="10" value="<?=_v($dados,"numero")?>">
            <?=form_error('numero')?>
        </div>
        </div>
    </div>
        

        
    <div class="field">
        <div class="three fields">
        <div class="field">
            <label for="bairro">
            Bairro
            <input type="text" id="bairro" name="bairro" maxlength="250" readonly value="<?=_v($dados,"bairro")?>">
            <?=form_error('bairro')?>
        </label>
        </div>
        <div class="field">
        <label for="cidade">
            Cidade
            <input type="text" id="cidade" name="cidade" maxlength="250" readonly value="<?=_v($dados,"cidade")?>">
            <?=form_error('cidade')?>
        </label>
        </div>
        <div class="field">
        <label for="cidade">
            <label for="uf">
                UF
                <input type="text" id="uf" name="uf" maxlength="2" readonly value="<?=_v($dados,"uf")?>">
                <?=form_error('uf')?>
            </label>
        </label>
        </div> 
        </div> 

    </div>*/ ?>


    <div class="field">
        <label for="nivel">Certificados</label>
        <?php
        $aprovado_certificado_participante = "";
        if (_v($dados,"aprovado_certificado_participante")){
            $aprovado_certificado_participante = "checked";
        }
        $aprovado_certificado_avaliador = "";
        if (_v($dados,"aprovado_certificado_avaliador")){
            $aprovado_certificado_avaliador = "checked";
        }
        $aprovado_certificado_palestrante = "";
        if (_v($dados,"aprovado_certificado_palestrante")){
            $aprovado_certificado_palestrante = "checked";
        }
        $aprovado_certificado_mesa_redonda = "";
        if (_v($dados,"aprovado_certificado_mesa_redonda")){
            $aprovado_certificado_mesa_redonda = "checked";
        }
        ?>

        <input type='hidden' name='aprovado_certificado_participante' value='0'>
        <input type='hidden' name='aprovado_certificado_avaliador' value='0'>
        <input type='hidden' name='aprovado_certificado_palestrante' value='0'>
        <input type='hidden' name='aprovado_certificado_mesa_redonda' value='0'>

                
        <label>
            <input type='checkbox' name='aprovado_certificado_participante' value='1' <?=$aprovado_certificado_participante?>>
            Aprovado para certificado de participante

            <?php
            if (_v($dados,"certificado_participante") != ""){
                print "<a href='".base_url()."certificados/participacao/{$dados['certificado_participante']}.pdf' target='_BLANK'>{$dados['certificado_participante']}.pdf</a>"
                            . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_participante_data"]))."</span>";
            }
            ?> 
        </label>
        
        <label class='avaliador_cert' >
            <input type='checkbox' id='aprovado_certificado_avaliador' name='aprovado_certificado_avaliador' value='1' <?=$aprovado_certificado_avaliador?> >
            Aprovado para certificado de avaliador

            <?php
            if (_v($dados,"certificado_avaliador") != ""){
                print "<a href='".base_url()."certificados/avaliador/{$dados['certificado_avaliador']}.pdf' target='_BLANK'>{$dados['certificado_avaliador']}.pdf</a>"
                            . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_avaliador_data"]))."</span>";
            }
            ?> 
        </label>

        <label >
            <input type='checkbox' id='aprovado_certificado_palestrante' name='aprovado_certificado_palestrante' value='1' <?=$aprovado_certificado_palestrante?> >
            Aprovado para certificado de palestrante

            <?php
            if (_v($dados,"certificado_palestrante") != ""){
                print "<a href='".base_url()."certificados/palestrante/{$dados['certificado_palestrante']}.pdf' target='_BLANK'>{$dados['certificado_palestrante']}.pdf</a>"
                            . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_palestrante_data"]))."</span>";
            }
            ?> 
        </label>

        <div class="field" id='titulo_palestra_label'>
            <label for="titulo_palestra">Título da palestra</label>
            <input type="text" id="titulo_palestra" name="titulo_palestra" maxlength="250" value="<?=_v($dados,"titulo_palestra")?>">
            <?=form_error('titulo_palestra')?>
        </div>

        <label >
            <input type='checkbox' id='aprovado_certificado_mesa_redonda' name='aprovado_certificado_mesa_redonda' value='1' <?=$aprovado_certificado_mesa_redonda?> >
            Aprovado para certificado de mesa redonda

            <?php
            if (_v($dados,"certificado_mesa_redonda") != ""){
                print "<a href='".base_url()."certificados/mesa_redonda/{$dados['certificado_mesa_redonda']}.pdf' target='_BLANK'>{$dados['certificado_mesa_redonda']}.pdf</a>"
                            . " <span class='date'>". date("d/m/Y H:i:s", strtotime($dados["certificado_mesa_redonda_data"]))."</span>";
            }
            ?> 
        </label>

        <div class="field" id='titulo_mesa_redonda_label'>
            <label for="titulo_mesa_redonda">Título da mesa redonda</label>
            <input type="text" id="titulo_mesa_redonda" name="titulo_mesa_redonda" maxlength="250" value="<?=_v($dados,"titulo_mesa_redonda")?>">
            <?=form_error('titulo_mesa_redonda')?>
        </div>
        
    </div>

        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>
    
    <?php if (isset($dados["id"])): ?>
    <button class="ui negative  button" type="button" onclick="showConf('<?=site_url("painel/usuarios/deletar/{$dados['id']}")?>')">
        <i class="ban icon"></i>
        Banir
    </button>
    <?php endif; ?>

    <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN && _v($dados,"pago") == 1 && (_v($dados,"aprovado_certificado_avaliador") || _v($dados,"aprovado_certificado_participante"))): ?>
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
                    <p>Você tem certeza que deseja gerar o certificado deste participante?
                    Isso acarretará em:
                    <ul>
                        <li>Geração do certificado se o participante tiver pago a inscrição e se estiver marcado como 'Aprovado para certificado de participante'.</li>
                        <li>Caso o usuário seja do tipo Avaliador, o certificado de avaliador de trabalho só será gerado se estiver marcado como 'Aprovado para certificado de avaliador'</li>
                        <li>Geração do certificado se o participante tiver pago a inscrição e se estiver marcado como 'Aprovado para certificado de palestrante'.</li>
                        <li>Geração do certificado se o participante tiver pago a inscrição e se estiver marcado como 'Aprovado para certificado de mesa redonda'.</li>
                        <li>Envio de e-mail para o participante avisando da disponibilidade do certificado.</li>
                    </ul>
                    </p>
                </div>
            <div class="actions">
                <div class="ui black deny button">
                Cancelar
                </div>
                <a class="ui positive right labeled icon button" href='<?=site_url("painel/usuarios/gerar_certificado/{$dados['id']}")?>'>
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

</form>

<?php endif; ?>

</div>

<?php if (isset($lista)) : ?>

<div class="fourteen wide column">

<form class="ui form" method="get" action="<?=site_url('painel/usuarios/index/') ?>"> 

    <div class="three fields">
        <div class="four wide field">
            <input type="text" id="filtro" name="filtro" maxlength="250" value="<?=_v($_GET,"filtro")?>" placeholder="Filtro">
            </label>
        </div>

        <div class="three wide field">
            <select name="nivel" id="nivel" class="ui search dropdown">
                <option value=""> - </option>
                <?php
                    foreach($niveis as $k=>$v){
                        if ($k == _v($_GET,"nivel")){
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        print "<option $selected value=$k>$v</option>";
                    }
                ?>
            </select>
        </div>

        <div class="field">
            <button class="ui positive  button" type="submit">
                <i class="search icon"></i>
                Filtrar
            </button>


            <?php if ($_SESSION['admin_user']["nivel"] == NIVEL_ADMIN): ?>
            <button type="button" class="ui primary button" onclick="$('#confirm_certificados').modal('show')" data-position="bottom left">
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
                            <li>Geração do certificado para todos os participantes que tiverem pago a inscrição e que estão como 'Aprovado para certificado de participante'.</li>
                            <li>Caso o usuário seja do tipo Avaliador, o certificado de avaliador de trabalho só será gerado se estiver marcado como 'Aprovado para certificado de avaliador'</li>
                            <li>Geração do certificado se o participante tiver pago a inscrição e se estiver marcado como 'Aprovado para certificado de palestrante'.</li>
                            <li>Geração do certificado se o participante tiver pago a inscrição e se estiver marcado como 'Aprovado para certificado de mesa redonda'.</li>
                            <li>Envio de e-mail para todos os participantes avisando da disponibilidade do certificado.</li>
                        </ul>
                        </p>
                    </div>
                <div class="actions">
                    <div class="ui black deny button">
                    Cancelar
                    </div>
                    <a class="ui positive right labeled icon button" href='<?=site_url("painel/usuarios/gerar_certificados")?>'>
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
        </div>

        
    </div>
</form>

<table class="ui celled table">
  <thead>
    <tr><th>Editar</th>
    <th><a href='<?=order_by_link("painel/usuarios/index", "nome_completo")?>'>Nome<?=order_by_img("nome_completo")?></a></th>
    <th><a href='<?=order_by_link("painel/usuarios/index", "email")?>'>E-mail<?=order_by_img("email")?></a></th>
    <th><a href='<?=order_by_link("painel/usuarios/index", "nivel")?>'>Nível<?=order_by_img("nivel")?></a></th>
    <th><a href='<?=order_by_link("painel/usuarios/index", "cpf")?>'>CPF<?=order_by_img("cpf")?></a></th>
    <?php /* <th><a href='<?=order_by_link("painel/usuarios/index", "pago")?>'>Pagamento<?=order_by_img("pago")?></a></th> */ ?>
    <th><a href='#'>Certificado<?=order_by_img("certificado")?></a></th>
  </tr></thead>
  <tbody>
    <?php foreach($lista['dados'] as $ln): ?>
        <tr>
            <td data-label="Editar" class="one wide"><a href='<?=site_url().'/painel/usuarios/index/'.$ln['id']?>'>Editar</a></td>
            <td data-label="Nome"><?=$ln['nome_completo']?></td>
            <td data-label="E-mail"><?=$ln['email']?></td>
            <td data-label="Nível"><?=$niveis[$ln['nivel']]?></td>
            <td data-label="CPF"><?=$ln['cpf']?></td>
            <?php /*<td data-label="Pagamento" class='<?=($ln['pago'] == 1) ? "positive" : "negative"?>'>
            <?php
            if ($ln['pago']){
                print '<i class="icon checkmark"></i>';
            } else {
                print '<i class="icon close"></i>';
            }
            ?>
            </td> */ ?>

            <?php 
            $cert_ok = true;
            if ($ln["aprovado_certificado_participante"] && $ln["certificado_participante"] == ""){
                $cert_ok = false;
            }
            if ($ln["aprovado_certificado_avaliador"] && $ln["certificado_avaliador"] == ""){
                $cert_ok = false;
            }
            if ($ln["aprovado_certificado_palestrante"] && $ln["certificado_palestrante"] == ""){
                $cert_ok = false;
            }
            if ($ln["aprovado_certificado_mesa_redonda"] && $ln["certificado_mesa_redonda"] == ""){
                $cert_ok = false;
            }
            $icon = "";
            if ($ln["certificado_avaliador"] != "" || $ln["certificado_participante"] != ""){
                $icon = "<i class='icon checkmark'></i>";
            }
            ?>

            <td data-label="Certificado" class='<?=($cert_ok) ? "positive" : "negative"?>'>
            <?php
            if ($cert_ok){
                print $icon;
            } else {
                print '<i class="icon close"></i> Certificado aprovado, mas não gerado.';
            }
            ?>
            </td>
        </tr>

    <?php endforeach; ?>
  </tbody>

  <?php
    $config['base_url'] = site_url().'/painel/usuarios';
    $this->pagination->initialize($lista);
    $links = $this->pagination->create_links();
    
    print "<tfoot><tr><th colspan='8'>Total: ".$lista['total_rows']."<div class='ui right floated pagination menu'>$links</div></th></tr></tfoot>";
    ?>
  
</table>


<?php endif; ?>

</div>

<?php require_once 'bottom.php'; ?>

