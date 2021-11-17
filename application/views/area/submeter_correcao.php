<?php include 'head.php' ?>


<h2 class="ui horizontal divider header">Revisar trabalho</h2>
<div class="ui text container">

<?php if (_v($dados,"idusuario") == $_SESSION["user"]["id"] && $dados["status"] != REPROVADO && _v($dados,"certificado") == ""): ?>
  <form action="<?=site_url('trabalhos/submeter_correcao') ?>" novalidate class="ui form" method="post" enctype="multipart/form-data">
<?php else: ?>
  <form class='ui form'>
<?php endif; ?>

    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
    
    <input type="hidden" name="id" value="<?=_v($dados,'id')?>" />

    <div class="field">
      <label for="titulo">Título:</label>
      <input id="titulo" value="<?=_v($dados,'titulo')?>" readonly>
    </div>

    <div class="field">
      <label for="titulo">Autor:</label>

      <div class="two wide fields">
        <div class="field">
        <input id="autor" value="<?=_v($dados,'nome_autor')?>" readonly>
        </div>
        <?php if (_v($dados,'certificado') != ""): ?>
          <div class="field">
          <a class="ui blue button" href="<?=base_url("./certificados/trabalhos/{$dados['certificado']}.pdf")?>" target="_BLANK">
              <i class="download icon"></i>
              Certificado do trabalho
          </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

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
          print "<a class='ui blue button' href='".base_url("./certificados/trabalhos/{$coautor['certificado']}.pdf")."' target='_BLANK' style='margin-bottom: 3px;'>
                    <i class='download icon'></i>Certificado do trabalho</a>";
        }

        print "</li>";
      }
      ?>
      </ul>
    </div>

    <div class="field">
      <label>Orientadores/Registro de ciência</label>
      <ul id="orientador_area">
      <?php 
      $i = 0;
      foreach( $dados["orientadores"] as $coautor) {
        print "<li id='orientador$i'>{$coautor['nome_completo']} &lt;{$coautor['email']}&gt; ";
        $i++;

        if ($coautor['ciente'] != ""){
          print '<i class="icon checkmark green" ></i>';
        } else {
            print '<i class="icon close red"></i>';
        }

        if ($coautor["certificado"] != ""){
          print "<a class='ui blue button' href='".base_url("./certificados/trabalhos/{$coautor['certificado']}.pdf")."' target='_BLANK' style='margin-bottom: 3px;'>
                    <i class='download icon'></i>Certificado do trabalho</a>";
        }

        print "</li>";
      }
      ?>
      </ul>
    </div>

    <!--div class="field">
      <label for="idgt">Grupo de trabalho: </label>
      <input value="<?=_v($dados,'gt')?>" readonly>
    </div-->

    <div class="field">
      <label for="idtipo_trabalho">Área: </label>
      <input id="area" value="<?=_v($dados,'area')?>" readonly>
    </div>

    <?php /*div class="field">
      <label for="idtipo_trabalho">Tipo de trabalho: </label>
      <input value="<?=_v($tiposTrabalhos,_v($dados,'idtipo_trabalho'))?>" readonly>
    </div */ ?>

    <div class="field">
      <label for="idtrilha">Trilha: </label>
      <input id="trilha" value="<?=_v($trilhas,_v($dados,'idtrilha'))?>" readonly>
    </div>

    <div class="field">
      <label>Status:</label>
      <?=statusColor($dados["status"],$status)?>
    </div>

    <div class="field required">
      <label id="url" for="url">URL do vídeo no Youtube</label>

      <?php
      $readOnly = "readonly";
      $msg = "";
      if ($dados["status"] != REPROVADO && $dados["certificado"] == "") {
        $readOnly = "";
        $msg = "<div class='ui pointing label'>Você ainda pode atualizar a URL.</div>";
      }?>


      <div class="ui right labeled input">
        <input type='url' name="url" id="url" value="<?=_v($dados,'url')?>" <?=$readOnly?>>
        <a class="ui tag label" target="_BLANK" href="<?=_v($dados,'url')?>">
            <i class="youtube icon"></i>
        </a>
      </div>
      <?=$msg?>
      <?=form_error('url') ?>
    </div>

    
    <?php if ($dados["status"] != PENDENTE && $dados['observacao'] != ""): ?>
      <div id="observacoes" class="ui message">
        <div class="header">Observações</div>
        <?=$dados['observacao']?>
      </div>
    <?php endif; ?>
    

    <?php foreach($dados["avaliadores"] as $i=>$av): ?>

      <?php if ($av['observacao'] != ""): ?>

      <div id="observacoes_av_<?=$i?>" class="ui message">
        <div class="header">Observações dos avaliador <?=$i+1?></div>
        <?=$av['observacao']?>
      </div>

      <?php endif; ?>
    <?php endforeach; ?>

    

    <div class="field">
      <label for="arquivo">Arquivo: </label>
      <a id="arquivo" class="ui blue button" href="<?=base_url()?>uploads/trabalhos/<?=_v($dados,'arquivo')?>" target="_BLANK">
        <i class="download icon"></i>
        <?=limitar(_v($dados,'arquivo'),40)?>
      </a>
      <span class="date">
        <?=date("d/m/Y H:i:s", strtotime($dados["submissao"]))?>
      </span>
    </div>




    <?php if ($evento['aceitando_correcoes']): #aceitando correcoes ?>

      <?php if (_v($dados,'arquivoCorrigido') != ""): #ja enviou correcao ?>
        <div class="field">
        <label for="arquivo">Arquivo com a versão corrigida:</label>
          <a id="correcaoSubmetida" class="ui blue button" href="<?=base_url()?>uploads/trabalhos/correcoes/<?=_v($dados,'arquivoCorrigido')?>" target="_BLANK">
            <i class="download icon"></i>
            <?=limitar(_v($dados,'arquivoCorrigido'),40)?>
          </a>
          <span class="date">
            <?=date("d/m/Y H:i:s", strtotime($dados["correcao"]))?>
          </span>
        </div>
        
      <?php endif; #ja enviou correcao ?>    

    <?php endif; #aceitando correcoes ?>    
    

    <?php if (_v($dados,"id") != "" && _v($dados,"idusuario") == $_SESSION["user"]["id"] && $dados["certificado"] == ""): #se trabalho valido e eu sou o dono ?>

        


      <?php if($dados["status"] == APROVADO || $dados["status"] == APROVADO_CORRECOES ): #se trabalho aprovado ?>
          <div class="ui message">
            Seu trabalho foi aprovado e não precisa de correções, mas você ainda pode atualizar a URL do vídeo do Youtube.
          </div>
      
      <?php elseif ($dados["status"] == APROVADO_CORRECOES_PENDENTES): #se aprovado com correcoes pendentes1 ?>
        
      <?php if ($evento['aceitando_correcoes']): #aceitando correcoes ?>
        <div class='field showIfCorrecaoSubmetida'>
          <label><input type='checkbox' class='ui checkbox' id='showCorrecaoSubmetida'>
            Reenviar arquivo com correção</label>
        </div>


        <div class="hideIfCorrecaoSubmetida">
        
        <div class='field'>
          <input type="file" name="arquivoCorrigido" required class="clearable file input" id="arquivoCorrigido" />
          <div class="ui pointing label">Somente .pdf com tamanho máximo de 10.00 MB</div>
          <?=form_error('arquivoCorrigido') ?>
        </div>

        <div class="ui yellow message">
            <i class="upload icon"></i>
            Envie a versão corrigida.
        </div>

        <?php endif; #aceitando correcoes ?>
      <?php elseif ($dados["status"] == REPROVADO): #se reprovado ?>
        <div class="ui yellow message">
          Trabalhos reprovados não poderão ser reenviados.
        </div>
      <?php endif; #se reprovado ?>


      <?php if ( $dados["status"] != REPROVADO): ?>
      <button type="submit" class="ui green button">
        <i class="upload icon"></i>
        Salvar
      </button>
      <?php endif; ?>

    <?php endif; #se trabalho valido e eu sou o dono ?>




  </form>
  
</div>


<?php include 'bottom.php'; ?>
