<?php
/**
 * JobBoard Account Functions
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
 * return account type.
 *
 * @return candidate/employer/null
 */
function jb_account_type(){
    global $jobboard;

    if(!isset($jobboard->account)){
       if(is_jb_candidate()){
           $jobboard->account = 'candidate';
       } elseif (is_jb_employer()){
           $jobboard->account = 'employer';
       } else {
           return null;
       }
    }

    return $jobboard->account;
}

/**
 * return navigation class.
 *
 * @since 1.0.0
 * @param string $endpoint
 * @return string
 */
function jb_account_navigation_class( $endpoint, $class = array()) {
    global $wp;

    $current = isset( $wp->query_vars[ $endpoint ] );

    if($endpoint == 'dashboard' && is_jb_dashboard() && count($wp->query_vars) == 1){
        $current = true;
    }

    if ( $current ) {
        $class[] = 'is-active';
    }

    $class = apply_filters( 'jb/account/navigation/class', $class, $endpoint );

    return implode( ' ', array_map( 'sanitize_html_class', $class ) );
}

/**
 * return account custom field
 *
 * @param $user_id
 * @param $roles
 * @return mixed|null
 */
function jb_account_custom_field($user_id, $roles){

    $fields = array();

    if(in_array('jobboard_role_candidate', $roles)){
        $fields = jb_get_option('candidate-custom-fields');
    } elseif (in_array('jobboard_role_employer', $roles)){
        $fields = jb_get_option('employer-custom-fields');
    }

    if(empty($fields))
        return null;

    foreach ($fields as $field){
        if(isset($field['id']) && $field['id'] == $user_id ){
            return $field;
        }
    }

    return null;
}

/**
 * return account media folder url.
 *
 * @param string $file
 * @return mixed|void
 */
function jb_account_get_media_url($file = ''){

    $upload_dir = wp_upload_dir();

    return apply_filters('jb/account/media/url', $upload_dir['baseurl'] . '/' . $file);
}

/**
 * change password.
 *
 * @param $user_id
 */
function jb_account_change_password($user_id){

    if(empty($_POST['new-password']) || empty($_POST['confirm-password'])){
        return;
    }

    $validate = array();

    jb_notices_clear();

    if(strlen($_POST['new-password']) < 8){

        $validate['new-password']       = 'new-password';

        jb_notice_add(esc_html__('Error : password must be greater than 8 characters.', 'jobboard'), 'error');

        return;
    }

    if($_POST['new-password'] !== $_POST['confirm-password']){

        $validate['new-password']       = 'new-password';
        $validate['confirm-password']   = 'confirm-password';

        jb_notice_add(esc_html__('Error : new password not same confirm password.', 'jobboard'), 'error');

        return;
    }

    wp_set_password($_POST['confirm-password'], $user_id);

    JB()->session->set( 'validate', $validate );

    jb_notice_add(esc_html__('Success : Password Changed !', 'jobboard'));
}

add_action( 'jobboard_profile_updated', 'jb_account_change_password');

/**
 * account applied job.
 *
 * @return mixed
 */
function jb_account_applied(){
    global $post;

    if(!$user_id = get_current_user_id()){
        return false;
    }

    return JB()->job->get_row($user_id, $post->ID, 'applied');
}

/**
 * return user custom fields values
 *
 * @param $user_id
 * @param $fields
 * @return mixed
 */
function jb_account_custom_fields_value($user_id = '', $fields){

    if(!$user_id){
        $user_id = get_current_user_id();
    }

    if(!$user_id || empty($fields)){
        return $fields;
    }

    $user       = get_userdata($user_id);
    $user_keys  = jb_user_keys();

    foreach ($fields as $k => $field) {

        if(in_array($field['id'], $user_keys)){

            $key                    = $field['id'];
            $fields[$k]['value']    = $user->data->$key;

            continue;
        }

        if($value = get_user_meta($user_id, $field['id'], true)){
            $fields[$k]['value'] = $value;
        }

        if($field['id'] == 'job_specialisms'){
            $fields[$k]['options'] = jb_get_specialism_options();
        }
    }

    return $fields;
}

function jb_account_count_jobs(){
    return JB()->job->count();
}

function jb_account_count_jobs_featured(){
    return JB()->job->count_featured();
}

function jb_account_the_user($user){
    $GLOBALS['jobboard_account'] = $user;
}

function jb_account_the_user_class(){
    echo 'class="' . jb_account_get_user_class() . '"';
}

function jb_account_the_count(){
    echo jb_account_get_count();
}

function jb_account_the_id(){
    echo jb_account_get_id();
}

function jb_account_the_type(){
    echo jb_account_get_type();
}

function jb_account_the_display_name(){
    echo jb_account_get_display_name();
}

function jb_account_the_address(){
    echo jb_account_get_address();
}

function jb_account_the_city(){
    echo jb_account_get_city();
}

function jb_account_the_country(){
    echo jb_account_get_country();
}

function jb_account_the_location(){
    echo jb_account_get_location();
}

function jb_account_the_phone(){
    echo jb_account_get_phone();
}

function jb_account_the_email(){
    echo jb_account_get_email();
}

function jb_account_the_description(){
    echo jb_account_get_description();
}

function jb_account_the_permalink(){
    echo jb_account_get_permalink();
}

function jb_account_the_post_link(){
    echo jb_account_get_post_link();
}

function jb_account_the_date(){
    echo jb_account_get_date();
}

function jb_account_the_social(){
    echo jb_account_get_social();
}

function jb_account_the_avatar(){
    echo jb_account_get_avatar();
}

function jb_account_the_cover_image(){
    echo jb_account_get_cover_image();
}

function jb_account_the_specialisms(){
    $specialisms = jb_account_get_specialisms();

    if(empty($specialisms)){
        return;
    }

    echo '<ul>';
    foreach ($specialisms as $specialism){
        echo '<li><a href="' . get_term_link($specialism->term_id, 'jobboard-tax-specialisms') . '">' . esc_html($specialism->name) . '</a></li>';
    };
    echo '</ul>';
}

function jb_account_the_search_query(){
    echo jb_account_get_search_query();
}

function jb_account_the_current_name(){
    echo jb_account_get_current_name();
}

function jb_account_the_current_email(){
    echo jb_account_get_current_email();
}

function jb_account_get_users(){
    global $users;
    return !empty($users->results) ? $users->results : null;
}

function jb_account_get_user_class(){
    $role   = 'role-' . jb_account_get_type(true);
    $class  = apply_filters('jobboard_user_class_args', array(
        'jobboard-user',
        $role
    ));

    return implode(' ', $class);
}

function jb_account_get_count($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $counts = JB()->job->count($user_id);

    return isset($counts['publish']) ? $counts['publish'] : 0;
}

function jb_account_get_id(){
    global $jobboard_account;
    return isset($jobboard_account->ID) ? $jobboard_account->ID : 0;
}

function jb_account_get_type($slug = false){
    global $jobboard_account;

    if(is_jb_candidate($jobboard_account->ID)){
        return $slug ? 'candidate' : esc_html__('Candidate', 'jobboard');
    } elseif (is_jb_employer($jobboard_account->ID)){
        return $slug ? 'employer' : esc_html__('Employer', 'jobboard');
    }

    return esc_html__('User', 'jobboard');
}

function jb_account_get_display_name($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $user = get_user_by('ID', $user_id);
    return $user->display_name;
}

function jb_account_get_address($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'address-1', true);
}

function jb_account_get_city($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'user_city', true);
}

function jb_account_get_country($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'user_country', true);
}

function jb_account_get_location($user_id = '', $separator = ', '){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $address_keys = array(
        'user_country',
        'user_city',
        'address-1'
    );

    $address = array();

    foreach ($address_keys as $key) {
        if ($val = get_user_meta($user_id, $key, true)) {
            $address[] = $val;
        }
    }

    return implode($separator, $address);
}

function jb_account_get_phone($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'user_phone', true);
}

function jb_account_get_email($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $user = get_user_by('ID', $user_id);
    return $user->user_email;
}

function jb_account_get_website($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'url', true);
}

function jb_account_get_description($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_user_meta($user_id, 'description', true);
}

function jb_account_get_social($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    if(is_jb_employer($user_id)){
        $fields = jb_get_option('employer-social-fields');
    } elseif (is_jb_candidate($user_id)){
        $fields = jb_get_option('candidate-social-fields');
    }

    if(empty($fields)){
        return '';
    }

    $html = '<ul>';

    foreach ($fields as $field){

        if(!$val = get_user_meta($user_id, $field['id'], true)){
            continue;
        }

        $html .= '<li><a href="' . esc_url($val) . '" title="' . esc_attr($field['title']) . '"><i class="' . $field['class'] . '"></i></a></li>';
    }

    $html .= '</ul>';

    return $html;
}

function jb_account_get_permalink($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $user = get_user_by('ID', $user_id);

    if(is_jb_employer($user_id)){
        $slug = jb_get_option('profile-employer-slug', 'employers');
    } elseif (is_jb_candidate($user_id)) {
        $slug = jb_get_option('profile-candidate-slug', 'candidates');
    }

    if(get_option('permalink_structure')){
        return site_url("/{$slug}/{$user->user_login}");
    } else {
        return site_url("/?{$slug}={$user->user_login}");
    }
}

function jb_account_get_post_link($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_author_posts_url($user_id);
}

function jb_account_get_date($format = 'M Y', $user_id = ''){
    global $jobboard_account;

    if(isset($jobboard_account->user_registered)){
        return date($format, strtotime($jobboard_account->user_registered));
    }

    return '';
}

function jb_account_get_avatar($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    return get_avatar($user_id);
}

function jb_account_get_cover_image($user_id = '', $size = 'full'){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $attachment = get_user_meta($user_id, 'user_cover', true);
    if(!empty($attachment['id'])){
        return wp_get_attachment_image_url($attachment['id'], $size);
    }

    return false;
}

function jb_account_get_specialisms($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    if($ids = get_user_meta($user_id, 'job_specialisms', true)){
        return get_terms(array('taxonomy'=> 'jobboard-tax-specialisms', 'include' => $ids));
    } else {
        return false;
    }
}

function jb_account_get_search_query(){
    $search = '';

    if(isset($_REQUEST['search'])){
        $search = $_REQUEST['search'];
    }

    return $search;
}

function jb_account_get_current_name(){
    $user = wp_get_current_user();
    if(isset($user->display_name)){
        return $user->display_name;
    } else {
        return '';
    }
}

function jb_account_get_current_email(){
    $user = wp_get_current_user();
    if(isset($user->user_email)){
        return $user->user_email;
    } else {
        return '';
    }
}