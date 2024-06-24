<?php
/**
 * WooCommerce Log Table List
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce\Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WC_Admin_Log_Table_List extends WP_List_Table {
	/**
	 * The key for the user option of how many list table items to display per page.
	 *
	 * @const string
	 */
	public const PER_PAGE_USER_OPTION_KEY = 'woocommerce_status_log_items_per_page';

	/**
	 * Initialize the log table list.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'log',
				'plural'   => 'logs',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Display level dropdown
	 *
	 * @global wpdb $wpdb
	 */
	public function level_dropdown() {
		$labels = WC_Log_Levels::get_all_level_labels();

		$levels = array_reduce(
			array_keys( $labels ),
			function( $carry, $item ) use ( $labels ) {
				$carry[] = array(
					'value' => $item,
					'label' => $labels[ $item ],
				);

				return $carry;
			},
			array()
		);

		$selected_level = isset( $_REQUEST['level'] ) ? $_REQUEST['level'] : '';
		?>
			<label for="filter-by-level" class="screen-reader-text"><?php esc_html_e( 'Filter by level', 'woocommerce' ); ?></label>
			<select name="level" id="filter-by-level">
				<option<?php selected( $selected_level, '' ); ?> value=""><?php esc_html_e( 'All levels', 'woocommerce' ); ?></option>
				<?php
				foreach ( $levels as $l ) {
					printf(
						'<option%1$s value="%2$s">%3$s</option>',
						selected( $selected_level, $l['value'], false ),
						esc_attr( $l['value'] ),
						esc_html( $l['label'] )
					);
				}
				?>
			</select>
		<?php
	}

	/**
	 * Generates the table rows.
	 *
	 * @return void
	 */
	public function display_rows() {
		foreach ( $this->items as $log ) {
			$this->single_row( $log );
			if ( ! empty( $log['context'] ) ) {
				$this->context_row( $log );
			}
		}
	}

	/**
	 * Render the additional table row that contains extra log context data.
	 *
	 * @param array $log Log entry data.
	 *
	 * @return void
	 */
	protected function context_row( $log ) {
		// Maintains alternating row background colors.
		?>
		<tr style="display: none"><td></td></tr>
		<tr id="log-context-<?php echo esc_attr( $log['log_id'] ); ?>" class="log-context">
			<td colspan="<?php echo esc_attr( $this->get_column_count() ); ?>">
				<p><strong><?php esc_html_e( 'Additional context', 'woocommerce' ); ?></strong></p>
				<pre><?php echo esc_html( $log['context'] ); ?></pre>
			</td>
		</tr>
		<?php
	}

	/**
	 * Get list columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'        => '<input type="checkbox" />',
			'timestamp' => __( 'Timestamp', 'woocommerce' ),
			'level'     => __( 'Level', 'woocommerce' ),
			'message'   => __( 'Message', 'woocommerce' ),
			'source'    => __( 'Source', 'woocommerce' ),
			'context'   => __( 'Context', 'woocommerce' ),
		);
	}

	/**
	 * Column cb.
	 *
	 * @param  array $log
	 * @return string
	 */
	public function column_cb( $log ) {
		return sprintf( '<input type="checkbox" name="log[]" value="%1$s" />', esc_attr( $log['log_id'] ) );
	}

	/**
	 * Timestamp column.
	 *
	 * @param  array $log
	 * @return string
	 */
	public function column_timestamp( $log ) {
		return esc_html(
			mysql2date(
				'Y-m-d H:i:s',
				$log['timestamp']
			)
		);
	}

	/**
	 * Level column.
	 *
	 * @param  array $log
	 * @return string
	 */
	public function column_level( $log ) {
		$level_key = WC_Log_Levels::get_severity_level( $log['level'] );
		$levels    = WC_Log_Levels::get_all_level_labels();

		if ( ! isset( $levels[ $level_key ] ) ) {
			return '';
		}

		$level       = $levels[ $level_key ];
		$level_class = sanitize_html_class( 'log-level--' . $level_key );
		return '<span class="log-level ' . $level_class . '">' . esc_html( $level ) . '</span>';
	}

	/**
	 * Message column.
	 *
	 * @param  array $log
	 * @return string
	 */
	public function column_message( $log ) {
		return sprintf(
			'<pre>%s</pre>',
			esc_html( $log['message'] )
		);
	}

	/**
	 * Source column.
	 *
	 * @param  array $log
	 * @return string
	 */
	public function column_source( $log ) {
		return esc_html( $log['source'] );
	}

	/**
	 * Context column.
	 *
	 * @param array $log Log entry data.
	 *
	 * @return string
	 */
	public function column_context( $log ) {
		$content = '';

		if ( ! empty( $log['context'] ) ) {
			ob_start();
			?>
				<button
					class="log-toggle button button-secondary button-small"
					data-log-id="<?php echo esc_attr( $log['log_id'] ); ?>"
					data-toggle-status="off"
					data-label-show="<?php esc_attr_e( 'Show context', 'woocommerce' ); ?>"
					data-label-hide="<?php esc_attr_e( 'Hide context', 'woocommerce' ); ?>"
				>
					<span class="dashicons dashicons-arrow-down-alt2"></span>
					<span class="log-toggle-label screen-reader-text"><?php esc_html_e( 'Show context', 'woocommerce' ); ?></span>
				</button>
			<?php
			$content = ob_get_clean();
		}

		return $content;
	}

	/**
	 * Get bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'woocommerce' ),
		);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination.
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		if ( 'top' === $which ) {
			echo '<div class="alignleft actions">';
				$this->level_dropdown();
				$this->source_dropdown();
				submit_button( __( 'Filter', 'woocommerce' ), '', 'filter-action', false );
			echo '</div>';
		}
	}

	/**
	 * Get a list of sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'timestamp' => array( 'timestamp', true ),
			'level'     => array( 'level', true ),
			'source'    => array( 'source', true ),
		);
	}

	/**
	 * Display source dropdown
	 *
	 * @global wpdb $wpdb
	 */
	protected function source_dropdown() {
		global $wpdb;

		$sources = $wpdb->get_col(
			"SELECT DISTINCT source
			FROM {$wpdb->prefix}woocommerce_log
			WHERE source != ''
			ORDER BY source ASC"
		);

		if ( ! empty( $sources ) ) {
			$selected_source = isset( $_REQUEST['source'] ) ? $_REQUEST['source'] : '';
			?>
				<label for="filter-by-source" class="screen-reader-text"><?php esc_html_e( 'Filter by source', 'woocommerce' ); ?></label>
				<select name="source" id="filter-by-source">
					<option<?php selected( $selected_source, '' ); ?> value=""><?php esc_html_e( 'All sources', 'woocommerce' ); ?></option>
					<?php
					foreach ( $sources as $s ) {
						printf(
							'<option%1$s value="%2$s">%3$s</option>',
							selected( $selected_source, $s, false ),
							esc_attr( $s ),
							esc_html( $s )
						);
					}
					?>
				</select>
			<?php
		}
	}

	/**
	 * Prepare table list items.
	 *
	 * @global wpdb $wpdb
	 */
	public function prepare_items() {
		global $wpdb;

		$this->prepare_column_headers();

		$per_page = $this->get_items_per_page(
			self::PER_PAGE_USER_OPTION_KEY,
			$this->get_per_page_default()
		);

		$where  = $this->get_items_query_where();
		$order  = $this->get_items_query_order();
		$limit  = $this->get_items_query_limit();
		$offset = $this->get_items_query_offset();

		$query_items = "
			SELECT log_id, timestamp, level, message, source, context
			FROM {$wpdb->prefix}woocommerce_log
			{$where} {$order} {$limit} {$offset}
		";

		$this->items = $wpdb->get_results( $query_items, ARRAY_A );

		$query_count = "SELECT COUNT(log_id) FROM {$wpdb->prefix}woocommerce_log {$where}";
		$total_items = $wpdb->get_var( $query_count );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Get prepared LIMIT clause for items query
	 *
	 * @global wpdb $wpdb
	 *
	 * @return string Prepared LIMIT clause for items query.
	 */
	protected function get_items_query_limit() {
		global $wpdb;

		$per_page = $this->get_items_per_page(
			self::PER_PAGE_USER_OPTION_KEY,
			$this->get_per_page_default()
		);

		return $wpdb->prepare( 'LIMIT %d', $per_page );
	}

	/**
	 * Get prepared OFFSET clause for items query
	 *
	 * @global wpdb $wpdb
	 *
	 * @return string Prepared OFFSET clause for items query.
	 */
	protected function get_items_query_offset() {
		global $wpdb;

		$per_page     = $this->get_items_per_page(
			self::PER_PAGE_USER_OPTION_KEY,
			$this->get_per_page_default()
		);
		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		return $wpdb->prepare( 'OFFSET %d', $offset );
	}

	/**
	 * Get prepared ORDER BY clause for items query
	 *
	 * @return string Prepared ORDER BY clause for items query.
	 */
	protected function get_items_query_order() {
		$valid_orders = array( 'level', 'source', 'timestamp' );
		if ( ! empty( $_REQUEST['orderby'] ) && in_array( $_REQUEST['orderby'], $valid_orders ) ) {
			$by = wc_clean( $_REQUEST['orderby'] );
		} else {
			$by = 'timestamp';
		}
		$by = esc_sql( $by );

		if ( ! empty( $_REQUEST['order'] ) && 'asc' === strtolower( $_REQUEST['order'] ) ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}

		return "ORDER BY {$by} {$order}, log_id {$order}";
	}

	/**
	 * Get prepared WHERE clause for items query
	 *
	 * @global wpdb $wpdb
	 *
	 * @return string Prepared WHERE clause for items query.
	 */
	protected function get_items_query_where() {
		global $wpdb;

		$where_conditions = array();
		$where_values     = array();
		if ( ! empty( $_REQUEST['level'] ) && WC_Log_Levels::is_valid_level( $_REQUEST['level'] ) ) {
			$where_conditions[] = 'level >= %d';
			$where_values[]     = WC_Log_Levels::get_level_severity( $_REQUEST['level'] );
		}
		if ( ! empty( $_REQUEST['source'] ) ) {
			$where_conditions[] = 'source = %s';
			$where_values[]     = wc_clean( $_REQUEST['source'] );
		}
		if ( ! empty( $_REQUEST['s'] ) ) {
			$where_conditions[] = 'message like %s';
			$where_values[]     = '%' . $wpdb->esc_like( wc_clean( wp_unslash( $_REQUEST['s'] ) ) ) . '%';
		}

		if ( empty( $where_conditions ) ) {
			return '';
		}

		return $wpdb->prepare( 'WHERE 1 = 1 AND ' . implode( ' AND ', $where_conditions ), $where_values );
	}

	/**
	 * Set _column_headers property for table list
	 */
	protected function prepare_column_headers() {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
		);
	}

	/**
	 * Helper to get the default value for the per_page arg.
	 *
	 * @return int
	 */
	public function get_per_page_default(): int {
		return 20;
	}
}
