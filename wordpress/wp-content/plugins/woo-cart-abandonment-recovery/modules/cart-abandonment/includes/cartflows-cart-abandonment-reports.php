<?php
/**
 * Cartflows view for cart abandonment reports.
 *
 * @package Woocommerce-Cart-Abandonment-Recovery
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="wcf-ca-report-btn">

	<div class="wcf-ca-left-report-field-group">
		<button onclick="window.location.search += '&filter=today';"
				class="button <?php echo 'today' === $filter ? 'button-primary' : 'button-secondary'; ?>"> <?php esc_html_e( 'Today', 'woo-cart-abandonment-recovery' ); ?>
		</button>

		<button onclick="window.location.search += '&filter=yesterday';"
				class="button <?php echo 'yesterday' === $filter ? 'button-primary' : 'button-secondary'; ?>"> <?php esc_html_e( 'Yesterday', 'woo-cart-abandonment-recovery' ); ?>
		</button>

		<button onclick="window.location.search += '&filter=last_week';"
				class="button <?php echo 'last_week' === $filter ? 'button-primary' : 'button-secondary'; ?>"> <?php esc_html_e( 'Last Week', 'woo-cart-abandonment-recovery' ); ?>
		</button>

		<button onclick="window.location.search += '&filter=last_month';"
				class="button <?php echo 'last_month' === $filter ? 'button-primary' : 'button-secondary'; ?> "> <?php esc_html_e( 'Last Month', 'woo-cart-abandonment-recovery' ); ?>
		</button>
	</div>

	<div class="wcf-ca-right-report-field-group">

		<input class="wcf-ca-filter-input" type="text" id="wcf_ca_custom_filter_from" placeholder="YYYY-MM-DD" value="<?php echo esc_attr( $from_date ); ?>"/>
		<input class="wcf-ca-filter-input" type="text" id="wcf_ca_custom_filter_to" placeholder="YYYY-MM-DD" value="<?php echo esc_attr( $to_date ); ?>" />
		<button id="wcf_ca_custom_filter"
				class="button <?php echo 'custom' === $filter ? 'button-primary' : 'button-secondary'; ?> "> <?php esc_html_e( 'Custom Filter', 'woo-cart-abandonment-recovery' ); ?>
		</button>

	</div>

</div>

<div class="wcf-ca-grid-container">

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title">
			<h3> <?php esc_html_e( 'Recoverable Orders', 'woo-cart-abandonment-recovery' ); ?> </h3>
		</div>
		<div class="wcf-ca-ibox-content">
			<h1> <?php echo esc_html( $abandoned_report['no_of_orders'] ); ?> </h1>
			<small> <?php esc_html_e( 'Total Recoverable Orders.', 'woo-cart-abandonment-recovery' ); ?>  </small>
		</div>
	</div>

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title"><h3><?php esc_html_e( 'Recovered Orders', 'woo-cart-abandonment-recovery' ); ?></h3></div>
		<div class="wcf-ca-ibox-content"><h1><?php echo esc_html( $recovered_report['no_of_orders'] ); ?></h1>
			<small> <?php esc_html_e( 'Total Recovered Orders.', 'woo-cart-abandonment-recovery' ); ?> </small>
		</div>
	</div>

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title"><h3><?php esc_html_e( 'Lost Orders', 'woo-cart-abandonment-recovery' ); ?></h3></div>
		<div class="wcf-ca-ibox-content"><h1
			><?php echo esc_html( $lost_report['no_of_orders'] ); ?></h1>
			<small> <?php esc_html_e( 'Total Lost Orders.', 'woo-cart-abandonment-recovery' ); ?>  </small>
		</div>
	</div>

</div>

<div class="wcf-ca-grid-container">

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title"><h3> <?php esc_html_e( 'Recoverable Revenue', 'woo-cart-abandonment-recovery' ); ?> </h3></div>
		<div class="wcf-ca-ibox-content">
			<h1>
				<?php
				echo esc_html( $currency_symbol );
				echo ( ! empty( $abandoned_report['revenue'] ) || ! is_null( $abandoned_report['revenue'] ) ) ? esc_html( number_format_i18n( floatval( $abandoned_report['revenue'] ), 2 ) ) : '0.0';
				?>
			</h1>
			<small> <?php esc_html_e( 'Total Recoverable Revenue.', 'woo-cart-abandonment-recovery' ); ?> </small>
		</div>
	</div>

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title"><h3><?php esc_html_e( 'Recovered Revenue', 'woo-cart-abandonment-recovery' ); ?></h3></div>
		<div class="wcf-ca-ibox-content"><h1>
				<?php
				echo esc_html( $currency_symbol );
				echo ( ! empty( $recovered_report['revenue'] ) || ! is_null( $recovered_report['revenue'] ) ) ? esc_html( number_format_i18n( floatval( $recovered_report['revenue'] ), 2 ) ) : '0.0';
				?>
			</h1>
			<small> <?php esc_html_e( 'Total Recovered Revenue.', 'woo-cart-abandonment-recovery' ); ?> </small>
		</div>
	</div>

	<div class="wcf-ca-ibox">
		<div class="wcf-ca-ibox-title"><h3> <?php esc_html_e( 'Recovery Rate', 'woo-cart-abandonment-recovery' ); ?> </h3></div>
		<div class="wcf-ca-ibox-content"><h1><?php echo esc_html( $conversion_rate ) . '%'; ?></h1>
			<small><?php esc_html_e( 'Total Percentage Of Recovered Orders After Abandonment.', 'woo-cart-abandonment-recovery' ); ?> </small>
		</div>
	</div>

</div>

<hr/>

<div class="wcf-ca-report-btn">
	<div class="wcf-ca-left-report-field-group">
		<button onclick="window.location.search += '&filter_table=<?php echo esc_attr( WCF_CART_ABANDONED_ORDER ); ?>';"
				class="button <?php echo WCF_CART_ABANDONED_ORDER === $filter_table ? 'button-primary' : 'button-secondary'; ?> "> <?php esc_html_e( 'Recoverable Orders', 'woo-cart-abandonment-recovery' ); ?>
		</button>
		<button onclick="window.location.search += '&filter_table=<?php echo esc_attr( WCF_CART_COMPLETED_ORDER ); ?>';"
				class="button <?php echo WCF_CART_COMPLETED_ORDER === $filter_table ? 'button-primary' : 'button-secondary'; ?>"><?php esc_html_e( 'Recovered Orders', 'woo-cart-abandonment-recovery' ); ?>
		</button>
		<button onclick="window.location.search += '&filter_table=<?php echo esc_attr( WCF_CART_LOST_ORDER ); ?>';"
				class="button <?php echo WCF_CART_LOST_ORDER === $filter_table ? 'button-primary' : 'button-secondary'; ?>"><?php esc_html_e( 'Lost Orders', 'woo-cart-abandonment-recovery' ); ?>
		</button>
	</div>

	<div class="wcf-ca-right-report-field-group">
		<div class="wcf-search-orders" id="wcf_search_wrapper" >
			<div class="search-box">
			<?php
				$search_term = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'search_term', 'GET' );
			?>
				<input type="search" id="wcf_search_id_search_input" name="s" placeholder="<?php echo esc_attr__( 'Search by email', 'woo-cart-abandonment-recovery' ); ?>" value="<?php echo esc_attr( $search_term ); ?>">
				<input type="submit" id="wcf_search_id_submit" class="button" value="<?php esc_attr_e( 'Search Orders', 'woo-cart-abandonment-recovery' ); ?>">
			</div>
		</div>
		<div class="wcf_export_orders">
			<?php
			if ( count( $wcf_list_table->items ) !== 0 ) {
				?>
				<button id="wcf_ca_export_orders"
						class="button-primary " > Export Orders <span class="dashicons dashicons-download wcf-ca-export-icon" ></span>
				</button>
			<?php } ?>
		</div>
	</div>
</div>

<?php
if ( count( $wcf_list_table->items ) ) {
	$wcar_page = Cartflows_Ca_Helper::get_instance()->sanitize_text_filter( 'page', 'GET' );
	?>
<form id="wcf-cart-abandonment-table" method="GET">
	<input type="hidden" name="page" value="<?php echo esc_attr( $wcar_page ); ?>"/>
	<input type="hidden" name="<?php echo esc_attr( WCF_REPORTS_TABLE_ACTION . '_nonce' ); ?>" value="<?php echo esc_attr( wp_create_nonce( WCF_REPORTS_TABLE_ACTION ) ); ?>"/>
	<?php $wcf_list_table->display(); ?>
</form>

	<?php
} else {

	echo '<div> <strong> ' . esc_html__( 'No Orders Found.', 'woo-cart-abandonment-recovery' ) . '</strong> </div>';

}

?>
