<?php
/**
 * JobBoard Session Handler.
 *
 * @class 		JobBoard_Session_Handler
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'JobBoard_Session' ) ) {
	include_once( 'abstracts/abstract-jb-session.php' );
}

/**
 * Handle data for the current customers session.
 * Implements the JobBoard_Session abstract class.
 *
 * @class    JobBoard_Session_Handler
 * @version  1.0.0
 * @package  JobBoard/Classes
 * @category Class
 * @author   FOX
 */
class JobBoard_Session_Handler extends JobBoard_Session {

}
