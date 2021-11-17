<html>
<head>
<style>


#logo_ifrn{
    width:280px;
    margin-top:20px;
    margin-left:40px;
    float:left;
    vertical-align: middle;
}

#evento_logo{
    width:280px;
    margin-right:40px;
    float:right;
    vertical-align: middle;
}

#clear{
    clear:both;
}


#texto{
    font-size: 22px;
    text-align: justify;
    margin-top: 45px;
    width: 100%;
}

#data {
    font-size: 22px;
    margin-top:20px;
    text-align:right;
}

#assinaturas{
    font-size: 22px;
    margin-top:90px;
    text-align:center;
}
.assinatura{
    width:210px;
    margin-left:20px;
    margin-right:20px;
}

#validacao{
    position:absolute;
    bottom:0px;
    left:0px;
    color:#636161;
}

h4, h1{
    text-align:center;
}
</style>
    <title>Certificado - Expotec - Nova Cruz</title>
</head>
<body>


<div>
    <img id="logo_ifrn" src='<?=$ifrn_logo?>' />
    <img id="evento_logo" src='<?=$evento_logo?>' />
    <div id='clear'></div>
</div>

<h4>VII EXPOSIÇÃO CIENTÍFICA, TECNOLÓGICA E CULTURAL DO CAMPUS IFRN NOVA CRUZ</h4>

<h1>Certificado</h1>


<div id="texto">
    
    <?php include "$view.php"; ?>
    
</div>

<div id="data">Nova Cruz-RN, <?=data_certificado()?></div>

<div id="assinaturas">
    <?php /*<img class="assinatura" src='<?=$assin1?>' >
    <img class="assinatura" src='<?=$assin2?>' >
    <img class="assinatura" src='<?=$assin3?>' > */
     ?>

    <div>Comissão Organizadora da VII EXPOTEC</div>
    <div>Campus Nova Cruz</div>
</div>

<div id="validacao">Este certificado pode ser validado através da URL: <?=site_url("certificados/validar/$hash")?></div>
    

</body>
</html>