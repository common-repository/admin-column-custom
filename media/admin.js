/*
 * Admin Column Custom
 *
 * Updated 1.0.11
 */
(function($){

    "use strict";

    if( typeof $.fn.sortable == 'undefined' ) return console.log('jQuery UI sortable not found');
    
    var list = $( "#list-custom-columns" ).sortable({
        update: function(event, ui){

            ui.item.find('.input-after').val( prev_element( ui.item ) );

            update_index();
        }
    });
    
    $("#custom-columns-form").each(function(){
        var f = this, html = $('#template_tr').html();
        
        $('tbody', f).on('click', '.btn-remove', function(e){
            e.preventDefault();

            $(this).closest('tr.hentry-drag').remove();

            update_index();
        }).on('change', '.select-type', function(e){
            e.preventDefault();

            if($(this).val() == 'media') {
                update_row_media($(this).closest('tr.hentry-drag')); 
            }
        });

        $('.btn-add', f).on('click', function(e){
            e.preventDefault();

            let hentry = $('tr.hentry-drag', list), row = $(html.replace(/index/g, hentry.length));
            
            if(hentry.length>0) {
                hentry.last().after(row);
            } else {
                $('tr:first', list).each(function(){
                    $('.input-after', row).val( $(this).attr('data-name') );                    
                }).after(row);
            }
        });
    });

    function prev_element( element )
    {
        let prev = element.prev(), name = 'first';

        while( prev.length > 0 ) {
            if( prev.hasClass('hentry-default') ) {
                name = prev.data('name') || '';
                break;
            }

            prev = prev.prev();
        }

        return name;
    }

    function update_index()
    {
        var prefix = list.data('prefix') + '[', plus = "][";

        $('tr.hentry-drag', list ).each(function( index ){
            var p = $(this).attr('data-index', index);

            $('[name*="' + prefix + '"]', p).each(function(){
                let input = $(this),
                    part = ( input.attr('name') || '' ).split(plus);

                if( part.length == 2 ) {
                    input.attr('name', prefix + index + plus + part[1] );
                }
            });
        });
    }

    function update_row_media(row)
    {
        if($('.input-name', row).val() == '') {
            $('.input-name', row).val('thumbnail');
            
            if($('.input-title', row).val() == '') {
                $('.input-title', row).val('Thumbnail');
            }
        }
    }

})(jQuery);