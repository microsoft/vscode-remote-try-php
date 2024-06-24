<?php
/**
 * Frontend JS File.
 *
 * @since 2.3.0
 * @var mixed[] $attr
 * @var int $id
 * @package uagb
 */

$selector   = '.uagb-block-' . $id . ' .uagb-swiper';
$block_name = 'slider';

$slider_options = apply_filters(
	'uagb_slider_options',
	array(
		'autoplay'   => $attr['autoplay'] ? array(
			'delay'                => is_int( $attr['autoplaySpeed'] ) ? $attr['autoplaySpeed'] : (int) $attr['autoplaySpeed'],
			'disableOnInteraction' => 'click' === $attr['pauseOn'] ? true : false,
			'pauseOnMouseEnter'    => 'hover' === $attr['pauseOn'] ? true : false,
			'stopOnLastSlide'      => $attr['infiniteLoop'] ? false : true,
		) : false,
		'loop'       => is_bool( $attr['infiniteLoop'] ) ? $attr['infiniteLoop'] : true,
		'speed'      => is_int( $attr['transitionSpeed'] ) ? $attr['transitionSpeed'] : (int) $attr['transitionSpeed'],
		'effect'     => $attr['transitionEffect'],
		'direction'  => $attr['verticalMode'] ? 'vertical' : 'horizontal',
		'flipEffect' => array(
			'slideShadows' => false,
		),
		'fadeEffect' => array(
			'crossFade' => true,
		),
		'pagination' => (bool) $attr['displayDots'] ? array(
			'el'          => '.uagb-block-' . $id . ' .swiper-pagination',
			'clickable'   => true,
			'hideOnClick' => false,
		) : false,
		'navigation' => (bool) $attr['displayArrows'] ? array(
			'nextEl' => '.uagb-block-' . $id . ' .swiper-button-next',
			'prevEl' => '.uagb-block-' . $id . ' .swiper-button-prev',
		) : false,
	),
	$attr
);

$settings = wp_json_encode( $slider_options );

ob_start();
?>
window.addEventListener("DOMContentLoaded", function(){
	var swiper = new Swiper( "<?php echo esc_attr( $selector ); ?>",
		<?php echo $settings; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	);
});

<?php

do_action( 'spectra_after_slider_options_loaded', $attr );

return ob_get_clean();
?>
