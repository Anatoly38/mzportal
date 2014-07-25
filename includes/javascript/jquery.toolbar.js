(function( $ ){

    var methods = {
        init : function( options ) {
            var buttonSettings = {
                form        : 'adminForm',
                action      : 'cancel',
                icon        : 'cancel',
                title       : 'Закрыть',
                showStatus  : true,
                showLabel   : true,
                dialog      : false,
                obligate    : false,
                number      : 1,
                validate    : false,
                trackdirty  : false
            };
            if ( options ) { 
                $.extend( buttonSettings, options );
            }
            //var td = buttonSettings.showLabel ? '<td id="' + buttonSettings.action + '">' + buttonSettings.title +'</td>' :  '<td></td>';
            //var img = '<span class="icon-32-'+buttonSettings.icon+'" title="'+buttonSettings.title+'"></span>';
            //var r = $(this).append(td);
            //var c = $(r).children().last().prepend(img);var td = buttonSettings.showLabel ? '<td id="' + buttonSettings.action + '">' + buttonSettings.title +'</td>' :  '<td></td>';
            //var img = '<span class="icon-32-'+buttonSettings.icon+'" title="'+buttonSettings.title+'"></span>';
            var buttonContent = '<div id="' + buttonSettings.action + '" class="toolbar-button">';
            buttonContent += '<span class="toolbar-image icon-32-'+ buttonSettings.icon +'" title="' + buttonSettings.title+'"></span>'; 
            buttonContent += buttonSettings.title +'</div>';
            var currentContent = $(this).html();
            $(this).html(currentContent + buttonContent);
            $(this).find("#" + buttonSettings.action).click( function () { 
                methods.call(buttonSettings); 
            } );
        },
        call : function(s) { 
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
                if (form.isDirty()) {
                    if (confirm("Сделанные изменения будут потеряны")) {
                        $("#adminForm").submit();
                    } else {
                        return false;
                    }
                }
            }
            if (s.obligate) {
                var message;
                var l = $(".grid_row");
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
                        message = "Выберите как минимум " + s.number + " элемент из списка";
                    } else if (s.number > 1 && s.number < 5)  { 
                        message = "Выберите как минимум " + s.number + " элемента из списка";
                    } else {
                        message = "Выберите как минимум " + s.number + " элементов из списка";
                    }
                    
                    
                    return false;
                }
            }
            if (s.dialog !== false) {
                s.dialog();
                return false;
            }
            $("#adminForm").submit();
        }, 
        showButton : function () {
        
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