<?php
/**
 * JobBoard Employer.
 *
 * @class 		JobBoard_Employer
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

class JobBoard_Employer{

    function __construct()
    {
        add_action('wp_ajax_jobboard_get_applications', array($this, 'ajax_get_applications'));
        add_action('wp_ajax_jobboard_update_applications', array($this, 'ajax_update_applications'));
        add_action('wp_ajax_jobboard_delete_job', array($this, 'ajax_delete_job'));
        add_filter('jobboard_add_job_fields', array($this, 'set_add_fields_value'));
    }

    function get_jobs($user_id = ''){
        global $wp_query;

        if(!$user_id){
            $user_id = get_current_user_id();
        }

        if(!$user_id){
            return;
        }

        if(!is_jb_employer($user_id)){
            return;
        }

        $paged      = 1;

        /* get current paged.  */
        if(!empty($_REQUEST['paged'])){
            $paged  =  $_REQUEST['paged'];
        } elseif (!empty($wp_query->query['jobs'])){
            $paged  = str_replace('page/', null, $wp_query->query['jobs']);
        }

        /* query job. */
        $query      = array(
            'post_type'     => 'jobboard-post-jobs',
            'post_status'   => array('publish', 'pending', 'trash'),
            'posts_per_page'=> jb_get_option('dashboard-per-page', 12),
            'author'        => $user_id,
            'paged'         => $paged
        );

        return new WP_Query($query);
    }

    /**
     * Get applications by post id.
     *
     * @param $post_id
     * @param $page
     * @param $search
     * @return array|null|object
     */
    function get_applications($post_id, $page = 0, $search = ''){
        global $wpdb;

        $query = array();

        $query['select'] = "SELECT *";
        $query['join']   = "FROM {$wpdb->prefix}jobboard_applied AS app LEFT JOIN {$wpdb->users} AS user ON app.user_id = user.ID";
        $query['where']  = $wpdb->prepare("WHERE app.post_id = %d AND app.app_status IN ('applied','approved','reject')", $post_id);

        if($search) {
            $query['like'] = $wpdb->prepare('AND (user.user_email LIKE \'%1$s\' OR user.display_name LIKE \'%2$s\' OR app.app_status LIKE \'%1$s\')', $search, "%$search%");
        }

        $query['order']  = 'ORDER BY app.app_date DESC';
        $query['limit']  = $wpdb->prepare('LIMIT %d,10', $page * 10);

        return $wpdb->get_results(implode(' ', $query));
    }

    /**
     * get application by id.
     *
     * @param $id
     * @return array|null|object|void
     */
    function get_application($id){
        global $wpdb;

        $query = array();

        $query['select'] = "SELECT *";
        $query['join']   = "FROM {$wpdb->prefix}jobboard_applied";
        $query['where']  = "WHERE app_id = %d";

        return $wpdb->get_row($wpdb->prepare(implode(' ', $query), $id));
    }

    function get_trending_taxonomies($user_id = '', $taxonomy, $limit = 10){
        global $wpdb;

        if(!$user_id){
            $user_id = get_current_user_id();
        }

        $query = array();

        $query['select']                        = "SELECT {$wpdb->terms}.*, COUNT(*) AS level";
        $query['from']                          = "FROM {$wpdb->posts}";
        $query['left-join-relationships']       = "LEFT JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id";
        $query['inner-join-taxonomy']           = "INNER JOIN {$wpdb->term_taxonomy} on {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id";
        $query['inner-join-terms']              = "INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id";
        $query['where']                         = "WHERE {$wpdb->posts}.post_author = %d";
        $query['and-post-type']                 = "AND {$wpdb->posts}.post_type = 'jobboard-post-jobs'";
        $query['and-taxonomy']                  = $wpdb->prepare("AND {$wpdb->term_taxonomy}.taxonomy = %s", $taxonomy);
        $query['group-by']                      = "GROUP BY {$wpdb->terms}.term_id";
        $query['order-by']                      = "ORDER BY level DESC";
        $query['limit']                         = "LIMIT 0,%d";

        return $wpdb->get_results($wpdb->prepare(implode(' ', $query), $user_id, $limit));
    }

    function set_add_fields_value($fields){
        $post_keys       = jb_job_keys();
        $tax_keys        = jb_job_tax_keys();

        if(!empty($_REQUEST['post_id']) && $post = get_post($_REQUEST['post_id'])){
            $thumbnail_id = get_post_thumbnail_id($post->ID);
        }

        foreach ($fields as $k => $field){
            if(isset($_POST[$field['id']])){
                $fields[$k]['value'] = $_POST[$field['id']];
            } elseif (!empty($post)){
                if(in_array($field['id'], $post_keys)){
                    $fields[$k]['value'] = $post->{$field['id']};
                } elseif (isset($tax_keys[$field['id']])){
                    $fields[$k]['value'] = wp_get_post_terms($post->ID, 'jobboard-tax-' . $field['id'], array('fields' => $tax_keys[$field['id']]));
                } elseif ($field['id'] == 'featured-image' && $thumbnail_id){
                    $fields[$k]['value'] = $thumbnail_id;
                } else {
                    $fields[$k]['value'] = get_post_meta($post->ID, $field['id'], true);
                }
            }
        }

        return $fields;
    }

    function ajax_get_applications(){

        $data = array('error' => 0, 'html' => '');

        if(!empty($_POST['id'])){

            $post_id        = $_POST['id'];
            $s              = !empty($_POST['s']) ? $_POST['s'] : '';
            $page           = !empty($_POST['page']) ? (int)$_POST['page'] : 0;
            $applications   = $this->get_applications($post_id, $page, $s);

            if ($applications){
                $data['error'] = 0;
            } elseif ($page > 0){
                $data['error'] = 2;
            } else {
                $data['error'] = 1;
            }

            ob_start();

            jb_template_employer_jobs_candidates($applications);

            $data['html'] = ob_get_clean();
        }

        wp_send_json($data);

        exit();
    }

    function ajax_update_applications(){

        if(false === $user_id = get_current_user_id()){
            exit();
        }

        if(empty($_POST['status']) || empty($_POST['id'])){
            exit();
        }

        $id     = $_POST['id'];
        $status = $_POST['status'];

        /* if application does not exist. */
        if(!$application = $this->get_application($id)){
            exit();
        };

        if($status == $application->app_status){
            exit();
        }

        $post = get_post($application->post_id);

        /* if post does not exist. */
        if(!$post){
            exit();
        }

        /* if user permission. */
        if($post->post_author != $user_id){
            exit();
        }

        global $wpdb;

        $wpdb->update($wpdb->prefix . 'jobboard_applied', array(
            'app_status' => $status
        ), array(
            'app_id'     => $id
        ), array(
            '%s'
        ), array(
            '%d'
        ));

        $employer   = get_userdata($user_id);
        $candidate  = get_userdata($application->user_id);
        $candidate->application = jb_candidate_applied_status($status);
        $send_email = new JobBoard_Emails();
        $send_email->application_update($post, $employer, $candidate);

        jb_template_employer_application_status($status);

        exit();
    }

    function ajax_delete_job(){

        if(false === $user_id = get_current_user_id()){
            exit(false);
        }

        if(empty($_POST['id'])){
            exit(false);
        }

        $id = $_POST['id'];

        if(!$post = get_post($id)){
            exit(false);
        }

        if($post->post_author != $user_id){
            exit(false);
        }

        wp_delete_post($id);

        exit(sprintf(esc_html__("Successfully Deleted for '%s'", 'jobboard'), $post->post_title));
    }

    function count_applied($post = '', $status = array()){
        global $post;

        if(!$post){
            return 0;
        }

        global $wpdb;

        $query = "SELECT COUNT(app_id) FROM {$wpdb->prefix}jobboard_applied WHERE post_id = %d";

        if(!empty($status)){
            $query .= " AND app_status IN ('".implode("','", $status)."')";
        }

        return $wpdb->get_var($wpdb->prepare($query, $post->ID));
    }

    function count_applied_since_days($user_id = '', $date = 30){

        if(!$user_id){
            $user_id = get_current_user_id();
        }

        if(!$user_id){
            return 0;
        }

        global $wpdb;

        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(p.ID) FROM {$wpdb->prefix}jobboard_applied AS app INNER JOIN {$wpdb->posts} AS p ON p.ID = app.post_id WHERE p.post_author = %d AND app.app_status NOT IN ('basket') AND app.app_date >= CURRENT_DATE - INTERVAL %d DAY",
            $user_id, $date)
        );
    }
}