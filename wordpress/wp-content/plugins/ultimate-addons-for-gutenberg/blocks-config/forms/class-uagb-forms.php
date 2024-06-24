<?php
/**
 * UAGB Forms.
 *
 * @package UAGB
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'UAGB_Forms' ) ) {

	/**
	 * Class UAGB_Forms.
	 */
	class UAGB_Forms {


		/**
		 * Member Variable
		 *
		 * @since 1.22.0
		 * @var instance
		 */
		private static $instance;

		/**
		 * Member Variable
		 *
		 * @since 1.22.0
		 * @var settings
		 */
		private static $settings;

		/**
		 *  Initiator
		 *
		 * @since 1.22.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 *
		 * Constructor
		 *
		 * @since 1.22.0
		 */
		public function __construct() {
			add_action( 'wp_ajax_uagb_process_forms', array( $this, 'process_forms' ) );
			add_action( 'wp_ajax_nopriv_uagb_process_forms', array( $this, 'process_forms' ) );

		}

		/**
		 * Return the blocks content for reusable block.
		 *
		 * @param int $reusable_ref_id reference id of reusable block.
		 * @since 2.6.2
		 * @return array
		 */
		public function reusable_block_content_on_page( $reusable_ref_id ) {
			if ( is_int( $reusable_ref_id ) ) {
				$content = get_post_field( 'post_content', $reusable_ref_id );
				return parse_blocks( $content );
			}
			return array();
		}

		/**
		 * Generates ids for all wp template part.
		 *
		 * @param array $block_attr attributes array.
		 * @since 2.6.2
		 * @return integer|boolean
		 */
		public function get_fse_template_part( $block_attr ) {
			if ( empty( $block_attr['slug'] ) ) {
				return false;
			}

			$id              = false;
			$slug            = $block_attr['slug'];
			$templates_parts = get_block_templates( array( 'slugs__in' => $slug ), 'wp_template_part' );
			foreach ( $templates_parts as $templates_part ) {
				if ( $slug === $templates_part->slug ) {
					$id = $templates_part->wp_id;
					break;
				}
			}
			return $id;
		}

		/**
		 * Return array of validated attributes.
		 *
		 * @param array  $block_attr of Block.
		 * @param string $block_id of Block.
		 * @since 2.6.2
		 * @return array
		 */
		public function uagb_forms_block_attr_check( $block_attr, $block_id ) {
			if ( ! empty( $block_attr['ref'] ) ) {
				$reusable_blocks_content = $this->reusable_block_content_on_page( $block_attr['ref'] );
				$block_attr              = $this->recursive_inner_forms( $reusable_blocks_content, $block_id );
			}

			if ( ! empty( $block_attr['slug'] ) ) {
				$id                      = $this->get_fse_template_part( $block_attr );
				$reusable_blocks_content = $this->reusable_block_content_on_page( $id );
				$block_attr              = $this->recursive_inner_forms( $reusable_blocks_content, $block_id );
			}

			return ( is_array( $block_attr ) && $block_attr['block_id'] === $block_id ) ? $block_attr : false;
		}

		/**
		 *  Get the Inner blocks array.
		 *
		 * @since 2.3.5
		 * @access private
		 *
		 * @param  array  $blocks_array Block Array.
		 * @param  string $block_id of Block.
		 *
		 * @return mixed $recursive_inner_forms inner blocks Array.
		 */
		private function recursive_inner_forms( $blocks_array, $block_id ) {
			if ( empty( $blocks_array ) ) {
				return;
			}

			foreach ( $blocks_array as $blocks ) {
				if ( empty( $blocks ) ) {
					continue;
				}

				if ( ! empty( $blocks['attrs'] ) && isset( $blocks['blockName'] ) && ( 'uagb/forms' === $blocks['blockName'] || 'core/block' === $blocks['blockName'] || 'core/template-part' === $blocks['blockName'] ) ) {
					$blocks_attrs = $this->uagb_forms_block_attr_check( $blocks['attrs'], $block_id );
					if ( ! $blocks_attrs ) {
						continue;
					}
					return $blocks_attrs;
				} else {
					if ( is_array( $blocks['innerBlocks'] ) && ! empty( $blocks['innerBlocks'] ) ) {
						foreach ( $blocks['innerBlocks'] as $j => $inner_block ) {
							if ( ! empty( $inner_block['attrs'] ) && isset( $inner_block['blockName'] ) && ( 'uagb/forms' === $inner_block ['blockName'] || 'core/block' === $inner_block['blockName'] || 'core/template-part' === $blocks['blockName'] ) ) {
								$inner_block_attrs = $this->uagb_forms_block_attr_check( $inner_block['attrs'], $block_id );
								if ( ! $inner_block_attrs ) {
									continue;
								}
								return $inner_block_attrs;
							} else {
								$temp_attrs = $this->recursive_inner_forms( $inner_block['innerBlocks'], $block_id );
								if ( ! empty( $temp_attrs ) && isset( $temp_attrs['block_id'] ) && $temp_attrs['block_id'] === $block_id ) {
									return $temp_attrs;
								}
							}
						}
					}
				}
			}
		}

		/**
		 *
		 * Form Process Initiated.
		 *
		 * @since 1.22.0
		 */
		public function process_forms() {
			check_ajax_referer( 'uagb_forms_ajax_nonce', 'nonce' );

			$options = array(
				'recaptcha_site_key_v2'   => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v2', '' ),
				'recaptcha_site_key_v3'   => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v3', '' ),
				'recaptcha_secret_key_v2' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v2', '' ),
				'recaptcha_secret_key_v3' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v3', '' ),
			);

			if ( empty( $_POST['post_id'] ) || empty( $_POST['block_id'] ) ) {
				wp_send_json_error( 400 );
			}
			$current_block_attributes = false;
			$block_id                 = sanitize_text_field( $_POST['block_id'] );

			$post_content = get_post_field( 'post_content', sanitize_text_field( $_POST['post_id'] ) );

			if ( has_block( 'uagb/forms', $post_content ) || has_block( 'core/block', $post_content ) ) {
				$blocks = parse_blocks( $post_content );
				if ( ! empty( $blocks ) && is_array( $blocks ) ) {
					$current_block_attributes = $this->recursive_inner_forms( $blocks, $block_id );
				}
			}
			if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
				$wp_query_args        = array(
					'post_status' => array( 'publish' ),
					'post_type'   => array( 'wp_template', 'wp_template_part' ),
				);
				$template_query       = new WP_Query( $wp_query_args );
				$template_query_posts = $template_query->posts;
				if ( ! empty( $template_query_posts ) && is_array( $template_query_posts ) ) {
					foreach ( $template_query_posts as $post ) {
						if ( ! function_exists( '_build_block_template_result_from_post' ) ) {
							continue;
						}
						$template = _build_block_template_result_from_post( $post );
						if ( is_wp_error( $template ) ) {
							continue;
						}
						$template_post_content = $template->content . ( ! empty( $post_content ) ? $post_content : '' );
						$template_content      = parse_blocks( $template_post_content );
						if ( get_template() === $template->theme && ! empty( $template_content ) && is_array( $template_content ) ) {
							$current_block_attributes = $this->recursive_inner_forms( $template_content, $block_id );
							if ( is_array( $current_block_attributes ) && $current_block_attributes['block_id'] === $block_id ) {
								break;
							}
						}
					}
				}
			}

			$widget_content = get_option( 'widget_block' );
			if ( ! empty( $widget_content ) && is_array( $widget_content ) && empty( $current_block_attributes ) ) {
				foreach ( $widget_content as $value ) {
					if ( ! is_array( $value ) || empty( $value['content'] ) ) {
						continue;
					}
					if ( has_block( 'uagb/forms', $value['content'] ) ) {
						$current_block_attributes = $this->recursive_inner_forms( parse_blocks( $value['content'] ), $block_id );
						if ( is_array( $current_block_attributes ) && $current_block_attributes['block_id'] === $block_id ) {
							break;
						}
					}
				}
			}

			// Check for $current_block_attributes is not set and check for Advanced Hooks.
			if ( empty( $current_block_attributes ) && defined( 'ASTRA_ADVANCED_HOOKS_POST_TYPE' ) ) {

				$option = array(
					'location'  => 'ast-advanced-hook-location',
					'exclusion' => 'ast-advanced-hook-exclusion',
					'users'     => 'ast-advanced-hook-users',
				);

				$result = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( ASTRA_ADVANCED_HOOKS_POST_TYPE, $option );

				if ( ! empty( $result ) && is_array( $result ) ) {
					$post_ids = array_keys( $result );

					foreach ( $post_ids as $post_id ) {

						$custom_post = get_post( $post_id );

						if ( ! $custom_post instanceof WP_Post ) {
							continue;
						}

						$post_content = $custom_post->post_content;
						if ( has_block( 'uagb/forms', $post_content ) ) {
							$blocks = parse_blocks( $post_content );
							if ( ! empty( $blocks ) && is_array( $blocks ) ) {
								$current_block_attributes = $this->recursive_inner_forms( $blocks, $block_id );
								if ( is_array( $current_block_attributes ) && $current_block_attributes['block_id'] === $block_id ) {
									break;
								}
							}
						}
					}
				}
			}

			if ( empty( $current_block_attributes ) ) {
				wp_send_json_error( 400 );
			}
			$admin_email = get_option( 'admin_email' );
			if ( is_array( $current_block_attributes ) ) {
				if ( isset( $current_block_attributes['afterSubmitToEmail'] ) && empty( trim( $current_block_attributes['afterSubmitToEmail'] ) ) && is_string( $admin_email ) ) {
					$current_block_attributes['afterSubmitToEmail'] = sanitize_email( $admin_email );
				}
				if ( ! isset( $current_block_attributes['reCaptchaType'] ) ) {
					$current_block_attributes['reCaptchaType'] = 'v2';
				}
				// bail if recaptcha is enabled and recaptchaType is not set.
				if ( ! empty( $current_block_attributes['reCaptchaEnable'] ) && empty( $current_block_attributes['reCaptchaType'] ) ) {
					wp_send_json_error( 400 );
				}

				if ( 'v2' === $current_block_attributes['reCaptchaType'] ) {

					$google_recaptcha_site_key   = $options['recaptcha_site_key_v2'];
					$google_recaptcha_secret_key = $options['recaptcha_secret_key_v2'];

				} elseif ( 'v3' === $current_block_attributes['reCaptchaType'] ) {

					$google_recaptcha_site_key   = $options['recaptcha_site_key_v3'];
					$google_recaptcha_secret_key = $options['recaptcha_secret_key_v3'];

				}

				if ( ! empty( $current_block_attributes['reCaptchaEnable'] ) && ! empty( $google_recaptcha_secret_key ) && ! empty( $google_recaptcha_site_key ) ) {

					// Google recaptcha secret key verification starts.
					$google_recaptcha = isset( $_POST['captcha_response'] ) ? sanitize_text_field( $_POST['captcha_response'] ) : '';
					$remoteip         = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';

					// calling google recaptcha api.
					$google_url = 'https://www.google.com/recaptcha/api/siteverify';

					$errors = new WP_Error();

					if ( empty( $google_recaptcha ) || empty( $remoteip ) ) {

						$errors->add( 'invalid_api', __( 'Please try logging in again to verify that you are not a robot.', 'ultimate-addons-for-gutenberg' ) );
						return $errors;

					} else {
						$google_response = wp_safe_remote_get(
							add_query_arg(
								array(
									'secret'   => $google_recaptcha_secret_key,
									'response' => $google_recaptcha,
									'remoteip' => $remoteip,
								),
								$google_url
							)
						);
						if ( is_wp_error( $google_response ) ) {

							$errors->add( 'invalid_recaptcha', __( 'Please try logging in again to verify that you are not a robot.', 'ultimate-addons-for-gutenberg' ) );
							return $errors;

						} else {
							$google_response        = wp_remote_retrieve_body( $google_response );
							$decode_google_response = json_decode( $google_response );

							if ( false === $decode_google_response->success ) {
								wp_send_json_error( 400 );
							}
						}
					}
				}
			}

			if ( empty( $google_recaptcha_secret_key ) && ! empty( $google_recaptcha_site_key ) ) {
				wp_send_json_error( 400 );
			}
			if ( ! empty( $google_recaptcha_secret_key ) && empty( $google_recaptcha_site_key ) ) {
				wp_send_json_error( 400 );
			}

			$form_data = isset( $_POST['form_data'] ) ? json_decode( stripslashes( $_POST['form_data'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$body  = '';
			$body .= '<div style="border: 50px solid #f6f6f6;">';
			$body .= '<div style="padding: 15px;">';

			foreach ( $form_data as $key => $value ) {

				if ( $key ) {

					if ( is_array( $value ) && stripos( wp_json_encode( $value ), '+' ) !== false ) {

						$val   = implode( '', $value );
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $val ) . '</p>';

					} elseif ( is_array( $value ) ) {

						$val   = implode( ', ', $value );
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $val ) . '</p>';

					} else {
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $value ) . '</p>';
					}
				}
			}
			$body .= '<p style="text-align:center;">This e-mail was sent from a ' . get_bloginfo( 'name' ) . ' ( ' . site_url() . ' )</p>';
			$body .= '</div>';
			$body .= '</div>';
			$this->send_email( $body, $form_data, $current_block_attributes );

		}

		/**
		 * Validate emails from $to, $cc and $bcc.
		 *
		 * @param array $emails array.
		 * @since 2.7.0
		 * @return array
		 */
		public function get_valid_emails( $emails ) {
			$valid_emails = array();

			if ( is_array( $emails ) ) {
				foreach ( $emails as $email ) {
					$email = trim( $email );
					$email = sanitize_email( $email );

					if ( is_email( $email ) ) {
						$valid_emails[] = $email;
					}
				}
			}

			return $valid_emails;
		}


		/**
		 *
		 * Trigger Mail.
		 *
		 * @param object $body Email Body.
		 * @param object $form_data Email Body Array.
		 * @param object $args Extra Data.
		 *
		 * @since 1.22.0
		 */
		public function send_email( $body, $form_data, $args ) {
			$to      = isset( $args['afterSubmitToEmail'] ) ? trim( $args['afterSubmitToEmail'] ) : sanitize_email( get_option( 'admin_email' ) );
			$cc      = isset( $args['afterSubmitCcEmail'] ) ? trim( $args['afterSubmitCcEmail'] ) : '';
			$bcc     = isset( $args['afterSubmitBccEmail'] ) ? trim( $args['afterSubmitBccEmail'] ) : '';
			$subject = isset( $args['afterSubmitEmailSubject'] ) ? $args['afterSubmitEmailSubject'] : __( 'Form Submission', 'ultimate-addons-for-gutenberg' );

			if ( ! empty( $to ) && is_string( $to ) ) {
				$to_emails = $this->get_valid_emails( explode( ',', $to ) );
			}

			if ( ! empty( $cc ) && is_string( $cc ) ) {
				$cc_emails = $this->get_valid_emails( explode( ',', $cc ) );
			}

			if ( ! empty( $bcc ) && is_string( $bcc ) ) {
				$bcc_emails = $this->get_valid_emails( explode( ',', $bcc ) );
			}

			if ( empty( $to_emails ) ) {
				wp_send_json_success( 400 );
			}

			$sender_email_address = ! empty( $form_data['Email'] ) ? sanitize_email( $form_data['Email'] ) : 'example@mail.com';

			$headers = array( 'Content-Type: text/html; charset=UTF-8', 'From: Email <' . $sender_email_address . '>' );

			foreach ( $to_emails as $email ) {
				$headers[] = 'Reply-To: ' . get_bloginfo( 'name' ) . ' <' . $email . '>';
			}

			if ( ! empty( $cc_emails ) ) {
				foreach ( $cc_emails as $email ) {
					$headers[] = 'Cc: ' . $email;
				}
			}

			if ( ! empty( $bcc_emails ) ) {
				foreach ( $bcc_emails as $email ) {
					$headers[] = 'Bcc: ' . $email;
				}
			}

			$successful_mail = wp_mail( $to_emails, $subject, $body, $headers );

			if ( $successful_mail ) {
				do_action( 'uagb_form_success', $form_data );
				wp_send_json_success( 200 );
			} else {
				wp_send_json_success( 400 );
			}
		}


	}

	/**
	 *  Prepare if class 'UAGB_Forms' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	UAGB_Forms::get_instance();
}
