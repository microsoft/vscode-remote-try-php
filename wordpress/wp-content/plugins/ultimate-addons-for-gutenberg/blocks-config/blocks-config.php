<?php
/**
 * Blocks config loader.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'UAGB_DIR' ) ) {
	exit; // Exit if accessed directly, or if UAGB_DIR is not defined.
}

// Require the dynamic block classes.
require_once UAGB_DIR . 'blocks-config/post/class-uagb-post.php';
require_once UAGB_DIR . 'blocks-config/post-timeline/class-uagb-post-timeline.php';
require_once UAGB_DIR . 'blocks-config/cf7-styler/class-uagb-cf7-styler.php';
require_once UAGB_DIR . 'blocks-config/gf-styler/class-uagb-gf-styler.php';
require_once UAGB_DIR . 'blocks-config/taxonomy-list/class-uagb-taxonomy-list.php';
require_once UAGB_DIR . 'blocks-config/table-of-content/class-uagb-table-of-content.php';
require_once UAGB_DIR . 'blocks-config/forms/class-uagb-forms.php';
require_once UAGB_DIR . 'blocks-config/lottie/class-uagb-lottie.php';
require_once UAGB_DIR . 'blocks-config/image/class-uagb-image.php';
require_once UAGB_DIR . 'blocks-config/image-gallery/class-spectra-image-gallery.php';
require_once UAGB_DIR . 'blocks-config/popup-builder/class-uagb-popup-builder.php';
require_once UAGB_DIR . 'blocks-config/buttons-child/class-uagb-buttons-child.php';
require_once UAGB_DIR . 'blocks-config/google-map/class-uagb-google-map.php';
require_once UAGB_DIR . 'blocks-config/icon/class-spectra-icon.php';
require_once UAGB_DIR . 'blocks-config/faq/class-uagb-faq.php';

// Require the advanced settings PHP classes.
require_once UAGB_DIR . 'blocks-config/advanced-settings/class-uagb-block-positioning.php';
