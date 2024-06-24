(function () {

    // Triggers sticky add to cart on scroll.
    const astraStickyAddToCart = document.querySelector(".ast-sticky-add-to-cart");

    if (astraStickyAddToCart) {
        const scrollOffset = document.querySelector('.product .single_add_to_cart_button').offsetTop;
        window.addEventListener("scroll", function () {
            if (window.scrollY >= scrollOffset) {
                astraStickyAddToCart.classList.add('is-active');
            } else {
                astraStickyAddToCart.classList.remove('is-active');
            }
        })
    }

    // Smooth scrolls if select option button is active.
    const AstraSmoothScrollBtn = document.querySelector(".ast-sticky-add-to-cart-action-wrap .single_link_to_cart_button");
    const element = document.querySelector(".single_add_to_cart_button");

    if (AstraSmoothScrollBtn && element) {
        const headerOffset = 230;
        const elementPosition = document.querySelector('.single_add_to_cart_button').offsetTop;
        if (elementPosition) {
            const offsetPosition = elementPosition - headerOffset;

            if (offsetPosition) {
                AstraSmoothScrollBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                });
            }
        }
    }

})();