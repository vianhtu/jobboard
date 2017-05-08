<?php
/**
 * Message Functions
 *
 * Functions for error/message handling and display.
 *
 * @author 		FOX
 * @category 	Core
 * @package 	JobBoard/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get the count of notices added, either for all notices (default) or for one.
 * particular notice type specified by $notice_type.
 *
 * @since 2.1
 * @param string $notice_type The name of the notice type - either error, success or notice. [optional]
 * @return int
 */
function jb_notice_count( $notice_type = '' ) {

    $notice_count = 0;
    $all_notices  = JB()->session->get( 'jb_notices', array() );

    if ( isset( $all_notices[$notice_type] ) ) {

        $notice_count = absint( sizeof( $all_notices[$notice_type] ) );

    } elseif ( empty( $notice_type ) ) {

        foreach ( $all_notices as $notices ) {
            $notice_count += absint( sizeof( $all_notices ) );
        }

    }

    return $notice_count;
}

/**
 * Add and store a notice.
 *
 * @since 2.1
 * @param string $message The text to display in the notice.
 * @param string $notice_type The singular name of the notice type - either error, success or notice. [optional]
 */
function jb_notice_add( $message, $notice_type = 'success' ) {

    $notices = JB()->session->get( 'jb_notices', array() );

    $notices[$notice_type][] = apply_filters( 'wpl_jobboard_add_' . $notice_type, $message );

    JB()->session->set( 'jb_notices', $notices );
}


/**
 * Unset all notices.
 *
 * @since 2.1
 */
function jb_notices_clear() {
    JB()->session->set( 'jb_notices', null );
}
