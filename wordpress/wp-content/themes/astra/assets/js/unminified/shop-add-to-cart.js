jQuery(document).ready(function() {
    jQuery(document.body).on('added_to_cart', function() {
        
        const singleProductButton = jQuery('.single_add_to_cart_button');
        if (!singleProductButton.hasClass('loading') && astra_shop_add_to_cart.shop_add_to_cart_action) {
            const slideInCart = jQuery('#astra-mobile-cart-drawer');
            if (astra_shop_add_to_cart.elementor_preview_active) {
                return;
            } else {
                if ('slide_in_cart' === astra_shop_add_to_cart.shop_add_to_cart_action && slideInCart) {
                    slideInCart.addClass('active');
                    jQuery('html').addClass('ast-mobile-cart-active');
                }
                if (astra_shop_add_to_cart.is_astra_pro) {
                    if ('redirect_cart_page' === astra_shop_add_to_cart.shop_add_to_cart_action) {
                        window.open(astra_shop_add_to_cart.cart_url, "_self");
                    }
                    if ('redirect_checkout_page' === astra_shop_add_to_cart.shop_add_to_cart_action) {
                        window.open(astra_shop_add_to_cart.checkout_url, "_self");
                    }
                }
            }
        }
    });
});