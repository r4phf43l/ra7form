//Array Functions
const { __, _x, _n, _nx } = wp.i18n;
const getRecipients = ( recipient ) => (
    Object.entries( Array.from( document.querySelectorAll( recipient ) ).reduce( ( a, e, i ) => (
    a[e.value] = Array.from( document.querySelectorAll( `#tag_${i}>.button` ) ).map( ( f ) => ( f.textContent ) ), a ), {} ) ) );
const mountField = ( a ) => ( a.map( ( [ k, v ] ) => ( `"${k} | ${v.join(', ')}"` || `` ) ).join(' ') );
const returnIndexByKeyName = ( a, n ) => Array.from( a ).map( e => e[0] ).indexOf( n );
const returnValueByIndex = ( a, n ) => a[1].indexOf( n );
const removeItem = ( a, i ) => a.splice( i, 1 );
const removeInItem = ( a, i, q ) => a[i][1].splice( q, 1 );
const addItem = ( a, n ) => a.push( [n] );
const updateItem = ( a, i, v ) => a[i][0] = v;
const addInItem = ( a, i, n ) => ( ( ( a[i][1] ) || ( a[i][1] = [] ) ).push( [n] ), a[i][1] = ( a[i][1] ).filter( e => e != '' ) );
const objToQueryString = obj => ( Object.keys(obj).map( k => k + '=' + obj[k]).join('&') );

const newContent = ( s ) => {
    let q = document.querySelector( '#recipient' );
    q.value = ( ! ( /\[your-recipient\]/ ).test( q.value ) ) ? ( q.value != '' ? `${q.value}, [your-recipient]` : "[your-recipient]" ) : q.value;
    let v = `[select${( ( document.querySelector( '#required' ).checked ) ? '*' : '' )} your-recipient ${s}]`;
    let w = /\[select(?:\*|) your-recipient((.|\n|\r)*?)\]/;
    let y = document.querySelector( '#hiddeninside' );
    let x = y.value.match( /\[select(?:\*|) your-recipient((.|\n|\r)*?)\]/ );
    return y.value.replace( ( x ? w : /\[submit((.|\n|\r)*?)\]/ ), ( x ? v : m => v + m ) );
};

//Static Buttons
const mails = getRecipients( `[id^='titleRecipient_']` );
const addRecipientButton = document.querySelector( `#addRecipient` );
const saveButton = document.querySelector( `#saveButton` );

//Dynamic Buttons
const allItems = document.querySelectorAll( `#postbox-container-3 tbody` );

//Visual Actions
const visualRemoveItem = ( t ) => {
    let r = t.getAttribute( 'rel' );
    if( confirm( `${__( 'Did you realy want to remove this recipient?', 'ra7form' )}\n${__( 'All emails related will be removed', 'ra7form' )}\n\n${__( 'This will take effect only after you click on \'Save recipient\' button', 'ra7form' )}` ) === true ) {
        document.querySelector( `#cp_${r}` ).remove();
        document.querySelector( `#rp_${r}` ).remove();
        removeItem( mails, r );
    }
}

const visualRemoveInItem = ( t ) => (
    removeInItem( mails,
        t.parentElement.parentElement.getAttribute( 'rel' ),
        returnValueByIndex( mails[t.parentElement.parentElement.getAttribute( 'rel' )],
        t.parentElement.textContent )
    ), t.parentElement.remove()
);

const visualAddInItem = ( t ) => {
    let j = t.getAttribute( 'rel' );
    let v = document.querySelector( `#mailRecipient_${j}` ).value;
    let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if( re.test( String( v ).toLowerCase() ) && ! mails[j][1].find( e => e == v ) ) {
        document.querySelector( `#tag_${j}` ).insertAdjacentHTML ( 'beforeend', `<span class=\'thickbox button\'><span class=\'dashicons dashicons-trash removeInItem\'>${v}</span></span>` );
        addInItem( mails, j, v );
        document.querySelector( `#mailRecipient_${j}` ).value = '';
        document.querySelector( `#mailRecipient_${j}` ).focus();
    } else {
        alert (`${__( 'Insert a valid email', 'ra7form' )}\n${__( 'Maybe this email is duplicated', 'ra7form' )}`);
        document.querySelector( `#mailRecipient_${j}` ).focus();
    }
}

//Static Actions
addRecipientButton.addEventListener( 'click', () => {
    let ni = mails.length;
    addItem( mails, __( 'New recipient', 'ra7form' ) );
    addInItem( mails, ni, '' );
    document.querySelector( `#postbox-container-3 tbody` ).insertAdjacentHTML( 'afterbegin', `<tr id=\'rp_${ni}\' class=\'recipient-prop\'><td><a class=\'remRec\'><span class=\'dashicons dashicons-trash removeItem\' rel=\'${ni}\'></span></a><input type=\'text\' id=\'titleRecipient_${ni}\' value=\'${__( 'New recipient', 'ra7form' )}\' class=\'widefat\'></td></tr><tr id=\'cp_${ni}\'><td><span id=\'tag_${ni}\' class=\'tag\' rel=\'${ni}\'><span class=\'inputSpan\'><input type=\'text\' id=\'mailRecipient_${ni}\' value=\'\' class=\'thick-box\'><a id=\'addMail_${ni}\'><span rel=\'${ni}\'class=\'dashicons dashicons-plus addInItem\'></span></a></span></td></tr>` );
} );

saveButton.addEventListener( 'click', () => {
    document.querySelector( '#publishing-action .spinner' ).style.visibility = 'visible';
    let s = mountField( mails );
    if( s != '' ) {
        newContent( s );
    }
    saveData({
        success: __( 'Changes saved.', 'ra7form' ),
        error: __( 'Something goes wrong and it has not be saved', 'ra7form' )
    });
} );

//Dynamic Actions
allItems.forEach( ( e, i ) => {
    allItems[i].addEventListener( 'click', e => {
        if( e.target && e.target.classList.contains( 'removeItem' ) ) {
            visualRemoveItem( e.target );
        }
        if( e.target && e.target.classList.contains( 'removeInItem' ) ) {
            visualRemoveInItem( e.target );
        }
        if( e.target && e.target.classList.contains( 'addInItem' ) ) {
            visualAddInItem( e.target );
        }
    } );
    allItems[i].addEventListener( 'focusout', e => {
        if( e.target && e.target.id.match( '\^titleRecipient_' ) ) {
            let j = e.target.id.split('_');
            updateItem( mails, j[1], e.target.value );
        }
    } );
    allItems[i].addEventListener( 'keyup', e => {
        if( e.target && e.target.id.match( '\^mailRecipient_' ) && e.code == 'Enter' ) {
            let j = e.target.id.split('_');
            visualAddInItem( document.querySelector( `#addMail_${j[1]} > span` ) );
        }
    } );
} );

window.addEventListener( 'load', () => document.querySelector( '#saveButton' ).disabled = false );
