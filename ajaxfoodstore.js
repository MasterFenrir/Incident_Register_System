$(document).ready(function() {

    //Hides date fields when #box exists and is checked
    if($('#clicky').length && $('#clicky').attr('checked')) {
        $('.hide').hide();
    }

    //Toggles visibility of date fields when clicked
    $('#clicky').click(function(){
        $('.hide').toggle();
    })

});
