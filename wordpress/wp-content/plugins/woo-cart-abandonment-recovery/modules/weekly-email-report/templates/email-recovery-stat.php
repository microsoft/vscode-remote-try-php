<?php
/**
 * Template Name: Email Header
 *
 * @package Cart Abandonment Recovery
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-4" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
					<tbody>
						<tr>
							<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 50px; padding-bottom: 20px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
								<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td style="text-align:center;width:100%;">
											<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 28px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
												<?php echo esc_html__( 'Full Overview', 'woo-cart-abandonment-recovery' ); ?></h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="5" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td>
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; font-size: 14px; text-align: center;">
														<span style="font-size:16px;"><?php echo esc_html( $from_date . ' - ' . $to_date ); ?></span>
													</p>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
					<tbody>
						<tr>
							<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="33.333333333333336%">
								<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td style="text-align:center;width:100%;padding-top:5px;">
											<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
												<?php echo esc_attr( $report_details['abandonded_orders'] ); ?></h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td style="padding-bottom:60px;padding-left:10px;padding-right:10px;padding-top:10px;">
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; text-align: center;"><span style="font-size:16px;">
															<?php
															echo esc_html__(
																'Carts Abandoned',
																'woo-cart-abandonment-recovery'
															)
															?>
														</span></p>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td class="column column-2" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="33.333333333333336%">
								<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td style="text-align:center;width:100%;padding-top:5px;">
											<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
												<?php echo esc_attr( $report_details['recovered_orders'] ); ?></h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:10px;">
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; text-align: center;"><span style="font-size:16px;">
															<?php
															echo esc_html__(
																'Carts Recovered',
																'woo-cart-abandonment-recovery'
															)
															?>
														</span></p>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
							<td class="column column-3" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="33.333333333333336%">
								<table border="0" cellpadding="0" cellspacing="0" class="heading_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td style="text-align:center;width:100%;padding-top:5px;">
											<h1 style="margin: 0; color: #1f2937; direction: ltr; font-family: Arial, Helvetica Neue, Helvetica, sans-serif; font-size: 30px; font-weight: 400; letter-spacing: normal; line-height: 120%; text-align: center; margin-top: 0; margin-bottom: 0;">
												<?php echo esc_attr( $report_details['conversion_rate'] ) . '%'; ?></h1>
										</td>
									</tr>
								</table>
								<table border="0" cellpadding="0" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td style="padding-bottom:15px;padding-left:10px;padding-right:10px;padding-top:10px;">
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; text-align: center;"><span style="font-size:16px;">
															<?php
															echo esc_html__(
																'Recovery Rate',
																'woo-cart-abandonment-recovery'
															)
															?>
														</span></p>
												</div>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<?php
