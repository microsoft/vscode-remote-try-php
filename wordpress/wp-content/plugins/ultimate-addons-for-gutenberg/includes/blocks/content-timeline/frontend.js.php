<?php
/**
 * Frontend JS File.
 *
 * @since 2.0.0
 *
 * @package uagb
 */

$selector = '.uagb-block-' . $id;

$timelineAlignment       = $attr['timelinAlignment'];
$timelineAlignmentTablet = ! empty( $attr['timelinAlignmentTablet'] ) ? $attr['timelinAlignmentTablet'] : $attr['timelinAlignment'];
$timelineAlignmentMobile = ! empty( $attr['timelinAlignmentMobile'] ) ? $attr['timelinAlignmentMobile'] : $timelineAlignmentTablet;

$js_attr = array(
	'block_id'               => $attr['block_id'],
	'timelinAlignment'       => $timelineAlignment,
	'timelinAlignmentTablet' => $timelineAlignmentTablet,
	'timelinAlignmentMobile' => $timelineAlignmentMobile,
);
ob_start();
?>
window.addEventListener("DOMContentLoaded", function(){
	UAGBTimelineClasses( <?php echo wp_json_encode( $js_attr ); ?>, '<?php echo esc_attr( $selector ); ?>' );
});
window.addEventListener("resize", function(){
	UAGBTimelineClasses( <?php echo wp_json_encode( $js_attr ); ?>, '<?php echo esc_attr( $selector ); ?>' );
});
<?php
return ob_get_clean();
?>
