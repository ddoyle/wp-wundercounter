jQuery(document).ready(function($){
    
    // hide/show simple/advanced
    $('#wundercounter-complexity-advanced,#wundercounter-complexity-simple').change(function() {
        //var $which = $("#wundercounter-complexity-advanced:checked").val() ? 'advanced' : 'simple';
        $('#wundercounter-advanced,#wundercounter-simple').toggle();
    });
    
    // enable/disable textbox for each advanced counter
    $('#adv_home,#adv_page,#adv_archive,#adv_search,#adv_post,#adv_default').change(function() {
        var id = '#' + $(this).attr('id') + '_id';
        if ($(this).val() == 'simple') {
            $(id).attr('disabled',false).css('background-color','#FFF');
        }
        else {
            $(id).attr('disabled','true').css('backgrond-color','#999');
        }
    });
    
    // hide/show visible counter options
    $('#type1,#type2,#type3,#style').change(function(){
        var $type = $('input[name*=type]:checked').val();
        var $style = $('#style').val();
        if($type == 'invisible') {
            $('#wundercounter-visual-style,#wundercounter-visual-align,#wundercounter-visual-textcolor,#wundercounter-visual-background').hide();
        }
        else {
            $('#wundercounter-visual-style,#wundercounter-visual-align').show();
            if($style == 'default') {
                $('#wundercounter-visual-textcolor,#wundercounter-visual-background').show();
            }
            else {
                $('#wundercounter-visual-textcolor,#wundercounter-visual-background').hide();
            }
        }
    });
 });  