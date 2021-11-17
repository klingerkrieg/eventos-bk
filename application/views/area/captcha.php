<?php if (isset($_SESSION['captcha'])): ?>
    <div class="two fields">
    <div id="captcha_img" class="field">
        <?=$_SESSION['captcha']["image"]?>
    </div>
    <div class="field">
        <button type="button" class="ui green button" onclick="recarregarCaptcha();">
            <img src="<?=base_url()?>static/img/refresh.png" style="width:12px;" />
            Recarregar imagem
        </button>
        <label for="captcha_id">Digite os caracteres da imagem</label>
        <input name="captcha" class="textinput" id="captcha_id" style="text-transform:uppercase;"/>
        <?=form_error('captcha')?>
    </div>
    </div>
    <script>
    var loading_captcha = false;
    function recarregarCaptcha(){
    if (loading_captcha)
        return;
    loading_captcha = true;
    $.ajax(
        '<?=site_url('captcha')?>',{
        success:function(data){
            $('#captcha_img img').attr('src',data);
            loading_captcha = false;
        }
        });
    }
    </script>
<?php endif ?>