UAGBCounter = {
	elements: {},
	init( mainSelector, data = {} ) {
		this.elements = this.getDefaultElements( mainSelector );
		const elements = document.querySelectorAll( `.wp-block-uagb-counter${mainSelector}` );
        if ( elements && elements.length > 1 ) {
            for( const element of elements ) {
                this.elements.counterWrapper = element;
                this.handleCounterWrapper( data );	
            }
        } else {
            this.handleCounterWrapper( data );
        }   
	},
	handleCounterWrapper( data ) {
		data = this._getCounterData( this.elements.counterWrapper, data );

		if( !data.isFrontend ){
			this.elements.counterWrapper.removeAttribute( 'played' );
		}
		if ( typeof this.elements.counterWrapper !== 'undefined' && this.elements.counterWrapper ) {
			const numberCount = this._numberCount( data );
			this._inViewInit( numberCount, data );
		}
	},
	getDefaultElements( mainSelector ) {
		const counterWrapper = this.getElement( mainSelector );
		return {
			counterWrapper,
		};
	},
	getElement( selector, childSelector = null ) {
		let domElement = document.querySelector( selector );
		if ( domElement ) {
			if ( childSelector ) {
				return domElement.querySelector( childSelector );
			}
		} else {
			const editorCanvas = document.querySelector( 'iframe[name="editor-canvas"]' );
			if ( editorCanvas && editorCanvas.contentDocument ) {
				domElement = editorCanvas.contentDocument.querySelector( selector );
				if ( childSelector ) {
					return ( domElement = domElement.querySelector( childSelector ) );
				}
			}
		}
		return domElement;
	},

	_inViewInit( countUp, data ) {
		const that = this;
		const callback = ( entries ) => {
			entries.forEach( ( entry ) => {
				const el = entry.target;
				const hasPlayed = el.hasAttribute( 'played' ); // Check if an animation has played; If played already, do mot re-trigger it.
				if ( entry.isIntersecting && ! hasPlayed ) {
					if ( ! countUp.error ) {
						if ( data.layout === 'bars' ) {
							that._triggerBar( el, data );
						} else if ( data.layout === 'circle' ) {
							that._triggerCircle( el, data );
						}
						countUp.start();
					} else {
						console.error( countUp.error ); // eslint-disable-line no-console
					}
				}
			} );
		};
		const IO = new IntersectionObserver( callback, { threshold: 0.75 } );
		IO.observe( that.elements.counterWrapper );
	},
	_numberCount( data ) {
		const that = this;
		const el = this.elements.counterWrapper.querySelector( '.uagb-counter-block-number' );
		if ( typeof el !== 'undefined' && el ) {
			const countUp = new window.countUp.CountUp( el, that._getEndNumber( data ), {
				startVal: that._getStartNumber( data ),
				duration: that._getAnimationDuration( data ),
				separator: data.thousandSeparator,
				useEasing: false,
				decimalPlaces: data.decimalPlaces,
			} );
			return countUp;
		}
	},

	_triggerBar( el, data ) {
		const that = this;
		const parentWrapClass = 'wp-block-uagb-counter--bars';
		const numberWrap = el.querySelector( '.wp-block-uagb-counter__number' );
		const duration = that._getAnimationDurationForCSS( data );
		const startWidth =
			that._getStartNumber( data ) < that._getTotalNumber( data )
				? Math.ceil( ( that._getStartNumber( data ) / that._getTotalNumber( data ) ) * 100 )
				: 100;
		const endWidth =
			that._getEndNumber( data ) <= that._getTotalNumber( data )
				? Math.ceil( ( that._getEndNumber( data ) / that._getTotalNumber( data ) ) * 100 )
				: 100;

		const animationKeyframes = [ { width: startWidth + '%' }, { width: endWidth + '%' } ];

		const animationProperties = {
			duration,
			fill: 'forwards',
		};

		// Condition to prevent an edge case bug where number layout gets animated like bar layout.
		if ( el.classList.contains( parentWrapClass ) ) {
			numberWrap?.animate( animationKeyframes, animationProperties );
		}

		el.setAttribute( 'played', true ); // Set: animation has played once.
	},

	_triggerCircle( el, data ) {
		const that = this;
		const circleWrap = el.querySelector(
			'.wp-block-uagb-counter-circle-container svg .uagb-counter-circle__progress'
		);

		const diameter = data.circleSize - data.circleStokeSize;
		const circumference = Math.PI * diameter;
		const totalNumber = that._getTotalNumber( data );

		let startPoint = 100 * ( that._getStartNumber( data ) / totalNumber );
		startPoint = startPoint < 100 ? startPoint : 100;
		startPoint = 100 - startPoint;
		startPoint = ( startPoint / 100 ) * circumference;

		let endPoint = 100 * ( that._getEndNumber( data ) / totalNumber );
		endPoint = endPoint < 100 ? endPoint : 100;
		endPoint = 100 - endPoint;
		endPoint = ( endPoint / 100 ) * circumference;

		const duration = that._getAnimationDurationForCSS( data );

		const animationKeyframes = [ { strokeDashoffset: startPoint + 'px' }, { strokeDashoffset: endPoint + 'px' } ];

		const animationProperties = {
			duration,
			fill: 'forwards',
		};

		circleWrap?.animate( animationKeyframes, animationProperties );

		el.setAttribute( 'played', true ); // Set: animation has played once.
	},

	_getAnimationDuration( data ) {
		return data.animationDuration / 1000;
	},

	_getAnimationDurationForCSS( data ) {
		return data.animationDuration;
	},

	_getStartNumber( data ) {
		if ( isNaN( data.startNumber ) ) {
			return parseFloat( 0 );
		}

		return data.startNumber || parseFloat( data.startNumber ) === parseFloat( 0 )
			? parseFloat( data.startNumber )
			: parseFloat( 0 );
	},
	_getEndNumber( data ) {
		if ( isNaN( data.endNumber ) ) {
			return parseFloat( 80 );
		}

		return data.endNumber || parseFloat( data.startNumber ) === parseFloat( 0 )
			? parseFloat( data.endNumber )
			: parseFloat( 80 );
	},
	_getTotalNumber( data ) {
		if ( isNaN( data.startNumber ) ) {
			return parseFloat( 0 );
		}

		return ( data.totalNumber || parseFloat( data.startNumber ) === parseFloat( 0 ) ) ? parseFloat( data.totalNumber ) : parseFloat( 100 );
	},
	_getCounterData( element,data ){
		
		// Getting data from html attribute data-counter and overwrite data which comes from php.
		let getCounterData = element?.getAttribute( 'data-counter' );
		if( ! getCounterData ){
			return data;
		}

		getCounterData = JSON.parse( getCounterData );
		if( getCounterData ){
			data = { ...data, ...getCounterData};
		}
		return data;
	}
};
