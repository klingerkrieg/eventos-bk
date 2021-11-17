<html>
<head>
<style>

body{
    width:600px;
}
table {
    border-collapse:collapse;
    width:600px;
}
table td{
    border: 1px solid;
}

.assinatura{
    width:200px;
}
</style>
<link rel="shortcut icon" href="<?=base_url()?>/static/img/favicon.ico" type="image/x-icon"/>
<title>Lista de frequência - <?=$minicurso['titulo']?></title>
</head>
<body>
<center>
    <h4><?=nome_evento()?></h4>
    <h3>Lista de frequência - <?=$minicurso['titulo']?></h3>
</center>
<b>Data:</b>__________________<br/>
<b>Ministrantes:</b><br/>
<?=$minicurso['nome_autor']?></br>
<?php foreach($minicurso['coautores'] as $coaut){
    print $coaut['nome_completo']."<br>";
}?>

<center>
<table>
    <tr>
        <th>Nome</th>
        <th>Assinatura</th>
    </tr>
<?php
foreach($minicurso['matriculados'] as $matr){
    print '<tr>';
    print "<td>{$matr['nome_completo']}</td><td class='assinatura'></td>";
    print '</tr>';
}
?>
</table>
</center>
<script>window.print()</script>
</body>
</html>