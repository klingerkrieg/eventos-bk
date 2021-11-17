<?php require_once 'head.php'; ?>

<div class="eight wide column">

<h2 class="ui header">
    <a href="<?=site_url('painel/evento/') ?>">Evento</a>
</h2>


<?php if (isset($dados)) : ?>


<form class="ui form" method="post" action="<?=site_url('painel/evento/salvar/') ?>" enctype="multipart/form-data">

    <input type="hidden" name="id" value="<?=_v($dados,"id")?>" >

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

    <div class="field required">
        <label for="evento">Nome do evento</label>
        <input type="text" id="evento" name="evento" maxlength="250" value="<?=_v($dados,"evento")?>" >
        <?=form_error('evento')?>
    </div>

    <div class="field required">
        <label for="email">E-mail</label>
        <input type="text" id="email" name="email" maxlength="250" value="<?=_v($dados,"email")?>" >
        <?=form_error('email')?>
    </div>


    <div class='fields'>

    <div class="field required">
        <label for="data_inicio">Data de início</label>
        <input type="date" id="data_inicio" name="data_inicio" maxlength="10" value="<?=date("Y-m-d", strtotime($dados["data_inicio"]))?>" >
        <?=form_error('data_inicio')?>
    </div>

    <div class="field required">
        <label for="data_fim">Data de fim</label>
        <input type="date" id="data_fim" name="data_fim" maxlength="10" value="<?=date("Y-m-d", strtotime($dados["data_fim"]))?>" >
        <?=form_error('data_fim')?>
    </div>

    </div>

    <div class="field required">
        <label for="data_certificado">Data que constará nos certificados</label>
        <input type="text" id="data_certificado" name="data_certificado" maxlength="250" value="<?=_v($dados,"data_certificado")?>" >
        <?=form_error('data_certificado')?>
    </div>

    <div class="field">
        <?php if (_v($dados,"evento_encerrado")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="evento_encerrado" value="0"/>
        <label for="evento_encerrado" 
                data-inverted=""
                data-tooltip="Marque para encerrar cadastros, matrículas e submissões de trabalhos/minicursos."> 
        <input  type="checkbox" 
                id="evento_encerrado" 
                name="evento_encerrado" value="1" <?=$checked?> > 
                Evento encerrado?</label>
        <?=form_error('evento_encerrado')?>
    </div>

    <div class="field">
        <label for="aberto_ate">Evento ativo até</label>
        <input type="date" name="aberto_ate" value="<?=date("Y-m-d", strtotime($dados["aberto_ate"]))?>" id="aberto_ate">
        <?=form_error('aberto_ate')?>
    </div>

    <div class="ui horizontal divider">Minicursos</div>

    <div class="field">
        <?php if (_v($dados,"aceitando_submissoes_minicursos")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="aceitando_submissoes_minicursos" value="0"/>
        <label for="aceitando_submissoes_minicursos"> 
        <input type="checkbox" id="aceitando_submissoes_minicursos" name="aceitando_submissoes_minicursos" value="1" <?=$checked?> > Aceitando submissões de minicursos?</label>
        <?=form_error('aceitando_submissoes_minicursos')?>
    </div>
    <div class="field">
        <label for="minicursos_ate">Submissões de minicursos até</label>
        <input type="date" name="minicursos_ate" value="<?=date("Y-m-d", strtotime($dados["minicursos_ate"]))?>" id="minicursos_ate">
        <?=form_error('minicursos_ate')?>
    </div>

    <div class="field">
        <?php if (_v($dados,"aceitando_matriculas_minicursos")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="aceitando_matriculas_minicursos" value="0"/>
        <label for="aceitando_matriculas_minicursos"> 
        <input type="checkbox" id="aceitando_matriculas_minicursos" name="aceitando_matriculas_minicursos" value="1" <?=$checked?> > Aceitando matrículas em minicursos?</label>
        <?=form_error('aceitando_matriculas_minicursos')?>
    </div>
    <div class="field">
        <label for="matriculas_ate">Matrículas em minicursos até</label>
        <input type="date" name="matriculas_ate" value="<?=date("Y-m-d", strtotime($dados["matriculas_ate"]))?>" id="matriculas_ate">
        <?=form_error('matriculas_ate')?>
    </div>

    <div class="field">
        <label for="limite_submissoes_minicursos">Limite de submissões de minicursos</label>
        <select name="limite_submissoes_minicursos" id="limite_submissoes_minicursos">
            <option value=""> - </option>
            <?php
                foreach([1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_submissoes_minicursos")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_submissoes_minicursos')?>
    </div>

    <div class="field">
        <label for="limite_coautores_minicursos">Limite de coautores de minicursos</label>
        <select name="limite_coautores_minicursos" id="limite_coautores_minicursos">
            <option value=""> - </option>
            <?php
                foreach([0,1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_coautores_minicursos")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_coautores_minicursos')?>
    </div>


    <div class="ui horizontal divider">Trabalhos</div>
    
    <div class="field">
        <?php if (_v($dados,"aceitando_submissoes")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="aceitando_submissoes" value="0"/>
        <label for="aceitando_submissoes"> <input type="checkbox" id="aceitando_submissoes" name="aceitando_submissoes" value="1" <?=$checked?> > Aceitando submissões de trabalhos?</label>
        <?=form_error('aceitando_submissoes')?>
    </div>
    <div class="field">
        <label for="submissoes_ate">Submissões de trabalhos até</label>
        <input type="date" name="submissoes_ate" value="<?=date("Y-m-d", strtotime($dados["submissoes_ate"]))?>" id="submissoes_ate">
        <?=form_error('submissoes_ate')?>
    </div>

    <div class="field">
        <?php if (_v($dados,"aceitando_correcoes")) {
            $checked = "checked";
        } else {
            $checked = "";
        } ?>
        
        <input type="hidden" name="aceitando_correcoes" value="0"/>
        <label for="aceitando_correcoes"> <input type="checkbox" id="aceitando_correcoes" name="aceitando_correcoes" value="1" <?=$checked?> > Aceitando submissões de correções de trabalhos?</label>
        <?=form_error('aceitando_correcoes')?>
    </div>
    <div class="field">
        <label for="correcoes_ate">Correções de trabalhos até</label>
        <input type="date" name="correcoes_ate" value="<?=date("Y-m-d", strtotime($dados["correcoes_ate"]))?>" id="correcoes_ate">
        <?=form_error('correcoes_ate')?>
    </div>

    <div class="field">
        <label for="limite_submissoes">Limite de submissões de trabalhos</label>
        <select name="limite_submissoes" id="limite_submissoes">
            <option value=""> - </option>
            <?php
                foreach([1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_submissoes")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_submissoes')?>
    </div>


    <div class="field">
        <label for="limite_coautores">Limite de coautores de trabalhos</label>
        <select name="limite_coautores" id="limite_coautores">
            <option value=""> - </option>
            <?php
                foreach([0,1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_coautores")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_coautores')?>
    </div>

    <div class="field">
        <label for="limite_orientadores">Limite de orientadores de trabalhos</label>
        <select name="limite_orientadores" id="limite_orientadores">
            <option value=""> - </option>
            <?php
                foreach([0,1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_orientadores")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_orientadores')?>
    </div>

    <div class="field">
        <label for="limite_avaliadores_trabalhos">Limite de avaliadores de trabalhos</label>
        <select name="limite_avaliadores_trabalhos" id="limite_avaliadores_trabalhos">
            <option value=""> - </option>
            <?php
                foreach([0,1,2,3,4,5] as $k=>$v){
                    if ($v == _v($dados,"limite_avaliadores_trabalhos")){
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    print "<option $selected value='$v'>$v</option>";
                }
            ?>
        </select>
        <?=form_error('limite_avaliadores_trabalhos')?>
    </div>


    

        
    <button class="ui positive  button" type="submit">
        <i class="save icon"></i>
        Salvar
    </button>
    

</form>

<?php endif; ?>

</div>


</div>

<?php require_once 'bottom.php'; ?>

