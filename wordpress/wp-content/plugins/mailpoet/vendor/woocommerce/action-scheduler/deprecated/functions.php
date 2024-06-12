<?php
if (!defined('ABSPATH')) exit;
function wc_schedule_single_action( $timestamp, $hook, $args = array(), $group = '' ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_single_action()' );
 return as_schedule_single_action( $timestamp, $hook, $args, $group );
}
function wc_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args = array(), $group = '' ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_recurring_action()' );
 return as_schedule_recurring_action( $timestamp, $interval_in_seconds, $hook, $args, $group );
}
function wc_schedule_cron_action( $timestamp, $schedule, $hook, $args = array(), $group = '' ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_schedule_cron_action()' );
 return as_schedule_cron_action( $timestamp, $schedule, $hook, $args, $group );
}
function wc_unschedule_action( $hook, $args = array(), $group = '' ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_unschedule_action()' );
 as_unschedule_action( $hook, $args, $group );
}
function wc_next_scheduled_action( $hook, $args = NULL, $group = '' ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_next_scheduled_action()' );
 return as_next_scheduled_action( $hook, $args, $group );
}
function wc_get_scheduled_actions( $args = array(), $return_format = OBJECT ) {
 _deprecated_function( __FUNCTION__, '2.1.0', 'as_get_scheduled_actions()' );
 return as_get_scheduled_actions( $args, $return_format );
}
