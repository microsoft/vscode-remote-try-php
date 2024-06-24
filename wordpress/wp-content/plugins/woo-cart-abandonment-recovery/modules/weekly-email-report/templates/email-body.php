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
ob_start();
?>

<!doctype html>
<html>

<head>
	<title><?php echo esc_html__( 'Cart Abandonment Recovery Weekly Report', 'woo-cart-abandonment-recovery' ); ?></title>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->
	<style>
		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			padding: 0;
		}

		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: inherit !important;
		}

		#MessageViewBody a {
			color: inherit;
			text-decoration: none;
		}

		p {
			line-height: inherit
		}

		@media (max-width:620px) {
			.row-content {
				width: 100% !important;
			}

			.column .border {
				display: none;
			}

			.stack .column {
				width: 100%;
				display: block;
			}
		}
	</style>
</head>

<body style="background-color: #FFFFFF; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;">
	<table border="0" cellpadding="0" cellspacing="0" class="nl-container" role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #FFFFFF;" width="100%">
		<tbody>
			<tr>
				<td>
					<?php
					require CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-header.php';

					require CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-content-section.php';

					require CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-recovery-stat.php';

					if ( ! class_exists( 'Cartflows_Pro_Loader' ) ) {
						include CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-cf-block.php';
					} else {
						include CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-bsf-product-block.php';
					}

					require CARTFLOWS_CA_DIR . 'modules/weekly-email-report/templates/email-footer.php';

					?>
				</td>
			</tr>
		</tbody>
	</table><!-- End -->
</body>

</html>

<?php
return ob_get_clean();
