/**
 *  Scroll To Top
 *
 * @package Astra
 * @since  1.0.0
 */

document.addEventListener("DOMContentLoaded", function() {

	var masthead        = document.querySelector( '#page header' );
	var astScrollTop 	= document.getElementById( 'ast-scroll-top' );
	if ( astScrollTop ) {
		astScrollToTop = function () {

			var content = getComputedStyle(astScrollTop).content,
				device  = astScrollTop.dataset.onDevices;
				content = content.replace( /[^0-9]/g, '' );

			if( 'both' == device || ( 'desktop' == device && '769' == content ) || ( 'mobile' == device && '' == content ) ) {

				// Get current window / document scroll.
				var  scrollTop = window.pageYOffset || document.body.scrollTop;
				// If masthead found.
				if( masthead && masthead.length ){
					if (scrollTop > masthead.offsetHeight + 100) {
						astScrollTop.style.display = "block";
					} else {
						astScrollTop.style.display = "none";
					}
				}
				else{
					// If there is no masthead set default start scroll
					if ( window.pageYOffset > 300 ) {
						astScrollTop.style.display = "block";
					} else {
						astScrollTop.style.display = "none";
					}
				}
			} else {
				astScrollTop.style.display = "none";
			}
		};
		astScrollToTop();

		window.addEventListener('scroll', function () {
			astScrollToTop();
		});

		astScrollTop.onclick = function(e){
			e.preventDefault();

			window.scrollTo({
				top: 0,
				left: 0,
				behavior: 'smooth'
			});
		};
	}
});
