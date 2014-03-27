(function( $ ){

    var methods = {
        init : function( options ) {
            var settings = {
                form        : 'adminForm',
                action      : 'cancel',
                icon        : 'cancel',
                title       : 'Закрыть',
                show_label  : true,
                dialog      : false,
                obligate    : false,
                number      : 1,
                validate    : false,
                trackdirty  : false
            };
            if ( options ) { 
                $.extend( settings, options );
            }
            var td = settings.show_label ? '<td id="' + settings.action + '">' + settings.title +'</td>' :  '<td></td>';
            var img = '<span class="icon-32-'+settings.icon+'" title="'+settings.title+'"></span>';
            var r = $(this).append(td);
            var c = $(r).children().last().prepend(img);
            $(c).click( function () { 
                $(this).toolbar('call', settings); 
            } );
            return c;
        },
        call    : function(s) {  
            form = $("#"+s.form);
            $("#task").val(s.action);
            if (s.validate) {
                if (form.valid()) {
                    form.submit();
                }
                else {
                    return false;
                }
            } 
            if (s.trackdirty) {
                if ($("#adminForm").isDirty()) {
                    if (confirm("Сделанные изменения будут потеряны")) {
                        $("#adminForm").submit();
                    } else {
                        return false;
                    }
                }
            }
            if (s.obligate) {
                l = $(".grid_row");
                if (l.length == 1) {
                    $("#adminForm").append('<input type="hidden" name="'+ $(l).attr("name") +'" value="'+ $(l).attr("id") +'" />');
                    if (s.dialog !== false) {
                        s.dialog();
                        return false;
                    }
                    $("#adminForm").submit();
                    return true;
                }
                if ($("td > span.ui-icon-check").length < s.number ) {
                    if (s.number == 1) {
                        alert ("Выберите как минимум " + s.number + " элемент из списка");
                    } else if (s.number > 1 && s.number < 5)  { 
                        alert ("Выберите как минимум " + s.number + " элемента из списка");
                    } else {
                        alert ("Выберите как минимум " + s.number + " элементов из списка");
                    }
                    return false;
                }
            }
            if (s.dialog !== false) {
                s.dialog();
                return false;
            }
            $("#adminForm").submit();
        }
    };

    $.fn.toolbar = function( method ) {
        if ( methods[method] ) {
            return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.toolbar' );
        }
  };
})( jQuery );