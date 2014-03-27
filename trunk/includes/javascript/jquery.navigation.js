(function( $ ){

    var methods = {
        init : function( options ) {
        var settings = {
            form        : 'adminForm',
            container   : 'toolbar-container',
            name        : 'cancel',
            icon        : 'icon-32-cancel',
            title       : 'Закрыть',
            dialog      : false,
            validate    : false
        };
        return this.each(function() {
            if ( options ) { 
                $.extend( settings, options );
            }
        }
        // Plugin code here
    },
        create  : function( ) {  },
        call    : function( ) {  },
        destroy : function( ) {  }
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