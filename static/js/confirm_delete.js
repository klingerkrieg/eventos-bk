var next_url = "";

function confirmDelete(){
    window.location = next_url;
}

function showConf(link,titulo){
    next_url = link;
    $('#confirmOrCancel').modal('show');
    if (titulo != null){
        $("#trabalhoSendoDeletado").html(titulo);
    }
}