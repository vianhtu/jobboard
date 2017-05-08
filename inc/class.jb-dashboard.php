<?php
/**
 * JobBoard Dashboard.
 *
 * @class 		JobBoard_Dashboard
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

class JobBoard_Dashboard{
    function __construct()
    {
        add_filter( 'the_content', array($this, 'dashboard_content') );
        add_filter( 'jobboard_class',array($this, 'dashboard_class'));
    }

    function dashboard_class($class = array()){
        if(!is_jb_dashboard()){
            return $class;
        }

        if($endpoint = JB()->query->get_current_endpoint()){
            $class[] = 'endpoint-' . $endpoint;
        }

        return $class;
    }

    function dashboard_content($content){
        if(is_jb_dashboard()){
            $content = '[jobboard-dashboard]';
        }
        return $content;
    }
}