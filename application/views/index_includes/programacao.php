<?php

function separarPorHorarios($lista){
    $horarios = [];
    foreach($lista as $minicurso){
        if (is_array($minicurso["horarios_escolhidos"])){
            foreach($minicurso["horarios_escolhidos"] as $horario){
                if (isset($horarios[$horario])){
                    array_push($horarios[$horario], $minicurso);
                } else {
                    $horarios[$horario] = [$minicurso];
                }
            }
        }
    }
    return $horarios;
}
$horarios = separarPorHorarios($lista);

function htmlMinicursos($horarios, $horario){
    $html = "";
    if (isset($horarios[$horario])){
        $html = "<ul>";

        foreach($horarios[$horario] as $minicurso){

            
            if ($minicurso['lattes'] == "" || strstr($minicurso['lattes'], "lattes") == false){ 
                $nome = ucwords( strtolower($minicurso['nome_autor']) );
            } else {
                $nome = "<a target='_BLANK' href='{$minicurso['lattes']}'>".ucwords( strtolower($minicurso['nome_autor']) )."</a>";
            }
            $ministrantes = [ $nome ];
            foreach($minicurso['coautores'] as $coautor){
                if ($coautor['lattes'] == "" || strstr($coautor['lattes'], "lattes") == false){
                    $nome = ucwords(strtolower($coautor['nome_completo']));
                } else {
                    $nome = "<a target='_BLANK' href='{$coautor['lattes']}'>".ucwords(strtolower($coautor['nome_completo']))."</a>";
                }
                array_push($ministrantes, $nome);
            }

            $html .= "<li><b>". $minicurso['titulo']."</b>: " . implode("; ", $ministrantes);
            
        }
        $html .= "</ul>";
    }
    return $html;
}

?>

<h2 class="ui stripe horizontal divider header">
    <a id="programacao">PROGRAMAÇÃO</a>
</h2>
<div class="ui basic segment">
    
<div class="ui text container">

<style>
#programacao .dia{
    color:#21ba45;
    font-weight:bold;
}

#programacao a.item{
    display:block;
    width:33%;
}

#programacao a.active{
    background-color:#21ba45;
    color:white;
}

#programacao .active .dia{
    color:white;
}

#programacao .header{
    color:#21ba45 !important;
}

</style>

<div id="programacao">

<?php /*<div class="ui info message">
  Para assistir aos minicursos faça o <a href='<?=site_url('home/login')?>'>login</a> e vá em '<a href='<?=site_url('minicursos')?>'>Minicursos</a>', lá você poderá ver os links dos minicursos em que você se matriculou.
</div> */ ?>

<div class="ui massive pointing secondary menu">
  <a class="item active" data-tab="first"><div class='dia'>DIA 1</div>12/04</a>
  <a class="item" data-tab="second"><div class='dia'>DIA 2</div> 13/04</a>
  <a class="item " data-tab="third"><div class='dia'>DIA 3</div> 14/04</a>
</div>
<div class="ui tab segment active" data-tab="first">

<div class="ui large relaxed divided list">
<?php 
$cron = ["08h00min - 09h30min"=>"<b>Cerimônia de abertura e Conferência de abertura: Todas as Ciências são Essenciais à Sociedade</b></br>
                                    <a href='http://lattes.cnpq.br/3946223809924016' target='_BLANK'>Prof. Dr. Cleonilson Mafra Barbosa</a><br/>
                                    <a target='_BLANK' href='https://www.youtube.com/channel/UCGCMcjU0DgFrveb1j9s5GRA' style='color:red;'>
                                        <i class='youtube icon'></i>
                                        Assista no Canal do IFRN-NC
                                    </a>",
            "09h30min - 11h30min"=>"
            <ul>
            <a target='_BLANK' href='#'>
            <li>Química</li>
        
            <ul>
                <li>DESENVOLVIMENTO DE MATERIAIS GRAFOTÁTEIS NO PROCESSO DE INCLUSÃO DE
                ALUNOS COM DEFICIÊNCIA VISUAL NO
                ENSINO DE QUÍMICA</li>
                <li>PRODUÇÃO DO PAPEL CARTONADO A
                PARTIR DE PAPEL RECICLADO E FIBRA
                VEGETAL</li>
                <li>ANÁLISE ESPECTRAL DE VINHOS
                EMPREGANDO ESPECTROSCOPIA UV-Vis E
                FERRAMENTAS QUIMIOMÉTRICAS</li>
                <li>Avaliações físico-químicas de húmus líquido
                obtido a partir do uso da composteira
                doméstica</li>
                <li>EFEITOS DOS ÍONS Na+ E Ca++ COMO
                CÁTIONS TROCÁVEIS NA ADSORÇÃO DO
                CORANTE AZUL DE METILENO POR ARGILA</li>
            </ul>
            </a>
        
            <a target='_BLANK' href='#'>
            <li>Administração</li>
            <ul>
                <li>Rotinas administrativas na Prefeitura de
                Serra de São Bento</li>
                <li>As dificuldades da gestão escolar no período
                pandêmico na Escola Municipal Manoel
                Firmino Alves</li>
                <li>Empreendedorismo Feminino: o caso de
                uma empreendedora do ramo de cosméticos
                e joias</li>
                <li>Empreendedorismo feminino: Um Estudo de
                Caso na cidade de Santo Antônio/RN</li>
                <li>Estudo de caso em uma loja de móveis e
                eletrodomésticos em Nova Cruz/RN</li>
            </ul>
            </a>
    
    
            <a target='_BLANK' href='#'>
            <li>Informática</li>
            <ul>
                <li>BioApp: Aplicativo para Dinamização do
                Ensino da Biologia</li>
                <li>DETECTOR DE GASES TÓXICOS E FUMAÇA
                UTILIZANDO ARDUINO</li>
                <li>MODULO ESPERTTI SIRENE (MES): Protótipo
                automático para sinal escolar.</li>
                <li>Pong com matriz de LED </li>
                <li>RELATÓRIO DE PRÁTICA PROFISSIONAL:
                SUPORTE E MANUTENÇÃO NA GUEDES
                MÓVEIS</li>
            </ul>
            </a>
    
        </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'><a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a></a>",
            "11h30min - 13h30min"=>"Intervalo de almoço",
            "13h30min - 15h00min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-12 13:30"),
            "15h00min - 17h00min"=>"
            <ul>
            <a target='_BLANK' href='#'>
            <li>Química</li>
        
            <ul>
                <li>TRATAMENTO E REAPROVEITAMENTO DE
                RESÍDUO CONTENDO CROMO
                </li>        
                <li>DETERMINAÇÃO DO FORMALDEÍDO EM
                AMOSTRAS DE LEITE POR ANÁLISE DE
                IMAGEM UTILIZANDO O PHOTOMETRIX:
                UMA ALTERNATIVA PARA A QUÍMICA
                ANALÍTICA</li>
                <li>ANÁLISE DA POTABILIDADE DA ÁGUA
                PRESENTE NOS BEBEDOUROS DO IFRN -
                CAMPUS NOVA CRUZ</li>
                <li>SOLUÇÕES PARA OS RESÍDUOS GERADOS
                NAS BARRACAS DE LANCHES</li>
            </ul>
            </a>
        
            <a target='_BLANK' href='#'>
            <li>Administração</li>
            <ul>
                <li>Relatório de Prática Profissional: A
                Experiência na Lotérica Bujarí Nova Cruz/RN</li>
                <li>Cultura Organizacional: uma análise
                comparativa da perspectiva dos gestores do
                Ideal Colégio e Curso e do Instituto Federal
                do Rio Grande do Norte - Campus Nova Cruz</li>
                <li>EMPODERAMENTO FEMININO NO
                MERCADO DE TRABALHO: FEMININISMO
                SEM RECOGNIÇÃO?</li>
                <li>GESTÃO DE PESSOAS: UM ESTUDO SOBRE A
                MOTIVAÇÃO NO TRABALHO DOS
                COLABORADORES DO SUPERMERCADO
                AGUIAR</li>
                <li>Modelagem de processos: uma
                implementação dos conceitos BPMN
                aplicados ao setor de digitalização do
                Tribunal de Justiça de Nova-Cruz</li>
            </ul>
            </a>
    
    
            <a target='_BLANK' href='#'>
            <li>Informática</li>
            <ul>
                <li>LEAN MATH: UMA FERRAMENTA WEB PARA
                AUXILIAR NA APRENDIZAGEM DE
                MATEMÁTICA</li>
                <li>SISTEMA DE IRRIGAÇÃO AUTOMÁTICO
                DESENVOLVIMENTO COM BASE EM
                ARDUÍNO</li>
                <li>MAINTECH: UM SITE PARA A IDENTIFICAÇÃO
                E RESOLUÇÃO DE PROBLEMAS
                COMPUTACIONAIS</li>
                <li>UMA FERRAMENTA WEB PARA AUXILIAR NA
                PREPARAÇÃO DE ALUNOS QUE PRETENDEM
                INGRESSAR NO ENSINO MÉDIO INTEGRADO
                DO IFRN</li>
            </ul>
            </a>
    
        </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a>",
            "17h00min - 19h00min"=>"Intervalo do jantar",
            "19h00min - 20h00min"=>"
            <ul>
            <a target='_BLANK' href='#'>
            <li>Química</li>
        
            <ul>
                <li>Estudo da potabilidade da água de chuva de
                Passagem - RN            </li>        
                <li>ELABORAÇÃO DE FILMES BIODEGRADÁVEIS
                A PARTIR DO AMIDO DE SEMENTE DE JACA
                INCORPORADO COM FIBRA DA PALMA
                FORRAGEIRA</li>
            </ul>
            </a>
        
            <a target='_BLANK' href='#'>
            <li>Administração</li>
            <ul>
                <li>Como o Marketing Digital ajudou nas vendas
                durante a pandemia</li>
                <li>Relatório de Prática Profissional           </li>
                <li>O Estágio como Instrumento de
                Aprendizagem Profissional</li>
            </ul>
            </a>
    
    
            <a target='_BLANK' href='#'>
            <li>Informática</li>
            <ul>
                <li>APLICATIVO PARA CONTROLE E
                GERENCIAMENTO DE APIÁRIO</li>
            </ul>
            </a>
    
        </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a>",
            "20h00min - 21h30min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-12 20:00")];

    foreach($cron as $key=>$value): ?>

    
    <div class="item">
        <div class="content">
            <div class="header"><?=$key?></div>
            <div class='description'><?=$value?></div>
        </div>
    </div>

    <?php endforeach; ?>
</div>


</div>
<div class="ui tab segment" data-tab="second">
   

<div class="ui large relaxed divided list">
<?php 
$cron = ["08h00min - 09h30min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-13 08:00"),
        "09h30min - 10h30min"=>"<p><b>Palestra de administração:</b> Carreiras no Mercado Financeiro</br>
                                    <a href='http://lattes.cnpq.br/8672376627184872'>Lemuel de Lemos Romão</a><br/>
                                    </p>
                                <p><b>Palestra de informática:</b> Trabalho remoto e produtividade</br>
                                    <a href='http://lattes.cnpq.br/5848281505652100'>Nélio Frazão Chaves</a><br/>
                                    </p>
                                <p><b>Palestra de química:</b> Unidades de Processamento do Gás Natural</br>
                                    <a href='http://lattes.cnpq.br/8423652689371761'>Prof. Dr. Wilaci Eutrópio Fernandes Júnior</a><br/>
                                    </p>
                                    
                                    ",
        "10h30min - 11h30min"=>"
        <ul>
            <a target='_BLANK' href='#'>
            <li>Química</li>
        
            <ul>
                <li>Caracterização de biofilmes de amido de Inhame reforçados com fibra do Noni</li>
                <li>USO DE COAGULANTE NATURAIS À BASE DE TANINOS DE Mimosa tenuiflora E SEMENTES DE Moringa oleífera NA REDUÇÃO DE TURBIDEZ DE ÁGUA.</li>
                <li>MAPEAMENTO DA EMISSÃO DE PARTICULADOS\GASES POLUENTES NO MUNICÍPIO DE SANTO ANTÔNIO/RN</li>
                <li>OS BENEFÍCIOS DOS BIOPOLÍMEROS E POLIMEROS BIODEGRADÁVEIS PARA A SUSTENTABILIDADE E AVALIAÇÃO DO CICLO DE VIDA EM COMPARAÇÃO AO PLÁSTICO CONVENCIONAL</li>
            </ul>
            </a>
        
            <a target='_BLANK' href='#'>
            <li>Administração</li>
            <ul>
                <li>Prática profissional: estudo de caso em uma
                concessionária de motos em Nova Cruz/ RN</li>
                <li>Relatório de Prática Profissional: Atividades
                desenvolvidas no Supermercado Gomes em
                Nova Cruz/RN</li>
            </ul>
            </a>


            <a target='_BLANK' href='#'>
            <li>Informática</li>
            <ul>
                <li>RELATÓRIO DE ESTÁGIO SUPERVISIONADO:
                ALIMENTAÇÃO DOS SISTEMAS DE
                LICITAÇÃO NA PREFEITURA MUNICIPAL DE
                PASSA E FICA</li>
                <li>RELATÓRIO DE PRÁTICA PROFISSIONAL DE
                INFORMÁTICA: RELATO DA APLICAÇÃO DE
                CONHECIMENTO NA CÂMARA MUNICIPAL
                DE NOVA CRUZ/RN</li>
                <li>RELATÓRIO DE PRÁTICA PROFISSIONAL:
                SUPORTE E DESENVOLVIMENTO EM
                SISTEMAS DE GESTÃO NA SYSDELTA-NC</li>
            </ul>
            </a>


        </ul>
        ",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'><a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a></a>",
        "11h30min - 13h30min"=>"Intervalo de almoço",
        "13h30min - 15h00min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-13 13:30"),
        "15h00min - 17h00min"=>"
        <ul>
        <a target='_BLANK' href='#'>
        <li>Química</li>
    
        <ul>
            <li>Produção de um xampu ecológico, em barra,
            à base de babosa (Aloe Vera)</li>
            <li>Aplicação de Técnicas em Tintas       </li>
            <li>PRODUÇÃO E CARACTERIZAÇÃO DO
            BIODIESEL PROVENIENTE DA SEMENTE DE
            MARACUJÁ AMARELO (PASSIFLORA EDULIS
            SIMS F. FLAVICARPA DEGENER) E DO
            MAMÃO (CARIOCA PAPAYA L.) POR MEIO DA
            TRANSESTERIFICAÇÃO IN</li>
            <li>Remoção de mercúrio (II) de soluções
            aquosas por adsorção: uma breve revisão da
            literatura</li>
        </ul>
        </a>
    
        <a target='_BLANK' href='#'>
        <li>Administração</li>
        <ul>
            <li>Percepção social da exposição do público
            LGBTQI+ no campo midiático</li>
            <li>PESQUISA DE CLIMA ORGANIZACIONAL</li>
            <li>Prática profissional: a experiência de
            aprendizagem no Fórum Municipal Djalma
            Marinho- Nova Cruz/RN</li>
            <li>RELATÓRIO DE ESTÁGIO NO TRIBUNAL DE
            JUSTIÇA DO RIO GRANDE DO NORTE</li>
            <li>RELATÓRIO DE ESTÁGIO: UMA EXPOSIÇÃO
            DAS ATIVIDADES DESEMPENHADAS NO
            TRIBUNAL DE JUSTIÇA DO RIO GRANDE DO
            NORTE NA COMARCA DE NOVA CRUZ/RN</li>
        </ul>
        </a>


        <a target='_BLANK' href='#'>
        <li>Informática</li>
        <ul>
            <li>SYSKEY - Sistema de Gerenciador de Chaves</li>
            <li>BODYEYE 1.0: Um equipamento vestível para
            o auxílio na locomoção de pessoas com
            deficiência visual</li>
            <li>ENSINO DE ROBÓTICA PARA REDE DE
            ENSINO PÚBLICA </li>
            <li>AGRIIF, UM APLICATIVO MOB PARA
            AUXILIAR AGRICULTORES NA PLANTAÇÃO DE
            CULTURAS ESPECÍFICAS</li>
            <li>Gideon - Assistente Pessoal Eletrônico</li>
            <li>INFOR+: icentivando o uso adequado das
            redes sociais para a segurança de dados e
            usuários</li>
        </ul>
        </a>


    </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a>",
        "17h00min - 18h30min"=>"<p><b>Palestra das propedêuticas:</b> A exclusão feminina nas ciências</br>
                                <a href='http://lattes.cnpq.br/5639409302231081'>Prof. Edilson Damasceno</a><br/>
                                </p>",
        "18h00min - 19h00min"=>"Intervalo de jantar",
        "19h00min - 20h00min"=>"
        <ul>
        <a target='_BLANK' href='#'>
        <li>Química</li>
    
        <ul>
            <li>CARACTERIZAÇÃO DA ÁGUA DE LAVAGEM
            DO ÓLEO UTILIZADO NA PRODUÇÃO DE
            SABÃO NO IFRN CAMPUS NOVA CRUZ</li>
            <li>Produção de tinta a partir de pigmento
            natural / Sustentabilidade na matéria-prima</li>
            <li>USO DA CASCA DE BANANA COMO
            BIOADORSORVENTE PARA REMOÇÃO DE
            METAIS PESADOS DE SOLUÇÕES AQUOSAS</li>
        </ul>
        </a>
    
        <a target='_BLANK' href='#'>
        <li>Administração</li>
        <ul>
            <li>ESTÁGIO: RELATO DA PRÁTICA
            PROFISSIONAL VIVENCIADA NO TRIBUNAL
            DE JUSTIÇA DA COMARCA DE NOVA
            CRUZ/RN.</li>
            <li>Padronização no arquivamento e registro de
            documentos funcionais na COGPE do IFRN/
            NC</li>
        </ul>
        </a>


        <a target='_BLANK' href='#'>
        <li>Propedêutica</li>
        <ul>
            <li>NUARTE NOVA CRUZ: AÇÕES SÓCIOCULTURAIS E INTERCULTURALIDADES</li>
            <li>MALEFÍCIOS DO CONSUMO DE BEBIDAS
            INDUSTRIALIZADAS UQE POSSUEM pH
            ÁCIDO</li>
        </ul>
        </a>


        <a target='_BLANK' href='#'>
        <li>Informática</li>
        <ul>
            <li>CONTRA REFLEXO: UM JOGO DIGITAL
            INCORPORANDO CONCEITOS DA FÍSICA</li>
            <li>CATCH-CATCH: Um Jogo para auxílio do
            processo de aprendizagem da leitura</li>
            <li>Matemática Dinâmica</li>
        </ul>
        </a>

    </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a>",
        "20h00min - 21h30min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-13 20:00")];

    foreach($cron as $key=>$value): ?>

    
    <div class="item">
        <div class="content">
            <div class="header"><?=$key?></div>
            <div class='description'><?=$value?></div>
        </div>
    </div>

    <?php endforeach; ?>
</div>


</div>
<div class="ui tab segment " data-tab="third">
  
  
<div class="ui large relaxed divided list">
<?php 
$cron = ["08h00min - 09h30min"=>"Sessão de minicursos". htmlMinicursos($horarios, "2021-04-14 08:00"),
        "09h30min - 11h30min"=>"
        <ul>
        <a target='_BLANK' href='#'>
        <li>Química</li>
    
        <ul>
            <li>Análise comparativa de parâmetros físicoquímicos e microbiológicos da água
            distribuída na cidade de Montanhas-RN</li>
            <li>Potencial de resíduos lignocelulósicos da
            agricultura regional para a produção de
            papel artesanal com enfase em um
            aplicativo voltado ao ensino de química para
            o Enem</li>
            <li>O QUE AS INDÚSTRIAS QUÍMICAS DA
            REGIÃO AGRESTE POTIGUAR E ADJACÊNCIAS
            BUSCAM NO PERFIL PROFISSIONAL DO
            TÉCNICO EM QUÍMICA?</li>
            <li>REAPROVEITAMENTO DAS CASCAS E
            SEMENTES DO MARACUJÁ PARA A
            FABRICAÇÃO DE SUBPRODUTOS</li>
        </ul>
        </a>
    
        <a target='_BLANK' href='#'>
        <li>Propedêutica</li>
        <ul>
            <li>A MELANCOLIA EM MANUEL BANDEIRA: UM
            ESTUDO FREUDIANO DO TRISTE LIRISMO DE
            CARNAVAL</li>
            <li>Algarismos significativos: diagnóstico em
            exames e livros didáticos</li>
            <li>Arte e Interculturalidade: um estudo sobre
            as transmissões culturais de grupos artísticos
            do Agreste Potiguar</li>
            <li>Instumentos Musicais: O universo das teclas            </li>
            <li>Mulheres nas ciências: notáveis e pouco
            conhecidas </li>
        </ul>
        </a>


        <a target='_BLANK' href='#'>
        <li>Informática</li>
        <ul>
            <li>Privacidade de dados no Instagram e
            Whatsapp: como o Facebook opera as duas
            redes sociais</li>
            <li>MEDPALM: Um Aplicativo para Gerência dos
            Remédios Pessoais para Idosos.</li>
            <li>Desenvolvimento de software protótipo
            para auxílio no gerenciamento da biblioteca
            municipal de montanhas - rn</li>
            <li>APLICATIVO PARA O APRENDIZADO PRÉCATEQUÉTICO INFANTIL (CATEkids) </li>
            <li>Informatizando: canal de podcast sobre
            atualidades do mundo digital</li>
            <li>Relatos das contribuições junto a UBS da
            Família de Santa Luzia-Nova Cruz/RN </li>
            <li>SOFTWARE PARA CONTROLE DE VENDAS DE
            PEQUENOS COMÉRCIOS SITUADOS NA
            REGIÃO AGRESTE PORTIGUAR</li>
        </ul>
        </a>

    </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'><a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a></a>",
        "11h30min - 13h30min"=>"Intervalo de almoço",
        "13h30min - 15h00min"=>"<p><b>Mesa redonda com ex-alunos</b> 
        <a target='_BLANK' href='https://www.youtube.com/channel/UCGCMcjU0DgFrveb1j9s5GRA' style='color:red;'>
            <i class='youtube icon'></i>
            Assista no Canal do IFRN-NC
        </a></p>
        
        <p><b>Mesa redonda de administração</b><br/>
        <b>Representante Técnico em administração:</b> Ana Maria Jerônimo Soares (Mestranda PPGA-UFRN)</p>

        <p><b>Mesa redonda de informática</b><br/>
        <b>Representante Técnico em informática:</b> Maria Gracielly Fernandes Coutinho (TJRN / Doutoranda PPgEEC-UFRN).<br/>
        <b>Representante TADS:</b> Adelson de Oliveira Câmara da Cruz</p>
        

        <p><b>Mesa redonda de química</b><br/>
        <b>Representante Técnico em Química:</b> João Marcos (Farma Fórmula)<br/>
        <b>Representante CSTPQ:</b> Simone Gomes (Vicunha)</p>
        
        ",
        "15h00min - 16h00min"=>"
        <ul>
        <a target='_BLANK' href='#'>
        <li>Química</li>
    
        <ul>
            <li>PRODUÇÃO DE SABONETE LÍQUIDO A
            PARTIR DO ÓLEO EXTRAÍDO DA MATRICARIA
            CHAMOMILLA</li>
            <li>PURIFICAÇÃO DA GLICERINA OBTIDA NA
            PRODUÇÃO DE BIODIESEL NO IFRN /
            CAMPUS NOVA CRUZ</li>
        </ul>
        </a>
    
        <a target='_BLANK' href='#'>
        <li>Administração</li>
        <ul>
            <li>Relatório de prática profissional: Experiência
            em Valdir Moto Peças</li>
            <li>RELATÓRIO DE PRÁTICA PROFISSIONAL: UM
            RELATO DA EXPERIÊNCIA DE ESTÁGIO NO
            TRIBUNAL DE JUSTIÇA DO RIO GRANDE DO
            NORTE</li>
            <li>RPP - Posto Odon</li>
        </ul>
        </a>


        <a target='_BLANK' href='#'>
        <li>Informática</li>
        <ul>
            <li>CELULAR ADVENTURE: UM JOGO DIGITAL
            COMO FERRAMENTA EDUCACIONAL PARA O
            ENSINO DE BIOLOGIA </li>
            <li>BioCultivando: Ferramenta Web de
            Caracterização de Plantas da Região Agreste</li>
            <li>A informática tem cor? Um estudo sobre a
            ausência de pessoas negras no meio
            tecnológico e suas consequências</li>
        </ul>
        </a>

    </ul>",#"<a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'><a target='_BLANK' href='".base_url('static/apresentacao-trabalhos.pdf')."' style='color:red;'>Sessão de apresentação de trabalhos</a></a>",
        "17h00min - 19h00min"=>"Intervalo de jantar",
        "19h00min - 21h00min"=>"Encerramento + Premiação dos melhores trabalhos <a target='_BLANK' href='https://www.youtube.com/channel/UCGCMcjU0DgFrveb1j9s5GRA' style='color:red;'>
        <i class='youtube icon'></i>
        Assista no Canal do IFRN-NC
    </a>"];

    foreach($cron as $key=>$value): ?>

    
    <div class="item">
        <div class="content">
            <div class="header"><?=$key?></div>
            <div class='description'><?=$value?></div>
        </div>
    </div>

    <?php endforeach; ?>
</div>


</div>
</div>

<script>
$('.pointing.menu .item').tab();
</script>



    
    
</div>
</div>