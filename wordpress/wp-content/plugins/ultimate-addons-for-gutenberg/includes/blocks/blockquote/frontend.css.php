<?php
/**
 * Frontend CSS & Google Fonts loading File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

// Adds Fonts.
UAGB_Block_JS::blocks_blockquote_gfont( $attr );

$tweetBtnPaddingTop    = isset( $attr['paddingBtnTop'] ) ? $attr['paddingBtnTop'] : $attr['tweetBtnVrPadding'];
$tweetBtnPaddingBottom = isset( $attr['paddingBtnBottom'] ) ? $attr['paddingBtnBottom'] : $attr['tweetBtnVrPadding'];
$tweetBtnPaddingLeft   = isset( $attr['paddingBtnLeft'] ) ? $attr['paddingBtnLeft'] : $attr['tweetBtnHrPadding'];
$tweetBtnPaddingRight  = isset( $attr['paddingBtnRight'] ) ? $attr['paddingBtnRight'] : $attr['tweetBtnHrPadding'];

if ( 'center' !== $attr['align'] || 'border' === $attr['skinStyle'] ) {
	$attr['authorSpace']       = 0;
	$attr['authorSpaceTablet'] = 0;
	$attr['authorSpaceMobile'] = 0;
}

// Set align to left for border style.
$text_align = $attr['align'];

if ( 'border' === $attr['skinStyle'] ) {
	$text_align = 'left';
}

$selectors = array(
	' .uagb-blockquote__content'                       => array(
		'color'         => $attr['descColor'],
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpace'], $attr['descSpaceUnit'] ),
		'text-align'    => $text_align,
	),
	' cite.uagb-blockquote__author'                    => array(
		'color'      => $attr['authorColor'],
		'text-align' => $text_align,
	),
	' .uagb-blockquote__skin-border blockquote.uagb-blockquote' => array( // for backward compatibility.
		'border-color'      => $attr['borderColor'],
		'border-left-style' => $attr['borderStyle'],
		'border-left-width' => UAGB_Helper::get_css_value( $attr['borderWidth'], $attr['borderWidthUnit'] ),
		'padding-left'      => UAGB_Helper::get_css_value( $attr['borderGap'], $attr['borderGapUnit'] ),
		'padding-top'       => UAGB_Helper::get_css_value( $attr['verticalPadding'], $attr['verticalPaddingUnit'] ),
		'padding-bottom'    => UAGB_Helper::get_css_value( $attr['verticalPadding'], $attr['verticalPaddingUnit'] ),
	),
	'.uagb-blockquote__skin-border blockquote.uagb-blockquote' => array(
		'border-color'      => $attr['borderColor'],
		'border-left-style' => $attr['borderStyle'],
		'border-left-width' => UAGB_Helper::get_css_value( $attr['borderWidth'], $attr['borderWidthUnit'] ),
		'padding-left'      => UAGB_Helper::get_css_value( $attr['borderGap'], $attr['borderGapUnit'] ),
		'padding-top'       => UAGB_Helper::get_css_value( $attr['verticalPadding'], $attr['verticalPaddingUnit'] ),
		'padding-bottom'    => UAGB_Helper::get_css_value( $attr['verticalPadding'], $attr['verticalPaddingUnit'] ),
	),

	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon-wrap' => array( // For Backword.
		'background'    => $attr['quoteBgColor'],
		'border-radius' => UAGB_Helper::get_css_value( $attr['quoteBorderRadius'], '%' ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['quoteTopMargin'], 'px' ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['quoteBottomMargin'], 'px' ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['quoteLeftMargin'], 'px' ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['quoteRightMargin'], 'px' ),
		'padding'       => UAGB_Helper::get_css_value( $attr['quotePadding'], $attr['quotePaddingType'] ),
	),
	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon' => array( // For Backword.
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
	),

	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon' => array(
		'background'    => $attr['quoteBgColor'],
		'border-radius' => UAGB_Helper::get_css_value( $attr['quoteBorderRadius'], $attr['quoteBorderRadiusUnit'] ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['quoteTopMargin'], $attr['quoteUnit'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['quoteBottomMargin'], $attr['quoteUnit'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['quoteLeftMargin'], $attr['quoteUnit'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['quoteRightMargin'], $attr['quoteUnit'] ),
		'padding'       => UAGB_Helper::get_css_value( $attr['quotePadding'], $attr['quotePaddingType'] ),
	),
	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
		'fill'   => $attr['quoteColor'],
	),

	'.uagb-blockquote__style-style_1 .uagb-blockquote' => array(
		'text-align' => $attr['align'],
	),

	' .uagb-blockquote__author-wrap'                   => array(
		'margin-bottom' => UAGB_Helper::get_css_value(
			$attr['authorSpace'],
			'px'
		),
	),
	' .uagb-blockquote__author-wrap img'               => array(
		'width'         => UAGB_Helper::get_css_value( $attr['authorImageWidth'], 'px' ),
		'height'        => UAGB_Helper::get_css_value( $attr['authorImageWidth'], 'px' ),
		'border-radius' => UAGB_Helper::get_css_value( $attr['authorImgBorderRadius'], '%' ),
	),

	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-right img' => array(
		'margin-left' => UAGB_Helper::get_css_value( $attr['authorImageGap'], $attr['authorImageGapUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-top img' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['authorImageGap'], $attr['authorImageGapUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-left img' => array(
		'margin-right' => UAGB_Helper::get_css_value( $attr['authorImageGap'], $attr['authorImageGapUnit'] ),
	),

	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon:hover svg' => array(
		'fill' => $attr['quoteHoverColor'],
	),

	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon:hover' => array(
		'background' => $attr['quoteBgHoverColor'],
	),

	'.uagb-blockquote__skin-border blockquote.uagb-blockquote:hover' => array(
		'border-left-color' => $attr['borderHoverColor'],
	),
	// Backword css.
	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'fill'   => $attr['quoteColor'],
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSize'], $attr['quoteSizeType'] ),
	),
	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon:hover svg' => array(
		'fill' => $attr['quoteHoverColor'],
	),

	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon-wrap:hover' => array(
		'background' => $attr['quoteBgHoverColor'],
	),

	' .uagb-blockquote__skin-border blockquote.uagb-blockquote:hover' => array(
		'border-left-color' => $attr['borderHoverColor'],
	),
	'.uagb-blockquote__align-center blockquote.uagb-blockquote' => array(
		'text-align' => $attr['align'],
	),
	// End backword.
);

if ( $attr['enableTweet'] ) {
	$selectors['.uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button'] = array(
		'color' => $attr['tweetLinkColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button svg'] = array(
		'fill' => $attr['tweetLinkColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button'] = array( // for backward compatibility.
		'color'            => $attr['tweetBtnColor'],
		'background-color' => $attr['tweetBtnBgColor'],
		'padding-left'     => UAGB_Helper::get_css_value( $tweetBtnPaddingLeft, $attr['paddingBtnUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $tweetBtnPaddingRight, $attr['paddingBtnUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
	);

	$selectors['.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button'] = array(
		'color'            => $attr['tweetBtnColor'],
		'background-color' => $attr['tweetBtnBgColor'],
		'padding-left'     => UAGB_Helper::get_css_value( $tweetBtnPaddingLeft, $attr['paddingBtnUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $tweetBtnPaddingRight, $attr['paddingBtnUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
	);

	$selectors['.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button svg'] = array(
		'fill' => $attr['tweetBtnColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button'] = array(
		'color'            => $attr['tweetBtnColor'],
		'background-color' => $attr['tweetBtnBgColor'],
		'padding-left'     => UAGB_Helper::get_css_value( $tweetBtnPaddingLeft, $attr['paddingBtnUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $tweetBtnPaddingRight, $attr['paddingBtnUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $tweetBtnPaddingBottom, $attr['paddingBtnUnit'] ),
	);

	// Backword CSS.
	$selectors[' .uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button'] = array(
		'color'            => $attr['tweetBtnColor'],
		'background-color' => $attr['tweetBtnBgColor'],
		'padding-left'     => UAGB_Helper::get_css_value( $tweetBtnPaddingLeft, $attr['paddingBtnUnit'] ),
		'padding-right'    => UAGB_Helper::get_css_value( $tweetBtnPaddingRight, $attr['paddingBtnUnit'] ),
		'padding-top'      => UAGB_Helper::get_css_value( $tweetBtnPaddingTop, $attr['paddingBtnUnit'] ),
		'padding-bottom'   => UAGB_Helper::get_css_value( $tweetBtnPaddingBottom, $attr['paddingBtnUnit'] ),
	);

	$selectors[' .uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:before'] = array(
		'border-right-color' => $attr['tweetBtnBgColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button:hover'] = array(
		'color' => $attr['tweetBtnHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button:hover'] = array(
		'color'            => $attr['tweetBtnHoverColor'],
		'background-color' => $attr['tweetBtnBgHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover'] = array(
		'color'            => $attr['tweetBtnHoverColor'],
		'background-color' => $attr['tweetBtnBgHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors[' .uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover:before'] = array(
		'border-right-color' => $attr['tweetBtnBgHoverColor'],
	);

	// End Backword.

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button svg'] = array(
		'fill' => $attr['tweetBtnColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:before'] = array(
		'border-right-color' => $attr['tweetBtnBgColor'],
	);

	$selectors[' a.uagb-blockquote__tweet-button svg'] = array(
		'width'  => UAGB_Helper::get_css_value( $attr['tweetBtnFontSize'], $attr['tweetBtnFontSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['tweetBtnFontSize'], $attr['tweetBtnFontSizeType'] ),
	);
 
	$icon_margin = is_rtl() ? 'margin-left' : 'margin-right';

	$selectors['.uagb-blockquote__tweet-icon_text a.uagb-blockquote__tweet-button svg'] = array(
		$icon_margin => UAGB_Helper::get_css_value( $attr['tweetIconSpacing'], $attr['tweetIconSpacingUnit'] ),
	);

	// Hover CSS.
	$selectors['.uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button:hover'] = array(
		'color' => $attr['tweetBtnHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-link a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button:hover'] = array(
		'color'            => $attr['tweetBtnHoverColor'],
		'background-color' => $attr['tweetBtnBgHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover'] = array(
		'color'            => $attr['tweetBtnHoverColor'],
		'background-color' => $attr['tweetBtnBgHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover svg'] = array(
		'fill' => $attr['tweetBtnHoverColor'],
	);

	$selectors['.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button:hover:before'] = array(
		'border-right-color' => $attr['tweetBtnBgHoverColor'],
	);
}

$t_selectors = array(
	' a.uagb-blockquote__tweet-button svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['tweetBtnFontSizeTablet'], $attr['tweetBtnFontSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['tweetBtnFontSizeTablet'], $attr['tweetBtnFontSizeType'] ),
	),
	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon' => array(
		'padding'       => UAGB_Helper::get_css_value( $attr['quotePaddingTablet'], $attr['quotePaddingType'] ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['quoteTopMarginTablet'], $attr['quotetabletUnit'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['quoteBottomMarginTablet'], $attr['quotetabletUnit'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['quoteLeftMarginTablet'], $attr['quotetabletUnit'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['quoteRightMarginTablet'], $attr['quotetabletUnit'] ),
	),
	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSizeTablet'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSizeTablet'], $attr['quoteSizeType'] ),
	),
	'.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomTablet'], $attr['tabletPaddingBtnUnit'] ),
	),
	'.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopTablet'], $attr['tabletPaddingBtnUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomTablet'], $attr['tabletPaddingBtnUnit'] ),
	),
	// Backword css.
	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSizeTablet'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSizeTablet'], $attr['quoteSizeType'] ),
	),
	' .uagb-blockquote__author-wrap'       => array(
		'margin-bottom' => UAGB_Helper::get_css_value(
			$attr['authorSpaceTablet'],
			'px'
		),
	),
	' .uagb-blockquote__author-wrap img'   => array(
		'width'         => UAGB_Helper::get_css_value( $attr['authorImageWidthTablet'], 'px' ),
		'height'        => UAGB_Helper::get_css_value( $attr['authorImageWidthTablet'], 'px' ),
		'border-radius' => UAGB_Helper::get_css_value( $attr['authorImgBorderRadiusTablet'], '%' ),
	),
	'.uagb-blockquote__skin-border blockquote.uagb-blockquote' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['borderGapTablet'], $attr['borderGapUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['verticalPaddingTablet'], $attr['verticalPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['verticalPaddingTablet'], $attr['verticalPaddingUnit'] ),
	),
	' .uagb-blockquote__content'           => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceTablet'], $attr['descSpaceUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-right img' => array(
		'margin-left'   => ( 'tablet' === $attr['stack'] ) ? '0px' : UAGB_Helper::get_css_value( $attr['authorImageGapTablet'], $attr['authorImageGapUnit'] ),
		'margin-bottom' => ( 'tablet' === $attr['stack'] ) ? UAGB_Helper::get_css_value( $attr['authorImageGapTablet'], $attr['authorImageGapUnit'] ) : '0px',
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-top img' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['authorImageGapTablet'], $attr['authorImageGapUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-left img' => array(
		'margin-right'  => ( 'tablet' === $attr['stack'] ) ? '0px' : UAGB_Helper::get_css_value( $attr['authorImageGapTablet'], $attr['authorImageGapUnit'] ),
		'margin-bottom' => ( 'tablet' === $attr['stack'] ) ? UAGB_Helper::get_css_value( $attr['authorImageGapTablet'], $attr['authorImageGapUnit'] ) : '0px',
	),
);

$m_selectors = array(
	' a.uagb-blockquote__tweet-button svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['tweetBtnFontSizeMobile'], $attr['tweetBtnFontSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['tweetBtnFontSizeMobile'], $attr['tweetBtnFontSizeType'] ),
	),
	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon' => array(
		'padding'       => UAGB_Helper::get_css_value( $attr['quotePaddingMobile'], $attr['quotePaddingType'] ),
		'margin-top'    => UAGB_Helper::get_css_value( $attr['quoteTopMarginMobile'], $attr['quotemobileUnit'] ),
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['quoteBottomMarginMobile'], $attr['quotemobileUnit'] ),
		'margin-left'   => UAGB_Helper::get_css_value( $attr['quoteLeftMarginMobile'], $attr['quotemobileUnit'] ),
		'margin-right'  => UAGB_Helper::get_css_value( $attr['quoteRightMarginMobile'], $attr['quotemobileUnit'] ),
	),
	'.uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSizeMobile'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSizeMobile'], $attr['quoteSizeType'] ),
	),
	'.uagb-blockquote__tweet-style-classic a.uagb-blockquote__tweet-button' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomMobile'], $attr['mobilePaddingBtnUnit'] ),
	),
	'.uagb-blockquote__tweet-style-bubble a.uagb-blockquote__tweet-button' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['paddingBtnLeftMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-right'  => UAGB_Helper::get_css_value( $attr['paddingBtnRightMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['paddingBtnTopMobile'], $attr['mobilePaddingBtnUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['paddingBtnBottomMobile'], $attr['mobilePaddingBtnUnit'] ),
	),
	' .uagb-blockquote__skin-quotation .uagb-blockquote__icon svg' => array(
		'width'  => UAGB_Helper::get_css_value( $attr['quoteSizeMobile'], $attr['quoteSizeType'] ),
		'height' => UAGB_Helper::get_css_value( $attr['quoteSizeMobile'], $attr['quoteSizeType'] ),
	),
	' .uagb-blockquote__author-wrap'       => array(
		'margin-bottom' => UAGB_Helper::get_css_value(
			$attr['authorSpaceMobile'],
			'px'
		),
	),
	' .uagb-blockquote__author-wrap img'   => array(
		'width'         => UAGB_Helper::get_css_value( $attr['authorImageWidthMobile'], 'px' ),
		'height'        => UAGB_Helper::get_css_value( $attr['authorImageWidthMobile'], 'px' ),
		'border-radius' => UAGB_Helper::get_css_value( $attr['authorImgBorderRadiusMobile'], '%' ),
	),
	' .uagb-blockquote__author-wrap img'   => array(
		'width'         => UAGB_Helper::get_css_value( $attr['authorImageWidthMobile'], 'px' ),
		'height'        => UAGB_Helper::get_css_value( $attr['authorImageWidthMobile'], 'px' ),
		'border-radius' => UAGB_Helper::get_css_value( $attr['authorImgBorderRadiusMobile'], '%' ),
	),
	'.uagb-blockquote__skin-border blockquote.uagb-blockquote' => array(
		'padding-left'   => UAGB_Helper::get_css_value( $attr['borderGapMobile'], $attr['borderGapUnit'] ),
		'padding-top'    => UAGB_Helper::get_css_value( $attr['verticalPaddingMobile'], $attr['verticalPaddingUnit'] ),
		'padding-bottom' => UAGB_Helper::get_css_value( $attr['verticalPaddingMobile'], $attr['verticalPaddingUnit'] ),
	),
	' .uagb-blockquote__content'           => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['descSpaceMobile'], $attr['descSpaceUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-right img' => array(
		'margin-left'   => ( 'none' !== $attr['stack'] ) ? '0px' : UAGB_Helper::get_css_value( $attr['authorImageGapMobile'], $attr['authorImageGapUnit'] ),
		'margin-bottom' => ( 'none' !== $attr['stack'] ) ? UAGB_Helper::get_css_value( $attr['authorImageGapMobile'], $attr['authorImageGapUnit'] ) : '0px',
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-top img' => array(
		'margin-bottom' => UAGB_Helper::get_css_value( $attr['authorImageGapMobile'], $attr['authorImageGapUnit'] ),
	),
	' .uagb-blockquote__author-wrap.uagb-blockquote__author-at-left img' => array(
		'margin-right'  => ( 'none' !== $attr['stack'] ) ? '0px' : UAGB_Helper::get_css_value( $attr['authorImageGapMobile'], $attr['authorImageGapUnit'] ),
		'margin-bottom' => ( 'none' !== $attr['stack'] ) ? UAGB_Helper::get_css_value( $attr['authorImageGapMobile'], $attr['authorImageGapUnit'] ) : '0px',
	),
);

$combined_selectors = array(
	'desktop' => $selectors,
	'tablet'  => $t_selectors,
	'mobile'  => $m_selectors,
);

$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'tweetBtn', ' .uagb-blockquote a.uagb-blockquote__tweet-button', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'author', ' cite.uagb-blockquote__author', $combined_selectors );
$combined_selectors = UAGB_Helper::get_typography_css( $attr, 'desc', ' .uagb-blockquote__content', $combined_selectors );

$base_selector = ( $attr['classMigrate'] ) ? '.uagb-block-' : '#uagb-blockquote-';

return UAGB_Helper::generate_all_css( $combined_selectors, $base_selector . $id );
