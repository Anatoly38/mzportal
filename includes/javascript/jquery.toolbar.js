(function( $ ){

    var methods = {
        init : function( options ) {
            var buttonSettings = {
                form        : 'adminForm',
                task        : null,
                icon        : null,
                title       : null,
                showStatus  : true,
                showLabel   : true,
                action      : false,
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
            var buttonContent = '<div id="' + buttonSettings.task + '" class="toolbar-button">';
            buttonContent += '<span class="toolbar-image icon-32-'+ buttonSettings.icon +'" title="' + buttonSettings.title+'"></span>'; 
            buttonContent += buttonSettings.title +'</div>';
            //var currentContent = $(this).html();
            $(this).append(buttonContent);
            var button = $("#" + buttonSettings.task);
            button.click( function () {
            //$(this).find("#" + buttonSettings.action).click( function () { 
                methods.call(buttonSettings); 
            } );
        },
        call : function(s) { 
            form = $("#"+s.form);
            var valid = true,
            noDirty = true,
            noObligate = true,
            deleteRows = true; 
            $("#task").val(s.task);
            if (s.validate) {
                if (!form.valid()) {
                    valid = false;

                } 
            }
            if (s.trackdirty) {
                if (form.isDirty()) {
                    noDirty = false;
                    form.append('<div id="discard-changes" title="Данные изменены"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Сделанные изменения будут потеряны?</p></div>');
                    $( "#discard-changes" ).dialog({
                        resizable: false,
                        width: 450,
                        height:160,
                        modal: true,
                        buttons: {
                            "Покинуть страницу": function() {
                                noDirty = true;
                                $(this).dialog( "close" );
                                form.submit();
                                return true;
                            },
                            "Продолжить редактирование": function() {
                                noDirty = false;
                                $(this).dialog( "close" );
                            }
                        }
                    });
                }
            }
            if (s.obligate) {
                var message;
                var l = $(".grid_row");
                if (l.length == 1) {
                    form.append('<input type="hidden" name="'+ $(l).attr("name") +'" value="'+ $(l).attr("id") +'" />'); // Нужно изменить - тут единственную строку выделить.
                    //if (typeof s.dialog === 'function') {
                    //    s.dialog();
                    //}
                }
                if ($("td > span.ui-icon-check").length < s.number ) {
                    noObligate = false;
                    if (s.number == 1) {
                        message = "Выберите как минимум " + s.number + " элемент из списка";
                    } else if (s.number > 1 && s.number < 5)  { 
                        message = "Выберите как минимум " + s.number + " элемента из списка";
                    } else {
                        message = "Выберите как минимум " + s.number + " элементов из списка";
                    }
                    var selection_dialog = '<div id="selection-warning" title="Не выбраны объекты">';
                    selection_dialog +=  '<p>' + message + '</p></div>';
                    form.append(selection_dialog);                    
                    $( "#selection-warning" ).dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                }
                
            }
            if (typeof s.action === 'function') {
                s.action();
            }
            if (valid && noDirty && noObligate && deleteRows) {
                form.submit();
            }
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