<?php
namespace Automattic\WooCommerce\Internal\Admin\Orders;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\Traits\AccessiblePrivateMethods;

/**
 * Controls the different pages/screens associated to the "Orders" menu page.
 */
class PageController {

	use AccessiblePrivateMethods;

	/**
	 * The order type.
	 *
	 * @var string
	 */
	private $order_type = '';

	/**
	 * Instance of the posts redirection controller.
	 *
	 * @var PostsRedirectionController
	 */
	private $redirection_controller;

	/**
	 * Instance of the orders list table.
	 *
	 * @var ListTable
	 */
	private $orders_table;

	/**
	 * Instance of orders edit form.
	 *
	 * @var Edit
	 */
	private $order_edit_form;

	/**
	 * Current action.
	 *
	 * @var string
	 */
	private $current_action = '';

	/**
	 * Order object to be used in edit/new form.
	 *
	 * @var \WC_Order
	 */
	private $order;

	/**
	 * Verify that user has permission to edit orders.
	 *
	 * @return void
	 */
	private function verify_edit_permission() {
		if ( 'edit_order' === $this->current_action && ( ! isset( $this->order ) || ! $this->order ) ) {
			wp_die( esc_html__( 'You attempted to edit an order that does not exist. Perhaps it was deleted?', 'woocommerce' ) );
		}

		if ( $this->order->get_type() !== $this->order_type ) {
			wp_die( esc_html__( 'Order type mismatch.', 'woocommerce' ) );
		}

		if ( ! current_user_can( get_post_type_object( $this->order_type )->cap->edit_post, $this->order->get_id() ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You do not have permission to edit this order', 'woocommerce' ) );
		}

		if ( 'trash' === $this->order->get_status() ) {
			wp_die( esc_html__( 'You cannot edit this item because it is in the Trash. Please restore it and try again.', 'woocommerce' ) );
		}
	}

	/**
	 * Verify that user has permission to create order.
	 *
	 * @return void
	 */
	private function verify_create_permission() {
		if ( ! current_user_can( get_post_type_object( $this->order_type )->cap->publish_posts ) && ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'You don\'t have permission to create a new order', 'woocommerce' ) );
		}

		if ( isset( $this->order ) ) {
			$this->verify_edit_permission();
		}
	}

	/**
	 * Claims the lock for the order being edited/created (unless it belongs to someone else).
	 * Also handles the 'claim-lock' action which allows taking over the order forcefully.
	 *
	 * @return void
	 */
	private function handle_edit_lock() {
		if ( ! $this->order ) {
			return;
		}

		$edit_lock = wc_get_container()->get( EditLock::class );

		$locked = $edit_lock->is_locked_by_another_user( $this->order );

		// Take over order?
		if ( ! empty( $_GET['claim-lock'] ) && wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'claim-lock-' . $this->order->get_id() ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$edit_lock->lock( $this->order );
			wp_safe_redirect( $this->get_edit_url( $this->order->get_id() ) );
			exit;
		}

		if ( ! $locked ) {
			$edit_lock->lock( $this->order );
		}

		add_action(
			'admin_footer',
			function() use ( $edit_lock ) {
				$edit_lock->render_dialog( $this->order );
			}
		);
	}

	/**
	 * Sets up the page controller, including registering the menu item.
	 *
	 * @return void
	 */
	public function setup(): void {
		global $plugin_page, $pagenow;

		$this->redirection_controller = new PostsRedirectionController( $this );

		// Register menu.
		if ( 'admin_menu' === current_action() ) {
			$this->register_menu();
		} else {
			add_action( 'admin_menu', 'register_menu', 9 );
		}

		// Not on an Orders page.
		if ( 'admin.php' !== $pagenow || 0 !== strpos( $plugin_page, 'wc-orders' ) ) {
			return;
		}

		$this->set_order_type();
		$this->set_action();

		$page_suffix = ( 'shop_order' === $this->order_type ? '' : '--' . $this->order_type );

		self::add_action( 'load-woocommerce_page_wc-orders' . $page_suffix, array( $this, 'handle_load_page_action' ) );
		self::add_action( 'admin_title', array( $this, 'set_page_title' ) );
	}

	/**
	 * Perform initialization for the current action.
	 */
	private function handle_load_page_action() {
		$screen            = get_current_screen();
		$screen->post_type = $this->order_type;

		if ( method_exists( $this, 'setup_action_' . $this->current_action ) ) {
			$this->{"setup_action_{$this->current_action}"}();
		}
	}

	/**
	 * Set the document title for Orders screens to match what it would be with the shop_order CPT.
	 *
	 * @param string $admin_title The admin screen title before it's filtered.
	 *
	 * @return string The filtered admin title.
	 */
	private function set_page_title( $admin_title ) {
		if ( ! $this->is_order_screen( $this->order_type ) ) {
			return $admin_title;
		}

		$wp_order_type = get_post_type_object( $this->order_type );
		$labels        = get_post_type_labels( $wp_order_type );

		if ( $this->is_order_screen( $this->order_type, 'list' ) ) {
			$admin_title = sprintf(
				// translators: 1: The label for an order type 2: The name of the website.
				esc_html__( '%1$s &lsaquo; %2$s &#8212; WordPress', 'woocommerce' ),
				esc_html( $labels->name ),
				esc_html( get_bloginfo( 'name' ) )
			);
		} elseif ( $this->is_order_screen( $this->order_type, 'edit' ) ) {
			$admin_title = sprintf(
				// translators: 1: The label for an order type 2: The title of the order 3: The name of the website.
				esc_html__( '%1$s #%2$s &lsaquo; %3$s &#8212; WordPress', 'woocommerce' ),
				esc_html( $labels->edit_item ),
				absint( $this->order->get_id() ),
				esc_html( get_bloginfo( 'name' ) )
			);
		} elseif ( $this->is_order_screen( $this->order_type, 'new' ) ) {
			$admin_title = sprintf(
				// translators: 1: The label for an order type 2: The name of the website.
				esc_html__( '%1$s &lsaquo; %2$s &#8212; WordPress', 'woocommerce' ),
				esc_html( $labels->add_new_item ),
				esc_html( get_bloginfo( 'name' ) )
			);
		}

		return $admin_title;
	}

	/**
	 * Determines the order type for the current screen.
	 *
	 * @return void
	 */
	private function set_order_type() {
		global $plugin_page;

		$this->order_type = str_replace( array( 'wc-orders--', 'wc-orders' ), '', $plugin_page );
		$this->order_type = empty( $this->order_type ) ? 'shop_order' : $this->order_type;

		$wc_order_type = wc_get_order_type( $this->order_type );
		$wp_order_type = get_post_type_object( $this->order_type );

		if ( ! $wc_order_type || ! $wp_order_type || ! $wp_order_type->show_ui || ! current_user_can( $wp_order_type->cap->edit_posts ) ) {
			wp_die();
		}
	}

	/**
	 * Sets the current action based on querystring arguments. Defaults to 'list_orders'.
	 *
	 * @return void
	 */
	private function set_action(): void {
		switch ( isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '' ) {
			case 'edit':
				$this->current_action = 'edit_order';
				break;
			case 'new':
				$this->current_action = 'new_order';
				break;
			default:
				$this->current_action = 'list_orders';
				break;
		}
	}

	/**
	 * Registers the "Orders" menu.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		$order_types = wc_get_order_types( 'admin-menu' );

		foreach ( $order_types as $order_type ) {
			$post_type = get_post_type_object( $order_type );

			add_submenu_page(
				'woocommerce',
				$post_type->labels->name,
				$post_type->labels->menu_name,
				$post_type->cap->edit_posts,
				'wc-orders' . ( 'shop_order' === $order_type ? '' : '--' . $order_type ),
				array( $this, 'output' )
			);
		}

		// In some cases (such as if the authoritative order store was changed earlier in the current request) we
		// need an extra step to remove the menu entry for the menu post type.
		add_action(
			'admin_init',
			function() use ( $order_types ) {
				foreach ( $order_types as $order_type ) {
					remove_submenu_page( 'woocommerce', 'edit.php?post_type=' . $order_type );
				}
			}
		);
	}

	/**
	 * Outputs content for the current orders screen.
	 *
	 * @return void
	 */
	public function output(): void {
		switch ( $this->current_action ) {
			case 'edit_order':
			case 'new_order':
				$this->order_edit_form->display();
				break;
			case 'list_orders':
			default:
				$this->orders_table->prepare_items();
				$this->orders_table->display();
				break;
		}
	}

	/**
	 * Handles initialization of the orders list table.
	 *
	 * @return void
	 */
	private function setup_action_list_orders(): void {
		$this->orders_table = wc_get_container()->get( ListTable::class );
		$this->orders_table->setup(
			array(
				'order_type' => $this->order_type,
			)
		);

		if ( $this->orders_table->current_action() ) {
			$this->orders_table->handle_bulk_actions();
		}

		$this->strip_http_referer();
	}

	/**
	 * Perform a redirect to remove the `_wp_http_referer` and `_wpnonce` strings if present in the URL (see also
	 * wp-admin/edit.php where a similar process takes place), otherwise the size of this field builds to an
	 * unmanageable length over time.
	 */
	private function strip_http_referer(): void {
		$current_url  = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
		$stripped_url = remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), $current_url );

		if ( $stripped_url !== $current_url ) {
			wp_safe_redirect( $stripped_url );
			exit;
		}
	}

	/**
	 * Prepares the order edit form for creating or editing an order.
	 *
	 * @see \Automattic\WooCommerce\Internal\Admin\Orders\Edit.
	 * @since 8.1.0
	 */
	private function prepare_order_edit_form(): void {
		if ( ! $this->order || ! in_array( $this->current_action, array( 'new_order', 'edit_order' ), true ) ) {
			return;
		}

		$this->order_edit_form = $this->order_edit_form ?? new Edit();
		$this->order_edit_form->setup( $this->order );
		$this->order_edit_form->set_current_action( $this->current_action );
	}

	/**
	 * Handles initialization of the orders edit form.
	 *
	 * @return void
	 */
	private function setup_action_edit_order(): void {
		global $theorder;
		$this->order = wc_get_order( absint( isset( $_GET['id'] ) ? $_GET['id'] : 0 ) );
		$this->verify_edit_permission();
		$this->handle_edit_lock();
		$theorder = $this->order;

		$this->prepare_order_edit_form();
	}

	/**
	 * Handles initialization of the orders edit form with a new order.
	 *
	 * @return void
	 */
	private function setup_action_new_order(): void {
		global $theorder;

		$this->verify_create_permission();

		$order_class_name = wc_get_order_type( $this->order_type )['class_name'];
		if ( ! $order_class_name || ! class_exists( $order_class_name ) ) {
			wp_die();
		}

		$this->order = new $order_class_name();
		$this->order->set_object_read( false );
		$this->order->set_status( 'auto-draft' );
		$this->order->set_created_via( 'admin' );
		$this->order->save();
		$this->handle_edit_lock();

		// Schedule auto-draft cleanup. We re-use the WP event here on purpose.
		if ( ! wp_next_scheduled( 'wp_scheduled_auto_draft_delete' ) ) {
			wp_schedule_event( time(), 'daily', 'wp_scheduled_auto_draft_delete' );
		}

		$theorder = $this->order;

		$this->prepare_order_edit_form();
	}

	/**
	 * Returns the current order type.
	 *
	 * @return string
	 */
	public function get_order_type() {
		return $this->order_type;
	}

	/**
	 * Helper method to generate a link to the main orders screen.
	 *
	 * @return string Orders screen URL.
	 */
	public function get_orders_url(): string {
		return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ?
			admin_url( 'admin.php?page=wc-orders' ) :
			admin_url( 'edit.php?post_type=shop_order' );
	}

	/**
	 * Helper method to generate edit link for an order.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return string Edit link.
	 */
	public function get_edit_url( int $order_id ) : string {
		if ( ! wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			return admin_url( 'post.php?post=' . absint( $order_id ) ) . '&action=edit';
		}

		$order = wc_get_order( $order_id );

		// Confirm we could obtain the order object (since it's possible it will not exist, due to a sync issue, or may
		// have been deleted in a separate concurrent request).
		if ( false === $order ) {
			wc_get_logger()->debug(
				sprintf(
					/* translators: %d order ID. */
					__( 'Attempted to determine the edit URL for order %d, however the order does not exist.', 'woocommerce' ),
					$order_id
				)
			);
			$order_type = 'shop_order';
		} else {
			$order_type = $order->get_type();
		}

		return add_query_arg(
			array(
				'action' => 'edit',
				'id'     => absint( $order_id ),
			),
			$this->get_base_page_url( $order_type )
		);
	}

	/**
	 * Helper method to generate a link for creating order.
	 *
	 * @param string $order_type The order type. Defaults to 'shop_order'.
	 * @return string
	 */
	public function get_new_page_url( $order_type = 'shop_order' ) : string {
		$url = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ?
			add_query_arg( 'action', 'new', $this->get_base_page_url( $order_type ) ) :
			admin_url( 'post-new.php?post_type=' . $order_type );

		return $url;
	}

	/**
	 * Helper method to generate a link to the main screen for a custom order type.
	 *
	 * @param string $order_type The order type.
	 *
	 * @return string
	 *
	 * @throws \Exception When an invalid order type is passed.
	 */
	public function get_base_page_url( $order_type ): string {
		$order_types_with_ui = wc_get_order_types( 'admin-menu' );

		if ( ! in_array( $order_type, $order_types_with_ui, true ) ) {
			// translators: %s is a custom order type.
			throw new \Exception( sprintf( __( 'Invalid order type: %s.', 'woocommerce' ), esc_html( $order_type ) ) );
		}

		return admin_url( 'admin.php?page=wc-orders' . ( 'shop_order' === $order_type ? '' : '--' . $order_type ) );
	}

	/**
	 * Helper method to check if the current admin screen is related to orders.
	 *
	 * @param string $type   Optional. The order type to check for. Default shop_order.
	 * @param string $action Optional. The purpose of the screen to check for. 'list', 'edit', or 'new'.
	 *                       Leave empty to check for any order screen.
	 *
	 * @return bool
	 */
	public function is_order_screen( $type = 'shop_order', $action = '' ) : bool {
		if ( ! did_action( 'current_screen' ) ) {
			wc_doing_it_wrong(
				__METHOD__,
				sprintf(
					// translators: %s is the name of a function.
					esc_html__( '%s must be called after the current_screen action.', 'woocommerce' ),
					esc_html( __METHOD__ )
				),
				'7.9.0'
			);

			return false;
		}

		$valid_types = wc_get_order_types( 'view-order' );
		if ( ! in_array( $type, $valid_types, true ) ) {
			wc_doing_it_wrong(
				__METHOD__,
				sprintf(
					// translators: %s is the name of an order type.
					esc_html__( '%s is not a valid order type.', 'woocommerce' ),
					esc_html( $type )
				),
				'7.9.0'
			);

			return false;
		}

		if ( wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			if ( $action ) {
				switch ( $action ) {
					case 'edit':
						$is_action = 'edit_order' === $this->current_action;
						break;
					case 'list':
						$is_action = 'list_orders' === $this->current_action;
						break;
					case 'new':
						$is_action = 'new_order' === $this->current_action;
						break;
					default:
						$is_action = false;
						break;
				}
			}

			$type_match   = $type === $this->order_type;
			$action_match = ! $action || $is_action;
		} else {
			$screen = get_current_screen();

			if ( $action ) {
				switch ( $action ) {
					case 'edit':
						$screen_match = 'post' === $screen->base && filter_input( INPUT_GET, 'post', FILTER_VALIDATE_INT );
						break;
					case 'list':
						$screen_match = 'edit' === $screen->base;
						break;
					case 'new':
						$screen_match = 'post' === $screen->base && 'add' === $screen->action;
						break;
					default:
						$screen_match = false;
						break;
				}
			}

			$type_match   = $type === $screen->post_type;
			$action_match = ! $action || $screen_match;
		}

		return $type_match && $action_match;
	}
}
