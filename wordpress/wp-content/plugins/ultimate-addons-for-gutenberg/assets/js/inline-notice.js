// eslint-disable-next-line no-undef
UAGBInlineNotice = {
	init( attr, id ) {
		const main = document.querySelectorAll( id );

		if ( main.length === 0 ) {
			return;
		}

		const uniqueId = attr.c_id;
		const isCookie = attr.cookies;
		const cookiesDays = attr.close_cookie_days;
		const currentCookie = Cookies.get( 'uagb-notice-' + uniqueId );

		for ( const mainWrap of main ) {
			if ( 'undefined' === typeof currentCookie && true === isCookie ) {
				mainWrap.style.display = 'block';
			}
			const noticeDismissClass = mainWrap.querySelector( '.uagb-notice-dismiss' ) || mainWrap.querySelector( 'svg' );
			const closeBtn = noticeDismissClass ? noticeDismissClass : mainWrap.querySelector( 'button[type="button"] svg' );
			if ( '' !== attr.noticeDismiss && '' !== attr.icon ) {
				closeBtn.addEventListener( 'click', function () {
					dismissClick( isCookie, currentCookie, uniqueId, cookiesDays, main );	
				} );
				main[0].addEventListener( 'keydown', function ( e ) {
					if ( e.keyCode === 13 || e.keyCode === 32 ) {	
						const focusedVisibleElement = document.querySelector( id + ' :focus-visible' );
						dismissClick( isCookie, currentCookie, uniqueId, cookiesDays, main, focusedVisibleElement );
					}
				} );
			}
		}
	},
};

function dismissClick( isCookie, currentCookie, uniqueId, cookiesDays, main, focusedVisibleElement ) { 
	if ( true === isCookie && 'undefined' === typeof currentCookie ) {
		Cookies.set( 'uagb-notice-' + uniqueId, true, { expires: cookiesDays } );
	} 
	main[0]?.classList?.add( 'uagb-notice__active' );
	if ( focusedVisibleElement ) {
		const closeDismiss = focusedVisibleElement?.parentElement;
		closeDismiss.style.display = 'none';
	} else {
		main[0].style.display = 'none';
	}
}