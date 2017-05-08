<?php
/**
 * JobBoard Template
 *
 * Functions for the templating system.
 *
 * @author   FOX
 * @category Core
 * @package  JobBoard/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/** Global ******************************************************************/
function jb_template_content_wrapper() {
    jb_get_template( 'global/wrapper-start.php' );
}

function jb_template_content_wrapper_end() {
    jb_get_template( 'global/wrapper-end.php' );
}

function jb_template_breadcrumb($args = array()){
    $args = wp_parse_args( $args, apply_filters( 'jb/template/breadcrumb/args', array(
        'delimiter'   => '&nbsp;&#47;&nbsp;',
        'wrap_before' => '<nav class="jobboard-breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>',
        'wrap_after'  => '</nav>',
        'before'      => '',
        'after'       => '',
        'home'        => _x( 'Home', 'breadcrumb', 'jobboard' )
    ) ) );

    $breadcrumbs = new JobBoard_Breadcrumb();

    if ( ! empty( $args['home'] ) ) {
        $breadcrumbs->add_crumb( $args['home'], apply_filters( 'jb/template/breadcrumb/home/url', home_url('/') ) );
    }

    $args['breadcrumb'] = $breadcrumbs->generate();

    jb_get_template( 'global/breadcrumb.php', $args );
}

function jb_template_catalog_ordering() {
    jb_get_template( 'archive/actions.php' );
}

function jb_template_catalog_orderby() {
    $orderby                 = isset( $_GET['orderby'] ) ? sanitize_text_field($_GET['orderby']) : '';
    $catalog_orderby_options = apply_filters( 'jobboard_catalog_orderby_args', array(
        'name'      => esc_html__( 'Sort by name', 'jobboard' ),
        'applied'   => esc_html__( 'Sort by applied', 'jobboard' ),
        'date'      => esc_html__( 'Sort by date posted', 'jobboard' ),
        'salary'    => esc_html__( 'Sort by salary', 'jobboard' )
    ) );

    jb_get_template( 'archive/actions/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby));
}

function jb_template_catalog_showing() {
    $showing = array(
        'paged'     => 0,
        'current'   => 0,
        'all'       => 0
    );

    jb_get_template( 'archive/actions/showing.php', array('showing' => apply_filters('jobboard_catalog_showing_args', $showing)));
}

function jb_template_catalog_input(){

    /* post type. */
    $input['post_type']     = '<input type="hidden" name="post_type" value="jobboard-post-jobs" />';

    /* tax filters. */
    if(is_jb_taxonomy()){

        $current_term = get_queried_object();

        unset($input['post_type']);

        $input['type']      = '<input type="hidden" name="'.$current_term->taxonomy.'" value="'.$current_term->slug.'" />';

    } elseif (is_author()){

        global $author;

        unset($input['post_type']);

        $input['author']    = '<input type="hidden" name="author" value="'.$author.'" />';

    } elseif (is_jb_search() && !empty($_GET['s'])){

        $input['s']         = '<input type="hidden" name="s" value="'.esc_attr($_GET['s']).'" />';
    }

    /* specialism filters */
    if(!empty($_GET['specialism-filters'])){
        foreach ($_GET['specialism-filters'] as $specialism) {
            $input['specialism-' . $specialism] = '<input type="hidden" name="specialism-filters[]" value="' . esc_attr($specialism) . '" />';
        }
    }

    /* date filters. */
    if(!empty($_GET['date-filters'])){
        $input['date-filters']    = '<input type="hidden" name="date-filters" value="'.esc_attr($_GET['date-filters']).'" />';
    }

    echo implode('', apply_filters('jobboard_catalog_input_args', $input));
}

function jb_template_sidebar() {
    jb_get_template( 'global/sidebar.php' );
}

function jb_template_sidebar_single(){
    jb_get_template( 'global/sidebar-single.php' );
}

function jb_template_sidebar_profile(){
    jb_get_template( 'global/sidebar-profile.php' );
}

function jb_template_sidebar_employers(){
    jb_get_template( 'global/sidebar-employers.php' );
}

function jb_template_sidebar_candidates(){
    jb_get_template( 'global/sidebar-candidates.php' );
}

/** Widget ****************************************************************/
function jb_template_search_form() {
    $search = apply_filters('jobboard_search_form_args', array(
        'name'          => 's',
        'placeholder'   => esc_attr_x( 'Search Jobs&hellip;', 'placeholder', 'jobboard' ),
        'button'        => esc_attr_x( 'Search', 'submit button', 'jobboard' ),
        'value'         => get_search_query(),
        'type'          => 'post_type',
        'type_value'    => 'jobboard-post-jobs'
    ));
    jb_get_template( 'search-form.php', array('search' => $search));
}

function jb_template_apply_form(){

    if(!is_jb_job()){
        return;
    }

    jb_get_template('modal/modal-start.php', array('modal' => 'jobboard-modal-apply'));

    if(!is_user_logged_in()) {
        jb_get_template('apply/login.php');
    } elseif (is_jb_candidate()){
        $candidate              = wp_get_current_user();
        $candidate->covering    = get_user_meta($candidate->ID, 'covering', true);
        $fields                 = jb_job_apply_fields();
        jb_get_template('apply/apply.php', array('candidate' => $candidate, 'fields' => $fields));
    } else {
        $user                   = wp_get_current_user();
        jb_get_template('apply/other.php', array('user' => $user));
    }

    jb_get_template('modal/modal-end.php');
}

function jb_template_alert_modal(){
    jb_get_template('modal/modal-start.php', array('modal' => 'jobboard-modal-alert'));
    jb_get_template('notices/alert.php');
    jb_get_template('modal/modal-end.php');
}

function jb_template_notices(){
    jb_get_template('notices/notices.php');
}

function jb_template_notices_system(){
    if(count($notices = JB()->session->get('jb_notices', array())) == 0){
        return;
    }

    foreach ($notices as $type => $messages ) {
        foreach ($messages as $message) {
            jb_get_template("notices/notices/{$type}.php", array('message' => $message));
        }
    }

    jb_notices_clear();
}

function jb_template_notices_blank(){
    jb_get_template('notices/notices/blank.php');
}

/** Loop ******************************************************************/
function jb_template_job_loop_start(){
    jb_get_template('loop/loop-start.php');
}

function jb_template_job_loop_thumbnail(){
    jb_get_template('loop/thumbnail.php');
}

function jb_template_job_loop_summary_start(){
    jb_get_template('loop/summary-start.php');
}

function jb_template_job_loop_summary_title(){
    jb_get_template('loop/title.php');
}

function jb_template_job_loop_summary_salary(){
    jb_get_template('loop/salary.php');
}

function jb_template_job_loop_summary_meta(){
    jb_get_template('loop/meta.php');
}

function jb_template_job_loop_summary_type(){
    jb_get_template('loop/type.php');
}

function jb_template_job_loop_summary_date(){
    global $post;

    $posted = array(
        'date'      => get_the_date( 'c' ),
        'posted'    => jb_get_timeago(get_post_time( 'G', true, $post->ID ))
    );

    jb_get_template('loop/posted.php', $posted);
}

/**
 * Output job loop item author.
 *
 * @subpackage	job
 */
function jb_template_job_loop_summary_author(){
    jb_get_template('loop/author.php', array('author_posts' => get_author_posts_url(get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ))));
}

/**
 * Output job loop item specialism.
 *
 * @subpackage	job
 */
function jb_template_job_loop_summary_specialism(){
    jb_get_template('loop/specialism.php');
}

/**
 * Output job loop item location.
 *
 * @subpackage	job
 */
function jb_template_job_loop_summary_location(){
    jb_get_template('loop/location.php');
}

/**
 * Output job loop item excerpt.
 *
 * @subpackage	job
 */
function jb_template_job_loop_summary_excerpt(){
    jb_get_template('loop/excerpt.php');
}

/**
 * Output job loop item end.
 *
 * @subpackage	job
 */
function jb_template_job_loop_summary_end(){
    jb_get_template('loop/summary-end.php');
}

/**
 * Output job loop item actions.
 *
 * @subpackage	job
 */
function jb_template_job_loop_actions(){
    jb_get_template('loop/actions.php');
}

/**
 * Output job loop item actions type.
 *
 * @subpackage	job
 */
function jb_template_job_loop_actions_readmore(){
    jb_get_template('loop/readmore.php');
}

function jb_template_job_loop_end(){
    jb_get_template('loop/loop-end.php');
}

function jb_template_job_loop_pagination(){
    jb_get_template( 'global/pagination.php' );
}

/** Single ********************************************************************/
function jb_template_job_summary_start(){
    jb_get_template('single/summary-start.php');
}

function jb_template_job_summary_end(){
    jb_get_template('single/summary-end.php');
}

function jb_template_job_image(){
    $class = apply_filters('jb/template/job/image/class', array('col-xs-12', 'col-sm-6', 'col-md-5', 'col-lg-5'));
    jb_get_template('single/thumbnail.php', array('class' => $class));
}

function jb_template_job_type(){
    jb_get_template('single/type.php');
}

function jb_template_job_title(){
    jb_get_template('single/title.php');
}

function jb_template_job_meta(){
    jb_get_template('single/meta.php');
}

function jb_template_job_meta_author(){
    jb_get_template('single/author.php', array('author_posts' => get_author_posts_url(get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ))));
}

function jb_template_job_meta_date(){
    jb_get_template('single/date.php');
}

function jb_template_job_meta_location(){
    jb_get_template('single/location.php');
}

function jb_template_job_actions(){
    jb_get_template('single/actions.php');
}

function jb_template_job_actions_apply(){
    if(!jb_account_applied()){
        jb_get_template('single/apply.php');
    } else {
        jb_get_template('single/applied.php', array('applied_page' => jb_page_endpoint_url('applied', jb_page_permalink('dashboard'))));
    }
}

function jb_template_job_content(){
    jb_get_template('single/description.php');
}

/** Users ****************************************************************/
function jb_template_user_loop_start(){
    jb_get_template('loop/loop-start.php');
}

function jb_template_user_loop_thumbnail(){
    jb_get_template('users/thumbnail.php');
}

function jb_template_user_loop_summary_start(){
    jb_get_template('users/summary-start.php');
}

function jb_template_user_loop_summary_title(){
    jb_get_template('users/title.php');
}

function jb_template_user_loop_summary_vacancies(){
    jb_get_template('users/vacancies.php', array('vacancies' => jb_employer_get_vacancies()));
}

function jb_template_user_loop_summary_salary(){
    jb_get_template('users/salary.php', array('salary' => jb_candidate_get_salary()));
}

function jb_template_user_loop_summary_specialism(){
    jb_get_template('users/specialism.php', array('specialisms' => jb_account_get_specialisms()));
}

function jb_template_user_loop_summary_location(){
    jb_get_template('users/location.php', array('location' => jb_account_get_location()));
}

function jb_template_user_loop_summary_end(){
    jb_get_template('users/summary-end.php');
}

function jb_template_user_loop_actions(){
    jb_get_template('users/actions.php');
}

function jb_template_user_loop_actions_view(){
    jb_get_template('users/listings.php');
}

function jb_template_user_loop_actions_cv(){
    jb_get_template('users/cv.php', array('cv' => jb_candidate_get_cv_url()));
}

function jb_template_user_loop_end(){
    jb_get_template('loop/loop-end.php');
}

function jb_template_user_loop_pagination(){
    jb_get_template( 'global/pagination.php' );
}
/** User *****************************************************************/
function jb_template_user_employer_summary(){
    $summaries  = apply_filters('jobboard_user_employer_summary_args', array(
        jb_employer_get_vacancies() => esc_html__('Vacancies: %s'),
        jb_account_get_count()      => esc_html__('Current Jobs : %s'),
        jb_account_get_date()       => esc_html__('Established: %s'),
        jb_account_get_email()      => array(
            'before'    => '<a href="mailto:%s">',
            'title' => esc_html__('Email: %s'),
            'after'     => '</a>'
        ),
        jb_account_get_website()    => array(
            'before'    => '<a href="%s">',
            'title'     => esc_html__('Visit Website: %s'),
            'after'     => '</a>'
        )
    ));

    jb_get_template('user/summary.php', array('summaries' => $summaries));
}

function jb_template_user_employer_summary_button(){
    jb_get_template('user/listings.php');
}

function jb_template_user_candidate_summary(){
    $summaries = apply_filters('jobboard_user_candidate_summary_args', array(
        jb_candidate_get_salary()   => esc_html__('Minimum Salary: %s'),
        jb_account_get_date()       => esc_html__('Established: %s'),
        jb_account_get_email()      => array(
            'before'    => '<a href="mailto:%s">',
            'title'     => esc_html__('Email: %s'),
            'after'     => '</a>'
        ),
        jb_account_get_website()    => array(
            'before'    => '<a href="%s">',
            'title'     => esc_html__('Visit Website: %s'),
            'after'     => '</a>'
        ),
    ));

    jb_get_template('user/summary.php', array('summaries' => $summaries));
}

function jb_template_user_candidate_summary_button(){
    jb_get_template('user/cv.php');
}

function jb_template_user_summary_social(){
    jb_get_template('user/social.php');
}

function jb_template_user_content(){
    jb_get_template('user/description.php');
}

function jb_template_user_jobs(){
    global $jobboard_account;

    $jobs = new WP_Query(array(
        'post_type'     => 'jobboard-post-jobs',
        'post_status'   => 'publish',
        'author' => $jobboard_account->ID,
        'posts_per_page' => 4
    ));

    jb_get_template('user/recent.php', array('jobs' => $jobs));

    wp_reset_query();
}

function jb_template_user_contact(){
    jb_get_template('user/contact.php');
}

/** Form *****************************************************************/
function jb_template_form_dynamic($fields){

    if(empty($fields)){
        return;
    }

    jb_get_template('fields/fields.php', array('fields' => $fields));
}

function jb_template_form_dynamic_field($fields){

    $GLOBALS['row'] = array(
        'start' => true,
        'end'   => true,
        'col'   => 0,
        'count' => count($fields),
        'row'   => 1,
    );

    add_action('jobboard_before_field', 'jb_template_form_dynamic_row_start', 10);
    add_action('jobboard_before_field', 'jb_template_form_dynamic_field_before', 50);
    add_action('jobboard_after_field',  'jb_template_form_dynamic_row_end', 100);
    add_action('jobboard_after_field',  'jb_template_form_dynamic_field_after', 50);

    $validate = JB()->session->get( 'validate', array() );

    foreach ($fields as $field){
        $field       = jb_parse_custom_fields($field);
        $class       = array('field', 'col-xs-12', 'col-sm-12');
        $class[]     = "col-md-".$field['col'];
        $class[]     = "field-" . $field['type'];

        if($field['require']){
            $class[] = 'field-validate';
        }

        if(isset($field['multi']) && $field['multi'] && $field['name'] === $field['id']){
            $field['name'] = $field['id'] . '[]';
        }

        if(in_array($field['id'], $validate)){
            $class[]           = 'field-validated';
            $field['subtitle'] = $field['notice'];
        }

        $field['class'] = apply_filters("jobboard_field_{$field['type']}_class", $class);
        $field['class'] = implode(' ', $field['class']);
        $template_path  = apply_filters("jobboard_field_{$field['type']}_template_part", '');
        $default_path   = apply_filters("jobboard_field_{$field['type']}_default_path", '');
        $field          = apply_filters("jobboard_field_{$field['type']}_args", $field);

        do_action('jobboard_before_field', $field);

        jb_get_template('fields/fields/' . $field['type'] . '.php', $field, $template_path, $default_path);

        do_action('jobboard_after_field', $field);
    }
}

function jb_template_form_dynamic_row_start($field){
    global $row;

    $_col = $field['col'] + $row['col'];

    if($_col == 12 && $row['col'] == 0){
        $row['start']   = true;
        $row['end']     = true;
    } elseif ($_col == 12 && $row['col'] > 0){
        $row['start']   = false;
        $row['end']     = true;
    } elseif ($_col < 12 && $row['col'] == 0){
        $row['col']     = $_col;
        $row['start']   = true;
        $row['end']     = $row['row'] == $row['count'] ? true : false;
    } elseif ($_col < 12 && $row['col'] > 0){
        $row['col']     = $_col;
        $row['start']   = false;
        $row['end']     = false;
    }

    if($row['start']) {
        jb_get_template('fields/row-start.php');
    }

    $row['row']++;
}

function jb_template_form_dynamic_row_end(){
    global $row;

    if($row['end']){
        jb_get_template('fields/row-end.php');
        $row['col'] = 0;
    }
}

function jb_template_form_dynamic_field_before($field){

    if($field['type'] == 'heading'){
        return;
    }

    jb_get_template('fields/field-before.php', $field);
}

function jb_template_form_dynamic_field_after($field){

    if($field['type'] == 'heading'){
        return;
    }

    jb_get_template('fields/field-after.php', $field);
}

function jb_template_field_cv(){
    $field = array(
        'id'    => 'cv',
        'title' => esc_html__('CV *', 'jobboard'),
        'value' => get_user_meta(get_current_user_id(), 'cv', true),
        'class' => ''
    );

    $class = array('jb-field', 'jb-field-media');

    jb_get_template('fields/fields/media.php', array('field' => $field, 'class'=> $class));
}

function jb_template_login_from($args = array()){

    $dashboard = '';

    if(is_jb_employer_dashboard()){
        $dashboard = 'employer';
    } elseif (is_jb_candidate_dashboard()){
        $dashboard = 'candidate';
    }

    $defaults = array(
        'form_id' => 'form-' . uniqid(),
        'label_username' => esc_html__( 'Username or Email Address', 'jobboard' ),
        'label_password' => esc_html__( 'Password', 'jobboard'),
        'label_remember' => esc_html__( 'Remember Me', 'jobboard'),
        'label_log_in'   => esc_html__( 'Login', 'jobboard'),
        'value_username' => '',
        'value_remember' => false,
        'redirect_to'    => jb_get_current_url(),
        'dashboard'      => $dashboard,
    );

    $args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

    jb_get_template('global/login-form.php', array('args' => $args));
}

/** Dashboard *****************************************************************/
function jb_template_account() {
    jb_get_template( "dashboard/account.php");
}

function jb_template_account_other(){
    jb_get_template( 'dashboard/global/other.php', array('user' => wp_get_current_user()));
}

function jb_template_account_content() {
    global $wp, $jobboard;

    /** Permalink settings off. */
    if(isset($wp->query_vars['page_id']) && count($wp->query_vars) == 1){
        do_action( "jobboard_endpoint_{$jobboard->account}_page", $wp->query_vars['page_id'] );
        return;
    }

    /* Add endpoint action. */
    foreach ( $wp->query_vars as $key => $value ) {
        // Ignore pagename param.
        if ( 'pagename' === $key ) {
            continue;
        }

        if ( has_action( "jobboard_endpoint_{$jobboard->account}_{$key}" ) ) {
            do_action( "jobboard_endpoint_{$jobboard->account}_{$key}", $value );
            return;
        }
    }
}

function jb_template_account_profile_actions(){
    jb_get_template('dashboard/global/profile-actions.php');
}

function jb_template_candidate_navigation(){
    $navigation = jb_candidate_navigation_args();

    if(!$navigation){
        return;
    }

    jb_get_template('dashboard/global/navigation.php', array('navigation' => $navigation, 'permalink' => jb_page_permalink('dashboard')));
}


function jb_template_candidate_account(){

    $fields = jb_candidate_profile_custom_field();

    if(!$fields){
        return;
    }

    jb_get_template('dashboard/global/account.php', array('fields' => $fields));
}

function jb_template_candidate_account_applied(){
    jb_get_template('dashboard/candidate/account-applied.php');
}


function jb_template_candidate_applied(){
    global $wp_query;

    $columns = apply_filters('jobboard_table_applied_columns', array(
        'title'     => esc_html__('Job Title', 'jobboard'),
        'type'      => esc_html__('Type', 'jobboard'),
        'date'      => esc_html__('Date Applied', 'jobboard'),
        'status'    => esc_html__('Status', 'jobboard'),
    ));

    $wp_query = JB()->candidate->get_applied();

    jb_get_template('dashboard/global/table.php', array('jobs' => $wp_query, 'table' => 'applied', 'columns' => $columns));
}

function jb_template_candidate_applied_locations(){
    jb_get_template('loop/location.php');
}

function jb_template_candidate_applied_salary(){
    jb_get_template('loop/salary.php');
}

function jb_template_candidate_applied_type(){
    jb_get_template('loop/type.php');
}

function jb_template_candidate_applied_date(){
    global $post;

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    $attr   = array(
        'date'  => mysql2date($date_format . ' ' . $time_format, $post->app_date),
        'title' => mysql2date($date_format, $post->app_date)
    );

    jb_get_template('dashboard/loop/date.php', $attr);
}

function jb_template_candidate_applied_status(){
    $status = jb_job_apply_status();
    $title  = jb_candidate_applied_status($status);

    jb_get_template('dashboard/loop/status.php', array('status' => $status, 'title' => $title));
}

function jb_template_candidate_applied_pagination(){
    $base = jb_page_endpoint_base_pagination('applied', jb_page_permalink('dashboard'));

    jb_get_template( 'global/pagination.php', array('base' => $base));

    jb_candidate_reset_applied_count();
    wp_reset_query();
}

function jb_template_candidate_profile(){
    $fields = jb_candidate_profile_custom_field();
    jb_get_template('dashboard/global/profile.php', array('fields' => $fields));
}

function jb_template_employer_navigation(){
    $navigation = jb_employer_navigation_args();

    if(!$navigation){
        return;
    }

    jb_get_template('dashboard/global/navigation.php', array('navigation' => $navigation, 'permalink' => jb_page_permalink('dashboard')));
}

function jb_template_employer_account(){
    $fields = jb_employer_profile_custom_field();

    if(!$fields){
        return;
    }

    jb_get_template('dashboard/global/account.php', array('fields' => $fields));
}

function jb_template_employer_account_applied(){
    jb_get_template('dashboard/employer/account-applied.php');
}

function jb_template_employer_jobs(){
    global $wp_query;

    $columns = apply_filters('jobboard_table_jobs_columns', array(
        'title'         => esc_html__('Your Jobs', 'jobboard'),
        'applications'  => esc_html__('Applications', 'jobboard'),
        'status'        => esc_html__('Status', 'jobboard'),
        'actions'       => esc_html__('Actions', 'jobboard'),
    ));

    $wp_query = JB()->employer->get_jobs();

    jb_get_template('dashboard/global/table.php', array('jobs' => $wp_query, 'table' => 'jobs', 'columns' => $columns));
}

function jb_template_employer_jobs_actions(){
    global $post;

    $edit       = jb_page_endpoint_url('new', add_query_arg( 'post_id', $post->ID, jb_page_permalink('dashboard')));
    $actions    = apply_filters( 'jobboard_template_jobs_actions' , array(
        array(
            'id'        => 'edit',
            'icon'      => 'fa fa-pencil',
            'title'     => esc_html__('Edit', 'jobboard'),
            'attribute' => array(
                'data-url' => $edit
            )
        ),
        array(
            'id'        => 'delete',
            'icon'      => 'fa fa-times',
            'title'     => esc_html__('Delete', 'jobboard'),
            'attribute' => array(
                'data-id'       => $post->ID,
                'data-confirm'  => esc_html__('Do you want to delete', 'jobboard'),
                'data-title'    => $post->post_title
            )
        )
    ));

    jb_get_template('dashboard/loop/actions.php', array('actions' => $actions));
}

function jb_template_employer_jobs_application(){
    $number = JB()->employer->count_applied('', array('approved', 'reject', 'applied'));
    jb_get_template('dashboard/loop/application.php', array('number' => $number));
}

function jb_template_employer_jobs_candidates($applications){

    if($applications){
        jb_get_template('dashboard/employer/applications.php', array('applications' => $applications));
    } else {
        jb_get_template('dashboard/employer/applications-not-found.php');
    }
}

function jb_template_employer_jobs_modal(){

    if(!is_jb_endpoint_url('jobs')){
        return;
    }

    do_action('jobboard_modal_applications');
}

function jb_template_employer_jobs_modal_start(){
    jb_get_template('modal/modal-start.php', array('modal' => 'applications'));
}

function jb_template_employer_jobs_modal_search(){
    jb_get_template('dashboard/employer/applications-filter.php');
}

function jb_template_employer_jobs_modal_table(){
    $columns = apply_filters('jobboard_table_jobs_columns', array(
        'candidates'    => esc_html__('Applications', 'jobboard'),
        'cv'            => esc_html__('Download CV', 'jobboard'),
        'status'        => esc_html__('Status', 'jobboard'),
        'actions'       => esc_html__('Actions', 'jobboard'),
    ));

    jb_get_template('dashboard/global/table.php', array('jobs' => null, 'table' => 'applications', 'columns' => $columns));
}

function jb_template_employer_jobs_modal_end(){
    jb_get_template('modal/modal-end.php');
}

function jb_template_employer_jobs_status(){
    global $post;

    $status = $post->post_status;
    $title  = jb_job_status($status);

    jb_get_template('dashboard/loop/status.php', array('status' => $status, 'title' => $title));
}

function jb_template_employer_jobs_pagination(){

    $base = jb_page_endpoint_base_pagination('jobs', jb_page_permalink('dashboard'));

    jb_get_template( 'global/pagination.php', array('base' => $base));

    wp_reset_query();
}

function jb_template_employer_application_status($status){
    $status = array(
        'status' => $status,
        'title'  => jb_candidate_applied_status($status)
    );

    jb_get_template('dashboard/loop/status.php', $status);
}

function jb_template_employer_application_actions($id){
    $actions    = apply_filters( 'jobboard_template_application_actions' , array(
        array(
            'id'        => 'approve',
            'icon'      => 'fa fa-check',
            'title'     => esc_html__('Approve', 'jobboard'),
            'attribute' => array(
                'data-id'       => $id,
            )
        ),
        array(
            'id'        => 'reject',
            'icon'      => 'fa fa-times',
            'title'     => esc_html__('Reject', 'jobboard'),
            'attribute' => array(
                'data-id'       => $id,
            )
        )
    ));

    jb_get_template('dashboard/loop/actions.php', array('actions' => $actions));
}

function jb_template_employer_job_new(){
    $fields = jb_employer_job_custom_field();
    jb_get_template('dashboard/employer/add.php', array('fields' => $fields));
}

function jb_template_employer_job_new_actions(){
    if(!empty($_GET['post_id']) && get_post(sanitize_text_field($_GET['post_id']))) {
        jb_get_template('dashboard/employer/edit-actions.php');
    } else {
        jb_get_template('dashboard/employer/add-actions.php');
    }
}

function jb_template_employer_profile(){
    $fields = jb_employer_profile_custom_field();
    jb_get_template('dashboard/global/profile.php', array('fields' => $fields));
}