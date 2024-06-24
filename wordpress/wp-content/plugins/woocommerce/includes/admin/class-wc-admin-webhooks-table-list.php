<?php
/**
 * WooCommerce Webhooks Table List
 *
 * @package WooCommerce\Admin
 * @version 3.3.0
 */

use Automattic\WooCommerce\Internal\Utilities\WebhookUtil;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Webhooks table list class.
 */
class WC_Admin_Webhooks_Table_List extends WP_List_Table {

	/**
	 * Initialize the webhook table list.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'webhook',
				'plural'   => 'webhooks',
				'ajax'     => false,
			)
		);
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		esc_html_e( 'No webhooks found.', 'woocommerce' );
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'           => '<input type="checkbox" />',
			'title'        => __( 'Name', 'woocommerce' ),
			'status'       => __( 'Status', 'woocommerce' ),
			'topic'        => __( 'Topic', 'woocommerce' ),
			'delivery_url' => __( 'Delivery URL', 'woocommerce' ),
		);
	}

	/**
	 * Column cb.
	 *
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @return string
	 */
	public function column_cb( $webhook ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', $this->_args['singular'], $webhook->get_id() );
	}

	/**
	 * Return title column.
	 *
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @return string
	 */
	public function column_title( $webhook ) {
		$edit_link = admin_url( 'admin.php?page=wc-settings&amp;tab=advanced&amp;section=webhooks&amp;edit-webhook=' . $webhook->get_id() );
		$output    = '';

		// Title.
		$warning_prefix =
			is_null( wc()->api ) && $this->uses_legacy_rest_api( $webhook ) ?
			sprintf(
				"<span title='%s'>⚠️</span>️ ",
				esc_html__( 'This webhook is configured to be delivered using the Legacy REST API, but the Legacy REST API plugin is not installed on this site.', 'woocommerce' )
			) : '';
		$output        .= '<strong>' . $warning_prefix . '<a href="' . esc_url( $edit_link ) . '" class="row-title">' . esc_html( $webhook->get_name() ) . '</a></strong>';

		// Get actions.
		$actions = array(
			/* translators: %s: webhook ID. */
			'id'     => sprintf( __( 'ID: %d', 'woocommerce' ), $webhook->get_id() ),
			'edit'   => '<a href="' . esc_url( $edit_link ) . '">' . esc_html__( 'Edit', 'woocommerce' ) . '</a>',
			/* translators: %s: webhook name */
			'delete' => '<a class="submitdelete" aria-label="' . esc_attr( sprintf( __( 'Delete "%s" permanently', 'woocommerce' ), $webhook->get_name() ) ) . '" href="' . esc_url(
				wp_nonce_url(
					add_query_arg(
						array(
							'delete' => $webhook->get_id(),
						),
						admin_url( 'admin.php?page=wc-settings&tab=advanced&section=webhooks' )
					),
					'delete-webhook'
				)
			) . '">' . esc_html__( 'Delete permanently', 'woocommerce' ) . '</a>',
		);

		$actions     = apply_filters( 'webhook_row_actions', $actions, $webhook );
		$row_actions = array();

		foreach ( $actions as $action => $link ) {
			$row_actions[] = '<span class="' . esc_attr( $action ) . '">' . $link . '</span>';
		}

		$output .= '<div class="row-actions">' . implode( ' | ', $row_actions ) . '</div>';

		return $output;
	}

	/**
	 * Return status column.
	 *
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @return string
	 */
	public function column_status( $webhook ) {
		return $webhook->get_i18n_status();
	}

	/**
	 * Return topic column.
	 *
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @return string
	 */
	public function column_topic( $webhook ) {
		return $webhook->get_topic();
	}

	/**
	 * Return delivery URL column.
	 *
	 * @param  WC_Webhook $webhook Webhook instance.
	 * @return string
	 */
	public function column_delivery_url( $webhook ) {
		return $webhook->get_delivery_url();
	}

	/**
	 * Get the status label for webhooks.
	 *
	 * @param string $status_name Status name.
	 * @param int    $amount      Amount of webhooks.
	 * @return array
	 */
	private function get_status_label( $status_name, $amount ) {
		$statuses = wc_get_webhook_statuses();

		if ( isset( $statuses[ $status_name ] ) ) {
			return array(
				'singular' => sprintf( '%s <span class="count">(%s)</span>', esc_html( $statuses[ $status_name ] ), $amount ),
				'plural'   => sprintf( '%s <span class="count">(%s)</span>', esc_html( $statuses[ $status_name ] ), $amount ),
				'context'  => '',
				'domain'   => 'woocommerce',
			);
		}

		return array(
			'singular' => sprintf( '%s <span class="count">(%s)</span>', esc_html( $status_name ), $amount ),
			'plural'   => sprintf( '%s <span class="count">(%s)</span>', esc_html( $status_name ), $amount ),
			'context'  => '',
			'domain'   => 'woocommerce',
		);
	}

	/**
	 * Table list views.
	 *
	 * @return array
	 */
	protected function get_views() {
		$status_links   = array();
		$data_store     = WC_Data_Store::load( 'webhook' );
		$num_webhooks   = $data_store->get_count_webhooks_by_status();
		$total_webhooks = array_sum( (array) $num_webhooks );
		$statuses       = array_keys( wc_get_webhook_statuses() );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$class = empty( $_REQUEST['status'] ) && empty( $_REQUEST['legacy'] ) ? ' class="current"' : '';

		/* translators: %s: count */
		$status_links['all'] = "<a href='admin.php?page=wc-settings&amp;tab=advanced&amp;section=webhooks'$class>" . sprintf( _nx( 'All <span class="count">(%s)</span>', 'All <span class="count">(%s)</span>', $total_webhooks, 'posts', 'woocommerce' ), number_format_i18n( $total_webhooks ) ) . '</a>';

		foreach ( $statuses as $status_name ) {
			$class = '';

			if ( empty( $num_webhooks[ $status_name ] ) ) {
				continue;
			}

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_REQUEST['status'] ) && sanitize_key( wp_unslash( $_REQUEST['status'] ) ) === $status_name ) {
				$class = ' class="current"';
			}

			$label = $this->get_status_label( $status_name, $num_webhooks[ $status_name ] );

			$status_links[ $status_name ] = "<a href='admin.php?page=wc-settings&amp;tab=advanced&amp;section=webhooks&amp;status=$status_name'$class>" . sprintf( translate_nooped_plural( $label, $num_webhooks[ $status_name ] ), number_format_i18n( $num_webhooks[ $status_name ] ) ) . '</a>';
		}

		$legacy_webhooks_count = wc_get_container()->get( WebhookUtil::class )->get_legacy_webhooks_count();
		if ( $legacy_webhooks_count > 0 ) {
			$class = '';

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( 'true' === sanitize_key( wp_unslash( $_REQUEST['legacy'] ?? '' ) ) ) {
				$class = ' class="current"';
			}

			$label = $this->get_status_label( __( 'Legacy', 'woocommerce' ), $legacy_webhooks_count );

			$status_links['legacy'] = "<a href='admin.php?page=wc-settings&amp;tab=advanced&amp;section=webhooks&amp;legacy=true'$class>" . sprintf( translate_nooped_plural( $label, $legacy_webhooks_count ), number_format_i18n( $legacy_webhooks_count ) ) . '</a>';
		}

		return $status_links;
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete permanently', 'woocommerce' ),
		);
	}

	/**
	 * Process bulk actions.
	 */
	public function process_bulk_action() {
		$action   = $this->current_action();
		$webhooks = isset( $_REQUEST['webhook'] ) ? array_map( 'absint', (array) $_REQUEST['webhook'] ) : array(); // WPCS: input var okay, CSRF ok.

		if ( false !== $action ) {
			check_admin_referer( 'woocommerce-settings' );

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You do not have permission to edit Webhooks', 'woocommerce' ) );
			}

			if ( 'delete' === $action ) {
				WC_Admin_Webhooks::bulk_delete( $webhooks );
			}
		}
	}

	/**
	 * Generate the table navigation above or below the table.
	 * Included to remove extra nonce input.
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 */
	protected function display_tablenav( $which ) {
		echo '<div class="tablenav ' . esc_attr( $which ) . '">';

		if ( $this->has_items() ) {
			echo '<div class="alignleft actions bulkactions">';
			$this->bulk_actions( $which );
			echo '</div>';
		}

		$this->extra_tablenav( $which );
		$this->pagination( $which );
		echo '<br class="clear" />';
		echo '</div>';
	}

	/**
	 * Search box.
	 *
	 * @param  string $text     Button text.
	 * @param  string $input_id Input ID.
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && ! $this->has_items() ) { // WPCS: input var okay, CSRF ok.
			return;
		}

		$input_id     = $input_id . '-search-input';
		$search_query = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : ''; // WPCS: input var okay, CSRF ok.

		echo '<p class="search-box">';
		echo '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html( $text ) . ':</label>';
		echo '<input type="search" id="' . esc_attr( $input_id ) . '" name="s" value="' . esc_attr( $search_query ) . '" />';
		submit_button(
			$text,
			'',
			'',
			false,
			array(
				'id' => 'search-submit',
			)
		);
		echo '</p>';
	}

	/**
	 * Prepare table list items.
	 */
	public function prepare_items() {
		$per_page     = $this->get_items_per_page( 'woocommerce_webhooks_per_page' );
		$current_page = $this->get_pagenum();

		// Query args.
		$args = array(
			'limit'  => $per_page,
			'offset' => $per_page * ( $current_page - 1 ),
		);

		// Handle the status query.
		if ( ! empty( $_REQUEST['status'] ) ) { // WPCS: input var okay, CSRF ok.
			$args['status'] = sanitize_key( wp_unslash( $_REQUEST['status'] ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_REQUEST['s'] ) ) { // WPCS: input var okay, CSRF ok.
			$args['search'] = sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ); // WPCS: input var okay, CSRF ok.
		}

		$args['paginate'] = true;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( 'true' === sanitize_key( wp_unslash( $_REQUEST['legacy'] ?? null ) ) ) {
			$args['api_version'] = -1;
		}

		// Get the webhooks.
		$data_store  = WC_Data_Store::load( 'webhook' );
		$webhooks    = $data_store->search_webhooks( $args );
		$this->items = array_map( 'wc_get_webhook', $webhooks->webhooks );

		// Set the pagination.
		$this->set_pagination_args(
			array(
				'total_items' => $webhooks->total,
				'per_page'    => $per_page,
				'total_pages' => $webhooks->max_num_pages,
			)
		);
	}

	/**
	 * Get how many of the existing webhooks are configured to use the legacy payload format.
	 *
	 * @since 9.0.0
	 *
	 * @return int Count of existing webhooks are configured to use the legacy payload format.
	 */
	public function get_legacy_api_webhooks_count() {
		return count( array_filter( $this->items, array( $this, 'uses_legacy_rest_api' ) ) );
	}

	/**
	 * Check if a given webhook is configured to use the legacy payload format.
	 *
	 * @param WC_Webhook $webhook Webhook object.
	 * @return bool True if the webhook is configured to use the legacy payload format.
	 */
	private function uses_legacy_rest_api( $webhook ) {
		return 0 === strpos( $webhook->get_api_version(), 'legacy' );
	}
}
