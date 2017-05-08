<?php
/**
 * JobBoard Job.
 *
 * @class 		JobBoard_Job
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

class JobBoard_Job{

    function __construct()
    {
        add_filter('jobboard_catalog_showing_args', array($this, 'showing_args'));
    }

    function apply($user_id, $post_id, $job_status = 'applied'){

        global $wpdb;

        $applied_id = false;

        /* if applied exists. */
        if($apply   = $this->get_row($user_id, $post_id)){
            /* update status. */
            if($apply->app_status != $job_status) {
                $wpdb->update($wpdb->prefix . 'jobboard_applied', array(
                    'app_status' => $job_status,
                    'app_date' => current_time('mysql'),
                ), array(
                    'app_id' => $apply->app_id
                ), array(
                    '%s',
                    '%s'
                ), array(
                    '%d'
                ));

                $applied_id = $apply->app_id;
            }
        } else {

            $data = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'app_status' => $job_status,
                'app_date' => current_time('mysql')
            );

            $format = array(
                '%d',
                '%d',
                '%s',
                '%s'
            );

            /* apply job. */
            $insert = $wpdb->insert($wpdb->prefix . 'jobboard_applied', $data, $format);

            if (!$insert) {
                return false;
            }

            $applied_id = absint($wpdb->insert_id);
        }

        if($applied_id === false){
            return false;
        }

        do_action( 'jobboard_job_applied', $user_id, $post_id, $job_status );

        return $applied_id;
    }

    function showing_args($showing){
        global $wp_query, $paged;

        if(!is_jb_jobs()){
            return $showing;
        }

        $posts_per_page     = $wp_query->get('posts_per_page');
        $showing['paged']   = $paged ? $paged : 1;
        $showing['current'] = $wp_query->post_count;
        $showing['all']     = $wp_query->found_posts;
        $posts_per_pages    = $showing['paged'] * $posts_per_page;

        if($posts_per_pages <= $showing['all']){
            $showing['current'] = $posts_per_pages;
        } else {
            $showing['current'] = $showing['all'];
        }

        $showing['paged']   = $showing['current'] - $wp_query->post_count;

        return $showing;
    }

    function get_row($user_id, $post_id, $status = ''){

        global $wpdb;

        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}jobboard_applied as jb WHERE jb.user_id = %d AND jb.post_id = %d", $user_id, $post_id);

        if($status){
            $query .= " AND jb.app_status = '{$status}'";
        }

        return $wpdb->get_row($query);
    }

    function get_status($post_id, $user_id = ''){

        if(!$user_id = get_current_user_id()){
            return null;
        }

        global $wpdb;

        return $wpdb->get_var($wpdb->prepare("SELECT jb.app_status FROM {$wpdb->prefix}jobboard_applied as jb WHERE jb.user_id = %d AND jb.post_id = %d", $user_id, $post_id));
    }

    function count($user_id = ''){

        if(!$user_id){
            $user_id = get_current_user_id();
        }

        $results     = array();
        $counts      = array();

        if($user_id) {

            global $wpdb;

            $query   = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
            $query  .= " AND post_author = %d";
            $query  .= ' GROUP BY post_status';

            $results = (array)$wpdb->get_results($wpdb->prepare($query, 'jobboard-post-jobs', $user_id), ARRAY_A);
        }

        if(!empty($results)){
            foreach ( $results as $row ) {
                $counts[ $row['post_status'] ] = $row['num_posts'];
            }
        }

        return apply_filters('jobboard_count_jobs', $counts);
    }

    function count_featured($user_id = ''){

        if(!$user_id){
            $user_id = get_current_user_id();
        }

        $count = 0;

        if($user_id) {

            global $wpdb;

            $query   = "SELECT COUNT(*) FROM {$wpdb->posts}";
            $query  .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id";
            $query  .= " WHERE post_type = %s AND post_author = %d AND {$wpdb->postmeta}.meta_key = %s AND {$wpdb->postmeta}.meta_value = %s";

            $count   = $wpdb->get_var($wpdb->prepare($query, 'jobboard-post-jobs', $user_id, '_featured', '1'));
        }

        return apply_filters('jobboard_count_jobs_featured', $count);
    }

    function posts_fields($fields){
        return $fields .= ",jb.*";
    }

    function posts_join($join){
        global $wpdb;

        $join .= "LEFT JOIN {$wpdb->prefix}jobboard_applied as jb ON $wpdb->posts.ID = jb.post_id";
        return $join;
    }

    function posts_where($where, $args){

        global $wpdb;

        if(!$user_id = get_current_user_id()){
            return $where;
        }

        $where      .= $wpdb->prepare(" AND jb.user_id = %d", $user_id);

        if(!empty($args->query['app_status'])){
            if(is_array($args->query['app_status'])){
                $status = implode("','", $args->query['app_status']);
                $where .= " AND jb.app_status IN ('$status')";
            } else {
                $where .= $wpdb->prepare(" AND jb.app_status = %s", $args->query['app_status']);
            }
        }

        return $where;
    }

    function posts_orderby($orderby, $args){

        if($args->query['orderby'] == 'app_date'){
            return "jb.app_date " . strtoupper($args->query['order']);
        }

        return $orderby;
    }

    function query($args){

        /* query job. */
        $query = wp_parse_args($args, array(
            'post_type'     => 'jobboard-post-jobs',
            'post_status'   => 'publish',
            'app_status'    => '',
            'paged'         => 1,
            'orderby'       => 'date',
            'order'         => 'DESC',
        ));

        /* add custom query. */
        add_filter('posts_fields'       , array($this, 'posts_fields'));
        add_filter('posts_join'         , array($this, 'posts_join'));
        add_filter('posts_where'        , array($this, 'posts_where'), 10, 2);
        add_filter('posts_orderby'      , array($this, 'posts_orderby'), 10, 2);

        $jobs   = new WP_Query(apply_filters('jb/job/query', $query));

        /* remove custom query */
        remove_filter('posts_fields'    , array($this, 'posts_fields'));
        remove_filter('posts_join'      , array($this, 'posts_join'));
        remove_filter('posts_where'     , array($this, 'posts_where'));
        remove_filter('posts_orderby'   , array($this, 'posts_orderby'));

        return $jobs;
    }

    function query_date_posted($date_posted = 0){

        $date_query = array();

        if(!$date_posted){
            return $date_query;
        }

        $date = date('Y-m-d H:i:s', strtotime("-{$date_posted} hour", current_time( 'timestamp' )));

        $date_query = array(
            array(
                'after' => $date
            )
        );

        return apply_filters('jb/job/query/date', $date_query, $date_posted);
    }
}