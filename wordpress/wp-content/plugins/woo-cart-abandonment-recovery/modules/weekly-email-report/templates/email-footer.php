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
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-7" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
					<tbody>
						<tr>
							<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
								<table border="0" cellpadding="20" cellspacing="0" class="social_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
									<tr>
										<td>
											<table align="center" border="0" cellpadding="0" cellspacing="0" class="social-table" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="138px">
												<tr>
													<td style="padding:0 7px 0 7px;"><a href="https://www.facebook.com/groups/cartflows/" target="_blank"><img alt="Facebook" height="32" src="<?php echo esc_url( $facebook_icon ); ?>" style="display: block; height: auto; border: 0;" title="facebook" width="32" /></a>
													</td>
													<td style="padding:0 7px 0 7px;"><a href="https://twitter.com/cartflows" target="_blank"><img alt="Twitter" height="32" src="<?php echo esc_url( $twitter_icon ); ?>" style="display: block; height: auto; border: 0;" title="twitter" width="32" /></a>
													</td>
													<td style="padding:0 7px 0 7px;"><a href="https://www.youtube.com/channel/UCEdXT5pEI_Vbd5te5v7sOpQ" target="_blank"><img alt="YouTube" height="32" src="<?php echo esc_url( $youtube_icon ); ?>" style="display: block; height: auto; border: 0;" title="YouTube" width="32" /></a>
													</td>
												</tr>
											</table>
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
<table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-8" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
	<tbody>
		<tr>
			<td>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="row-content stack" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 600px;" width="600">
					<tbody>
						<tr>
							<td class="column column-1" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;" width="100%">
								<table border="0" cellpadding="10" cellspacing="0" class="text_block" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;" width="100%">
									<tr>
										<td>
											<div style="font-family: sans-serif">
												<div style="font-size: 14px; mso-line-height-alt: 16.8px; color: #393d47; line-height: 1.2; font-family: Arial, Helvetica Neue, Helvetica, sans-serif;">
													<p style="margin: 0; text-align: center;">
													<?php
														echo wp_kses_post(
															sprintf( /* translators: %1$s - link to a site; */
																__( 'This email was auto-generated and sent from %1$s.', 'woo-cart-abandonment-recovery' ),
																'<a href="' . esc_url( home_url() ) . '" style="text-decoration:none;" >' . esc_html( wp_specialchars_decode( get_bloginfo( 'name' ) ) ) . '</a>'
															)
														);
														?>
													</p>
													<p style="margin: 0; text-align: center; mso-line-height-alt: 16.8px;">
														 </p>
													<p style="margin: 0; text-align: center;"><a href="<?php echo esc_url( $unsubscribe_link ); ?>" rel="noopener" style="color: #8a3b8f;" target="_blank"><?php echo esc_html__( 'Unsubscribe', 'woo-cart-abandonment-recovery' ); ?></a></p>
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
