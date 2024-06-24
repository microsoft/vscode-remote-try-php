<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 * @var mixed[] $attr
 * @var string $id
 * @package uagb
 */

$dots   = ( 'dots' === $attr['arrowDots'] || 'arrowDots' === $attr['arrowDots'] ) ? true : false;
$arrows = ( 'arrows' === $attr['arrowDots'] || 'arrowDots' === $attr['arrowDots'] ) ? true : false;

$slick_options = apply_filters(
	'uagb_testimonials_slick_options',
	array(
		'slidesToShow'   => is_int( $attr['columns'] ) ? $attr['columns'] : (int) $attr['columns'],
		'slidesToScroll' => 1,
		'autoplaySpeed'  => esc_html( $attr['autoplaySpeed'] ),
		'autoplay'       => is_bool( $attr['autoplay'] ) ? $attr['autoplay'] : true,
		'infinite'       => is_bool( $attr['infiniteLoop'] ) ? $attr['infiniteLoop'] : true,
		'pauseOnHover'   => is_bool( $attr['pauseOnHover'] ) ? $attr['pauseOnHover'] : true,
		'speed'          => esc_html( $attr['transitionSpeed'] ),
		'arrows'         => $arrows,
		'dots'           => $dots,
		'rtl'            => is_rtl(),
		'prevArrow'      => "<button type='button' data-role='none' class='slick-prev' aria-label='Previous' tabindex='0' role='button' style='border-color: " . esc_attr( $attr['arrowColor'] ) . ';border-radius:' . esc_attr( $attr['arrowBorderRadius'] ) . 'px;border-width:' . esc_attr( $attr['arrowBorderSize'] ) . "px'><svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 256 512' height ='" . esc_attr( $attr['arrowSize'] ) . "' width = '" . esc_attr( $attr['arrowSize'] ) . "' fill ='" . esc_attr( $attr['arrowColor'] ) . "'  ><path d='M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z'></path></svg></button>",
		'nextArrow'      => "<button type='button' data-role='none' class='slick-next' aria-label='Next' tabindex='0' role='button' style='border-color: " . esc_attr( $attr['arrowColor'] ) . ';border-radius:' . esc_attr( $attr['arrowBorderRadius'] ) . 'px;border-width:' . esc_attr( $attr['arrowBorderSize'] ) . "px'><svg xmlns='https://www.w3.org/2000/svg' viewBox='0 0 256 512' height ='" . esc_attr( $attr['arrowSize'] ) . "' width = '" . esc_attr( $attr['arrowSize'] ) . "' fill ='" . esc_attr( $attr['arrowColor'] ) . "' ><path d='M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z'></path></svg></button>",
		'responsive'     => array(
			array(
				'breakpoint' => 1024,
				'settings'   => array(
					'slidesToShow'   => is_int( $attr['tcolumns'] ) ? $attr['tcolumns'] : (int) $attr['tcolumns'],
					'slidesToScroll' => 1,
				),
			),
			array(
				'breakpoint' => 767,
				'settings'   => array(
					'slidesToShow'   => is_int( $attr['mcolumns'] ) ? $attr['mcolumns'] : (int) $attr['mcolumns'],
					'slidesToScroll' => 1,
				),
			),
		),
	),
	$id
);
$equal_height  = isset( $attr['equalHeight'] ) ? $attr['equalHeight'] : '';

$settings      = wp_json_encode( $slick_options );
$base_selector = ( isset( $attr['classMigrate'] ) && $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-testimonial-';
$selector      = $base_selector . $id;
ob_start();
?>
jQuery( document ).ready( function() {
	if( jQuery( '<?php echo esc_html( $selector ); ?>' ).length > 0 ){
	jQuery( '<?php echo esc_html( $selector ); ?>' ).find( ".is-carousel" ).slick( <?php echo $settings; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> );
	}
	var $scope = jQuery('.uagb-block-<?php echo esc_html( $id ); ?>');
	var enableEqualHeight = ( '<?php echo esc_html( $equal_height ); ?>' );
			if( enableEqualHeight ){
				$scope.imagesLoaded( function() {
					UAGBTestimonialCarousel._setHeight( $scope );
				});

				$scope.on( "afterChange", function() {
					UAGBTestimonialCarousel._setHeight( $scope );
				} );
			}
} );
<?php
return ob_get_clean();
?>
