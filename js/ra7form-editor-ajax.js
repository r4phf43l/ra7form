function saveData( messages ) {
    messages = messages;
    let ajax = new XMLHttpRequest();
    let data = {
		action: 'ra7form_ajax_save',
		_ajax_nonce: ra7form_globals._ajax_nonce,
        id: ra7form_globals._id,
        sender: document.querySelector( '#sender' ).value,
        recipient: document.querySelector( '#recipient' ).value,
        content: newContent( mountField( mails ) )
	};
    ajax.open( 'POST', ra7form_globals.ajax_url, true );
    ajax.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
    ajax.send( objToQueryString(data) );
    ajax.onreadystatechange = function() {
        if ( ajax.readyState == 4 && ajax.status == 200 ) {
            let msg = ( Object.values( JSON.parse( ajax.responseText ) ).find( e => e == true ) ) ? `success` : `error`;
            document.querySelector( '.wrap > h2' ).insertAdjacentHTML ( 'afterend', `<div id=\'ajax-message\' class=\'notice notice-${msg} is-dismissible\'><p>${messages[msg]}</p><button type=\'button\' class=\'notice-dismiss\' onClick=\'javascript:closeMsg();\'><span class=\'screen-reader-text\'>${__( 'Close this warning.', 'ra7form' )}</span></button></div>` );
            document.querySelector( '#publishing-action .spinner' ).style.visibility = 'hidden';
        } else if( ajax.readyState == 4 ) {
            console.log( `State: ${ajax.readyState} / Status: ${ajax.status}` );
        }
    }    
}
const closeMsg = () => document.querySelector( '#ajax-message' ).remove();
