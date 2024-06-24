(function ($) {
	/**
	 * BSFQuickLinks
	 *
	 * @since 1.0.0
	 */
	BSFQuickLinks = {
		/**
		 * Initializes Events.
		 *
		 * @since 1.0.0
		 * @method init
		 */
		init: function () {
			this._bind();
		},

		/**
		 * Binds events for the BSF Quick Links
		 *
		 * @since 1.0.0
		 * @access private
		 * @method _bind
		 */
		_bind: function () {
			$(document).on("click", ".bsf-quick-link", BSFQuickLinks._toggle);
			$(document).on("click", BSFQuickLinks._onClickOutside);
			$(document).on("click", BSFQuickLinks._movePopUpToTop);
			$(document).ready(BSFQuickLinks._movePopUpToTop);
		},

		_movePopUpToTop: function (e) {
			if ($('.single-site-footer').length) {
				$(".bsf-quick-link").css("bottom", "80px");
				$(".bsf-quick-link-items-wrap").css("bottom", "11em");
			} else {
				$(".bsf-quick-link").css("bottom", "40px");
				$(".bsf-quick-link-items-wrap").css("bottom", "8em");
			}
			if ($('.astra-sites-result-preview').length && !$('.astra-sites-result-preview').is(':empty')) {
				$(".bsf-quick-link").css("z-index", "-10");
			} else {
				$(".bsf-quick-link").css("z-index", "10");
			}
		},

		_onClickOutside: function (e) {
			if ($(e.target).closest('.bsf-quick-link-wrap').length === 0) {
				$('.bsf-quick-link-items-wrap').removeClass('show-popup');
			}
		},

		_toggle: function (event) {
			event.preventDefault();
			var wrap = $('.bsf-quick-link-items-wrap');
			wrap.removeClass('hide-wrapper');

			if (wrap.hasClass('show-popup')) {
				wrap.removeClass('show-popup');
			} else {
				wrap.addClass('show-popup');
			}

			var logoWrapper = $('.bsf-quick-link');
			if (logoWrapper.hasClass('show')) {
				logoWrapper.removeClass('show');
			} else {
				logoWrapper.addClass('show');
			}
		}
	};

	$(function () {
		BSFQuickLinks.init();
	});
})(jQuery);
