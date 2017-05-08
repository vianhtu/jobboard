<?php
/**
 * JobBoard Shortcodes.
 *
 * @class 		JobBoard_Shortcodes
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!class_exists('JobBoard_Shortcodes')):

    class JobBoard_Shortcodes {

        function __construct() {
            add_shortcode( 'jobboard-dashboard', array($this, 'shortcodes_dashboard') );
        }

        function shortcodes_dashboard($atts = array(), $content = ''){
            global $jobboard;
            if(is_jb_candidate_dashboard()){
                $atts['type'] = 'candidate';
            } elseif (is_jb_employer_dashboard()){
                $atts['type'] = 'employer';
            } elseif (get_current_user_id()) {
                $atts['type'] = 'other';
            } else {
                $atts['type'] = 'not_logged';
            }
            $jobboard->account = $atts['type'];
            ob_start();
            jb_get_template( 'dashboard/dashboard.php', array('atts' => $atts, 'content' => $content));
            return ob_get_clean();
        }
    }

endif;