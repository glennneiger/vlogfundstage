var DDLayout = DDLayout || {};
DDLayout.DDL_TabsCellFrontend = function($){

    self.init = function(){
        self.fixActiveClasses();
    };

    self.fixActiveClasses = function(){

        if( jQuery( '.tab-content' ).length === 0 ){
            return;
        }

        jQuery.each( jQuery( '.tab-content' ), function( key, value ) {
            jQuery(value).find( '.tab-pane' ).first().addClass( 'active' );
        });
    };

    self.init();

};

(function ($) {
    jQuery(function ($) {
        new DDLayout.DDL_TabsCellFrontend($);
    });
}(jQuery));

