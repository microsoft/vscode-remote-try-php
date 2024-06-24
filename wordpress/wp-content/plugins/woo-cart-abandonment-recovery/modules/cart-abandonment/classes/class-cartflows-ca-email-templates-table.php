<?php
/**
 * Cart Abandonment
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Cart abandonment templates table class.
 */
class Cartflows_Ca_Email_Templates_Table extends WP_List_Table {




	/**
	 * URL of this page
	 *
	 * @var   string
	 * @since 2.5.2
	 */
	public $base_url;

	/**
	 *  Constructor function.
	 */
	public function __construct() {
		global $status, $page;

		parent::__construct(
			array(
				'singular' => 'id',
				'plural'   => 'ids',
			)
		);

		$this->base_url = admin_url( 'admin.php?page=' . WCF_CA_PAGE_NAME . '&action=' . WCF_ACTION_EMAIL_TEMPLATES );
	}

	/**
	 * Default columns.
	 *
	 * @param object $item        item.
	 * @param string $column_name column name.
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * This is how id column renders.
	 *
	 * @param  object $item item.
	 * @return HTML
	 */
	public function column_template_name( $item ) {

		$row_actions['edit'] = '<a href="' . wp_nonce_url(
			add_query_arg(
				array(
					'action'     => WCF_ACTION_EMAIL_TEMPLATES,
					'sub_action' => WCF_SUB_ACTION_EDIT_EMAIL_TEMPLATES,
					'id'         => $item['id'],
				),
				$this->base_url
			),
			WCF_EMAIL_TEMPLATES_NONCE
		) . '">' . __( 'Edit', 'woo-cart-abandonment-recovery' ) . '</a>';

		$row_actions['delete'] = '<a onclick="return confirm(\'Are you sure to delete this email template?\');" href="' . wp_nonce_url(
			add_query_arg(
				array(
					'action'     => WCF_ACTION_EMAIL_TEMPLATES,
					'sub_action' => WCF_SUB_ACTION_DELETE_EMAIL_TEMPLATES,
					'id'         => $item['id'],
				),
				$this->base_url
			),
			WCF_EMAIL_TEMPLATES_NONCE
		) . '">' . __( 'Delete', 'woo-cart-abandonment-recovery' ) . '</a>';

		$row_actions['clone'] = '<a href="' . wp_nonce_url(
			add_query_arg(
				array(
					'action'     => WCF_ACTION_EMAIL_TEMPLATES,
					'sub_action' => WCF_SUB_ACTION_CLONE_EMAIL_TEMPLATES,
					'id'         => $item['id'],
				),
				$this->base_url
			),
			WCF_EMAIL_TEMPLATES_NONCE
		) . '">' . __( 'Clone', 'woo-cart-abandonment-recovery' ) . '</a>';

		return sprintf( '%s %s', esc_html( $item['template_name'] ), $this->row_actions( $row_actions ) );
	}

	/**
	 * This is how checkbox column renders.
	 *
	 * @param  object $item item.
	 * @return HTML
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="id[]" value="%s" />', esc_html( $item['id'] ) );
	}

	/**
	 * [OPTIONAL] Return array of bult actions if has any
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array(
			WCF_ACTION_EMAIL_TEMPLATES => __( 'Delete', 'woo-cart-abandonment-recovery' ),
		);
		return $actions;
	}

	/**
	 * Whether the table has items to display or not
	 *
	 * @return bool
	 */
	public function has_items() {
		return ! empty( $this->items );
	}

	/**
	 * Fetch data from the database to render on view.
	 *
	 * @param string $cart_type abandoned|completed.
	 */
	public function prepare_items( $cart_type = WCF_CART_ABANDONED_ORDER ) {
		global $wpdb;
		$cart_abandonment_template_table_name = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;

		$per_page = 10;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$total_items = $wpdb->get_var(
			"SELECT COUNT(id) FROM {$cart_abandonment_template_table_name}" //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		); // db call ok; no-cache ok.

		$paged        = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
		$helper_class = Cartflows_Ca_Helper::get_instance();
		$orderby      = $helper_class->sanitize_text_filter( 'orderby', 'GET' );
		$order        = $helper_class->sanitize_text_filter( 'order', 'GET' );

		$orderby = strtolower( str_replace( ' ', '_', $orderby ) );

		$paged   = $paged ? max( 0, $paged - 1 ) : 0;
		$orderby = ( $orderby && in_array( $orderby, array_keys( $this->get_sortable_columns() ), true ) ) ? $orderby : 'id';
		$order   = ( $order && in_array( $order, array( 'asc', 'desc' ), true ) ) ? $order : 'desc';

		// [REQUIRED] configure pagination
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
		// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
		$this->items = $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$cart_abandonment_template_table_name} ORDER BY %s %s LIMIT %d OFFSET %d", $orderby, $order, $per_page, $paged * $per_page ), //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			ARRAY_A
		); // db call ok; no-cache ok.
	}

	/**
	 * Table columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'            => '<input type="checkbox" />',
			'template_name' => __( 'Template Name', 'woo-cart-abandonment-recovery' ),
			'email_subject' => __( 'Email Subject', 'woo-cart-abandonment-recovery' ),
			'trigger_time'  => __( 'Trigger After', 'woo-cart-abandonment-recovery' ),
			'is_activated'  => __( 'Activate Template', 'woo-cart-abandonment-recovery' ),

		);
		return $columns;
	}


	/**
	 * Column name trigger_time.
	 *
	 * @param  object $item item.
	 * @return string
	 */
	protected function column_trigger_time( $item ) {

		return sprintf(
			'%d %s',
			esc_html( $item['frequency'] ),
			' - ' . esc_html( $item['frequency_unit'] )
		);
	}

	/**
	 * Column name trigger_time.
	 *
	 * @param  object $item item.
	 */
	protected function column_is_activated( $item ) {
		global $wpdb;
		if ( isset( $item['id'] ) ) {
			$id = $item['id'];
		}
		$is_activated  = '';
		$active_status = 0;
		if ( $item && isset( $item['is_activated'] ) ) {
			$active_status = stripslashes( $item['is_activated'] );
			$is_activated  = $active_status ? 'on' : 'off';

		}
		print '<button type="button" id="' . esc_attr( $id ) . '" class="wcf-ca-switch wcf-toggle-template-status wcar-switch-grid"  wcf-ca-template-switch="' . esc_attr( $is_activated ) . '"> ' . esc_html( $is_activated ) . ' </button>';
		print '<input type="hidden" name="wcf_activate_email_template" id="wcf_activate_email_template" value="' . esc_attr( $active_status ) . '" />';
	}

	/**
	 * Table sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable = array(
			'id'            => array( 'id', true ),
			'template_name' => array( 'Template Name', true ),
			'email_subject' => array( 'Email Subject', true ),
		);
		return $sortable;
	}

	/**
	 * Processes bulk actions
	 */
	public function process_bulk_action() {

		global $wpdb;
		$action_nonce = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES . '_nonce', 'GET' );

		// Process the delete only if the nonce is verified and the current user has the capability to manage it.
		if ( ! empty( $action_nonce ) && wp_verify_nonce( $action_nonce, WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES ) && current_user_can( 'manage_woocommerce' ) ) {

			$action = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'sub_action', 'GET' );

			if ( WCF_SUB_ACTION_DELETE_BULK_EMAIL_TEMPLATES === $action ) {

				$table_name = $wpdb->prefix . CARTFLOWS_CA_EMAIL_TEMPLATE_TABLE;

				$request_id = isset( $_REQUEST['id'] ) && is_array( $_REQUEST['id'] ) ? array_map( 'intval', $_REQUEST['id'] ) : array(); //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$ids        = implode( ',', $request_id );

				if ( ! empty( $ids ) ) {
					// Can't use placeholders for table/column names, it will be wrapped by a single quote (') instead of a backquote (`).
					$wpdb->query(
						"DELETE FROM {$table_name} WHERE id IN($ids)" //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					); // db call ok; no-cache ok.
				}
			}       
		}

	}
}
