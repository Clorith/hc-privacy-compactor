(function ( api ) {
    api.panel( 'hc-privacy-compactor', function( section ) {
        section.expanded.bind( function( isExpanded ) {
            if ( isExpanded ) {
                api.previewer.previewUrl.set( hc_privacy_compactor.privacy_url );
            }
        } );
    } );
} ( wp.customize ) );