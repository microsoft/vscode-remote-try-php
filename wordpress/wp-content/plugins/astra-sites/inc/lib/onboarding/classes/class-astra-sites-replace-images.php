<?php
/**
 * Replace Images
 *
 * @since 3.1.4
 * @package Astra Sites
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace Images
 */
class Astra_Sites_Replace_Images {

    /**
     * Member Variable
     *
     * @var instance
     */
    private static $instance;

    /**
	 * Image index
	 *
	 * @since 4.1.0
	 * @var array<string,int>
	 */
	public static $image_index = 0;

	/**
	 * Old Images ids
	 * 
	 * @var array<int,int>
	 * @since 4.1.0
	 */
	public static $old_image_urls = array();

	/**
	 * Reusable block tracking.
	 * 
	 * @var array<int,int>
	 */
	public static $reusable_blocks = array();

	/**
	 * Filtered images.
	 * 
	 * @var array<string, array<string, string>>
	 */
	public static $filtered_images = array();

    /**
     * Initiator
     *
     * @since 3.1.4
     */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * @since 3.1.4
     */
    public function __construct() {
        add_action( 'wp_ajax_astra-sites-download-image', array( $this, 'download_selected_image' ) );
    }
	
	/**
	 * Download Images
	 *
	 * @since 4.1.0
	 * @return void
	 */
	public function download_selected_image(  ){

		check_ajax_referer( 'astra-sites', '_ajax_nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error(
                array(
                    'data' => 'You do not have permission to do this action.',
                    'status'  => false,

                )
            );
        }

		$index = isset( $_POST['index'] ) ? sanitize_text_field( wp_unslash( $_POST['index'] ) ) : '';
		$images = Astra_Sites_ZipWP_Helper::get_business_details('images');

		if ( empty( $images ) ) {
			wp_send_json_success(
				array(
					'data' => 'No images selected to download!',
					'status'  => true,
				)
			);
		}	

		$image = $images[ $index ];

		if( empty( $image ) ){
			wp_send_json_success(
				array(
					'data' => 'No image to download!',
					'status'  => true,
				)
			);
		}

		$prepare_image = array(
			'id'  => $image['id'],
			'url' => $image['url'],
			'description'  => isset( $image['description'] ) ? $image['description'] : '',
		);

		Astra_Sites_Importer_Log::add( 'Downloading Image ' . $image['url'] );
		$id = Astra_Sites_ZipWP_Helper::download_image( $prepare_image );
		Astra_Sites_Importer_Log::add( 'Downloaded Image attachment id: ' . $id );
		
		wp_send_json_success(
			array(
				'data' => 'Image downloaded successfully!',
				'status'  => true,
			)
		);
		
	}
	


    /**
     * Replace images in pages.
     * @since 4.1.0
	 * 
	 * @retuen void
     */
    public function replace_images() {
		
		$this->replace_in_pages();

		$this->replace_in_post();

		// Replace customizer content.
		if ( function_exists( 'astra_update_option' ) && function_exists( 'astra_get_option' ) ) {
			$this->replace_in_customizer();
		}

		$this->cleanup();
    }

	 /**
     * Replace images in post.
     * @since 4.1.0
	 * 
	 * @retuen void
     */
	public function replace_in_post(){

		$posts = $this->get_pages( 'post' );
		foreach ( $posts as $key => $post ) {
			if ( ! is_object( $post ) ) {
				continue;
			}

			$this->parse_featured_image( $post );
		}
	}

	/** Parses images and other content in the Spectra Info Box block.
	 *
	 * @since {{since}}
	 * @param \WP_Post $post Post.
	 * @return void
	 */
	public function parse_featured_image( $post ) {

		$image       = $this->get_image( self::$image_index );

		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return;
		}

		$image = Astra_Sites_ZipWP_Helper::download_image( $image );

		if ( is_wp_error( $image ) ) {
			Astra_Sites_Importer_Log::add( 'Replacing Image problem :  Warning: ' . wp_json_encode( $image ), 'warning' );
			return;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( ! is_array( $attachment ) ) {
			return;
		}

		Astra_Sites_Importer_Log::add( 'Replacing thumbnail Image to ' . $attachment['url'] . '" with index "' . self::$image_index . '"' );

		set_post_thumbnail( $post, $attachment['id'] );

		$this->increment_image_index();
	}

	/**
	 * Cleanup the old images.
	 * 
	 * @return void
	 * @since 4.1.0
	 */
	public function cleanup() {
		$old_image_urls = self::$old_image_urls;
		Astra_Sites_Importer_Log::add( 'Cleaning up old images - ' . print_r( $old_image_urls, true ) );
		if ( ! empty( $old_image_urls ) ) {

			$guid_list = implode("', '", $old_image_urls);

			global $wpdb;
			$query = "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND guid IN ('$guid_list')";

			$old_image_ids = $wpdb->get_results($query);
			foreach ( $old_image_ids as $old_image_id ) {
				wp_delete_attachment( $old_image_id->ID, true );
			}
		}
		delete_option( 'ast_sites_downloaded_images' );
	}
	
	/**
     * Replace images in customizer.
     *
     * @since 4.1.0
     */
	public function replace_in_customizer() {

		$footer_image_obj = astra_get_option( 'footer-bg-obj-responsive' );
		if ( isset( $footer_image_obj ) && ! empty( $footer_image_obj ) ) {
			$footer_image_obj = $this->get_updated_astra_option( $footer_image_obj );
			astra_update_option( 'footer-bg-obj-responsive', $footer_image_obj );
		}

		$header_image_obj = astra_get_option( 'header-bg-obj-responsive' );
		if ( isset( $header_image_obj ) && ! empty( $header_image_obj ) ) {
			$header_image_obj = $this->get_updated_astra_option( $header_image_obj );
			astra_update_option( 'header-bg-obj-responsive', $header_image_obj );
		}

		$blog_archieve_image_obj = astra_get_option( 'ast-dynamic-archive-post-banner-custom-bg' );
		if ( isset( $blog_archieve_image_obj ) && ! empty( $blog_archieve_image_obj ) ) {
			$blog_archieve_image_obj = $this->get_updated_astra_option( $blog_archieve_image_obj );
			astra_update_option( 'ast-dynamic-archive-post-banner-custom-bg', $blog_archieve_image_obj );
		}
		
		$social_options = $this->get_options();

		/**
		 * Social Element Options
		 */
		$this->update_social_options( $social_options );
	}

	/**
	 * Update the Social options
	 *
	 * @param array $options Social Options.
	 * @since  {{since}}
	 * @return void
	 */
	public function update_social_options( $options ) {
		if ( ! empty( $options ) ) {
			$social_profiles = Astra_Sites_ZipWP_Helper::get_business_details( 'social_profiles' );
			$business_phone = Astra_Sites_ZipWP_Helper::get_business_details( 'business_phone' );
			$business_email = Astra_Sites_ZipWP_Helper::get_business_details( 'business_email' );
			foreach ( $options as $key => $name ) {
				$value           = astra_get_option( $name );
				$items           = isset( $value['items'] ) ? $value['items'] : array();
				$social_icons    = array_map(
					function( $item ) {
						return $item['type'];
					},
					$social_profiles
				);

				$social_icons = array_merge( $social_icons, array( 'phone', 'email' ) );

				if ( is_array( $items ) && ! empty( $items ) ) {
					foreach ( $items as $index => &$item ) {

						$cached_first_item = isset( $items[0] ) ? $items[0] : [];

						if ( ! in_array( $item['id'], $social_icons, true ) ) {
							unset( $items[ $index ] );
							continue;
						}

						if ( $item['enabled'] && false !== strpos( $item['id'], 'phone' ) && '' !== $business_phone  ) {
							$item['url'] = $business_phone ;
							Astra_Sites_Importer_Log::add( 'Replacing Social Icon - "' . $item['id'] . '" Phone value with "' . $business_phone  . '"' );
						}
						if ( $item['enabled'] && false !== strpos( $item['id'], 'email' ) && '' !== $business_email ) {
							$item['url'] = $business_email;
							Astra_Sites_Importer_Log::add( 'Replacing Social Icon - "' . $item['id'] . '" email value with "' . $business_email . '"' );
						}
						if ( ! empty( $social_profiles ) ) {
							$id  = $item['id'];
							$src = array_reduce(
								$social_profiles,
								function ( $carry, $element ) use ( $id ) {
									if ( ! $carry && $element['type'] === $id ) {
										$carry = $element;
									}
									return $carry;
								}
							);
							if ( ! empty( $src ) ) {
								$item['url'] = $src['url'];
								Astra_Sites_Importer_Log::add( 'Replacing Social Icon - "' . $item['id'] . '" value with "' . $src['url'] . '"' );
							}
						}
					}
					$yelp_google = [ 'yelp', 'google' ];

					foreach ( $yelp_google as $yelp_google_item ) {
						if ( in_array( $yelp_google_item, $social_icons, true ) && ! empty( $cached_first_item ) ) {
							$new_inner_item          = $cached_first_item;
							$new_inner_item['id']    = $yelp_google_item;
							$new_inner_item['icon']  = $yelp_google_item;
							$new_inner_item['label'] = ucfirst( $yelp_google_item );
							$link                    = '#';
							foreach ( $social_profiles as $social_icon ) {
								if ( $yelp_google_item === $social_icon['type'] ) {
									$link = $social_icon['url'];
									break;
								}
							}
							$new_inner_item['url'] = $link;
							$items[]               = $new_inner_item;
						}
					}
					$value['items'] = array_values( $items );
					astra_update_option( $name, $value );
				}
			}
		}
	}


	/**
	 * Gather all options eligible for replacement algorithm.
	 * All elements placed in Header and Footer builder.
	 *
	 * @since  {{since}}
	 * @return array $options Options.
	 */
	public function get_options() {
		$zones          = array( 'above', 'below', 'primary', 'popup' );
		$header         = astra_get_option( 'header-desktop-items', array() );
		$header_mobile  = astra_get_option( 'header-mobile-items', array() );
		$footer         = astra_get_option( 'footer-desktop-items', array() );
		$social_options = array();

		foreach ( $zones as $locations ) {

			// Header - Desktop Scanning for replacement text.
			if ( ! empty( $header[ $locations ] ) ) {
				foreach ( $header[ $locations ] as $location ) {

					if ( empty( $location ) ) {
						continue;
					}

					foreach ( $location as $loc ) {
						if ( false !== strpos( $loc, 'social-icons' ) ) {
							$social_options[] = 'header-' . $loc;
						}
					}
				}
			}

			// Header - Mobile Scanning for replacement text.
			if ( ! empty( $header_mobile[ $locations ] ) ) {
				foreach ( $header_mobile[ $locations ] as $location ) {

					if ( empty( $location ) ) {
						continue;
					}

					foreach ( $location as $loc ) {
						if ( false !== strpos( $loc, 'social-icons' ) ) {
							$social_options[] = 'header-' . $loc;
						}
					}
				}
			}

			// Footer Scanning for replacement text.
			if ( ! empty( $footer[ $locations ] ) ) {
				foreach ( $footer[ $locations ] as $location ) {

					if ( empty( $location ) ) {
						continue;
					}

					foreach ( $location as $loc ) {
						if ( false !== strpos( $loc, 'social-icons' ) ) {
							$social_options[] = 'footer-' . $loc;
						}
					}
				}
			}
		}

		return $social_options;
	}

	/**
	 * Updating the header and footer background image.
	 *
	 * @since 4.1.0
	 * @param array<string,array<string,string>> $obj Reference of Block array.
	 * @return array<string,array<string,int|string>> $obj Updated Block array.
	 */
	public function get_updated_astra_option( $obj ) {
		$image_id = ( isset( $obj['desktop']['background-media'] ) ) ? $obj['desktop']['background-media'] : 0;
		if ( 0 === $image_id ) {
			return $obj;
		}
		$image       = $this->get_image( self::$image_index );

		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return $obj;
		}

		$image = Astra_Sites_ZipWP_Helper::download_image( $image );

		if ( is_wp_error( $image ) ) {
			Astra_Sites_Importer_Log::add( 'Replacing Image problem : ' . $obj['desktop']['background-image'] . ' Warning: ' . wp_json_encode( $image ) );
			return $obj;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );

		Astra_Sites_Importer_Log::add( 'Replacing Image : ' . $obj['desktop']['background-image'] . ' with: ' . $attachment['url'] );
		$obj['desktop']['background-image'] = $attachment['url'];
		$obj['desktop']['background-media'] = $attachment['id'];

		$this->increment_image_index();

		return $obj;
	}
    
    /**
	 * Replace the content with AI generated data in all Pages.
	 *
	 * @since 4.1.0
	 * @return void
	 */
	public function replace_in_pages() {

		$pages = $this->get_pages();

		foreach ( $pages as $key => $post ) {

			if ( ! is_object( $post ) ) {
				continue;
			}

			// Log the page title.
			Astra_Sites_Importer_Log::add( 'Building "' . $post->post_title . '" page with  AI content' );

			// Replaced content.
			$new_content = $this->parse_replace_images( $post->post_content );

			// Update content.
			wp_update_post(
				array(
					'ID'           => $post->ID,
					'post_content' => $new_content,
				)
			);

			Astra_Sites_Importer_Log::add( 'Replaced the images for Page ' . $post->post_title . '[ID:' . $post->ID . ']' );
		}
	}

    /**
	 * Parse the content for potential AI based content.
	 *
	 * @since 4.1.0
	 * @param string $content Post Content.
	 * @return string $content Modified content.
	 */
	public function parse_replace_images( $content ) {

		$blocks = parse_blocks( $content );

		// Get replaced blocks images.
		$content = serialize_blocks( $this->get_updated_blocks( $blocks ) );

		return $this->replace_content_glitch( $content );
	}

    	/**
	 * Update the Blocks with new mapping data.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $blocks Array of Blocks.
	 * @return array<mixed> $blocks Modified array of Blocks.
	 */
	public function get_updated_blocks( &$blocks ) {
		foreach ( $blocks as $i => &$block ) {

			if ( is_array( $block ) ) {

				if ( '' === $block['blockName'] ) {
					continue;
				}

				if ( 'core/block' === $block['blockName'] && isset( $block['attrs']['ref'] ) ) {
					$reusable_block_id = $block['attrs']['ref'];
					$reusable_block    = get_post( $reusable_block_id );

					if ( empty( $reusable_block ) || ! is_object( $reusable_block ) ) {
						continue;
					}
					Astra_Sites_Importer_Log::add( 'Reusable ID: ' . $reusable_block->ID );

					if ( in_array( $reusable_block_id, self::$reusable_blocks, true ) ) {
						continue;
					}

					self::$reusable_blocks[] = $reusable_block_id;

					Astra_Sites_Importer_Log::add( 'Replacing content for Reusable ID: ' . $reusable_block->ID );
					// Update content.
					wp_update_post(
						array(
							'ID'           => $reusable_block->ID,
							'post_content' => $this->parse_replace_images( $reusable_block->post_content ),
						)
					);
				}

                /** Replace images if present in the block */
                $this->replace_images_in_blocks( $block );

				if ( ! empty( $block['innerBlocks'] ) ) {
					/** Find the last node of the nested blocks */
					$this->get_updated_blocks( $block['innerBlocks'] );
				}
			}
		}

		return $blocks;
	}

	/**
	 * Parse social icon list.
	 *
	 * @since {{since}}
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_social_icons( $block ) {

		$social_profile = Astra_Sites_ZipWP_Helper::get_business_details( 'social_profiles' );

		if ( empty( $social_profile ) ) {
			return $block;
		}

		$social_icons = array_map(
			function( $item ) {
				return $item['type'];
			},
			$social_profile
		);

		$social_icons = array_merge( $social_icons, array( 'phone' ) );

		foreach ( $social_icons as $key => $social_icon ) {
			if ( 'linkedin' === $social_icon ) {
				$social_icons[ $key ] = 'linkedin-in';
				break;
			}
		}

		$inner_blocks = $block['innerBlocks'];

		if ( is_array( $inner_blocks ) ) {

			$cached_first_item = isset( $block['innerBlocks'][0] ) ? $block['innerBlocks'][0] : [];

			$list_social_icons = array_map(
				function( $item ) {
					return isset( $item['attrs']['icon'] ) ? $item['attrs']['icon'] : '';
				},
				$inner_blocks
			);

			if ( ! in_array( 'facebook', $list_social_icons, true ) ) {
				return $block;
			}

			foreach ( $inner_blocks as $index => &$inner_block ) {

				if ( 'uagb/icon-list-child' !== $inner_block['blockName'] ) {
					continue;
				}

				$icon = $inner_block['attrs']['icon'];

				if ( empty( $icon ) ) {
					continue;
				}

				if ( ! in_array( $icon, $social_icons, true ) ) {
					unset( $block['innerBlocks'][ $index ] );
				}
			}

			$yelp_google = [
				'yelp'   => '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 384 512"><path d="M42.9 240.3l99.62 48.61c19.2 9.4 16.2 37.51-4.5 42.71L30.5 358.5a22.79 22.79 0 0 1 -28.21-19.6 197.2 197.2 0 0 1 9-85.32 22.8 22.8 0 0 1 31.61-13.21zm44 239.3a199.4 199.4 0 0 0 79.42 32.11A22.78 22.78 0 0 0 192.9 490l3.9-110.8c.7-21.3-25.5-31.91-39.81-16.1l-74.21 82.4a22.82 22.82 0 0 0 4.09 34.09zm145.3-109.9l58.81 94a22.93 22.93 0 0 0 34 5.5 198.4 198.4 0 0 0 52.71-67.61A23 23 0 0 0 364.2 370l-105.4-34.26c-20.31-6.5-37.81 15.8-26.51 33.91zm148.3-132.2a197.4 197.4 0 0 0 -50.41-69.31 22.85 22.85 0 0 0 -34 4.4l-62 91.92c-11.9 17.7 4.7 40.61 25.2 34.71L366 268.6a23 23 0 0 0 14.61-31.21zM62.11 30.18a22.86 22.86 0 0 0 -9.9 32l104.1 180.4c11.7 20.2 42.61 11.9 42.61-11.4V22.88a22.67 22.67 0 0 0 -24.5-22.8 320.4 320.4 0 0 0 -112.3 30.1z"></path></svg>',
				'google' => '<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 488 512"><path d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"></path></svg>',
			];

			foreach ( $yelp_google as $yelp_google_key => $yelp_google_item ) {
				if ( in_array( $yelp_google_key, $social_icons, true ) && ! empty( $cached_first_item ) ) {

					$new_inner_block                  = $cached_first_item;
					$new_inner_block['attrs']['icon'] = $yelp_google_key;
					$link                             = '#';
					foreach ( $social_profile as $social_icon ) {
						if ( $yelp_google_key === $social_icon['type'] ) {
							$link = $social_icon['url'];
							break;
						}
					}
					$new_inner_block['attrs']['link'] = $link;

					$svg_pattern = '/<svg.*?>.*?<\/svg>/s'; // The 's' modifier allows the dot (.) to match newline characters.
					preg_match( $svg_pattern, $new_inner_block['innerHTML'], $matches );
					$new_inner_block['innerHTML'] = str_replace( $matches[0], $yelp_google_item, $new_inner_block['innerHTML'] );

					$href_pattern = '/href=".*?"/s'; // The 's' modifier allows the dot (.) to match newline characters.
					preg_match( $href_pattern, $new_inner_block['innerHTML'], $href_matches );
					if ( ! empty( $href_matches ) ) {
						$new_inner_block['innerHTML'] = str_replace( $href_matches[0], 'href="' . $link . '"', $new_inner_block['innerHTML'] );
					}
					foreach ( $new_inner_block['innerContent'] as $key => $inner_content ) {
						if ( empty( $inner_content ) ) {
							continue;
						}
						preg_match( $svg_pattern, $inner_content, $matches );
						if ( ! empty( $matches ) ) {
							$new_inner_block['innerContent'][ $key ] = str_replace( $matches[0], $yelp_google_item, $new_inner_block['innerContent'][ $key ] );
						}

						preg_match( $href_pattern, $inner_content, $href_matches );
						if ( ! empty( $href_matches ) ) {
							$new_inner_block['innerContent'][ $key ] = str_replace( $href_matches[0], 'href="' . $link . '"', $new_inner_block['innerContent'][ $key ] );
						}
					}

					array_push( $block['innerBlocks'], $new_inner_block );

					$last_index = count( $block['innerContent'] ) - 1;
					array_splice( $block['innerContent'], $last_index, 0, '' );

					$new_last_index = count( $block['innerContent'] ) - 1;
					array_splice( $block['innerContent'], $new_last_index, 0, [ null ] );
				}
			}

			$block['innerBlocks'] = array_values( $block['innerBlocks'] );
		}

		return $block;
	}

    /**
	 * Replace the image in the block if present from the AI generated images.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Reference of Block array.
	 * @return void
	 */
	public function replace_images_in_blocks( &$block ) {
		switch ( $block['blockName'] ) {
			case 'uagb/container':
				$block = $this->parse_spectra_container( $block );
				break;

			case 'uagb/image':
				$block = $this->parse_spectra_image( $block );
				break;

			case 'uagb/image-gallery':
				$block = $this->parse_spectra_gallery( $block );
				break;

			case 'uagb/info-box':
				$block = $this->parse_spectra_infobox( $block );
				break;

			case 'uagb/google-map':
				$block = $this->parse_spectra_google_map( $block );
				break;
			
			case 'uagb/forms':
				$block = $this->parse_spectra_form( $block );
				break;

			case 'uagb/icon-list':
				$block = $this->parse_social_icons( $block );
		}
	}

    /**
	 * Get pages.
	 *
	 * @return array<int|\WP_Post> Array for pages.
	 * @param string $type Post type.
	 * @since 4.1.0
	 */
	public static function get_pages( $type = 'page' ) {
		$query_args = array(
			'post_type'           => array( $type ),
			// Query performance optimization.
			'fields'              => array( 'ids', 'post_content', 'post_title' ),
			'posts_per_page'      => '10',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'     => array(
				array(
					'key'     => '_astra_sites_imported_post', // Replace 'your_meta_key' with your actual meta key
					'value'   => '1', // Replace 'desired_meta_value' with the value you are querying
					'compare' => '=', // Change the comparison operator if needed
				),
			),
		);

		$query = new WP_Query( $query_args );

		$desired_first_page_id = intval( get_option( 'page_on_front', 0 ) );
		$pages                 = $query->posts ? $query->posts : [];

		$desired_page_index = false;

		if ( is_array( $pages ) && ! empty( $pages ) && ! empty( $desired_first_page_id ) ) {
			foreach ( $pages as $key => $page ) {

				if ( isset( $page->ID ) && $page->ID === $desired_first_page_id ) {
					$desired_page_index = $key;
					break;
				}
			}

			if ( false !== $desired_page_index ) {
				$desired_page = $pages[ $desired_page_index ];
				unset( $pages[ $desired_page_index ] );
				array_unshift( $pages, $desired_page );
			}
		}

		return $pages;
	}

	/**
	 * Parses Spectra form block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return void
	 */
	public function parse_spectra_form( $block ) {

		$business_email = Astra_Sites_ZipWP_Helper::get_business_details( 'business_email' );

		if ( ! empty( $business_email ) ) {
			$block['attrs']['afterSubmitToEmail'] = $business_email;
		}

		return $block;
	}

	/**
	 * Parses Google Map for the Spectra Google Map block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_google_map( $block ) {

		$address = Astra_Sites_ZipWP_Helper::get_business_details( 'business_address' );
		if ( empty( $address ) ) {
			return $block;
		}

		$block['attrs']['address'] = $address;
		Astra_Sites_Importer_Log::add( 'Replacing Google Map to "' . $address );

		return $block;
	}

    /**
	 * Parses images and other content in the Spectra Container block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_container( $block ) {
		
		if (
			! isset( $block['attrs']['backgroundImageDesktop'] ) ||
			empty( $block['attrs']['backgroundImageDesktop'] ) ||
			$this->is_skipable( $block['attrs']['backgroundImageDesktop']['url'] )
		) {
			return $block;
		}

		$image       = $this->get_image( self::$image_index );
		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return $block;
		}

		$image = Astra_Sites_ZipWP_Helper::download_image( $image );

		if ( is_wp_error( $image ) ) {
			Astra_Sites_Importer_Log::add( 'Replacing Image problem : ' . $block['attrs']['backgroundImageDesktop']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( is_wp_error( $attachment ) || ! is_array( $attachment ) ) {
			return $block;
		}

		self::$old_image_urls[] = $block['attrs']['backgroundImageDesktop']['url'];
		Astra_Sites_Importer_Log::add( 'Replacing Image from ' . $block['attrs']['backgroundImageDesktop']['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . self::$image_index . '"' );
		$block['attrs']['backgroundImageDesktop'] = $attachment;
		$this->increment_image_index();

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Info Box block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_infobox( $block ) {
		
		if (
			! isset( $block['attrs']['iconImage'] ) ||
			empty( $block['attrs']['iconImage'] ) ||
			$this->is_skipable( $block['attrs']['iconImage']['url'] )
		) {
			return $block;
		}

		$image       = $this->get_image( self::$image_index );
		if ( empty( $image ) || ! is_array( $image ) || is_bool( $image ) ) {
			return $block;
		}

		$image = Astra_Sites_ZipWP_Helper::download_image( $image );

		if ( is_wp_error( $image ) ) {
			Astra_Sites_Importer_Log::add( 'Replacing Image problem : ' . $block['attrs']['iconImage']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );

		if ( is_wp_error( $attachment ) || ! is_array( $attachment ) ) {
			return $block;
		}
		
		self::$old_image_urls[] = $block['attrs']['iconImage']['url'];
		if ( ! empty( $block['attrs']['iconImage']['url'] ) ) {

			Astra_Sites_Importer_Log::add( 'Replacing Image from ' . $block['attrs']['iconImage']['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . self::$image_index . '"' );
			$block['innerHTML'] = str_replace( $block['attrs']['iconImage']['url'], $attachment['url'], $block['innerHTML'] );
		}

		foreach ( $block['innerContent'] as $key => &$inner_content ) {

			if ( is_string( $block['innerContent'][ $key ] ) && '' === trim( $block['innerContent'][ $key ] ) ) {
				continue;
			}
			$block['innerContent'][ $key ] = str_replace( $block['attrs']['iconImage']['url'], $attachment['url'], $block['innerContent'][ $key ] );
		}
		$block['attrs']['iconImage'] = $attachment;
		$this->increment_image_index();

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Image block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_image( $block ) {
		
		if (
			! isset( $block['attrs']['url'] ) ||
			$this->is_skipable( $block['attrs']['url'] )
		) {
			return $block;
		}

		$image       = $this->get_image( self::$image_index );

		if ( empty( $image ) || ! is_array( $image ) ) {
			return $block;
		}

		$image = Astra_Sites_ZipWP_Helper::download_image( $image );

		if ( is_wp_error( $image ) ) {
			Astra_Sites_Importer_Log::add( 'Replacing Image problem : ' . $block['attrs']['url'] . ' Warning: ' . wp_json_encode( $image ) );
			return $block;
		}

		$attachment = wp_prepare_attachment_for_js( absint( $image ) );
		if ( is_wp_error( $attachment ) || ! is_array( $attachment ) ) {
			return $block;
		}

		self::$old_image_urls[] = $block['attrs']['url'];
		Astra_Sites_Importer_Log::add( 'Replacing Image from ' . $block['attrs']['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . self::$image_index . '"' );
		$block['innerHTML'] = str_replace( $block['attrs']['url'], $attachment['url'], $block['innerHTML'] );

		$tablet_size_slug = ! empty( $block['attrs']['sizeSlugTablet'] ) ? $block['attrs']['sizeSlugTablet'] : '';
		$mobile_size_slug = ! empty( $block['attrs']['sizeSlugMobile'] ) ? $block['attrs']['sizeSlugMobile'] : '';
		$tablet_dest_url  = '';
		$mobile_dest_url  = '';

		if (
			isset( $block['attrs']['urlTablet'] ) &&
			! empty( $block['attrs']['urlTablet'] ) &&
			! empty( $tablet_size_slug )
		) {
			$tablet_dest_url             = isset( $attachment['sizes'][ $tablet_size_slug ]['url'] ) ? $attachment['sizes'][ $tablet_size_slug ]['url'] : $attachment['url'];
			$block['innerHTML']          = str_replace( $block['attrs']['urlTablet'], $tablet_dest_url, $block['innerHTML'] );
			$block['attrs']['urlTablet'] = $tablet_dest_url;
		}
		if (
			isset( $block['attrs']['urlMobile'] ) &&
			! empty( $block['attrs']['urlMobile'] ) &&
			! empty( $mobile_size_slug )
		) {
			$mobile_dest_url             = isset( $attachment['sizes'][ $mobile_size_slug ]['url'] ) ? $attachment['sizes'][ $mobile_size_slug ]['url'] : $attachment['url'];
			$block['innerHTML']          = str_replace( $block['attrs']['urlMobile'], $mobile_dest_url, $block['innerHTML'] );
			$block['attrs']['urlMobile'] = $mobile_dest_url;
		}

		$block['innerHTML'] = str_replace( 'uag-image-' . $block['attrs']['id'], 'uag-image-' . $attachment['id'], $block['innerHTML'] );

		foreach ( $block['innerContent'] as $key => &$inner_content ) {

			if ( is_string( $block['innerContent'][ $key ] ) && '' === trim( $block['innerContent'][ $key ] ) ) {
				continue;
			}
			$block['innerContent'][ $key ] = str_replace( $block['attrs']['url'], $attachment['url'], $block['innerContent'][ $key ] );

			if ( isset( $block['attrs']['urlTablet'] ) && ! empty( $block['attrs']['urlTablet'] ) ) {
				$block['innerContent'][ $key ] = str_replace( $block['attrs']['urlTablet'], $tablet_dest_url, $block['innerContent'][ $key ] );
			}
			if ( isset( $block['attrs']['urlMobile'] ) && ! empty( $block['attrs']['urlMobile'] ) ) {
				$block['innerContent'][ $key ] = str_replace( $block['attrs']['urlMobile'], $mobile_dest_url, $block['innerContent'][ $key ] );
			}

			$block['innerContent'][ $key ] = str_replace( 'uag-image-' . $block['attrs']['id'], 'uag-image-' . $attachment['id'], $block['innerContent'][ $key ] );
		}

		$block['attrs']['url'] = $attachment['url'];
		$block['attrs']['id']  = $attachment['id'];


		$this->increment_image_index();

		return $block;
	}

	/**
	 * Parses images and other content in the Spectra Info Box block.
	 *
	 * @since 4.1.0
	 * @param array<mixed> $block Block.
	 * @return array<mixed> $block Block.
	 */
	public function parse_spectra_gallery( $block ) {
		$images      = $block['attrs']['mediaGallery'];
		$gallery_ids = [];
		foreach ( $images as $key => &$image ) {

			if (
				! isset( $image ) ||
				empty( $image ) ||
				$this->is_skipable( $image['url'] )
			) {
				continue;
			}
			
			$new_image   = $this->get_image( self::$image_index );

			if ( empty( $new_image ) || ! is_array( $new_image ) || is_bool( $new_image ) ) {
				continue;
			}

			$new_image = Astra_Sites_ZipWP_Helper::download_image( $new_image );

			if ( is_wp_error( $new_image ) ) {
				Astra_Sites_Importer_Log::add( 'Replacing Image problem : ' . $image['url'] . ' Warning: ' . wp_json_encode( $new_image ) );
				continue;
			}

			$attachment = wp_prepare_attachment_for_js( absint( $new_image ) );

			if ( is_wp_error( $attachment ) || ! is_array( $attachment ) ) {
				continue;
			}

			$gallery_ids[] = $attachment['id'];
			self::$old_image_urls[] = $image['url'];

			Astra_Sites_Importer_Log::add( 'Replacing Image from ' . $image['url'] . ' to "' . $attachment['url'] . '" for ' . $block['blockName'] . '" with index "' . self::$image_index . '"' );
			$image['url']     = ! empty( $attachment['url'] ) ? $attachment['url'] : $image['url'];
			$image['sizes']   = ! empty( $attachment['sizes'] ) ? $attachment['sizes'] : $image['sizes'];
			$image['mime']    = ! empty( $attachment['mime'] ) ? $attachment['mime'] : $image['mime'];
			$image['type']    = ! empty( $attachment['type'] ) ? $attachment['type'] : $image['type'];
			$image['subtype'] = ! empty( $attachment['subtype'] ) ? $attachment['subtype'] : $image['subtype'];
			$image['id']      = ! empty( $attachment['id'] ) ? $attachment['id'] : $image['id'];
			$image['alt']     = ! empty( $attachment['alt'] ) ? $attachment['alt'] : $image['alt'];
			$image['link']    = ! empty( $attachment['link'] ) ? $attachment['link'] : $image['link'];

			$this->increment_image_index();
		}
		$block['attrs']['mediaGallery'] = $images;
		$block['attrs']['mediaIDs']     = $gallery_ids;

		return $block;
	}

    /**
	 * Check if we need to skip the URL.
	 *
	 * @param string $url URL to check.
	 * @return boolean
	 * @since 4.1.0
	 */
	public static function is_skipable( $url ) {
		if ( strpos( $url, 'skip' ) !== false ) {
			return true;
		}
		return false;
	}

    /**
	 * Get Image for the specified index
	 *
	 * @param int    $index Index of the image.
	 * @return array|boolean Array of images or false.
	 * @since 4.1.0
	 */
	public function get_image( $index = 0 ) {

		$this->set_images();
		Astra_Sites_Importer_Log::add( 'Fetching image with index ' . $index );
		return ( isset( self::$filtered_images[ $index ] ) ) ? self::$filtered_images[ $index ] : false;
	}

	/**
	 * Set Image as per oriantation
	 *
	 * @return void
	 */
	public function set_images() {
		if( empty( self::$filtered_images ) ){
			$images = Astra_Sites_ZipWP_Helper::get_business_details('images');
			if( ! empty( $images ) ){
				foreach ( $images as $image ) {
					self::$filtered_images[] = $image;
				}
			} else {
				$placeholder_images = Astra_Sites_ZipWP_Helper::get_image_placeholders();
				self::$filtered_images[] = $placeholder_images[0];
				self::$filtered_images[] = $placeholder_images[1];
			}
		}
	}

    /**
	 * Increment Image index
	 *
	 *
	 * @return void
	 */
	public function increment_image_index() {

		$this->set_images();

		$new_index = self::$image_index + 1;

		if ( ! isset( self::$filtered_images[ $new_index ] ) ) {
			$new_index = 0;
		}

		self::$image_index = $new_index;
	}

	/**
	 * Fix to alter the Astra global color variables.
	 *
	 * @since {{since}}
	 * @param string $content Post Content.
	 * @return string $content Modified content.
	 */
	public function replace_content_glitch( $content ) {
		$content = str_replace( 'var(\u002d\u002dast', 'var(--ast', $content );
		$content = str_replace( 'var(u002du002dast', 'var(--ast', $content );
		$content = str_replace( ' u0026', '&amp;', $content );
		$content = str_replace( '\u0026', '&amp;', $content );
		return $content;
	}
    
}

Astra_Sites_Replace_Images::get_instance();