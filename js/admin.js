jQuery(document).ready(function($){
    $('#wundercounter-complexity-advanced,#wundercounter-complexity-simple').change(function() {
        //var $which = $("#wundercounter-complexity-advanced:checked").val() ? 'advanced' : 'simple';
        $('#wundercounter-advanced,#wundercounter-simple').toggle();
    });
 });  