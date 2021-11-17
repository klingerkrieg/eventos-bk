<?php require_once 'head.php'; ?>


<?php
$html = "";
if ($_SESSION["admin_user"]["certificado_avaliador"] != "" ){
  $url = base_url("certificados/avaliador/{$_SESSION["admin_user"]["certificado_avaliador"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado de Avaliador</a></li>";
}

if ($_SESSION["admin_user"]["certificado_participante"] != "" ){
  $url = base_url("certificados/participacao/{$_SESSION["admin_user"]["certificado_participante"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado de Participante</a></li>";
}

if ($_SESSION["admin_user"]["certificado_palestrante"] != "" ){
  $url = base_url("certificados/palestrante/{$_SESSION["admin_user"]["certificado_palestrante"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado de Palestrante</a></li>";
}

if ($_SESSION["admin_user"]["certificado_mesa_redonda"] != "" ){
  $url = base_url("certificados/mesa_redonda/{$_SESSION["admin_user"]["certificado_mesa_redonda"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado de Mesa Redonda</a></li>";
}

foreach($trabalhosCert as $cert){
  $url = base_url("certificados/trabalhos/{$cert["certificado"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado do trabalho: {$cert["titulo"]}</a></li>";
}

foreach($minicursosCert as $cert){
  $url = base_url("certificados/minicursos/{$cert["certificado"]}.pdf");
  $html .= "<li><a target='_BLANK' href='$url'>Certificado do minicurso: {$cert["titulo"]}</a></li>";
}


if ($html != ""){
  $html = "<div class='ui horizontal divider'>Certificados disponíveis</div>" 
        . "<div class='row'><ul style='text-align:left;'>" . $html . "</ul></div>";
  print $html;
}

?>


<div class="ui horizontal divider">Configurações de avaliador</div>
<div class='row'>
<div class="eight wide column">
<form class="ui form" action="<?=site_url("painel/avaliadores/salvar_areas")?>" method="post">

  <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />

  <div class='field'>
      <label>Escolha as áreas de sua preferência</label>

      <div id='msg_areas_interesse' class="ui pointing below red basic label" style='display:none;'>
        Por favor avaliador, preencha as suas áreas de interesse.
      </div>
      <div class="ui fluid action input" >
      <select id="areas_avaliador" name="areas[]" class="ui fluid search dropdown category" multiple="">
      <?php
        $garea = null;
        foreach($areas as $item){
            $selected = "";
            if ( in_array($item['id'], $minhasAreas)){
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
      

      <button class='ui button green'>
        <i class="save icon"></i>
        Salvar
      </button>

      </div>

      <div class="ui pointing label">
        Essas áreas irão nos ajudar a direcionar os trabalhos que sejam do seu interesse.
      </div>
      <?=form_error('areas[]')?>
    
    
  </div>
</form>
</div>
</div>



<div class="ui horizontal divider">Estatísticas</div>
<div class="row">
  <div class="ui stackable four column grid statistics center aligned">

    <a class="column" href='<?=site_url('painel/usuarios')?>' style='text-decoration:none;'>
      <div class="statistic">
        <div class="value">
          <i class="user circle icon"></i><?=$estatisticas["inscritos"]?>
        </div>
        <div class="label">Inscritos</div>
      </div>
    </a>

    <a class="column" href='<?=site_url('painel/minicursos')?>' style='text-decoration:none;'>
      <div class="statistic">
          <div class="value">
            <i class="envelope outline icon"></i><?=$estatisticas["minicursos_submetidos"]?>
          </div>
          <div class="label">Minicursos submetidos</div>
      </div>
    </a>

    <a class="column" href='<?=site_url('painel/minicursos/index/?filtro=&status=10')?>' style='text-decoration:none;'>
      <div class="statistic">
          <div class="value">
            <i class="certificate icon"></i><?=$estatisticas["minicursos_aprovados"]?>
          </div>
          <div class="label">Minicursos aprovados</div>
      </div>
    </a>


    <a class="column" href='<?=site_url('painel/trabalhos')?>' style='text-decoration:none;'>
      <div class="statistic">
        <div class="value">
          <i class="envelope outline icon"></i><?=$estatisticas["submetidos"]?>
        </div>
        <div class="label">Trabalhos submetidos</div>
      </div>
    </a>

    <a class="column" href='<?=site_url('painel/trabalhos')?>' style='text-decoration:none;'>
      <div class="statistic">
        <div class="value">
            <i class="envelope open outline icon"></i><?=$estatisticas["corrigidos"]?>
        </div>
        <div class="label">Correções realizadas</div>
      </div>
    </a>
    
    <a class="column" href='<?=site_url('painel/trabalhos')?>' style='text-decoration:none;'>
      <div class="statistic">
        <div class="value">
          <i class="certificate icon"></i><?=$estatisticas["aprovados"]?>
        </div>
        <div class="label">Trabalhos aprovados</div>
      </div>
    </a>


    <a class="column" href='<?=site_url('painel/minicursos')?>' style='text-decoration:none;'>
      <div class="statistic">
        <div class="value">
          <i class="address card outline icon"></i><?=$estatisticas["total_vagas_ocupadas_minicursos"]."/".$estatisticas["total_vagas_minicursos"]?>
        </div>
        <div class="label">Vagas preenchidas em minicursos</div>
      </div>
    </a>

  </div>
</div>


<div class="ui horizontal divider">Logs</div>

<div class="row">
<div class="ui feed">
<?php while($log = $topLogs->unbuffered_row()):?>


  <div class="event">
    <div class="content">
      <div class="summary">
        <a href="<?=site_url("painel/usuarios/index/$log->iduser")?>" class="user">
          <?=$log->nome_completo?>
        </a> <?=$log->descricao?>
        <div class="date">
        <?= date("d/m/Y H:i:s", strtotime($log->data_hora))?>
        </div>
      </div>
    </div>
  </div>

<?php endwhile; ?>
</div>
</div>
<?php require_once 'bottom.php'; ?>

