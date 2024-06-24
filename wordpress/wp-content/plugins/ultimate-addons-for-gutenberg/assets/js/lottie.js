UAGBLottie = {
	getElement : ( id ) => {
		// Check if the script has run once already on the given element (required for homepage sidebar usage case).
		const getJsELement = document.querySelector( `.${id}:not(.uagb-activated-script)` );
		if( ! getJsELement ) return null;

		// Ensures that the script only runs once on the given element (required for homepage sidebar usage case).
		getJsELement.classList.add( 'uagb-activated-script' );
		return getJsELement;
	}, 
	_run( attr, id ) {
		const getLottieElement = UAGBLottie.getElement( id );
		if( ! getLottieElement ){
			return;
		}

		const animation = bodymovin.loadAnimation( {
			container: getLottieElement,
			renderer: 'svg',
			loop: attr.loop,
			autoplay: 'none' === attr.playOn ? true : false,
			path: attr.lottieURl,
			rendererSettings: {
				preserveAspectRatio: 'xMidYMid',
				className: 'uagb-lottie-inner-wrap',
			},
		} );

		animation.setSpeed( attr.speed );

		const reversedir = attr.reverse && attr.loop ? -1 : 1;

		animation.setDirection( reversedir );

		if ( 'hover' === attr.playOn ) {
			getLottieElement.addEventListener( 'mouseenter', function () {
				animation.play();
			} );
			getLottieElement.addEventListener( 'mouseleave', function () {
				animation.stop();
			} );
		} else if ( 'click' === attr.playOn ) {
			getLottieElement.addEventListener( 'click', function () {
				animation.stop();
				animation.play();
			} );
		} else if ( 'scroll' === attr.playOn ) {
			window.addEventListener( 'scroll', function () {
				animation.stop();
				animation.play();
			} );
		}
	},
};
