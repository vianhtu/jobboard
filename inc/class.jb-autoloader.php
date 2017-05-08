<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * JobBoard Autoloader.
 *
 * @class 		JobBoard_Autoloader
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */
class JobBoard_Autoloader {

	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 */
	private $include_path = '';

	/**
	 * The Constructor.
	 */
	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path =  JB()->plugin_directory . 'inc/';
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param  string $class
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		return 'class.' . str_replace( 'jobboard_', 'jb-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @param  string $path
	 * @return bool successful or not
	 */
	private function load_file( $path ) {

		if ( $path && is_readable( $path ) ) {
			include_once( $path );
			return true;
		}
		return false;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @param string $class
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );

        if (strpos( $class, 'jobboard_' ) === 0) {
            $this->load_file($this->include_path . $file);
        }
	}
}

new JobBoard_Autoloader();
