( function( $, api ) {
    var $document = $( document );

    wp.customize.bind( 'preview-ready', function() {

        var defaultTarget = window.parent === window ? null : window.parent;
        $document.on(
            'click',
            '.site-header-focus-item .item-customizer-focus, .builder-item-focus .edit-row-action',
            function(e) {

                e.preventDefault();
                e.stopPropagation();
                var p = $( this ).closest( '.site-header-focus-item' );
                var section_id = p.attr( 'data-section' ) || '';
                if ( section_id ) {
                    if ( defaultTarget.wp.customize.section( section_id ) ) {
                        defaultTarget.wp.customize.section( section_id ).focus();
                    }
                }
            }
        );

        $document.on(
            'click',
            '.site-footer-focus-item .item-customizer-focus',
            function(e) {

                e.preventDefault();
                e.stopPropagation();
                var p = $( this ).closest( '.site-footer-focus-item' );
                var section_id = p.attr( 'data-section' ) || '';
                if ( section_id ) {
                    if ( defaultTarget.wp.customize.section( section_id ) ) {
                        defaultTarget.wp.customize.section( section_id ).focus();
                    }
                }
            }
        );

        $document.on(
            'click',
            '.customizer-navigate-on-focus',
            function(e) {
                e.preventDefault();
                e.stopPropagation();

                const currentElement = $( this ).closest( '.customizer-navigate-on-focus' );
                const section_id = currentElement.attr( 'data-section' ) || '';
                const type       = currentElement.attr( 'data-type' ) ? currentElement.attr( 'data-type' ) : 'section';

                if ( section_id && type ) {

                    if( 'section' === type ) {
                        if ( defaultTarget.wp.customize.section( section_id ) ) {
                            defaultTarget.wp.customize.section( section_id ).focus();
                        }
                    }

                    if( 'control' === type ) {
                        if ( defaultTarget.wp.customize.control( section_id ) ) {
                            defaultTarget.wp.customize.control( section_id ).focus();
                        }
                    }

                    if( 'panel' === type ) {
                        if ( defaultTarget.wp.customize.panel( section_id ) ) {
                            defaultTarget.wp.customize.panel( section_id ).focus();
                        }
                    }

                }
            }
        );

        /**
         * Ajax quantity input show.
         */
        wp.customize( 'astra-settings[woo-header-cart-click-action]', function( setting ) {
            setting.bind( function( action ) {
                $( document.body ).trigger( 'wc_fragment_refresh' );
            } );
        } );

		/**
		 * Register partial refresh events at once asynchronously.
		 */
		wp.customize.preview.bind( 'active', function() {
			var partials = $.extend({}, astraCustomizer.dynamic_partial_options), key;
			var register_partial = async function () {
				for ( key in partials) {
					wp.customize.selectiveRefresh.partial.add(
						new wp.customize.selectiveRefresh.Partial(
							key,
							_.extend({params: partials[key]}, partials[key])
						)
					);
					await null;
				}
			}
			register_partial();
		});

    } );

    /**
     * Inline logo and title css only.
     */
    wp.customize( 'astra-settings[logo-title-inline]', function( value ) {
        value.bind( function( is_checked ) {
           jQuery('#masthead').toggleClass( 'ast-logo-title-inline', is_checked );
        } );
    } );

} )( jQuery, wp );


// Single Post Content Width
wp.customize( 'astra-settings[blog-single-width]', function( value ) {
    value.bind( function( value ) {

        var single_post_max_width = wp.customize('astra-settings[blog-single-max-width]').get();

        var dynamicStyle = '';

        if ( 'custom' === value ) {

            dynamicStyle += '.single-post .site-content > .ast-container {';
            dynamicStyle += 'max-width: ' + single_post_max_width + 'px;';
            dynamicStyle += '} ';
        }
        else{
            wp.customize.preview.send( 'refresh' );
        }
        astra_add_dynamic_css( 'blog-single-width', dynamicStyle );
    } );
} );

// Blog Post Content Width
wp.customize( 'astra-settings[blog-width]', function( value ) {
    value.bind( function( value ) {

        var blog_max_width = wp.customize('astra-settings[blog-max-width]').get();

        var dynamicStyle = '';

        if ( 'custom' === value ) {

            dynamicStyle += '.blog .site-content > .ast-container, .archive .site-content > .ast-container, .search .site-content > .ast-container {';
            dynamicStyle += 'max-width: ' + blog_max_width + 'px;';
            dynamicStyle += '} ';
        }
        else{
            wp.customize.preview.send( 'refresh' );
        }
        astra_add_dynamic_css( 'blog-width', dynamicStyle );
    } );
} );

// Blog Post Content Width
wp.customize( 'astra-settings[edd-archive-grids]', function( value ) {
    value.bind( function( value ) {

        for ( var i = 1; i < 7; i++ ) {
            jQuery('body').removeClass( 'columns-' + i );
            jQuery('body').removeClass( 'tablet-columns-' + i );
            jQuery('body').removeClass( 'mobile-columns-' + i );
        }

        if ( jQuery('body').hasClass( 'ast-edd-archive-page' ) ) {

            jQuery('body').addClass( 'columns-' + value['desktop'] );
            jQuery('body').addClass( 'tablet-columns-' + value['tablet'] );
            jQuery('body').addClass( 'mobile-columns-' + value['mobile'] );
        }
    } );
} );


// Blog Post Content Width
wp.customize( 'astra-settings[edd-archive-width]', function( value ) {
    value.bind( function( value ) {

        var edd_archive_max_width = wp.customize('astra-settings[edd-archive-max-width]').get();
        edd_archive_max_width = ( 'custom' === value ) ? edd_archive_max_width : edd_archive_max_width + 40;

        var dynamicStyle = '';
        dynamicStyle += '.ast-edd-archive-page .site-content > .ast-container {';
        dynamicStyle += 'max-width: ' + edd_archive_max_width + 'px;';
        dynamicStyle += '} ';

        astra_add_dynamic_css( 'edd-archive-width', dynamicStyle );
    } );
} );

// WooCommerce store notice color configs.
astra_css( 'astra-settings[store-notice-text-color]', 'color', 'body p.demo_store, body .woocommerce-store-notice, body p.demo_store a, body .woocommerce-store-notice a' );
astra_css( 'astra-settings[store-notice-background-color]', 'background-color', 'body p.demo_store, body .woocommerce-store-notice, body p.demo_store a, body .woocommerce-store-notice a' );

// WooCommerce store notice position preview.
wp.customize( 'astra-settings[store-notice-position]', function( setting ) {
    setting.bind( function( position ) {
		if( 'hang-over-top' === position ) {
			wp.customize.preview.send( 'refresh' );
		} else {
			jQuery('body').removeClass( 'ast-woocommerce-store-notice-hanged' );
			jQuery('.woocommerce-store-notice').attr( 'data-position', position );
		}
    } );
} );

wp.customize( 'astra-settings[blog-meta-date-type]', function( setting ) {
	setting.bind( function( val ) {
		wp.customize.preview.send( 'refresh' );
	} );
} );
wp.customize( 'astra-settings[blog-meta-date-format]', function( setting ) {
	setting.bind( function( val ) {
		wp.customize.preview.send( 'refresh' );
	} );
} );

astra_refresh_customizer(
    'astra-settings[blog-hover-effect]'
);

astra_refresh_customizer(
    'astra-settings[blog-image-ratio-type]'
);

astra_refresh_customizer(
    'astra-settings[blog-image-size]'
);

astra_refresh_customizer(
    'astra-settings[blog-image-ratio-pre-scale]'
);

astra_refresh_customizer(
    'astra-settings[blog-image-custom-scale-width]'
);

astra_refresh_customizer(
    'astra-settings[blog-image-custom-scale-height]'
);

astra_refresh_customizer(
    'astra-settings[blog-post-per-page]'
);

// Global Typography Refresh - START
const bodyFontFamily = [
	'body-font-family',
	'body-font-variant',
	'font-size-body',
	'body-font-weight',
	'body-font-extras',
	'headings-font-family',
	'headings-font-variant',
	'headings-font-weight',
	'headings-font-extras'
];

bodyFontFamily.forEach(element => {
	// Body Font Family
	wp.customize( 'astra-settings['+element+']', function( value ) {
		value.bind( function( value ) {
			wp.customize.preview.send( 'refresh' );
		} );
	} );

});

// Global Typography Refresh - END
