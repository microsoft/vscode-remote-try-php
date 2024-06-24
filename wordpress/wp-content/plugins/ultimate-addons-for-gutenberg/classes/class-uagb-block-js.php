<?php
/**
 * UAGB Block Helper.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Block_JS' ) ) {

	/**
	 * Class UAGB_Block_JS.
	 */
	class UAGB_Block_JS {

		/**
		 * Adds Google fonts for Advanced Heading block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_advanced_heading_gfont( $attr ) {

			$head_load_google_font = isset( $attr['headLoadGoogleFonts'] ) ? $attr['headLoadGoogleFonts'] : '';
			$head_font_family      = isset( $attr['headFontFamily'] ) ? $attr['headFontFamily'] : '';
			$head_font_weight      = isset( $attr['headFontWeight'] ) ? $attr['headFontWeight'] : '';

			$subhead_load_google_font = isset( $attr['subHeadLoadGoogleFonts'] ) ? $attr['subHeadLoadGoogleFonts'] : '';
			$subhead_font_family      = isset( $attr['subHeadFontFamily'] ) ? $attr['subHeadFontFamily'] : '';
			$subhead_font_weight      = isset( $attr['subHeadFontWeight'] ) ? $attr['subHeadFontWeight'] : '';

			$highlight_head_load_google_font = isset( $attr['highLightLoadGoogleFonts'] ) ? $attr['highLightLoadGoogleFonts'] : '';
			$highlight_head_font_family      = isset( $attr['highLightFontFamily'] ) ? $attr['highLightFontFamily'] : '';
			$highlight_head_font_weight      = isset( $attr['highLightFontWeight'] ) ? $attr['highLightFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $head_load_google_font, $head_font_family, $head_font_weight );
			UAGB_Helper::blocks_google_font( $subhead_load_google_font, $subhead_font_family, $subhead_font_weight );
			UAGB_Helper::blocks_google_font( $highlight_head_load_google_font, $highlight_head_font_family, $highlight_head_font_weight );
		}

		/**
		 * Adds Google fonts for How To block.
		 *
		 * @since 1.15.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_how_to_gfont( $attr ) {

			$head_load_google_font = isset( $attr['headLoadGoogleFonts'] ) ? $attr['headLoadGoogleFonts'] : '';
			$head_font_family      = isset( $attr['headFontFamily'] ) ? $attr['headFontFamily'] : '';
			$head_font_weight      = isset( $attr['headFontWeight'] ) ? $attr['headFontWeight'] : '';

			$subhead_load_google_font = isset( $attr['subHeadLoadGoogleFonts'] ) ? $attr['subHeadLoadGoogleFonts'] : '';
			$subhead_font_family      = isset( $attr['subHeadFontFamily'] ) ? $attr['subHeadFontFamily'] : '';
			$subhead_font_weight      = isset( $attr['subHeadFontWeight'] ) ? $attr['subHeadFontWeight'] : '';

			$price_load_google_font = isset( $attr['priceLoadGoogleFonts'] ) ? $attr['priceLoadGoogleFonts'] : '';
			$price_font_family      = isset( $attr['priceFontFamily'] ) ? $attr['priceFontFamily'] : '';
			$price_font_weight      = isset( $attr['priceFontWeight'] ) ? $attr['priceFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $head_load_google_font, $head_font_family, $head_font_weight );
			UAGB_Helper::blocks_google_font( $subhead_load_google_font, $subhead_font_family, $subhead_font_weight );
			UAGB_Helper::blocks_google_font( $price_load_google_font, $price_font_family, $price_font_weight );
		}
		/**
		 * Adds Google fonts for How To Step block.
		 *
		 * @since 2.0.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_how_to_step_gfont( $attr ) {

			$url_load_google_font = isset( $attr['urlLoadGoogleFonts'] ) ? $attr['urlLoadGoogleFonts'] : '';
			$url_font_family      = isset( $attr['urlFontFamily'] ) ? $attr['urlFontFamily'] : '';
			$url_font_weight      = isset( $attr['urlFontWeight'] ) ? $attr['urlFontWeight'] : '';

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$description_load_google_font = isset( $attr['descriptionLoadGoogleFonts'] ) ? $attr['descriptionLoadGoogleFonts'] : '';
			$description_font_family      = isset( $attr['descriptionFontFamily'] ) ? $attr['descriptionFontFamily'] : '';
			$description_font_weight      = isset( $attr['descriptionFontWeight'] ) ? $attr['descriptionFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $url_load_google_font, $url_font_family, $url_font_weight );
			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $description_load_google_font, $description_font_family, $description_font_weight );
		}

		/**
		 * Adds Google fonts for review block.
		 *
		 * @since 1.19.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_review_gfont( $attr ) {

			$head_load_google_font = isset( $attr['headLoadGoogleFonts'] ) ? $attr['headLoadGoogleFonts'] : '';
			$head_font_family      = isset( $attr['headFontFamily'] ) ? $attr['headFontFamily'] : '';
			$head_font_weight      = isset( $attr['headFontWeight'] ) ? $attr['headFontWeight'] : '';

			$subhead_load_google_font = isset( $attr['subHeadLoadGoogleFonts'] ) ? $attr['subHeadLoadGoogleFonts'] : '';
			$subhead_font_family      = isset( $attr['subHeadFontFamily'] ) ? $attr['subHeadFontFamily'] : '';
			$subhead_font_weight      = isset( $attr['subHeadFontWeight'] ) ? $attr['subHeadFontWeight'] : '';

			$content_load_google_fonts = isset( $attr['contentLoadGoogleFonts'] ) ? $attr['contentLoadGoogleFonts'] : '';
			$content_font_family       = isset( $attr['contentFontFamily'] ) ? $attr['contentFontFamily'] : '';
			$content_font_weight       = isset( $attr['contentFontWeight'] ) ? $attr['contentFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $subhead_load_google_font, $subhead_font_family, $subhead_font_weight );
			UAGB_Helper::blocks_google_font( $head_load_google_font, $head_font_family, $head_font_weight );
			UAGB_Helper::blocks_google_font( $content_load_google_fonts, $content_font_family, $content_font_weight );
		}

		/**
		 * Adds Google fonts for Inline Notice block.
		 *
		 * @since 1.16.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_inline_notice_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$desc_load_google_font = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family      = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight      = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $desc_load_google_font, $desc_font_family, $desc_font_weight );
		}

		/**
		 * Adds Google fonts for CF7 Styler block.
		 *
		 * @since 1.10.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_cf7_styler_gfont( $attr ) {

			$label_load_google_font = isset( $attr['labelLoadGoogleFonts'] ) ? $attr['labelLoadGoogleFonts'] : '';
			$label_font_family      = isset( $attr['labelFontFamily'] ) ? $attr['labelFontFamily'] : '';
			$label_font_weight      = isset( $attr['labelFontWeight'] ) ? $attr['labelFontWeight'] : '';

			$input_load_google_font = isset( $attr['inputLoadGoogleFonts'] ) ? $attr['inputLoadGoogleFonts'] : '';
			$input_font_family      = isset( $attr['inputFontFamily'] ) ? $attr['inputFontFamily'] : '';
			$input_font_weight      = isset( $attr['inputFontWeight'] ) ? $attr['inputFontWeight'] : '';

			$radio_check_load_google_font = isset( $attr['radioCheckLoadGoogleFonts'] ) ? $attr['radioCheckLoadGoogleFonts'] : '';
			$radio_check_font_family      = isset( $attr['radioCheckFontFamily'] ) ? $attr['radioCheckFontFamily'] : '';
			$radio_check_font_weight      = isset( $attr['radioCheckFontWeight'] ) ? $attr['radioCheckFontWeight'] : '';

			$button_load_google_font = isset( $attr['buttonLoadGoogleFonts'] ) ? $attr['buttonLoadGoogleFonts'] : '';
			$button_font_family      = isset( $attr['buttonFontFamily'] ) ? $attr['buttonFontFamily'] : '';
			$button_font_weight      = isset( $attr['buttonFontWeight'] ) ? $attr['buttonFontWeight'] : '';

			$msg_font_load_google_font = isset( $attr['msgLoadGoogleFonts'] ) ? $attr['msgLoadGoogleFonts'] : '';
			$msg_font_family           = isset( $attr['msgFontFamily'] ) ? $attr['msgFontFamily'] : '';
			$msg_font_weight           = isset( $attr['msgFontWeight'] ) ? $attr['msgFontWeight'] : '';

			$validation_msg_load_google_font = isset( $attr['validationMsgLoadGoogleFonts'] ) ? $attr['validationMsgLoadGoogleFonts'] : '';
			$validation_msg_font_family      = isset( $attr['validationMsgFontFamily'] ) ? $attr['validationMsgFontFamily'] : '';
			$validation_msg_font_weight      = isset( $attr['validationMsgFontWeight'] ) ? $attr['validationMsgFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $msg_font_load_google_font, $msg_font_family, $msg_font_weight );
			UAGB_Helper::blocks_google_font( $validation_msg_load_google_font, $validation_msg_font_family, $validation_msg_font_weight );

			UAGB_Helper::blocks_google_font( $radio_check_load_google_font, $radio_check_font_family, $radio_check_font_weight );
			UAGB_Helper::blocks_google_font( $button_load_google_font, $button_font_family, $button_font_weight );

			UAGB_Helper::blocks_google_font( $label_load_google_font, $label_font_family, $label_font_weight );
			UAGB_Helper::blocks_google_font( $input_load_google_font, $input_font_family, $input_font_weight );
		}


		/**
		 * Adds Google fonts for Gravity Form Styler block.
		 *
		 * @since 1.12.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_gf_styler_gfont( $attr ) {

			$label_load_google_font = isset( $attr['labelLoadGoogleFonts'] ) ? $attr['labelLoadGoogleFonts'] : '';
			$label_font_family      = isset( $attr['labelFontFamily'] ) ? $attr['labelFontFamily'] : '';
			$label_font_weight      = isset( $attr['labelFontWeight'] ) ? $attr['labelFontWeight'] : '';

			$input_load_google_font = isset( $attr['inputLoadGoogleFonts'] ) ? $attr['inputLoadGoogleFonts'] : '';
			$input_font_family      = isset( $attr['inputFontFamily'] ) ? $attr['inputFontFamily'] : '';
			$input_font_weight      = isset( $attr['inputFontWeight'] ) ? $attr['inputFontWeight'] : '';

			$radio_check_load_google_font = isset( $attr['radioCheckLoadGoogleFonts'] ) ? $attr['radioCheckLoadGoogleFonts'] : '';
			$radio_check_font_family      = isset( $attr['radioCheckFontFamily'] ) ? $attr['radioCheckFontFamily'] : '';
			$radio_check_font_weight      = isset( $attr['radioCheckFontWeight'] ) ? $attr['radioCheckFontWeight'] : '';

			$button_load_google_font = isset( $attr['buttonLoadGoogleFonts'] ) ? $attr['buttonLoadGoogleFonts'] : '';
			$button_font_family      = isset( $attr['buttonFontFamily'] ) ? $attr['buttonFontFamily'] : '';
			$button_font_weight      = isset( $attr['buttonFontWeight'] ) ? $attr['buttonFontWeight'] : '';

			$msg_font_load_google_font = isset( $attr['msgLoadGoogleFonts'] ) ? $attr['msgLoadGoogleFonts'] : '';
			$msg_font_family           = isset( $attr['msgFontFamily'] ) ? $attr['msgFontFamily'] : '';
			$msg_font_weight           = isset( $attr['msgFontWeight'] ) ? $attr['msgFontWeight'] : '';

			$validation_msg_load_google_font = isset( $attr['validationMsgLoadGoogleFonts'] ) ? $attr['validationMsgLoadGoogleFonts'] : '';
			$validation_msg_font_family      = isset( $attr['validationMsgFontFamily'] ) ? $attr['validationMsgFontFamily'] : '';
			$validation_msg_font_weight      = isset( $attr['validationMsgFontWeight'] ) ? $attr['validationMsgFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $msg_font_load_google_font, $msg_font_family, $msg_font_weight );
			UAGB_Helper::blocks_google_font( $validation_msg_load_google_font, $validation_msg_font_family, $validation_msg_font_weight );

			UAGB_Helper::blocks_google_font( $radio_check_load_google_font, $radio_check_font_family, $radio_check_font_weight );
			UAGB_Helper::blocks_google_font( $button_load_google_font, $button_font_family, $button_font_weight );

			UAGB_Helper::blocks_google_font( $label_load_google_font, $label_font_family, $label_font_weight );
			UAGB_Helper::blocks_google_font( $input_load_google_font, $input_font_family, $input_font_weight );
		}

		/**
		 * Adds Google fonts for Marketing Button block.
		 *
		 * @since 1.11.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_marketing_btn_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$prefix_load_google_font = isset( $attr['prefixLoadGoogleFonts'] ) ? $attr['prefixLoadGoogleFonts'] : '';
			$prefix_font_family      = isset( $attr['prefixFontFamily'] ) ? $attr['prefixFontFamily'] : '';
			$prefix_font_weight      = isset( $attr['prefixFontWeight'] ) ? $attr['prefixFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $prefix_load_google_font, $prefix_font_family, $prefix_font_weight );
		}

		/**
		 * Adds Google fonts for Table Of Contents block.
		 *
		 * @since 1.13.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_table_of_contents_gfont( $attr ) {
			$load_google_font         = isset( $attr['loadGoogleFonts'] ) ? $attr['loadGoogleFonts'] : '';
			$font_family              = isset( $attr['fontFamily'] ) ? $attr['fontFamily'] : '';
			$font_weight              = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			$heading_load_google_font = isset( $attr['headingLoadGoogleFonts'] ) ? $attr['headingLoadGoogleFonts'] : '';
			$heading_font_family      = isset( $attr['headingFontFamily'] ) ? $attr['headingFontFamily'] : '';
			$heading_font_weight      = isset( $attr['headingFontWeight'] ) ? $attr['headingFontWeight'] : '';
			UAGB_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight );
			UAGB_Helper::blocks_google_font( $heading_load_google_font, $heading_font_family, $heading_font_weight );
		}

		/**
		 * Adds Google fonts for Blockquote.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_blockquote_gfont( $attr ) {

			$desc_load_google_font = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family      = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight      = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			$author_load_google_font = isset( $attr['authorLoadGoogleFonts'] ) ? $attr['authorLoadGoogleFonts'] : '';
			$author_font_family      = isset( $attr['authorFontFamily'] ) ? $attr['authorFontFamily'] : '';
			$author_font_weight      = isset( $attr['authorFontWeight'] ) ? $attr['authorFontWeight'] : '';

			$tweet_btn_load_google_font = isset( $attr['tweetBtnLoadGoogleFonts'] ) ? $attr['tweetBtnLoadGoogleFonts'] : '';
			$tweet_btn_font_family      = isset( $attr['tweetBtnFontFamily'] ) ? $attr['tweetBtnFontFamily'] : '';
			$tweet_btn_font_weight      = isset( $attr['tweetBtnFontWeight'] ) ? $attr['tweetBtnFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $desc_load_google_font, $desc_font_family, $desc_font_weight );
			UAGB_Helper::blocks_google_font( $author_load_google_font, $author_font_family, $author_font_weight );
			UAGB_Helper::blocks_google_font( $tweet_btn_load_google_font, $tweet_btn_font_family, $tweet_btn_font_weight );
		}

		/**
		 * Adds Google fonts for Testimonials block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_testimonial_gfont( $attr ) {
			$desc_load_google_fonts = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family       = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight       = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			$name_load_google_fonts = isset( $attr['nameLoadGoogleFonts'] ) ? $attr['nameLoadGoogleFonts'] : '';
			$name_font_family       = isset( $attr['nameFontFamily'] ) ? $attr['nameFontFamily'] : '';
			$name_font_weight       = isset( $attr['nameFontWeight'] ) ? $attr['nameFontWeight'] : '';

			$company_load_google_fonts = isset( $attr['companyLoadGoogleFonts'] ) ? $attr['companyLoadGoogleFonts'] : '';
			$company_font_family       = isset( $attr['companyFontFamily'] ) ? $attr['companyFontFamily'] : '';
			$company_font_weight       = isset( $attr['companyFontWeight'] ) ? $attr['companyFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $desc_load_google_fonts, $desc_font_family, $desc_font_weight );
			UAGB_Helper::blocks_google_font( $name_load_google_fonts, $name_font_family, $name_font_weight );
			UAGB_Helper::blocks_google_font( $company_load_google_fonts, $company_font_family, $company_font_weight );
		}

		/**
		 * Adds Google fonts for Advanced Heading block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_team_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$prefix_load_google_font = isset( $attr['prefixLoadGoogleFonts'] ) ? $attr['prefixLoadGoogleFonts'] : '';
			$prefix_font_family      = isset( $attr['prefixFontFamily'] ) ? $attr['prefixFontFamily'] : '';
			$prefix_font_weight      = isset( $attr['prefixFontWeight'] ) ? $attr['prefixFontWeight'] : '';

			$desc_load_google_font = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family      = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight      = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $prefix_load_google_font, $prefix_font_family, $prefix_font_weight );
			UAGB_Helper::blocks_google_font( $desc_load_google_font, $desc_font_family, $desc_font_weight );
		}

		/**
		 *
		 * Adds Google fonts for Restaurant Menu block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_restaurant_menu_gfont( $attr ) {
			$title_load_google_fonts = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family       = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight       = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$price_load_google_fonts = isset( $attr['priceLoadGoogleFonts'] ) ? $attr['priceLoadGoogleFonts'] : '';
			$price_font_family       = isset( $attr['priceFontFamily'] ) ? $attr['priceFontFamily'] : '';
			$price_font_weight       = isset( $attr['priceFontWeight'] ) ? $attr['priceFontWeight'] : '';

			$desc_load_google_fonts = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family       = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight       = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_fonts, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $price_load_google_fonts, $price_font_family, $price_font_weight );
			UAGB_Helper::blocks_google_font( $desc_load_google_fonts, $desc_font_family, $desc_font_weight );
		}

		/**
		 * Adds Google fonts for Content Timeline block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_content_timeline_gfont( $attr ) {
			$head_load_google_fonts = isset( $attr['headLoadGoogleFonts'] ) ? $attr['headLoadGoogleFonts'] : '';
			$head_font_family       = isset( $attr['headFontFamily'] ) ? $attr['headFontFamily'] : '';
			$head_font_weight       = isset( $attr['headFontWeight'] ) ? $attr['headFontWeight'] : '';

			$subheadload_google_fonts = isset( $attr['subHeadLoadGoogleFonts'] ) ? $attr['subHeadLoadGoogleFonts'] : '';
			$subheadfont_family       = isset( $attr['subHeadFontFamily'] ) ? $attr['subHeadFontFamily'] : '';
			$subheadfont_weight       = isset( $attr['subHeadFontWeight'] ) ? $attr['subHeadFontWeight'] : '';

			$date_load_google_fonts = isset( $attr['dateLoadGoogleFonts'] ) ? $attr['dateLoadGoogleFonts'] : '';
			$date_font_family       = isset( $attr['dateFontFamily'] ) ? $attr['dateFontFamily'] : '';
			$date_font_weight       = isset( $attr['dateFontWeight'] ) ? $attr['dateFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $head_load_google_fonts, $head_font_family, $head_font_weight );
			UAGB_Helper::blocks_google_font( $subheadload_google_fonts, $subheadfont_family, $subheadfont_weight );
			UAGB_Helper::blocks_google_font( $date_load_google_fonts, $date_font_family, $date_font_weight );
		}

		/**
		 * Adds Google fonts for Post Timeline block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_post_timeline_gfont( $attr ) {
			self::blocks_content_timeline_gfont( $attr );

			$author_load_google_fonts = isset( $attr['authorLoadGoogleFonts'] ) ? $attr['authorLoadGoogleFonts'] : '';
			$author_font_family       = isset( $attr['authorFontFamily'] ) ? $attr['authorFontFamily'] : '';
			$author_font_weight       = isset( $attr['authorFontWeight'] ) ? $attr['authorFontWeight'] : '';

			$cta_load_google_fonts = isset( $attr['ctaLoadGoogleFonts'] ) ? $attr['ctaLoadGoogleFonts'] : '';
			$cta_font_family       = isset( $attr['ctaFontFamily'] ) ? $attr['ctaFontFamily'] : '';
			$cta_font_weight       = isset( $attr['ctaFontWeight'] ) ? $attr['ctaFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $author_load_google_fonts, $author_font_family, $author_font_weight );
			UAGB_Helper::blocks_google_font( $cta_load_google_fonts, $cta_font_family, $cta_font_weight );
		}

		/**
		 * Adds Google fonts for Mulit Button's block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_buttons_gfont( $attr ) {

			$load_google_font = isset( $attr['loadGoogleFonts'] ) ? $attr['loadGoogleFonts'] : '';
			$font_family      = isset( $attr['fontFamily'] ) ? $attr['fontFamily'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';
			UAGB_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight );
		}

		/**
		 * Adds Google fonts for Post block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_post_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$meta_load_google_font = isset( $attr['metaLoadGoogleFonts'] ) ? $attr['metaLoadGoogleFonts'] : '';
			$meta_font_family      = isset( $attr['metaFontFamily'] ) ? $attr['metaFontFamily'] : '';
			$meta_font_weight      = isset( $attr['metaFontWeight'] ) ? $attr['metaFontWeight'] : '';

			$excerpt_load_google_font = isset( $attr['excerptLoadGoogleFonts'] ) ? $attr['excerptLoadGoogleFonts'] : '';
			$excerpt_font_family      = isset( $attr['excerptFontFamily'] ) ? $attr['excerptFontFamily'] : '';
			$excerpt_font_weight      = isset( $attr['excerptFontWeight'] ) ? $attr['excerptFontWeight'] : '';

			$cta_load_google_font = isset( $attr['ctaLoadGoogleFonts'] ) ? $attr['ctaLoadGoogleFonts'] : '';
			$cta_font_family      = isset( $attr['ctaFontFamily'] ) ? $attr['ctaFontFamily'] : '';
			$cta_font_weight      = isset( $attr['ctaFontWeight'] ) ? $attr['ctaFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );

			UAGB_Helper::blocks_google_font( $meta_load_google_font, $meta_font_family, $meta_font_weight );

			UAGB_Helper::blocks_google_font( $excerpt_load_google_font, $excerpt_font_family, $excerpt_font_weight );

			UAGB_Helper::blocks_google_font( $cta_load_google_font, $cta_font_family, $cta_font_weight );
		}

		/**
		 * Adds Google fonts for Advanced Heading block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_info_box_gfont( $attr ) {

			$head_load_google_font = isset( $attr['headLoadGoogleFonts'] ) ? $attr['headLoadGoogleFonts'] : '';
			$head_font_family      = isset( $attr['headFontFamily'] ) ? $attr['headFontFamily'] : '';
			$head_font_weight      = isset( $attr['headFontWeight'] ) ? $attr['headFontWeight'] : '';

			$prefix_load_google_font = isset( $attr['prefixLoadGoogleFonts'] ) ? $attr['prefixLoadGoogleFonts'] : '';
			$prefix_font_family      = isset( $attr['prefixFontFamily'] ) ? $attr['prefixFontFamily'] : '';
			$prefix_font_weight      = isset( $attr['prefixFontWeight'] ) ? $attr['prefixFontWeight'] : '';

			$subhead_load_google_font = isset( $attr['subHeadLoadGoogleFonts'] ) ? $attr['subHeadLoadGoogleFonts'] : '';
			$subhead_font_family      = isset( $attr['subHeadFontFamily'] ) ? $attr['subHeadFontFamily'] : '';
			$subhead_font_weight      = isset( $attr['subHeadFontWeight'] ) ? $attr['subHeadFontWeight'] : '';

			$cta_load_google_font = isset( $attr['ctaLoadGoogleFonts'] ) ? $attr['ctaLoadGoogleFonts'] : '';
			$cta_font_family      = isset( $attr['ctaFontFamily'] ) ? $attr['ctaFontFamily'] : '';
			$cta_font_weight      = isset( $attr['ctaFontWeight'] ) ? $attr['ctaFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $cta_load_google_font, $cta_font_family, $cta_font_weight );
			UAGB_Helper::blocks_google_font( $head_load_google_font, $head_font_family, $head_font_weight );
			UAGB_Helper::blocks_google_font( $prefix_load_google_font, $prefix_font_family, $prefix_font_weight );
			UAGB_Helper::blocks_google_font( $subhead_load_google_font, $subhead_font_family, $subhead_font_weight );
		}

		/**
		 * Adds Google fonts for Call To Action block.
		 *
		 * @since 1.9.1
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_call_to_action_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$desc_load_google_font = isset( $attr['descLoadGoogleFonts'] ) ? $attr['descLoadGoogleFonts'] : '';
			$desc_font_family      = isset( $attr['descFontFamily'] ) ? $attr['descFontFamily'] : '';
			$desc_font_weight      = isset( $attr['descFontWeight'] ) ? $attr['descFontWeight'] : '';

			$cta_load_google_font = isset( $attr['ctaLoadGoogleFonts'] ) ? $attr['ctaLoadGoogleFonts'] : '';
			$cta_font_family      = isset( $attr['ctaFontFamily'] ) ? $attr['ctaFontFamily'] : '';
			$cta_font_weight      = isset( $attr['ctaFontWeight'] ) ? $attr['ctaFontWeight'] : '';

			$second_cta_load_google_font = isset( $attr['secondCtaLoadGoogleFonts'] ) ? $attr['secondCtaLoadGoogleFonts'] : '';
			$second_cta_font_family      = isset( $attr['secondCtaFontFamily'] ) ? $attr['secondCtaFontFamily'] : '';
			$second_cta_font_weight      = isset( $attr['secondCtaFontWeight'] ) ? $attr['secondCtaFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $cta_load_google_font, $cta_font_family, $cta_font_weight );
			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $desc_load_google_font, $desc_font_family, $desc_font_weight );
			UAGB_Helper::blocks_google_font( $second_cta_load_google_font, $second_cta_font_family, $second_cta_font_weight );
		}

		/**
		 * Adds Google fonts for FAQ block.
		 *
		 * @since 1.15.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_faq_gfont( $attr ) {

			$question_load_google_font = isset( $attr['questionloadGoogleFonts'] ) ? $attr['questionloadGoogleFonts'] : '';
			$question_font_family      = isset( $attr['questionFontFamily'] ) ? $attr['questionFontFamily'] : '';
			$question_font_weight      = isset( $attr['questionFontWeight'] ) ? $attr['questionFontWeight'] : '';

			$answer_load_google_font = isset( $attr['answerloadGoogleFonts'] ) ? $attr['answerloadGoogleFonts'] : '';
			$answer_font_family      = isset( $attr['answerFontFamily'] ) ? $attr['answerFontFamily'] : '';
			$answer_font_weight      = isset( $attr['answerFontWeight'] ) ? $attr['answerFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $question_load_google_font, $question_font_family, $question_font_weight );
			UAGB_Helper::blocks_google_font( $answer_load_google_font, $answer_font_family, $answer_font_weight );

		}

		/**
		 * Adds Google fonts for WP Search block.
		 *
		 * @since 1.16.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_wp_search_gfont( $attr ) {

			$input_load_google_font = isset( $attr['inputloadGoogleFonts'] ) ? $attr['inputloadGoogleFonts'] : '';
			$input_font_family      = isset( $attr['inputFontFamily'] ) ? $attr['inputFontFamily'] : '';
			$input_font_weight      = isset( $attr['inputFontWeight'] ) ? $attr['inputFontWeight'] : '';

			$button_load_google_font = isset( $attr['buttonloadGoogleFonts'] ) ? $attr['buttonloadGoogleFonts'] : '';
			$button_font_family      = isset( $attr['buttonFontFamily'] ) ? $attr['buttonFontFamily'] : '';
			$button_font_weight      = isset( $attr['buttonFontWeight'] ) ? $attr['buttonFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $button_load_google_font, $button_font_family, $button_font_weight );
			UAGB_Helper::blocks_google_font( $input_load_google_font, $input_font_family, $input_font_weight );
		}

		/**
		 *
		 * Adds Google fonts for Separator block.
		 *
		 * @since 2.6.0
		 * @param array $attr the blocks attr.
		 * @return void
		 */
		public static function blocks_separator_gfont( $attr ) {
			$element_text_load_google_font = isset( $attr['elementTextLoadGoogleFonts'] ) ? $attr['elementTextLoadGoogleFonts'] : '';
			$element_text_font_family      = isset( $attr['elementTextFontFamily'] ) ? $attr['elementTextFontFamily'] : '';
			$element_text_font_weight      = isset( $attr['elementTextFontWeight'] ) ? $attr['elementTextFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $element_text_load_google_font, $element_text_font_family, $element_text_font_weight );
		}

		/**
		 * Adds Google fonts for Taxonomy List block.
		 *
		 * @since 1.18.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_taxonomy_list_gfont( $attr ) {

			$title_load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$title_font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$title_font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			$count_load_google_font = isset( $attr['countLoadGoogleFonts'] ) ? $attr['countLoadGoogleFonts'] : '';
			$count_font_family      = isset( $attr['countFontFamily'] ) ? $attr['countFontFamily'] : '';
			$count_font_weight      = isset( $attr['countFontWeight'] ) ? $attr['countFontWeight'] : '';

			$list_load_google_font = isset( $attr['listLoadGoogleFonts'] ) ? $attr['listLoadGoogleFonts'] : '';
			$list_font_family      = isset( $attr['listFontFamily'] ) ? $attr['listFontFamily'] : '';
			$list_font_weight      = isset( $attr['listFontWeight'] ) ? $attr['listFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $title_load_google_font, $title_font_family, $title_font_weight );
			UAGB_Helper::blocks_google_font( $count_load_google_font, $count_font_family, $count_font_weight );
			UAGB_Helper::blocks_google_font( $list_load_google_font, $list_font_family, $list_font_weight );

		}

		/**
		 * Adds Google fonts for Forms block.
		 *
		 * @since 1.22.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_forms_gfont( $attr ) {

			$submitText_load_google_font = isset( $attr['submitTextloadGoogleFonts'] ) ? $attr['submitTextloadGoogleFonts'] : '';
			$submitText_font_family      = isset( $attr['submitTextFontFamily'] ) ? $attr['submitTextFontFamily'] : '';
			$submitText_font_weight      = isset( $attr['submitTextFontWeight'] ) ? $attr['submitTextFontWeight'] : '';

			$label_load_google_font = isset( $attr['labelloadGoogleFonts'] ) ? $attr['labelloadGoogleFonts'] : '';
			$label_font_family      = isset( $attr['labelFontFamily'] ) ? $attr['labelFontFamily'] : '';
			$label_font_weight      = isset( $attr['labelFontWeight'] ) ? $attr['labelFontWeight'] : '';

			$input_load_google_font = isset( $attr['inputloadGoogleFonts'] ) ? $attr['inputloadGoogleFonts'] : '';
			$input_font_family      = isset( $attr['inputFontFamily'] ) ? $attr['inputFontFamily'] : '';
			$input_font_weight      = isset( $attr['inputFontWeight'] ) ? $attr['inputFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $submitText_load_google_font, $submitText_font_family, $submitText_font_weight );
			UAGB_Helper::blocks_google_font( $label_load_google_font, $label_font_family, $label_font_weight );
			UAGB_Helper::blocks_google_font( $input_load_google_font, $input_font_family, $input_font_weight );
		}

		/**
		 * Adds Google fonts for Star Rating block.
		 *
		 * @since 2.0.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_star_rating_gfont( $attr ) {

			$load_google_font = isset( $attr['loadGoogleFonts'] ) ? $attr['loadGoogleFonts'] : '';
			$font_family      = isset( $attr['fontFamily'] ) ? $attr['fontFamily'] : '';
			$font_weight      = isset( $attr['fontWeight'] ) ? $attr['fontWeight'] : '';

			UAGB_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight );
		}
		/**
		 * Adds Google fonts for Tabs block.
		 *
		 * @since 2.0.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_tabs_gfont( $attr ) {

			$load_google_font = isset( $attr['titleLoadGoogleFonts'] ) ? $attr['titleLoadGoogleFonts'] : '';
			$font_family      = isset( $attr['titleFontFamily'] ) ? $attr['titleFontFamily'] : '';
			$font_weight      = isset( $attr['titleFontWeight'] ) ? $attr['titleFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $load_google_font, $font_family, $font_weight );
		}

		/**
		 * Adds Google fonts for Advanced Image block.
		 *
		 * @since 2.0.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_advanced_image_gfont( $attr ) {

			$heading_load_google_font = isset( $attr['headingLoadGoogleFonts'] ) ? $attr['headingLoadGoogleFonts'] : '';
			$heading_font_family      = isset( $attr['headingFontFamily'] ) ? $attr['headingFontFamily'] : '';
			$heading_font_weight      = isset( $attr['headingFontWeight'] ) ? $attr['headingFontWeight'] : '';

			$caption_load_google_font = isset( $attr['captionLoadGoogleFonts'] ) ? $attr['captionLoadGoogleFonts'] : '';
			$caption_font_family      = isset( $attr['captionFontFamily'] ) ? $attr['captionFontFamily'] : '';
			$caption_font_weight      = isset( $attr['captionFontWeight'] ) ? $attr['captionFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $heading_load_google_font, $heading_font_family, $heading_font_weight );
			UAGB_Helper::blocks_google_font( $caption_load_google_font, $caption_font_family, $caption_font_weight );
		}

		/**
		 * Adds Google fonts for Counter block.
		 *
		 * @since 2.1.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_counter_gfont( $attr ) {

			$heading_load_google_font = isset( $attr['headingLoadGoogleFonts'] ) ? $attr['headingLoadGoogleFonts'] : '';
			$heading_font_family      = isset( $attr['headingFontFamily'] ) ? $attr['headingFontFamily'] : '';
			$heading_font_weight      = isset( $attr['headingFontWeight'] ) ? $attr['headingFontWeight'] : '';

			$number_load_google_font = isset( $attr['numberLoadGoogleFonts'] ) ? $attr['numberLoadGoogleFonts'] : '';
			$number_font_family      = isset( $attr['numberFontFamily'] ) ? $attr['numberFontFamily'] : '';
			$number_font_weight      = isset( $attr['numberFontWeight'] ) ? $attr['numberFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $heading_load_google_font, $heading_font_family, $heading_font_weight );
			UAGB_Helper::blocks_google_font( $number_load_google_font, $number_font_family, $number_font_weight );
		}

		/**
		 * Adds Google fonts for Image Gallery block.
		 *
		 * @since 2.1.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_image_gallery_gfont( $attr ) {

			$caption_load_google_font = isset( $attr['captionLoadGoogleFonts'] ) ? $attr['captionLoadGoogleFonts'] : '';
			$caption_font_family      = isset( $attr['captionFontFamily'] ) ? $attr['captionFontFamily'] : '';
			$caption_font_weight      = isset( $attr['captionFontWeight'] ) ? $attr['captionFontWeight'] : '';

			$load_more_load_google_font = isset( $attr['loadMoreLoadGoogleFonts'] ) ? $attr['loadMoreLoadGoogleFonts'] : '';
			$load_more_font_family      = isset( $attr['loadMoreFontFamily'] ) ? $attr['loadMoreFontFamily'] : '';
			$load_more_font_weight      = isset( $attr['loadMoreFontWeight'] ) ? $attr['loadMoreFontWeight'] : '';

			$lightbox_load_google_font = isset( $attr['lightboxLoadGoogleFonts'] ) ? $attr['lightboxLoadGoogleFonts'] : '';
			$lightbox_font_family      = isset( $attr['lightboxFontFamily'] ) ? $attr['lightboxFontFamily'] : '';
			$lightbox_font_weight      = isset( $attr['lightboxFontWeight'] ) ? $attr['lightboxFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $caption_load_google_font, $caption_font_family, $caption_font_weight );
			UAGB_Helper::blocks_google_font( $load_more_load_google_font, $load_more_font_family, $load_more_font_weight );
			UAGB_Helper::blocks_google_font( $lightbox_load_google_font, $lightbox_font_family, $lightbox_font_weight );
		}

		/**
		 * Adds Google fonts for Countdown block.
		 *
		 * @since 2.4.0
		 * @param array $attr the blocks attr.
		 * @return void
		 */
		public static function blocks_countdown_gfont( $attr ) {

			$digit_load_google_font = isset( $attr['digitLoadGoogleFonts'] ) ? $attr['digitLoadGoogleFonts'] : '';
			$digit_font_family      = isset( $attr['digitFontFamily'] ) ? $attr['digitFontFamily'] : '';
			$digit_font_weight      = isset( $attr['digitFontWeight'] ) ? $attr['digitFontWeight'] : '';

			$label_load_google_font = isset( $attr['labelLoadGoogleFonts'] ) ? $attr['labelLoadGoogleFonts'] : '';
			$label_font_family      = isset( $attr['labelFontFamily'] ) ? $attr['labelFontFamily'] : '';
			$label_font_weight      = isset( $attr['labelFontWeight'] ) ? $attr['labelFontWeight'] : '';

			$separator_load_google_font = isset( $attr['separatorLoadGoogleFonts'] ) ? $attr['separatorLoadGoogleFonts'] : '';
			$separator_font_family      = isset( $attr['separatorFontFamily'] ) ? $attr['separatorFontFamily'] : '';
			$separator_font_weight      = isset( $attr['separatorFontWeight'] ) ? $attr['separatorFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $digit_load_google_font, $digit_font_family, $digit_font_weight );
			UAGB_Helper::blocks_google_font( $label_load_google_font, $label_font_family, $label_font_weight );
			UAGB_Helper::blocks_google_font( $separator_load_google_font, $separator_font_family, $separator_font_weight );
		}

		/**
		 * Adds Google fonts for Modal block.
		 *
		 * @since 2.2.0
		 * @param array $attr the blocks attr.
		 */
		public static function blocks_modal_gfont( $attr ) {

			$text_load_google_font = isset( $attr['textLoadGoogleFonts'] ) ? $attr['textLoadGoogleFonts'] : '';
			$text_font_family      = isset( $attr['textFontFamily'] ) ? $attr['textFontFamily'] : '';
			$text_font_weight      = isset( $attr['textFontWeight'] ) ? $attr['textFontWeight'] : '';

			$btn_load_google_font = isset( $attr['btnLoadGoogleFonts'] ) ? $attr['btnLoadGoogleFonts'] : '';
			$btn_font_family      = isset( $attr['btnFontFamily'] ) ? $attr['btnFontFamily'] : '';
			$btn_font_weight      = isset( $attr['btnFontWeight'] ) ? $attr['btnFontWeight'] : '';

			UAGB_Helper::blocks_google_font( $text_load_google_font, $text_font_family, $text_font_weight );
			UAGB_Helper::blocks_google_font( $btn_load_google_font, $btn_font_family, $btn_font_weight );
		}
	}
}
