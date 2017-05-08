<?php
/**
 * WooCommerce Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @author 		WooThemes
 * @category 	Core
 * @package 	WooCommerce/Functions
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include widget classes.
include_once( 'abstracts/abstract-jb-widget.php' );
include_once( 'widgets/class-jb-widget-search.php' );
include_once( 'widgets/class-jb-widget-type.php' );
include_once( 'widgets/class-jb-widget-date-filters.php' );
include_once( 'widgets/class-jb-widget-specialism-filters.php' );
include_once( 'widgets/class-jb-widget-jobs.php' );
include_once( 'widgets/class-jb-widget-specialism-list.php' );

/**
 * Register Widgets.
 *
 * @since 2.3.0
 */
function jb_widget_register_widgets() {
	register_widget( 'JB_Widget_Search' );
	register_widget( 'JB_Widget_Type' );
	register_widget( 'JB_Widget_Date_Filters' );
	register_widget( 'JB_Widget_Specialism_Filters' );
	register_widget( 'JB_Widget_Jobs' );
	register_widget( 'JB_Widget_Specialism_List' );
}

add_action( 'widgets_init', 'jb_widget_register_widgets' );
