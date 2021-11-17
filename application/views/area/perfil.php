<?php include 'head.php' ?>
<script>
function togglePassword(){
    if ($("#alterarSenha").prop("checked") == false){
        $("#password1").attr("disabled",true).val("");
        $("#password2").attr("disabled",true).val("");
    } else {
        $("#password1").removeAttr("disabled");
        $("#password2").removeAttr("disabled");
    }
}
</script>

<h2 class="ui horizontal divider header">Meus dados</h2>
<div class="ui text container">


  
  <form novalidate class="ui form" method="post" action="<?=site_url('perfil/salvar')?>" enctype="multipart/form-data">

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    
    <div>
      <div class="field required">
        <label for="nome_completo" >Nome completo</label>
        <input type="text" name="nome_completo" id="nome_completo" maxlength="250" value="<?=_v($dados,'nome_completo')?>" />
        <?=form_error('nome_completo')?>
    </div>

    <?php /* div class="field">
      <label for="nome_social" >Nome social</label>
      <input type="text" name="nome_social" id="nome_social" maxlength="100" value="<?=_v($dados, 'nome_social' )?>" />
    </div*/ ?>

    <div class="field required">
        <label for="email" >E-mail</label>            
        <input type="email" name="email" id="email" maxlength="250" value="<?=_v($dados,'email')?>" />
        <?=form_error('email')?>
    </div>

    <div class="field required">
        <label for="cpf" >CPF</label>
        <input type="text" name="cpf" id="cpf" class="cpf" maxlength="14" value="<?=_v($dados,'cpf')?>" />
        <?=form_error('cpf')?>
    </div>


    <div class="field">
        <label >
            <input id="alterarSenha" type="checkbox" onclick="togglePassword()"/>
            Desejo alterar a senha</label>
    </div>

    <div class="field">
        <label for="password1" >Senha</label>
        <input disabled type="password" name="password1" id="password1" maxlength="40" value="<?=_v($dados,'password1')?>" />
        <?=form_error('password1')?>
    </div>

    <div class="field">
        <label for="cpf" >Confirmação da senha</label>
        <input disabled type="password" name="password2" id="password2" maxlength="40" value="<?=_v($dados,'password2')?>" />
        <?=form_error('password2')?>
    </div>


    
    <?php if ($_SESSION['user']['pago'] != true): ?>

        <div class="field required">
            <label for="tipoInscricao">Tipo de inscrição</label>
            <select name="tipoInscricao" id="tipoInscricao">
                <option value=""> - </option>
                <?php
                    foreach($tiposInscricao as $k=>$v){
                        if ($k == _v($dados,"tipoInscricao")){
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        print "<option $selected value=$k>$v</option>";
                    }
                ?>
            </select>
            <?=form_error('tipoInscricao')?>
        </div>

        <?php else: ?>

        <div id="div_id_tipoInscricao" class="field required">
            <label for="id_tipoInscricao" >Tipo de inscrição</label>
            <div id="tipoInscricao"><?=_v($tiposInscricao,_v($dados,"tipoInscricao"))?></div>
        </div>
        <?php endif; ?>


        <div class="field">
            <label for="telefone">Telefone</label>
            <input type="text" name="telefone" class="telefone" id="telefone" maxlength="15" value="<?=_v($dados,"telefone")?>">
            <?=form_error('telefone')?>
        </div>

        <div class="fields">
            <div class='field'>
            <div class='field'><b>Apresentação pessoal</b></div>
                <label><input type="radio" name="lattesC" id="radioLattes" hide="curriculoDiv" show="lattesDiv">Lattes</label>
                <label><input type="radio" name="lattesC" id="radioCurriculo" hide="lattesDiv" show="curriculoDiv">Currículo</label>
            </div>
        </div>
        
        <div class="field" id='lattesDiv'>
            <label for="lattes" >Lattes</label>
            <input type="text" name="lattes" id="lattes" maxlength="250" value="<?=_v($dados,'lattes')?>" />
            <?=form_error('lattes')?>
        </div>

        <div class="field" id='curriculoDiv'>
            <label for="curriculo" >Currículo</label>
            <textarea name="curriculo" id="curriculo" maxlength="1000"><?=_v($dados,'curriculo')?></textarea>
            <?=form_error('curriculo')?>
        </div>




        <div class="field">
        <label for="instituicao">Instituição</label>
        <select name="instituicao" id="instituicao">
            <option value=""> - </option>
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

        <div id="outra_instituicao" class="field">
          <label>Informe o nome da sua instituição</label>
          <input type="text" name="outra_instituicao" value="<?=_v($dados,'outra_instituicao')?>" maxlength="250">
          <?=form_error('outra_instituicao')?>
        </div>

        <div class="field">
        <label for="curso">Curso</label>
        <select name="curso" id="curso">
            <option value=""> - </option>
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
        <select name="idnivelcurso" id="idnivelcurso">
            <option value=""> - </option>
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


        <div class="fields">
            <?php
            if (_v($dados,"foto") == ""){
                $foto = base_url("static/img/no-photo.jpg");
            } else {
                $foto = $dados["foto"];
            }
            ?>
            <div class="field">
                <img id="fotoImg" class="ui small image" src="<?=$foto?>">
            </div>
            <div class="field">
                <label for="foto">Foto</label>
                <input type="file" name="foto" class="clearable file input" id="foto" />
                <?=form_error('foto')?>
            </div>
        </div>

    <?php /*<div class="ui horizontal divider">Endereço</div>

    <div class="field">
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
        </div>*/ ?>



    
    <button type="submit" class="ui green button">
        <i class="save icon"></i>
        Salvar
    </button>
  </form>
</div>

</div>


<?php include 'bottom.php' ?>