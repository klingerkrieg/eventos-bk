<?php include 'head.php' ?>

<h2 class="ui horizontal divider header">Inscrever-se</h2>
<div class="ui text container">
  <div class="ui warning message">
  Já se inscreveu? <a href="<?=site_url('home/login')?>">Faça login</a> ao invés disso.
  </div>
  <form novalidate class="ui form" method="post" action="<?=site_url('home/salvar_registro')?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
        
    <div class="field required">
        <label for="nome_completo">Nome completo</label>
        <input value="<?=_v($dados,'nome_completo')?>" type="text" name="nome_completo" id="nome_completo" class="textInput" maxlength="250" />
        <?=form_error('nome_completo')?>
    </div>

    <div class="field">
        <label for="sou_aluno">
        <input type="checkbox" id="sou_aluno" >
        Sou aluno/servidor do IFRN</label>
        <div id="sou_aluno_msg" class="ui message" style="display:none;">
          Se você é aluno ou servidor do IFRN não é necessário preencher este cadastro, faça o <a href="<?=site_url('home/login')?>">login</a> com a sua matrícula e senha do SUAP.
        </div>
    </div>

    <?php /*div class="field">
      <label for="nome_social" >Nome social</label>
      <input value="<?=_v($dados,'nome_social')?>" type="text" name="nome_social" id="nome_social" class="textinput textInput" maxlength="200" />
      <?=form_error('nome_social')?>
    </div*/ ?>

    <div class="field required">
        <label for="email" >E-mail</label>            
        <input value="<?=_v($dados,'email')?>" type="email" name="email" id="email" required maxlength="250" />
        <?=form_error('email')?>
    </div>

    <div id='emailConfirmDiv' class="field required">
        <label for="emailConfirm" >Confirmar e-mail</label>            
        <input value="<?=_v($dados,'emailConfirm')?>" type="email" name="emailConfirm" id="emailConfirm" required maxlength="250" />
        <?=form_error('emailConfirm')?>
    </div>

    <div class="field">
        <label for="telefone">Telefone</label>
        <input type="text" name="telefone" class="telefone" id="telefone" maxlength="15" value="<?=_v($dados,"telefone")?>">
        <?=form_error('telefone')?>
    </div>

    <div class="field required">
        <label for="cpf" >CPF</label>
        <input value="<?=_v($dados,'cpf')?>" type="text" class='cpf' name="cpf" id="cpf" class="textInput" maxlength="14" />
        <?=form_error('cpf')?>
    </div>

    <?php /* <div class="field">
        <label for="cep" >CEP</label>
        <input value="<?=_v($dados,'cep')?>" type="text" class="cep" name="cep" id="cep" class="textInput" maxlength="9" />        
        <?=form_error('cep')?>
    </div>

    <div class="fields">
      <div class="wide thirteen field">
          <label for="logradouro" >Logradouro</label>
          <input value="<?=_v($dados,'logradouro')?>" type="text" name="logradouro" id="logradouro" class="textInput" maxlength="250" />
          <?=form_error('logradouro')?>
      </div>

      <div class="wide three field">
          <label for="numero" >Número</label>
          <input value="<?=_v($dados,'numero')?>" type="text" name="numero" id="numero" class="textInput" maxlength="10" />
          <?=form_error('numero')?>
      </div>
    </div>

    

    <div class="fields">
      <div class="wide eight field required">
        <div class="field">
        <label for="bairro" >Bairro</label>
        <input value="<?=_v($dados,'bairro')?>" type="text" name="bairro" id="bairro" class="textInput" readonly />
        <?=form_error('bairro')?>
        </div>
      </div>

      <div class="wide eight field required">
        <div class="field">
          <label for="cidade" >Cidade</label>
          <input value="<?=_v($dados,'cidade')?>" type="text" name="cidade" id="cidade" class="textInput" readonly />
          <?=form_error('cidade')?>
        </div>
      </div>
      <div class="wide four field required">
        <div class="field">
            <label for="uf" >UF</label>
            <input value="<?=_v($dados,'uf')?>" type="text" name="uf" id="uf" class="textInput" maxlength="2" readonly/>
            <?=form_error('uf')?>
        </div>
      </div>
    </div> */ ?>


    <div class="field required">
        <label for="tipoInscricao" >Tipo de inscrição</label>
        <select name="tipoInscricao" id="tipoInscricao" class="ui search dropdown">
            <option value=""> Selecione o tipo de inscrição </option>
            <?php
                foreach($tiposInscricao as $k=>$v){
                    $selected = "";
                    if (_v($dados,"tipoInscricao") == $k){
                      $selected = "selected";
                    }
                    print "<option value='$k' $selected>$v</option>";
                }
            ?>
        </select>
        <?=form_error('tipoInscricao')?>
    </div>

      <div class="field">
        <label for="instituicao">Instituição</label>
        <select name="instituicao" id="instituicao" class="ui search dropdown">
          <option value=""> Selecione a instituição </option>
          <option value="outra" <?php if (_v($dados,"instituicao") == "outra") {print "selected";} ?>>Outra instituição</option>
            <?php foreach($instituicoes as $item){
                $selected = "";
                if (_v($dados,"instituicao") == $item['id']){
                  $selected = "selected";
                }
                print "<option value='{$item['id']}' $selected >{$item['instituicao']}</option>";
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
        <select name="curso" id="curso"  class="ui search dropdown">
            <option value=""> Selecione o curso </option>
            <?php foreach($cursos as $item){
                if ($item['id'] == _v($dados,"curso")){
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
                if ($k == _v($dados,"curso")){
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                print "<option value='{$k}' $selected>{$item}</option>";
            } ?>
        </select>
        <?=form_error('idnivelcurso')?>
      </div>


      <div class="field required">
        <label for="password1" >
            Senha
        </label>
        <input type="password" name="password1" minlength="6" required class="textInput" id="password1" />
        <div id="hint_password1" class="ui pointing label">Use pelo menos 6 caracteres.</div>        
        <?=form_error('password1')?>
      </div>

      <div id="div_id_password2" class="field required">
        <label for="password2" >
            Confirmar senha
        </label>
        <input type="password" name="password2" minlength="6" required class="textInput" id="password2" />
        <div id="hint_password2" class="ui pointing label">Use pelo menos 6 caracteres.</div>
        <?=form_error('password2')?>
      </div>

      <br/>

      <?php include 'captcha.php'; ?>

      <button type="submit" class="ui green button">
        <i class="user circle icon"></i>
        Inscrever-se
      </button>
  </form>
</div>


<?php include 'bottom.php' ?>