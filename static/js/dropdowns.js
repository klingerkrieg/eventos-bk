$(function(){
    $('.ui.search.dropdown').dropdown({
        fullTextSearch:true,
        className:{
            label       : 'ui blue label',
        },
    });


    /** Se tiver mais de um category na mesma página provavelmente isso dará problema */
    var $menu = $('<div/>').addClass('menu');
    // add the head items based on the optgroup and the items based on the options
    $('optgroup').each(function (index, element) {
        $menu.append('<div class="header">' + element.label + '</div>')
        $(element).children().each(function(i, e){
            $menu.append('<div class="item" data-value="' + e.value + '">' + e.innerHTML + '</div>');
        })
    });
    $('.dropdown.category .menu').html($menu.html());


    $(".menu_superior").dropdown({
        on:'hover'
    });

});