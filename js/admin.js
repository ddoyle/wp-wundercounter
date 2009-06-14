jQuery(document).ready(function($){
    
    $('#wundercounter-complexity-advanced,#wundercounter-complexity-simple').change(function() {
        //var $which = $("#wundercounter-complexity-advanced:checked").val() ? 'advanced' : 'simple';
        $('#wundercounter-advanced,#wundercounter-simple').toggle();
    });
    
    
    $('#adv_home,#adv_page,#adv_archive,#adv_search,#adv_post,#adv_default').change(function() {
        var id = '#' + $(this).attr('id') + '_id';
        if ($(this).val() == 'simple') {
            $(id).attr('disabled',false).css('background-color','#FFF');
        }
        else {
            $(id).attr('disabled','true').css('backgrond-color','#999');
        }
    });
 });  