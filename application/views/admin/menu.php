<?php if (isset($_SESSION['admin_user'])): ?>
    <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
        <div class="ui dropdown item topMenu">
            Inscrições <i class="dropdown icon"></i>
            <div class="menu">
            <a class="item" href="<?=site_url("painel/usuarios")?>">Usuários</a>
            <a class="item" href="<?=site_url("painel/instituicoes")?>">Instituições</a>
            <a class="item" href="<?=site_url("painel/cursos")?>">Cursos</a>
            </div>
        </div>
        <div class="ui dropdown item topMenu">
            Minicursos <i class="dropdown icon"></i>
            <div class="menu">
            <a class="item" href="<?=site_url("painel/minicursos")?>">Submissões</a>
            <a class="item" href="<?=site_url("painel/minicursos/distribuicao")?>">Distribuição de horários</a>
            <a class="item" href="<?=site_url("painel/grandesareas")?>">Grandes áreas</a>
            <a class="item" href="<?=site_url("painel/areas")?>">Áreas</a>
            </div>
        </div>
    <?php endif; ?>


    <div class="ui dropdown item topMenu">
        Trabalhos <i class="dropdown icon"></i>
        <div class="menu">
        <a class="item" href="<?=site_url("painel/trabalhos")?>">Submissões</a>
        <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
            <a class="item" href="<?=site_url("painel/gts")?>">Grupos de trabalhos</a>
        <?php endif;?>
        </div>
    </div>

    <?php if ($_SESSION['admin_user']['nivel'] >= NIVEL_EQUIPE): ?>
        <a class="item" href="<?=site_url("painel/evento")?>">Evento</a>
    <?php endif; ?>


    <div class="right menu">
        <a class="item" href="#"><?=$_SESSION['admin_user']['email']?></a>
        <a href="<?=site_url("admin/logout")?>" class="item">Sair</a>
    </div>
<?php endif; ?>