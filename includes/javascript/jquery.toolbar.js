(function( $ ){

    var methods = {
        init : function( options ) {
            var buttonSettings = {
                form            : 'adminForm',
                task            : null,
                icon            : null,
                title           : null,
                confirmDelete   : false,
                showStatus      : true,
                showLabel       : true,
                action          : function () { $("#" + buttonSettings.form).submit() },
                obligate        : false,
                number          : 1,
                validate        : false,
                trackdirty      : false
            };
            if ( options ) { 
                $.extend( buttonSettings, options );
            }
            var buttonContent = '<div id="' + buttonSettings.task + '" class="toolbar-button">';
            buttonContent += '<span class="toolbar-image icon-32-'+ buttonSettings.icon +'" title="' + buttonSettings.title+'"></span>'; 
            buttonContent += buttonSettings.title +'</div>';
            $(this).append(buttonContent);
            var button = $("#" + buttonSettings.task);
            if (!buttonSettings.showStatus) {
                button.hide();
            }
            button.click( function () {
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
                    $('<div id="discard-changes" title="Данные изменены"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Сделанные изменения будут потеряны?</p></div>')
                    .appendTo('body').dialog({
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
                                $("#task").val(null);
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
                    $(selection_dialog).appendTo('body').dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                                $("#task").val(null);
                            }
                        }
                    });
                }
                
            }
            if (s.confirmDelete) {
                deleteRows = false;
                var delete_dialog = '<div id="delete-warning" title="Подтвердите действие">';
                delete_dialog += '<p>Выделенные объекты будут удалены. Вы уверены?</p></div>'
                $(delete_dialog).appendTo('body').dialog({
                    resizable: false,
                    height: 170,
                    modal: true,
                    buttons: {
                        "Удалить": function() {
                            $( this ).dialog( "close" );
                            form.submit();
                            return true;
                        },
                        "Отменить": function() {
                            $( this ).dialog( "close" );
                            $("#task").val(null);
                            deleteRows = false;
                            return false;
                        }
                    }
                });
            }
            if ( valid && noDirty && noObligate && deleteRows ) {
                s.action();
            }
        }, 
        showButton : function () {
            $(this).show();
        },
        hideButton : function () {
            $(this).hide();
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