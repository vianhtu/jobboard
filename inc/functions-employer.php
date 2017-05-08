<?php
/**
 * JobBoard Employer Functions
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

function jb_employer_navigation_args() {
    $endpoint_jobs     = jb_get_option('endpoint-jobs', 'jobs');
    $endpoint_profile  = jb_get_option('endpoint-profile', 'profile');
    $endpoint_new      = jb_get_option('endpoint-new', 'new');
    $navigation        = apply_filters( 'jobboard_employer_navigation_args', array(
        array(
            'id'        => 'dashboard',
            'endpoint'  => 'dashboard',
            'title'     => esc_html__( 'My Account', 'jobboard' )
        ),
        array(
            'id'        => 'jobs',
            'endpoint'  => $endpoint_jobs,
            'title'     => esc_html__( 'Application History', 'jobboard' )
        ),
        array(
            'id'        => 'profile',
            'endpoint'  => $endpoint_profile,
            'title'     => esc_html__( 'Manage Profile', 'jobboard' )
        ),
        array(
            'id'        => 'new',
            'endpoint'  => $endpoint_new,
            'title'     => esc_html__( 'Post New', 'jobboard' )
        )
    ));

    $navigation[] = array(
        'id'        => 'logout',
        'endpoint'  => 'logout',
        'title'     => esc_html__( 'Logout', 'jobboard' )
    );

    return $navigation;
}

function jb_employer_count_applied($user_id = '', $date = 30){
    $applied = JB()->employer->count_applied_since_days($user_id, $date);
    return apply_filters('jobboard_employer_applied_count', $applied);
}

function jb_employer_profile_custom_field(){
    $fields = apply_filters('jobboard_employer_profile_fields', jb_get_option('employer-custom-fields'));
    return jb_account_custom_fields_value('', $fields);
}

function jb_employer_job_custom_field(){
    $fields     = array(
        array(
            'id'         => 'post-heading',
            'title'      => esc_html__('Post A Job', 'jobboard' ),
            'type'       => 'heading',
            'heading'    => 'h3'
        ),
        array (
            'id'         => 'post_title',
            'title'      => esc_html__('Job Title *', 'jobboard' ),
            'subtitle'   => esc_html__('Enter your job title', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'type'       => 'text',
            'require'    => 1,
            'col'        => 6,
            'placeholder'=> esc_html__('Job Title *', 'jobboard' )
        ),
        array (
            'id'         => '_salary_min',
            'title'      => esc_html__('Min Salary *', 'jobboard' ),
            'subtitle'   => esc_html__('Enter min salary (number)', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'type'       => 'text',
            'input'      => 'number',
            'require'    => 1,
            'col'        => 6,
            'placeholder'=> esc_html__('Min Salary *', 'jobboard' )
        ),
        array (
            'id'         => '_salary_max',
            'title'      => esc_html__('Max Salary', 'jobboard' ),
            'subtitle'   => esc_html__('Enter max salary (number)', 'jobboard' ),
            'type'       => 'text',
            'input'      => 'number',
            'col'        => 6,
            'placeholder'=> esc_html__('Max Salary', 'jobboard' )
        ),
        array (
            'id'         => '_salary_currency',
            'title'      => esc_html__('Currency', 'jobboard' ),
            'subtitle'   => esc_html__('Select currency for salary', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'type'       => 'select',
            'col'        => 6,
            'require'    => 1,
            'value'      => jb_get_option('default-currency', 'USD'),
            'options'    => jb_get_currencies_options(),
        ),
        array (
            'id'         => '_salary_extra',
            'title'      => esc_html__('Bonus or Exception', 'jobboard' ),
            'subtitle'   => esc_html__('Enter your bonus, exception, condition...', 'jobboard' ),
            'type'       => 'text',
            'placeholder'=> esc_html__('+ Relocation Bonus', 'jobboard' )
        ),
        array (
            'id'         => 'types',
            'title'      => esc_html__('Contract Type *', 'jobboard' ),
            'subtitle'   => esc_html__('Select a job type', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'type'       => 'radio',
            'value'      => 2,
            'require'    => 1,
            'options'    => jb_get_type_options()
        ),
        array (
            'id'         => 'post_content',
            'title'      => esc_html__('Job Description *', 'jobboard' ),
            'subtitle'   => esc_html__('Enter your job content.', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'type'       => 'textarea',
            'require'    => 1,
            'placeholder'=> esc_html__('Job description *', 'jobboard' )
        ),
        array (
            'id'         => 'specialisms',
            'name'       => 'specialisms[]',
            'title'      => esc_html__('Specialisms & Skill', 'jobboard' ),
            'subtitle'   => esc_html__('Select specialisms and skill for job.', 'jobboard' ),
            'notice'     => esc_html__('is required !', 'jobboard'),
            'placeholder'=> esc_html__('Specialisms & Skill *', 'jobboard' ),
            'type'       => 'select',
            'multi'      => true,
            'col'        => 6,
            'require'    => 1,
            'options'    => jb_get_specialism_options(),
        ),
        array (
            'id'         => 'tags',
            'name'       => 'tags[]',
            'title'      => esc_html__('Tags', 'jobboard' ),
            'subtitle'   => esc_html__('Enter your job tags.', 'jobboard' ),
            'placeholder'=> esc_html__('Tags', 'jobboard' ),
            'type'       => 'tags',
            'col'        => 6,
            'options'    => array(),
        ),
        array(
            'id'         => 'featured-image',
            'title'      => esc_html__('Featured Image', 'jobboard' ),
            'type'       => 'media',
            'input'      => 'image',
            'types'      => 'jpg',
            'size'       => 1024
        ),
        array(
            'id'         => 'locations',
            'type'       => 'location',
            'title'      => esc_html__('Job Address', 'jobboard' ),
            'subtitle'   => esc_html__('Select job address.', 'jobboard' ),
            'taxonomy'   => 'jobboard-tax-locations',
            'options'    => array(
                array(
                    'id'            => 'country',
                    'placeholder'   => esc_html__('Country', 'jobboard' )
                ),
                array(
                    'id'            => 'city',
                    'placeholder'   => esc_html__('City', 'jobboard' )
                ),
                array(
                    'id'            => 'district',
                    'placeholder'   => esc_html__('District', 'jobboard' )
                ),
            )
        ),
        array (
            'id'         => '_address',
            'title'      => esc_html__('Complete Address', 'jobboard' ),
            'subtitle'   => esc_html__('Enter your job address.', 'jobboard' ),
            'type'       => 'textarea',
            'placeholder'=> esc_html__('Complete Address', 'jobboard' )
        )
    );

    return apply_filters('jobboard_add_job_fields', $fields);
}

function jb_employer_trending_specialisms(){
    global $jobboard_account;
    $user_id = isset($jobboard_account->ID) ? $jobboard_account->ID : '';
    $specialisms = JB()->employer->get_trending_taxonomies($user_id, 'jobboard-tax-specialisms', 5);

    if(empty($specialisms)){
        return;
    }

    echo '<ul>';

    foreach ($specialisms as $specialism){
        $term_url = get_term_link((int)$specialism->term_id, 'jobboard-tax-specialisms');
        if(is_wp_error($term_url)){
            continue;
        }
        echo '<li><a href="' . esc_url($term_url) . '">' . esc_html($specialism->name) . '</a></li>';
    }

    echo '<ul>';
}

function jb_employer_the_vacancies(){
    echo jb_employer_get_vacancies();
}

function jb_employer_get_vacancies($user_id = ''){
    global $jobboard_account;

    if(!$user_id && isset($jobboard_account->ID)) {
        $user_id = $jobboard_account->ID;
    }

    $vacancies = get_user_meta($user_id, 'job_vacancies', true);

    return $vacancies ? $vacancies : 0;
}