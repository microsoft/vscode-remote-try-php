<?php
/**
 * Frontend JS File.
 *
 * @since 2.8.0
 * @package uagb
 */

/**
 * Adding this comment to avoid PHPStan errors of undefined variable as these variables are defined elsewhere.
 *
 * @var int $id  The block ID.
 */

$selector = '.uagb-block-' . $id;
$js       = '';

if ( ! empty( $attr['UAGPosition'] ) && is_string( $attr['UAGPosition'] ) ) {
	$position_attrs = array_merge(
		$attr,
		array(
			'UAGStickyLocation'   => ! empty( $attr['UAGStickyLocation'] ) ? $attr['UAGStickyLocation'] : 'top',
			'UAGStickyRestricted' => ! empty( $attr['UAGStickyRestricted'] ) ? $attr['UAGStickyRestricted'] : false,
			'UAGStickyOffset'     => ( ! empty( $attr['UAGStickyOffset'] ) && is_numeric( $attr['UAGStickyOffset'] ) ) ? $attr['UAGStickyOffset'] : 0,
		)
	);

	$js = 'UAGBBlockPositioning.init( ' . wp_json_encode( $position_attrs ) . ', "' . esc_attr( $selector ) . '" );';
}

return $js;
