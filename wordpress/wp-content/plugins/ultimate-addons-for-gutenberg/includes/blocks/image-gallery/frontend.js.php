<?php
/**
 * Frontend JS File.
 *
 * @since 2.1.0
 *
 * @package uagb
 */

$block_name = 'image-gallery';
$selector   = '.uagb-block-' . $id;
$js         = '';

$is_rtl = is_rtl();

$slick_options = apply_filters(
	'uagb_image_gallery_slick_options',
	array(
		'arrows'        => is_bool( $attr['paginateUseArrows'] ) ? $attr['paginateUseArrows'] : true,
		'dots'          => is_bool( $attr['paginateUseDots'] ) ? $attr['paginateUseDots'] : true,
		'initialSlide'  => is_int( $attr['carouselStartAt'] ) ? $attr['carouselStartAt'] : (int) $attr['carouselStartAt'],
		'infinite'      => is_bool( $attr['carouselLoop'] ) ? $attr['carouselLoop'] : true,
		'autoplay'      => is_bool( $attr['carouselAutoplay'] ) ? $attr['carouselAutoplay'] : true,
		'autoplaySpeed' => is_int( $attr['carouselAutoplaySpeed'] ) ? $attr['carouselAutoplaySpeed'] : (int) $attr['carouselAutoplaySpeed'],
		'pauseOnHover'  => is_bool( $attr['carouselPauseOnHover'] ) ? $attr['carouselPauseOnHover'] : true,
		'speed'         => is_int( $attr['carouselTransitionSpeed'] ) ? $attr['carouselTransitionSpeed'] : (int) $attr['carouselTransitionSpeed'],
		'slidesToShow'  => is_int( $attr['columnsDesk'] ) ? $attr['columnsDesk'] : (int) $attr['columnsDesk'],
		'prevArrow'     => "<button type='button' data-role='none' class='spectra-image-gallery__control-arrows spectra-image-gallery__control-arrows--carousel slick-prev slick-arrow' aria-label='Previous' tabindex='0' role='button'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512' width='" . esc_attr( $attr['paginateArrowSize'] ) . "' height='" . esc_attr( $attr['paginateArrowSize'] ) . "'><path d='M31.7 239l136-136c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9L127.9 256l96.4 96.4c9.4 9.4 9.4 24.6 0 33.9L201.7 409c-9.4 9.4-24.6 9.4-33.9 0l-136-136c-9.5-9.4-9.5-24.6-.1-34z'></path></svg></button>",
		'nextArrow'     => "<button type='button' data-role='none' class='spectra-image-gallery__control-arrows spectra-image-gallery__control-arrows--carousel slick-next slick-arrow' aria-label='Previous' tabindex='0' role='button'><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 256 512' width='" . esc_attr( $attr['paginateArrowSize'] ) . "' height='" . esc_attr( $attr['paginateArrowSize'] ) . "'><path d='M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z'></path></svg></button>",
		'rtl'           => $is_rtl,
		'responsive'    => array(
			array(
				'breakpoint' => 1024,
				'settings'   => array(
					'slidesToShow' => is_int( $attr['columnsTab'] ) ? $attr['columnsTab'] : (int) $attr['columnsTab'],
				),
			),
			array(
				'breakpoint' => 767,
				'settings'   => array(
					'slidesToShow' => is_int( $attr['columnsMob'] ) ? $attr['columnsMob'] : (int) $attr['columnsMob'],
				),
			),
		),
	),
	$id
);

// The Thumbnail Swiper Association is handled in the JS in Class Spectra Image Gallery.
$lightbox_options = apply_filters(
	'uagb_image_gallery_lightbox_options',
	array(
		'lazy'          => true,
		'slidesPerView' => 1,
		'navigation'    => array(
			'nextEl' => $selector . '+.spectra-image-gallery__control-lightbox .swiper-button-next',
			'prevEl' => $selector . '+.spectra-image-gallery__control-lightbox .swiper-button-prev',
		),
		'keyboard'      => array(
			'enabled' => true,
		),
	),
	$id
);

$thumbnail_options = apply_filters(
	'uagb_image_gallery_thumbnail_options',
	array(
		'centeredSlides'        => true,
		'slidesPerView'         => 5,
		'slideToClickedSlide'   => true,
		'watchSlidesProgres'    => true,
		'watchSlidesVisibility' => true,
		// Swiper Breakpoints go Upward.
		'breakpoints'           => array(
			768  => array(
				'slidesPerView' => 7,
			),
			1024 => array(
				'slidesPerView' => 9,
			),
		),
	),
	$id
);

$settings           = wp_json_encode( $slick_options );
$lightbox_settings  = is_array( $lightbox_options ) ? $lightbox_options : array();
$thumbnail_settings = ( ! empty( $attr['lightboxThumbnails'] ) && is_array( $thumbnail_options ) ) ? $thumbnail_options : array();

if ( $attr['mediaGallery'] ) {
	switch ( $attr['feedLayout'] ) {
		case 'grid':
			$js = $attr['feedPagination']
				? Spectra_Image_Gallery::render_frontend_grid_pagination( $id, $attr, $selector, $lightbox_settings, $thumbnail_settings )
				: '';
			break;
		case 'masonry':
			$js = Spectra_Image_Gallery::render_frontend_masonry_layout( $id, $attr, $selector, $lightbox_settings, $thumbnail_settings );
			break;
		case 'carousel':
			$js = Spectra_Image_Gallery::render_frontend_carousel_layout( $id, $settings, $selector );
			break;
		case 'tiled':
			$js = Spectra_Image_Gallery::render_frontend_tiled_layout( $id );
			break;
	}
	switch ( $attr['imageClickEvent'] ) {
		case 'lightbox':
			$js .= Spectra_Image_Gallery::render_frontend_lightbox( $id, $attr, $lightbox_settings, $thumbnail_settings, $selector );
			break;
		case 'image':
			$js .= Spectra_Image_Gallery::render_image_click( $id, $attr );
			break;
		case 'url':
			$js = apply_filters( 'uagb_image_gallery_pro_custom_url_js', $js, $id, $attr );
			break;
	}
}

return $js;
