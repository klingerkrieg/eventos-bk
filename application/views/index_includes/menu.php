<a class="item page-scroll" href="<?=site_url('home') ?>">Início</a>
<a class="item page-scroll" href="<?=site_url('home') ?>#sobre">Sobre</a>

<?php if (!isset($_SESSION["user"])): ?>
    <a class="item page-scroll" href="<?=site_url('home') ?>#modelos">Modelos</a>
<?php endif; ?>

<a class="item page-scroll" href="<?=site_url('home') ?>#programacao">Programação</a>
<!--a class="item page-scroll" href="<?=site_url('home') ?>#inscricoes">Inscrições</a-->
<!--a class="item page-scroll" href="<?=site_url('home') ?>#gts">GTs</a-->

<?php if (isset($_SESSION["user"])): ?>

    <a class="item page-scroll" href="<?=site_url('trabalhos/index') ?>">Minhas submissões</a>
    <a class="item page-scroll" href="<?=site_url('minicursos/index') ?>">Minicursos
        <?php /*if (temMinicursos()) { ?>
            <i class="info circle icon menuAlert" ></i>
        <?php }*/ ?>
    </a>

<?php else: ?>

    <!--a class="item page-scroll" href="<?=site_url('home') ?>#palestrantes">Palestrantes</a-->
    <!--a class="item page-scroll" href="<?=site_url('home') ?>#a-cidade">A cidade</a-->
    <a class="item page-scroll" href="<?=site_url('home') ?>#organizacao">Organização</a>
    <a class="item page-scroll" href="<?=site_url('home') ?>#contato">Contato</a>

<?php endif; ?>