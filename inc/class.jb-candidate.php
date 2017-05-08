<?php
/**
 * JobBoard Candidate.
 *
 * @class 		JobBoard_Candidate
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

class JobBoard_Candidate{

    /**
     * Get Applied.
     *
     * @return mixed
     */
    public function get_applied(){
        global $wp_query;

        $paged =  1;

        if(!empty($_REQUEST['paged'])){
            $paged  =  $_REQUEST['paged'];
        } elseif (!empty($wp_query->query['applied'])){
            $paged  =  str_replace('page/', null, $wp_query->query['applied']);
        }

        $query = array(
            'paged'         => $paged,
            'app_status'    => array('applied', 'approved', 'rejected'),
            'orderby'       => 'app_date',
            'posts_per_page'=> jb_get_option('dashboard-per-page', 12),
        );

        return JB()->job->query($query);
    }

    /**
     * Count Applied / 30 Days
     *
     * @param string $user_id
     * @param int $date
     * @return int|null|string
     */
    public function count_applied($user_id = '', $date = 30){
        if(!$user_id){
            $user_id = get_current_user_id();
        }

        if(!$user_id){
            return 0;
        }

        global $wpdb;

        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(post_id) FROM {$wpdb->prefix}jobboard_applied WHERE user_id = %d AND app_status NOT IN ('basket') AND app_date >= CURRENT_DATE - INTERVAL %d DAY",
            $user_id, $date)
        );
    }
}