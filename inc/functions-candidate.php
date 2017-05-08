<?php
/**
 * JobBoard Candidate Functions
 *
 * Functions for account specific things.
 *
 * @author   FOX
 * @category Core
 * @package  JobBoard/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * return candidate navigation.
 *
 * @return array
 */
function jb_candidate_navigation_args() {

    $endpoint_applied = jb_get_option('endpoint-applied', 'applied');
    $endpoint_profile = jb_get_option('endpoint-profile', 'profile');

    $navigation = apply_filters( 'jobboard_candidate_navigation_args', array(
        array(
            'id'        => 'dashboard',
            'endpoint'  => 'dashboard',
            'title'     => esc_html__( 'My Account', 'jobboard' )
        ),
        array(
            'id'        => 'applied',
            'endpoint'  => $endpoint_applied,
            'title'     => esc_html__( 'Application History', 'jobboard' )
        ),
        array(
            'id'        => 'profile',
            'endpoint'  => $endpoint_profile,
            'title'     => esc_html__( 'Manage Profile', 'jobboard' )
        ),
    ));

    $navigation[] = array(
        'id'        => 'logout',
        'endpoint'  => 'logout',
        'title'     => esc_html__( 'Logout', 'jobboard' )
    );

    return $navigation;
}

/**
 * return applied number.
 *
 * @return number/0
 */
function jb_candidate_count_applied($user_id = '', $date = 30){
    $applied = JB()->candidate->count_applied($user_id, $date);

    return apply_filters('jb/candidate/applied/count', $applied);
}

/**
 * Applied status.
 *
 * @param $status
 * @return status label.
 */
function jb_candidate_applied_status($status = ''){

    switch ($status) {
        case 'approved':
            $label = esc_html__('Approved', 'jobboard');
            break;
        case 'applied':
            $label = esc_html__('Pending', 'jobboard');
            break;
        default:
            $label = esc_html__('Rejected', 'jobboard');
    }

    return apply_filters('jb/candidate/job/applied/status', $label, $status);
}

/**
 * return candidate custom field.
 *
 * @return mixed
 */
function jb_candidate_profile_custom_field(){
    $fields = apply_filters('jobboard_candidate_profile_fields', jb_get_option('candidate-custom-fields'));
    return jb_account_custom_fields_value('', $fields);
}

/**
 * return applied count notice.
 *
 * @param $title
 * @return string
 */
function jb_candidate_profile_applied_count($title){
    $applied = get_user_meta(get_current_user_id(), '_jobboard_applied_ids', true);

    if(empty($applied)){
        return $title;
    }

    ob_start();

    jb_get_template('global/count.php', array('count' => count($applied)));

    return $title . ob_get_clean();
}

add_filter('jobboard_dashboard_navigation_applied_title', 'jb_candidate_profile_applied_count');

function jb_candidate_reset_applied_count(){
    $user_id = get_current_user_id();
    $applied = get_user_meta($user_id, '_jobboard_applied_ids', true);

    if(empty($applied)){
        return;
    }

    update_user_meta($user_id, '_jobboard_applied_ids', array());
}

function jb_candidate_the_cv_url(){
    echo esc_url(jb_candidate_get_cv_url());
}

function jb_candidate_the_salary(){
    echo esc_html(jb_candidate_get_salary());
}

function jb_candidate_get_cv_url($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $attachment = get_user_meta($user_id, 'cv', true);
    return !empty($attachment['id']) ? wp_get_attachment_url($attachment['id']) : '';
}

function jb_candidate_get_salary($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $salary = get_user_meta($user_id, 'job_salary', true);

    if($salary){
        $salary = jb_get_salary_currency($salary);
    }

    return $salary;
}