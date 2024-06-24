<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$selector        = '.uagb-block-' . $id;
$current_post_id = get_the_ID();
$curr_user       = wp_get_current_user();
$default_email   = $curr_user->user_email;
$js_attr         = array(
	'block_id'                => $attr['block_id'],
	'reCaptchaEnable'         => $attr['reCaptchaEnable'],
	'reCaptchaType'           => $attr['reCaptchaType'],
	'reCaptchaSiteKeyV2'      => $attr['reCaptchaSiteKeyV2'],
	'reCaptchaSecretKeyV2'    => $attr['reCaptchaSecretKeyV2'],
	'reCaptchaSiteKeyV3'      => $attr['reCaptchaSiteKeyV3'],
	'reCaptchaSecretKeyV3'    => $attr['reCaptchaSecretKeyV3'],
	'afterSubmitToEmail'      => ! empty( trim( $attr['afterSubmitToEmail'] ) ) ? $attr['afterSubmitToEmail'] : $default_email,
	'afterSubmitCcEmail'      => $attr['afterSubmitCcEmail'],
	'afterSubmitBccEmail'     => $attr['afterSubmitBccEmail'],
	'afterSubmitEmailSubject' => $attr['afterSubmitEmailSubject'],
	'sendAfterSubmitEmail'    => $attr['sendAfterSubmitEmail'],
	'confirmationType'        => $attr['confirmationType'],
	'hidereCaptchaBatch'      => $attr['hidereCaptchaBatch'],
	'captchaMessage'          => $attr['captchaMessage'],
	'confirmationUrl'         => $attr['confirmationUrl'],
);
ob_start();
?>
window.addEventListener("DOMContentLoaded", function(){
	UAGBForms.init( <?php echo wp_json_encode( $js_attr ); ?>, '<?php echo esc_attr( $selector ); ?>', <?php echo wp_json_encode( $current_post_id ); ?> );
});
<?php
return ob_get_clean();
?>
