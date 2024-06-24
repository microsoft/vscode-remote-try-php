/**
 * === Whats New RSS ===
 *
 * Version: 1.0.3
 * Generated on: 14th May, 2024
 * Documentation: https://github.com/brainstormforce/whats-new-rss/blob/master/README.md
 */

import { useEffect } from 'react';
// import './whats-new-rss.scss';
var __assign =
	( this && this.__assign ) ||
	function () {
		__assign =
			Object.assign ||
			function ( t ) {
				for ( var s, i = 1, n = arguments.length; i < n; i++ ) {
					s = arguments[ i ];
					for ( var p in s )
						if ( Object.prototype.hasOwnProperty.call( s, p ) )
							t[ p ] = s[ p ];
				}
				return t;
			};
		return __assign.apply( this, arguments );
	};
var __awaiter =
	( this && this.__awaiter ) ||
	function ( thisArg, _arguments, P, generator ) {
		function adopt( value ) {
			return value instanceof P
				? value
				: new P( function ( resolve ) {
						resolve( value );
				  } );
		}
		return new ( P || ( P = Promise ) )( function ( resolve, reject ) {
			function fulfilled( value ) {
				try {
					step( generator.next( value ) );
				} catch ( e ) {
					reject( e );
				}
			}
			function rejected( value ) {
				try {
					step( generator[ 'throw' ]( value ) );
				} catch ( e ) {
					reject( e );
				}
			}
			function step( result ) {
				result.done
					? resolve( result.value )
					: adopt( result.value ).then( fulfilled, rejected );
			}
			step(
				( generator = generator.apply(
					thisArg,
					_arguments || []
				) ).next()
			);
		} );
	};
var __generator =
	( this && this.__generator ) ||
	function ( thisArg, body ) {
		var _ = {
				label: 0,
				sent: function () {
					if ( t[ 0 ] & 1 ) throw t[ 1 ];
					return t[ 1 ];
				},
				trys: [],
				ops: [],
			},
			f,
			y,
			t,
			g;
		return (
			( g = { next: verb( 0 ), throw: verb( 1 ), return: verb( 2 ) } ),
			typeof Symbol === 'function' &&
				( g[ Symbol.iterator ] = function () {
					return this;
				} ),
			g
		);
		function verb( n ) {
			return function ( v ) {
				return step( [ n, v ] );
			};
		}
		function step( op ) {
			if ( f ) throw new TypeError( 'Generator is already executing.' );
			while ( ( g && ( ( g = 0 ), op[ 0 ] && ( _ = 0 ) ), _ ) )
				try {
					if (
						( ( f = 1 ),
						y &&
							( t =
								op[ 0 ] & 2
									? y[ 'return' ]
									: op[ 0 ]
									? y[ 'throw' ] ||
									  ( ( t = y[ 'return' ] ) && t.call( y ),
									  0 )
									: y.next ) &&
							! ( t = t.call( y, op[ 1 ] ) ).done )
					)
						return t;
					if ( ( ( y = 0 ), t ) ) op = [ op[ 0 ] & 2, t.value ];
					switch ( op[ 0 ] ) {
						case 0:
						case 1:
							t = op;
							break;
						case 4:
							_.label++;
							return { value: op[ 1 ], done: false };
						case 5:
							_.label++;
							y = op[ 1 ];
							op = [ 0 ];
							continue;
						case 7:
							op = _.ops.pop();
							_.trys.pop();
							continue;
						default:
							if (
								! ( ( t = _.trys ),
								( t = t.length > 0 && t[ t.length - 1 ] ) ) &&
								( op[ 0 ] === 6 || op[ 0 ] === 2 )
							) {
								_ = 0;
								continue;
							}
							if (
								op[ 0 ] === 3 &&
								( ! t ||
									( op[ 1 ] > t[ 0 ] && op[ 1 ] < t[ 3 ] ) )
							) {
								_.label = op[ 1 ];
								break;
							}
							if ( op[ 0 ] === 6 && _.label < t[ 1 ] ) {
								_.label = t[ 1 ];
								t = op;
								break;
							}
							if ( t && _.label < t[ 2 ] ) {
								_.label = t[ 2 ];
								_.ops.push( op );
								break;
							}
							if ( t[ 2 ] ) _.ops.pop();
							_.trys.pop();
							continue;
					}
					op = body.call( thisArg, _ );
				} catch ( e ) {
					op = [ 6, e ];
					y = 0;
				} finally {
					f = t = 0;
				}
			if ( op[ 0 ] & 5 ) throw op[ 1 ];
			return { value: op[ 0 ] ? op[ 1 ] : void 0, done: true };
		}
	};
var WhatsNewRSSDefaultArgs = {
	rssFeedURL: '',
	selector: '',
	loaderIcon:
		'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">\n\t<circle cx="50" cy="50" fill="none" stroke="#9f9f9f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138">\n\t\t<animateTransform attributeName="transform" type="rotate" repeatCount="indefinite" dur="1s" values="0 50 50;360 50 50" keyTimes="0;1"></animateTransform>\n\t</circle>\n\t</svg>',
	viewAll: {
		link: '',
		label: 'View All',
	},
	triggerButton: {
		icon: '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.61703 13.1998C8.04294 13.1503 7.46192 13.125 6.875 13.125H6.25C4.17893 13.125 2.5 11.4461 2.5 9.375C2.5 7.30393 4.17893 5.625 6.25 5.625H6.875C7.46192 5.625 8.04294 5.59972 8.61703 5.55018M8.61703 13.1998C8.82774 14.0012 9.1031 14.7764 9.43719 15.5195C9.64341 15.9782 9.48685 16.5273 9.05134 16.7787L8.50441 17.0945C8.04492 17.3598 7.45466 17.1921 7.23201 16.7106C6.70983 15.5811 6.30451 14.3866 6.03155 13.1425M8.61703 13.1998C8.29598 11.9787 8.125 10.6968 8.125 9.375C8.125 8.05316 8.29598 6.77125 8.61703 5.55018M8.61703 13.1998C11.25 13.427 13.737 14.1643 15.9789 15.3124M8.61703 5.55018C11.25 5.323 13.737 4.58569 15.9789 3.43757M15.9789 3.43757C15.8808 3.12162 15.7751 2.80903 15.662 2.5M15.9789 3.43757C16.4247 4.87356 16.7131 6.37885 16.8238 7.93326M15.9789 15.3124C15.8808 15.6284 15.7751 15.941 15.662 16.25M15.9789 15.3124C16.4247 13.8764 16.7131 12.3711 16.8238 10.8167M16.8238 7.93326C17.237 8.2772 17.5 8.79539 17.5 9.375C17.5 9.95461 17.237 10.4728 16.8238 10.8167M16.8238 7.93326C16.8578 8.40942 16.875 8.8902 16.875 9.375C16.875 9.8598 16.8578 10.3406 16.8238 10.8167" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		beforeBtn: '',
		afterBtn: '',
		className: '',
		onClick: function () {},
	},
	notification: {
		setLastPostUnixTime: null,
		getLastPostUnixTime: null,
	},
	flyout: {
		title: "What's New?",
		excerpt: {
			wordLimit: 500,
			moreSymbol: '&hellip;',
			readMore: {
				label: 'Read More',
				className: '',
			},
		},
		className: '',
		closeOnEsc: true,
		closeOnOverlayClick: true,
		closeBtnIcon:
			'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6 18L18 6M6 6L18 18" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		formatDate: null,
		onOpen: function () {},
		onClose: function () {},
		onReady: function () {},
	},
};
var WhatsNewRSS = /** @class */ ( function () {
	/**
	 * Initialize our class.
	 *
	 * @param {ConstructorArgs} args
	 */
	function WhatsNewRSS( args ) {
		this.rssFeedURLs = [];
		/**
		 * UnixTime stamp of the last seen or read post.
		 */
		this.lastPostUnixTime = 0;
		/**
		 * UnixTime stamp of the last seen or read post for multi feeds by feed key.
		 */
		this.multiLastPostUnixTime = {};
		/**
		 * Total number of new notification counts.
		 */
		this.notificationsCount = 0;
		/**
		 * Notification counts for multi feeds by feed key.
		 */
		this.multiNotificationCount = {};
		/**
		 * Check if has new feeds.
		 */
		this.hasNewFeeds = false;
		/**
		 * Check if has new feeds in multi feeds mode.
		 */
		this.multiHasNewFeeds = {};
		this.validateArgs( args );
		this.parseDefaults( args );
		this.setElement();
		this.setID();
		this.setRSSFeedURLs();
		WhatsNewRSSCacheUtils.setInstanceID( this.getID() );
		this.RSS_Fetch_Instance = new WhatsNewRSSFetch( this );
		this.RSS_View_Instance = new WhatsNewRSSView( this );
		this.setNotificationsCount();
		this.setTriggers();
	}
	/**
	 * Validate the passed arguments in constructor.
	 *
	 * @param {ConstructorArgs} args
	 */
	WhatsNewRSS.prototype.validateArgs = function ( args ) {
		[ 'rssFeedURL', 'selector' ].map( function ( requiredArg ) {
			if ( ! args[ requiredArg ] ) {
				throw new Error(
					''.concat(
						requiredArg,
						' is a required argument. It cannot be empty or undefined.'
					)
				);
			}
			switch ( requiredArg ) {
				case 'rssFeedURL':
					var arg = args[ requiredArg ];
					if ( Array.isArray( arg ) ) {
						arg.forEach( function ( rssFeedURL ) {
							if (
								! ( rssFeedURL === null || rssFeedURL === void 0
									? void 0
									: rssFeedURL.key )
							) {
								throw new Error(
									'The parameter "key" is required for "'.concat(
										requiredArg,
										'" parameter in multi-feed mode.'
									)
								);
							}
							if ( rssFeedURL.key.includes( ' ' ) ) {
								throw new Error(
									'The parameter "key" cannot have spaces for "'
										.concat(
											requiredArg,
											'" parameter in multi-feed mode. Ref Key: "'
										)
										.concat( rssFeedURL.key, '"' )
								);
							}
						} );
					}
					break;
				default:
					break;
			}
		} );
	};
	/**
	 * Parse the arguments passed by the user with the defaults.
	 *
	 * @param {ConstructorArgs} args
	 */
	WhatsNewRSS.prototype.parseDefaults = function ( args ) {
		var _a;
		this.args = __assign(
			__assign( __assign( {}, WhatsNewRSSDefaultArgs ), args ),
			{
				viewAll: __assign(
					__assign( {}, WhatsNewRSSDefaultArgs.viewAll ),
					args === null || args === void 0 ? void 0 : args.viewAll
				),
				triggerButton: __assign(
					__assign( {}, WhatsNewRSSDefaultArgs.triggerButton ),
					args === null || args === void 0
						? void 0
						: args.triggerButton
				),
				flyout: __assign(
					__assign(
						__assign( {}, WhatsNewRSSDefaultArgs.flyout ),
						args === null || args === void 0 ? void 0 : args.flyout
					),
					{
						excerpt: __assign(
							__assign(
								{},
								WhatsNewRSSDefaultArgs.flyout.excerpt
							),
							( _a =
								args === null || args === void 0
									? void 0
									: args.flyout ) === null || _a === void 0
								? void 0
								: _a.excerpt
						),
					}
				),
			}
		);
	};
	/**
	 * Returns parsed args.
	 *
	 * @returns {ConstructorArgs}
	 */
	WhatsNewRSS.prototype.getArgs = function () {
		return this.args;
	};
	/**
	 * Sets the HTML element queried using passed selector.
	 */
	WhatsNewRSS.prototype.setElement = function () {
		this.element = document.querySelector( this.args.selector );
	};
	/**
	 * Returns the html element according to the selector.
	 *
	 * @returns {HTMLElement}
	 */
	WhatsNewRSS.prototype.getElement = function () {
		return this.element;
	};
	/**
	 * Creates unique ID for current instance, that can be used by the library elements.
	 */
	WhatsNewRSS.prototype.setID = function () {
		var data = [ this.getArgs().selector ];
		var rssFeedURL = this.getArgs().rssFeedURL;
		if ( Array.isArray( rssFeedURL ) ) {
			rssFeedURL.forEach( function ( _rssFeedURL ) {
				data.push( _rssFeedURL.key );
			} );
		} else {
			data.push( rssFeedURL );
		}
		this.ID = btoa( data.join( '-' ) ).slice( -12 ).replace( /=/g, '' );
	};
	/**
	 * Whether or not multiple feed urls is provided or not.
	 *
	 * @returns {boolean}
	 */
	WhatsNewRSS.prototype.isMultiFeedRSS = function () {
		return 'string' !== typeof this.getArgs().rssFeedURL;
	};
	WhatsNewRSS.prototype.setRSSFeedURLs = function () {
		var _this = this;
		var rssFeedURL = this.getArgs().rssFeedURL;
		if ( ! this.isMultiFeedRSS() ) {
			this.rssFeedURLs.push( {
				key: null,
				label: '',
				url: rssFeedURL.toString(),
			} );
		} else {
			if ( Array.isArray( rssFeedURL ) ) {
				rssFeedURL.forEach( function ( _item ) {
					_this.rssFeedURLs.push( _item );
				} );
			}
		}
	};
	WhatsNewRSS.prototype.getRSSFeedURLs = function () {
		return this.rssFeedURLs;
	};
	/**
	 * Returns the current instance unique ID.
	 *
	 * @returns {string}
	 */
	WhatsNewRSS.prototype.getID = function () {
		return this.ID;
	};
	/**
	 * Checks and counts new notification for the notification badge.
	 */
	WhatsNewRSS.prototype.setNotificationsCount = function () {
		return __awaiter( this, void 0, void 0, function () {
			var _this = this;
			return __generator( this, function ( _a ) {
				switch ( _a.label ) {
					case 0:
						return [
							4 /*yield*/,
							Promise.all(
								this.getRSSFeedURLs().map( function ( _a ) {
									var key = _a.key;
									return __awaiter(
										_this,
										void 0,
										void 0,
										function () {
											var lastPostUnixTime;
											return __generator(
												this,
												function ( _b ) {
													switch ( _b.label ) {
														case 0:
															lastPostUnixTime = 0;
															if (
																! (
																	'function' ===
																	typeof this.getArgs()
																		.notification
																		.getLastPostUnixTime
																)
															)
																return [
																	3 /*break*/,
																	2,
																];
															return [
																4 /*yield*/,
																this.getArgs().notification.getLastPostUnixTime(
																	key,
																	this
																),
															];
														case 1:
															lastPostUnixTime =
																_b.sent();
															return [
																3 /*break*/, 3,
															];
														case 2:
															lastPostUnixTime =
																WhatsNewRSSCacheUtils.getLastPostUnixTime(
																	key
																);
															_b.label = 3;
														case 3:
															if (
																this.isMultiFeedRSS()
															) {
																this.multiLastPostUnixTime[
																	key
																] =
																	+lastPostUnixTime;
															} else {
																this.lastPostUnixTime =
																	+lastPostUnixTime;
															}
															return [
																2 /*return*/,
															];
													}
												}
											);
										}
									);
								} )
							),
						];
					case 1:
						_a.sent();
						return [
							4 /*yield*/,
							this.RSS_Fetch_Instance.fetchData().then( function (
								res
							) {
								Object.keys( res ).forEach( function ( key ) {
									var data = res[ key ];
									if ( ! data.length ) {
										return;
									}
									_this.multiNotificationCount[ key ] = 0;
									var currentPostUnixTime = +data[ 0 ].date;
									var lastPostUnixTime =
										_this.isMultiFeedRSS()
											? _this.multiLastPostUnixTime[ key ]
											: _this.lastPostUnixTime;
									if (
										currentPostUnixTime > lastPostUnixTime
									) {
										data.forEach( function ( item ) {
											if (
												item.date > lastPostUnixTime
											) {
												if ( _this.isMultiFeedRSS() ) {
													_this
														.multiNotificationCount[
														key
													]++;
													_this.multiHasNewFeeds[
														key
													] = true;
												}
												// Keep a record of total notifications even in multi-feed mode.
												_this.notificationsCount++;
												_this.hasNewFeeds = true;
											}
										} );
										_this.RSS_View_Instance.setNotification(
											_this.notificationsCount
										);
									}
								} );
							} ),
						];
					case 2:
						_a.sent();
						return [ 2 /*return*/ ];
				}
			} );
		} );
	};
	/**
	 * Returns total number of new notifications.
	 *
	 * @returns {number}
	 */
	WhatsNewRSS.prototype.getNotificationsCount = function () {
		return this.notificationsCount;
	};
	/**
	 * Sets the triggers for the library, eg: close, open, fetch.
	 */
	WhatsNewRSS.prototype.setTriggers = function () {
		var _this = this;
		var triggerButton = document.getElementById(
			this.RSS_View_Instance.getTriggerButtonID()
		);
		var flyout = document.getElementById(
			this.RSS_View_Instance.getFlyoutID()
		);
		var flyoutInner = flyout.querySelector(
			'.whats-new-rss-flyout-inner-content'
		);
		var flyoutCloseBtn = document.getElementById(
			this.RSS_View_Instance.getFlyoutCloseBtnID()
		);
		var multiFeedNav = document.getElementById(
			this.RSS_View_Instance.getFlyoutMultiFeedNavID()
		);
		var injectContents = function ( key ) {
			/**
			 * Fetch data on flyout open.
			 */
			_this.RSS_Fetch_Instance.fetchData().then( function ( res ) {
				flyoutInner.innerHTML = '';
				var data = res[ key ];
				if ( ! data.length ) {
					return;
				}
				var currentPostUnixTime = +data[ 0 ].date;
				var lastPostUnixTime = _this.isMultiFeedRSS()
					? _this.multiLastPostUnixTime[ key ]
					: _this.lastPostUnixTime;
				data.forEach( function ( item ) {
					var isNewPost = !! lastPostUnixTime
						? item.date > lastPostUnixTime
						: false;
					var innerContent =
						'\n\t\t\t\t\t\t\t\t<div class="rss-content-header">\n\t\t\t\t\t\t\t\t\t<p>'
							.concat(
								_this.RSS_View_Instance.formatDate(
									new Date( item.date )
								),
								'</p>\n\t\t\t\t\t\t\t\t\t<a href="'
							)
							.concat(
								item.postLink,
								'" target="_blank">\n\t\t\t\t\t\t\t\t\t\t<h2>'
							)
							.concat(
								item.title,
								'</h2>\n\t\t\t\t\t\t\t\t\t</a>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t'
							)
							.concat(
								_this.RSS_View_Instance.createExcerpt(
									item.description,
									item.postLink,
									_this.getArgs().flyout.excerpt
								),
								'\n\t\t\t\t\t\t\t\t'
							)
							.concat(
								_this.RSS_View_Instance.listChildrenPosts(
									item.children
								),
								'\n\t\t\t\t\t\t\t'
							);
					flyoutInner.innerHTML +=
						_this.RSS_View_Instance.innerContentWrapper(
							innerContent,
							isNewPost,
							!! key
								? 'inner-content-item-feed-key-'.concat( key )
								: ''
						);
				} );
				if ( _this.getArgs().viewAll.link ) {
					// If we have link provided for the view all button then append a view all button at the end of the contents.
					flyoutInner.innerHTML +=
						_this.RSS_View_Instance.innerContentWrapper(
							'\n\t\t\t\t\t\t\t<a href="'
								.concat(
									_this.getArgs().viewAll.link,
									'" class="button view-all">'
								)
								.concat(
									_this.getArgs().viewAll.label,
									'</a>\n\t\t\t\t\t\t\t'
								)
						);
				}
				_this.RSS_View_Instance.setIsLoading( false );
				flyout.classList.add( 'ready' );
				_this.getArgs().flyout.onReady( _this );
				/**
				 * Change focus to flyout on flyout ready.
				 */
				flyout.focus();
				// Set the last latest post date for notification handling.
				if ( ! _this.isMultiFeedRSS() ) {
					_this.lastPostUnixTime = currentPostUnixTime;
					if ( _this.hasNewFeeds ) {
						if (
							'function' ===
							typeof _this.getArgs().notification
								.setLastPostUnixTime
						) {
							_this
								.getArgs()
								.notification.setLastPostUnixTime(
									currentPostUnixTime,
									key
								);
						} else {
							WhatsNewRSSCacheUtils.setLastPostUnixTime(
								currentPostUnixTime,
								key
							);
						}
					}
				}
			} );
		};
		/**
		 * Open flyout on trigger button click.
		 * Flyout has three states: `closed | open | ready`
		 */
		triggerButton.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			_this.getArgs().triggerButton.onClick( _this );
			_this.RSS_View_Instance.setIsLoading( true );
			flyout.classList.remove( 'closed' );
			flyout.classList.add( 'open' );
			document.body.classList.add( 'whats-new-rss-is-active' );
			_this.getArgs().flyout.onOpen( _this );
			if ( ! _this.isMultiFeedRSS() ) {
				return injectContents( null );
			}
			var navBtns = multiFeedNav.querySelectorAll( 'button' );
			navBtns.forEach( function ( navBtn ) {
				_this.RSS_View_Instance.setMultiFeedTabNotificationCount(
					navBtn.dataset.feedKey,
					_this.multiNotificationCount[ navBtn.dataset.feedKey ]
				);
				navBtn.addEventListener( 'click', function ( e ) {
					e.preventDefault();
					var currentFeedKey = navBtn.dataset.feedKey;
					_this.multiNotificationCount[ currentFeedKey ] = 0;
					_this.RSS_Fetch_Instance.fetchData().then( function (
						res
					) {
						var currentPostUnixTime =
							res[ currentFeedKey ][ 0 ].date;
						_this.multiLastPostUnixTime[ currentFeedKey ] =
							currentPostUnixTime;
						if (
							true === _this.multiHasNewFeeds[ currentFeedKey ]
						) {
							if (
								'function' ===
								typeof _this.getArgs().notification
									.setLastPostUnixTime
							) {
								_this
									.getArgs()
									.notification.setLastPostUnixTime(
										currentPostUnixTime,
										currentFeedKey
									);
							} else {
								WhatsNewRSSCacheUtils.setLastPostUnixTime(
									currentPostUnixTime,
									currentFeedKey
								);
							}
						}
						_this.multiHasNewFeeds[ currentFeedKey ] = false;
					} );
					navBtns.forEach( function ( navBtn ) {
						navBtn.classList.remove( 'selected' );
						var feedKey = navBtn.dataset.feedKey;
						var innerContentClassName =
							'.inner-content-item-feed-key-'.concat( feedKey );
						document
							.querySelectorAll( innerContentClassName )
							.forEach( function ( item ) {
								if ( currentFeedKey !== feedKey ) {
									item.classList.add( 'hidden' );
								} else {
									item.classList.remove( 'hidden' );
								}
							} );
					} );
					navBtn.classList.add( 'selected' );
					injectContents( currentFeedKey );
				} );
			} );
			navBtns[ 0 ].click();
		} );
		/**
		 * Handle events for the closing of the flyout.
		 */
		var handleFlyoutClose = function () {
			flyout.classList.add( 'closed' );
			flyout.classList.remove( 'open' );
			flyout.classList.remove( 'ready' );
			document.body.classList.remove( 'whats-new-rss-is-active' );
			if ( _this.isMultiFeedRSS() ) {
				_this.RSS_View_Instance.setNotification(
					Object.values( _this.multiNotificationCount ).filter(
						Boolean
					).length
				);
			} else {
				_this.hasNewFeeds = false;
				_this.RSS_View_Instance.setNotification( false );
			}
			flyoutInner.innerHTML = '';
			_this.getArgs().flyout.onClose( _this );
			/**
			 * Change focus back to trigger button after flyout close.
			 */
			triggerButton.focus();
		};
		if ( this.getArgs().flyout.closeOnEsc ) {
			document.addEventListener( 'keydown', function ( e ) {
				if ( 'Escape' !== e.key ) return;
				if ( ! flyout.classList.contains( 'open' ) ) return;
				handleFlyoutClose();
			} );
		}
		if ( this.getArgs().flyout.closeOnOverlayClick ) {
			flyout
				.querySelector( '.whats-new-rss-flyout-overlay' )
				.addEventListener( 'click', handleFlyoutClose );
		}
		flyoutCloseBtn.addEventListener( 'click', handleFlyoutClose );
	};
	return WhatsNewRSS;
} )();
var WhatsNewRSSCacheUtils = /** @class */ ( function () {
	function WhatsNewRSSCacheUtils() {}
	WhatsNewRSSCacheUtils.setInstanceID = function ( instanceID ) {
		if ( ! this.instanceID ) {
			this.instanceID = instanceID;
		}
	};
	WhatsNewRSSCacheUtils.prefixer = function ( key, prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		if ( ! this.instanceID ) {
			throw new Error( 'Instance ID not set.' );
		}
		return !! prefixKey
			? ''
					.concat( this.keys[ key ], '-' )
					.concat( this.instanceID, '-' )
					.concat( prefixKey )
			: ''.concat( this.keys[ key ], '-' ).concat( this.instanceID );
	};
	WhatsNewRSSCacheUtils._setDataExpiry = function ( prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		var expiryInSeconds = 86400; // Defaults to 24 hours.
		var now = new Date();
		var expiry = now.getTime() + expiryInSeconds * 1000;
		sessionStorage.setItem(
			this.prefixer( 'SESSION_DATA_EXPIRY', prefixKey ),
			JSON.stringify( expiry )
		);
	};
	WhatsNewRSSCacheUtils._isDataExpired = function ( prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		var key = this.prefixer( 'SESSION_DATA_EXPIRY', prefixKey );
		var value = window.sessionStorage.getItem( key );
		if ( ! value ) {
			return true;
		}
		var expiry = JSON.parse( value );
		var now = new Date();
		if ( now.getTime() > expiry ) {
			window.sessionStorage.removeItem( key );
			return true;
		}
		return false;
	};
	WhatsNewRSSCacheUtils.setSessionData = function ( data, prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		this._setDataExpiry( prefixKey );
		return window.sessionStorage.setItem(
			this.prefixer( 'SESSION', prefixKey ),
			data
		);
	};
	WhatsNewRSSCacheUtils.getSessionData = function ( prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		if ( ! this._isDataExpired( prefixKey ) ) {
			return window.sessionStorage.getItem(
				this.prefixer( 'SESSION', prefixKey )
			);
		}
		return '{}';
	};
	WhatsNewRSSCacheUtils.setLastPostUnixTime = function (
		unixTime,
		prefixKey
	) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		return window.localStorage.setItem(
			this.prefixer( 'LAST_LATEST_POST', prefixKey ),
			unixTime.toString()
		);
	};
	WhatsNewRSSCacheUtils.getLastPostUnixTime = function ( prefixKey ) {
		if ( prefixKey === void 0 ) {
			prefixKey = '';
		}
		return +window.localStorage.getItem(
			this.prefixer( 'LAST_LATEST_POST', prefixKey )
		);
	};
	WhatsNewRSSCacheUtils.keys = {
		SESSION_DATA_EXPIRY: 'whats-new-cache-expiry',
		LAST_LATEST_POST: 'whats-new-last-unixtime',
		SESSION: 'whats-new-cache',
	};
	return WhatsNewRSSCacheUtils;
} )();
/**
 * Class for handling the data fetching.
 * It also handles the session caching of the fetched data internally.
 */
var WhatsNewRSSFetch = /** @class */ ( function () {
	function WhatsNewRSSFetch( RSS ) {
		var _this = this;
		this.data = {};
		this.RSS = RSS;
		this.RSS.getRSSFeedURLs().forEach( function ( feed ) {
			var sessionCache = JSON.parse(
				WhatsNewRSSCacheUtils.getSessionData( feed.key )
			);
			if ( sessionCache && sessionCache.length ) {
				_this.data[ feed.key ] = sessionCache;
			}
		} );
	}
	WhatsNewRSSFetch.prototype.fetchData = function () {
		return __awaiter( this, void 0, void 0, function () {
			var fetchPromises;
			var _this = this;
			return __generator( this, function ( _a ) {
				switch ( _a.label ) {
					case 0:
						if ( Object.keys( this.data ).length ) {
							return [ 2 /*return*/, this.data ];
						}
						fetchPromises = this.RSS.getRSSFeedURLs().map(
							function ( feed ) {
								return __awaiter(
									_this,
									void 0,
									void 0,
									function () {
										var res, data, parser, xmlDoc, items;
										var _this = this;
										return __generator(
											this,
											function ( _a ) {
												switch ( _a.label ) {
													case 0:
														this.data[ feed.key ] =
															[];
														return [
															4 /*yield*/,
															fetch( feed.url ),
														];
													case 1:
														res = _a.sent();
														return [
															4 /*yield*/,
															res.text(),
														];
													case 2:
														data = _a.sent();
														/**
														 * There was an issue with the xml content parse
														 * And during parse we were getting "<parsererror>" because of the ‘raquo’ entity.
														 */
														data = data.replace(
															/&raquo;/g,
															'&amp;raquo;'
														);
														parser =
															new DOMParser();
														xmlDoc =
															parser.parseFromString(
																data,
																'text/xml'
															);
														items =
															xmlDoc.querySelectorAll(
																'item'
															);
														items.forEach(
															function ( item ) {
																var _a;
																var title =
																	item.querySelector(
																		'title'
																	).textContent;
																var link =
																	item.querySelector(
																		'link'
																	).textContent;
																var contentEncoded =
																	item.querySelector(
																		'content\\:encoded, encoded'
																	);
																var content =
																	contentEncoded
																		? contentEncoded.textContent
																		: '';
																var rssDate =
																	item.querySelector(
																		'pubDate'
																	).innerHTML;
																_this.data[
																	feed.key
																].push( {
																	title: title,
																	date: !! rssDate
																		? +new Date(
																				rssDate
																		  )
																		: null,
																	postLink:
																		link,
																	description:
																		content,
																	children:
																		JSON.parse(
																			( ( _a =
																				item.querySelector(
																					'children'
																				) ) ===
																				null ||
																			_a ===
																				void 0
																				? void 0
																				: _a.innerHTML ) ||
																				'{}'
																		),
																} );
															}
														);
														WhatsNewRSSCacheUtils.setSessionData(
															JSON.stringify(
																this.data[
																	feed.key
																]
															),
															feed.key
														);
														return [ 2 /*return*/ ];
												}
											}
										);
									}
								);
							}
						);
						return [ 4 /*yield*/, Promise.all( fetchPromises ) ];
					case 1:
						_a.sent();
						return [ 2 /*return*/, this.data ];
				}
			} );
		} );
	};
	return WhatsNewRSSFetch;
} )();
/**
 * The class for handling library trigger button and flyout elements.
 * It also provides some necessary methods that can be used during development.
 */
var WhatsNewRSSView = /** @class */ ( function () {
	function WhatsNewRSSView( RSS ) {
		this.RSS = RSS;
		this.createTriggerButton();
		this.createFlyOut();
	}
	WhatsNewRSSView.prototype.getTriggerButtonID = function () {
		return 'whats-new-rss-btn-'.concat( this.RSS.getID() );
	};
	WhatsNewRSSView.prototype.getFlyoutID = function () {
		return 'whats-new-rss-flyout-'.concat( this.RSS.getID() );
	};
	WhatsNewRSSView.prototype.getFlyoutCloseBtnID = function () {
		return 'whats-new-rss-flyout-close-'.concat( this.RSS.getID() );
	};
	WhatsNewRSSView.prototype.getFlyoutMultiFeedNavID = function () {
		return 'whats-new-rss-flyout-multi-feed-nav-'.concat(
			this.RSS.getID()
		);
	};
	WhatsNewRSSView.prototype.setIsLoading = function ( isLoading ) {
		if ( isLoading === void 0 ) {
			isLoading = false;
		}
		var flyoutWrapper = document.getElementById( this.getFlyoutID() );
		if ( isLoading ) {
			flyoutWrapper.classList.add( 'is-loading' );
		} else {
			flyoutWrapper.classList.remove( 'is-loading' );
		}
	};
	WhatsNewRSSView.prototype.setNotification = function (
		notificationsCount
	) {
		var notificationBadge = document.querySelector(
			'#'.concat(
				this.getTriggerButtonID(),
				' .whats-new-rss-notification-badge'
			)
		);
		if ( !! notificationsCount ) {
			if ( this.RSS.isMultiFeedRSS() ) {
				notificationBadge.innerHTML = '';
				notificationBadge.classList.add( 'is-multi-feed' );
			} else {
				notificationBadge.innerHTML =
					notificationsCount > 9
						? '9+'
						: notificationsCount.toString();
			}
			notificationBadge.classList.remove( 'hide' );
		} else {
			notificationBadge.classList.add( 'hide' );
		}
	};
	WhatsNewRSSView.prototype.createTriggerButton = function () {
		var button = '\n\t\t'
			.concat(
				this.RSS.getArgs().triggerButton.beforeBtn,
				'\n\t\t<a class="whats-new-rss-trigger-button" id="'
			)
			.concat( this.getTriggerButtonID(), '">\n\t\t\t' )
			.concat(
				this.RSS.getArgs().triggerButton.icon,
				'\n\t\t\t<div class="whats-new-rss-notification-badge hide">0</div>\n\t\t</a>\n\t\t'
			)
			.concat( this.RSS.getArgs().triggerButton.afterBtn, '\n\t\t' );
		this.RSS.getElement().innerHTML += button;
	};
	WhatsNewRSSView.prototype.createFlyOut = function () {
		var wrapperClasses = [ 'whats-new-rss-flyout', 'closed' ];
		if ( this.RSS.getArgs().flyout.className ) {
			wrapperClasses.push( this.RSS.getArgs().flyout.className );
		}
		var multiFeedNav = [];
		if ( this.RSS.isMultiFeedRSS() ) {
			multiFeedNav.push(
				'<nav id="'.concat(
					this.getFlyoutMultiFeedNavID(),
					'" class="whats-new-rss-multi-feed-nav">'
				)
			);
			this.RSS.getRSSFeedURLs().forEach( function ( feed ) {
				multiFeedNav.push(
					'<button type="button" data-feed-key="'
						.concat( feed.key, '">\n\t\t\t\t\t\t' )
						.concat(
							feed.label,
							'\n\t\t\t\t\t\t<div class="new-notification-count"></div>\n\t\t\t\t\t</button>\n\t\t\t\t\t'
						)
				);
			} );
			multiFeedNav.push( '</nav>' );
		}
		var flyoutWrapper = document.createElement( 'div' );
		flyoutWrapper.setAttribute( 'id', this.getFlyoutID() );
		flyoutWrapper.setAttribute( 'class', wrapperClasses.join( ' ' ) );
		flyoutWrapper.setAttribute( 'role', 'dialog' );
		flyoutWrapper.innerHTML =
			'\n\t\t<div class="whats-new-rss-flyout-contents">\n\n\t\t\t<div class="whats-new-rss-flyout-inner-header">\n\n\t\t\t\t<div class="whats-new-rss-flyout-inner-header__title-icon-wrapper">\n\t\t\t\t\t<h3>'
				.concat(
					this.RSS.getArgs().flyout.title,
					'</h3>\n\n\t\t\t\t\t<span class="whats-new-rss-flyout-inner-header__loading-icon">\n\t\t\t\t\t'
				)
				.concat(
					this.RSS.getArgs().loaderIcon,
					'\n\t\t\t\t\t</span>\n\t\t\t\t</div>\n\n\t\t\t\t<button type="button" id="'
				)
				.concat( this.getFlyoutCloseBtnID(), '">' )
				.concat(
					this.RSS.getArgs().flyout.closeBtnIcon,
					'</button>\n\t\t\t</div>\n\n\t\t\t'
				)
				.concat(
					multiFeedNav.join( '' ),
					'\n\n\t\t\t<div class="whats-new-rss-flyout-inner-content">\n\t\t\t\t<div class="skeleton-container">\n\t\t\t\t\t<div class="skeleton-row whats-new-rss-flyout-inner-content-item"></div>\n\t\t\t\t\t<div class="skeleton-row whats-new-rss-flyout-inner-content-item"></div>\n\t\t\t\t\t<div class="skeleton-row whats-new-rss-flyout-inner-content-item"></div>\n\t\t\t\t</div>\n\t\t\t</div>\n\n\t\t</div>\n\n\t\t<div class="whats-new-rss-flyout-overlay"></div>\n\t\t'
				);
		document.body.appendChild( flyoutWrapper );
	};
	WhatsNewRSSView.prototype.setMultiFeedTabNotificationCount = function (
		key,
		notificationCount
	) {
		if ( notificationCount === void 0 ) {
			notificationCount = 0;
		}
		var tabBtn = document.querySelector(
			'#'
				.concat(
					this.getFlyoutMultiFeedNavID(),
					' button[data-feed-key="'
				)
				.concat( key, '"]' )
		);
		if ( ! tabBtn ) {
			return;
		}
		var el = tabBtn.querySelector( '.new-notification-count' );
		if ( notificationCount ) {
			var _count = notificationCount > 9 ? '9+' : notificationCount;
			el.innerHTML = _count.toString();
		} else {
			el.innerHTML = '';
		}
	};
	WhatsNewRSSView.prototype.innerContentWrapper = function (
		content,
		isNewPost,
		additionalClasses
	) {
		if ( isNewPost === void 0 ) {
			isNewPost = false;
		}
		if ( additionalClasses === void 0 ) {
			additionalClasses = '';
		}
		var classes = [ 'whats-new-rss-flyout-inner-content-item' ];
		if ( isNewPost ) {
			classes.push( 'rss-new-post' );
		}
		if ( !! additionalClasses ) {
			classes.push( additionalClasses );
		}
		return '\n\t\t<div class="'
			.concat( classes.join( ' ' ), '">\n\t\t\t' )
			.concat(
				isNewPost ? '<small class="new-post-badge">New ✨</small>' : '',
				'\n\t\t\t'
			)
			.concat( content, '\n\t\t</div>\n\t\t' );
	};
	WhatsNewRSSView.prototype.createExcerpt = function (
		content,
		readMoreLink,
		options
	) {
		var wordLimit = options.wordLimit,
			moreSymbol = options.moreSymbol,
			readMore = options.readMore;
		if ( ! wordLimit ) {
			return content;
		}
		var plainText = content.replace( /<[^>]*>/g, '' );
		var words = plainText.split( /\s+/ );
		var rawExcerpt = words.slice( 0, wordLimit ).join( ' ' );
		if ( moreSymbol ) {
			rawExcerpt += moreSymbol;
		}
		if ( wordLimit > words.length ) {
			return content;
		}
		if (
			!! readMoreLink &&
			!! ( readMore === null || readMore === void 0
				? void 0
				: readMore.label )
		) {
			return '<p>'
				.concat( rawExcerpt, ' <a href="' )
				.concat( readMoreLink, '" target="_blank" class="' )
				.concat( readMore.className, '">' )
				.concat( readMore.label, '</a></p>' );
		}
		return '<p>'.concat( rawExcerpt, '</p>' );
	};
	WhatsNewRSSView.prototype.listChildrenPosts = function ( children ) {
		var _this = this;
		var _children = Object.values( children );
		if ( ! _children.length ) return '';
		var details = document.createElement( 'details' );
		var summary = document.createElement( 'summary' );
		var itemsWrapper = document.createElement( 'div' );
		_children.forEach( function ( child ) {
			var postContentDoc = new DOMParser().parseFromString(
				child.post_content,
				'text/html'
			);
			var itemDiv = document.createElement( 'div' );
			itemDiv.classList.add( 'sub-version-item' );
			itemDiv.innerHTML =
				'\n\t\t\t\t<div class="sub-version-header">\n\t\t\t\t\t<h4 class="sub-version-title">'
					.concat(
						child.post_title,
						'</h4>\n\t\t\t\t\t<span class="sub-version-date">'
					)
					.concat(
						_this.formatDate( new Date( child.post_date ) ),
						'</span>\n\t\t\t\t</div>\n\t\t\t\t<div class="sub-version-content">'
					)
					.concat(
						postContentDoc.documentElement.textContent,
						'</div>\n\t\t\t'
					);
			itemsWrapper.appendChild( itemDiv );
		} );
		summary.innerHTML =
			'<p class="text-see-more">See More</p><p class="text-see-less">See Less</p>';
		details.appendChild( summary );
		details.appendChild( itemsWrapper );
		itemsWrapper.classList.add( 'sub-version-items-wrapper' );
		details.classList.add( 'whats-new-rss-sub-version-details' );
		return details.outerHTML;
	};
	WhatsNewRSSView.prototype.formatDate = function ( date ) {
		if ( 'function' === typeof this.RSS.getArgs().flyout.formatDate ) {
			return this.RSS.getArgs().flyout.formatDate( date );
		}
		var currentDate = new Date();
		var timestamp = date.getTime();
		var currentTimestamp = currentDate.getTime();
		var difference = currentTimestamp - timestamp;
		// Define time intervals in milliseconds
		var minute = 60 * 1000;
		var hour = minute * 60;
		var day = hour * 24;
		var week = day * 7;
		var month = day * 30; // Rough estimate, assuming 30 days in a month
		if ( difference < minute ) {
			return 'Just now';
		} else if ( difference < hour ) {
			var minutes = Math.floor( difference / minute );
			return ''
				.concat( minutes, ' minute' )
				.concat( minutes > 1 ? 's' : '', ' ago' );
		} else if ( difference < day ) {
			var hours = Math.floor( difference / hour );
			return ''
				.concat( hours, ' hour' )
				.concat( hours > 1 ? 's' : '', ' ago' );
		} else if ( difference < week ) {
			var days = Math.floor( difference / day );
			return ''
				.concat( days, ' day' )
				.concat( days > 1 ? 's' : '', ' ago' );
		} else if ( difference < month ) {
			var weeks = Math.floor( difference / week );
			return ''
				.concat( weeks, ' week' )
				.concat( weeks > 1 ? 's' : '', ' ago' );
		} else {
			// Handle months and years accordingly
			// This is a rough estimate and may not be accurate in all cases
			var months = Math.floor( difference / month );
			return ''
				.concat( months, ' month' )
				.concat( months > 1 ? 's' : '', ' ago' );
		}
	};
	return WhatsNewRSSView;
} )();

let wnr = null;

function useWhatsNewRSS( args ) {
	useEffect( () => {
		if ( ! wnr ) {
			wnr = new WhatsNewRSS( args );
		}
	}, [ args ] );
}
export default useWhatsNewRSS;
