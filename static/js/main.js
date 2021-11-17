var emailInvalid;

$(function(){

  //remove o menu flutuante nas páginas internas quando houver ancora
  if (window.location.hash != ""){
    $('html, body').stop().animate({
        scrollTop: $(window.location.hash).offset().top - 50
    }, 1500, 'easeInOutExpo');
  }


  $(".emailSuap").keyup(function(){
    if (isNaN($(this).val())){
      $(".loginType").remove();
      $(this).before("<div class='ui label loginType'><i class='envelope outline icon'></i></div>");
    } else {
      $(".loginType").remove();
      $(this).before("<div class='ui label loginType'>SUAP</div>");
    }  
  });

  $("#sou_aluno").click(function(){
    if ($(this).prop("checked")){
      $("#sou_aluno_msg").show();
    } else {
      $("#sou_aluno_msg").hide();
    }
  });




  emailInvalid = $('#emailConfirmDiv').find('.invalid');
  if (emailInvalid.length == 0){
    emailInvalid = $("<div class='invalid'>A confirmação do e-mail está diferente do campo e-mail.</div>");
  }

  $("#emailConfirm").blur(function(){
    if ($("#email").val() == $(this).val()){
      emailInvalid.hide();
    } else {
      $(this).after(emailInvalid);
      emailInvalid.show();
    }
  });

  $('.special.cards .image').dimmer({on: 'hover'});

  $('a.page-scroll').bind('click', function(event) {

      $('.ui.sidebar').sidebar('hide');

      var $anchor = $(this);
      href = $anchor.attr('href');
      href = href.substr(href.indexOf("#"));

      $('html, body').stop().animate({
          scrollTop: $(href).offset().top - 100
      }, 1500, 'easeInOutExpo');
      event.preventDefault();
  });


  $(".showIfCorrecaoSubmetida").hide();
  if ($("#correcaoSubmetida").length > 0){
    $(".hideIfCorrecaoSubmetida").hide();
    $(".showIfCorrecaoSubmetida").show();
    $("#showCorrecaoSubmetida").click(function(){
      $(".hideIfCorrecaoSubmetida").show();
      $(".showIfCorrecaoSubmetida").hide();
    });
  }
  


});

function callModal(selector){
  $(selector).modal('show');
}
