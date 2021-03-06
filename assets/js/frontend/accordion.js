jQuery( document ).ready(function( $ ) {
    $( '.hc-accordion-trigger' ).click(function() {
        var isExpanded = ( 'true' === $( this ).attr( 'aria-expanded' ) );

        if ( isExpanded ) {
            $( this ).attr( 'aria-expanded', 'false' );
            $( '#' + $( this ).attr( 'aria-controls' ) ).attr( 'hidden', true );
        } else {
            $( this ).attr( 'aria-expanded', 'true' );
            $( '#' + $( this ).attr( 'aria-controls' ) ).attr( 'hidden', false );
        }
    });

    $( '.hc-accordion' ).on( 'keyup', '.hc-accordion-trigger', function( e ) {
        if ( '38' === e.keyCode.toString() ) {
            $( '.hc-accordion-trigger', $( this ).closest( 'dt' ).prevAll( 'dt' ) ).focus();
        } else if ( '40' === e.keyCode.toString() ) {
            $( '.hc-accordion-trigger', $( this ).closest( 'dt' ).nextAll( 'dt' ) ).focus();
        }
    });
});
