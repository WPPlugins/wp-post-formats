
jQuery( document ).ready( function($){

    /**
     * Initialise ACE Editor
     */
    var _editors = document.getElementsByClassName( 'editable' );
    var editors = [];

    var heightUpdateFunction = function() {

        if( editors.length < 1 ) return;

        for( var i = 0; i < editors.length; i++ ){

            var newHeight =
                editors[i].getSession().getScreenLength()
                    * editors[i].renderer.lineHeight
                    + editors[i].renderer.scrollBar.getWidth();

            jQuery( editors[i].container ).height(newHeight.toString() + "px");
            jQuery( editors[i].container ).parent().height(newHeight.toString() + "px");

            editors[i].resize();

        }

    };

    for ( var i = 0; i < _editors.length; i++ ) {

        var editor = ace.edit( _editors[i].id  );
        editor.setTheme("ace/theme/dawn");
        editor.getSession().setMode("ace/mode/twig");

        editors[i] = editor;

        heightUpdateFunction();

        editors[i].getSession().on('change', function( e ){
            heightUpdateFunction();
        });

    }

    $( 'input[name=wppf_options_submit]' ).on( 'click', function( e ){

        if( editors.length < 1 ) return;

        for( var i = 0; i < editors.length; i++ ){
            $( '#' + editors[i].container.id + '_content' ).val( editors[i].getSession().getValue() );
        }

        return true;

    });

});