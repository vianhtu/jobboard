<?php
/**
 * JobBoard Template Hooks
 *
 * Action/filter hooks used for JobBoard functions/templates.
 *
 * @author 		FOX
 * @package 	JobBoard/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * footer
 */
add_action('wp_footer',                                 'jb_template_apply_form');
add_action('wp_footer',                                 'jb_template_notices');
add_action('wp_footer',                                 'jb_template_alert_modal');
add_action('wp_footer',                                 'jb_template_employer_jobs_modal');

/**
 * Notices
 */
add_action('jobboard_notices_content',                  'jb_template_notices_system', 10);
add_action('jobboard_notices_content',                  'jb_template_notices_blank', 20);

/**
 * Content Template Actions.
 */
add_action('jobboard_main_before_content',              'jb_template_content_wrapper', 5 );
add_action('jobboard_main_before_content',              'jb_template_breadcrumb', 10);
add_action('jobboard_main_before_content',              'jb_template_catalog_ordering', 20);
add_action('jobboard_main_after_content',               'jb_template_content_wrapper_end', 100 );

/**
 * Archive Template Actions.
 */
add_action('jobboard_archive_actions',                  'jb_template_catalog_showing', 10);
add_action('jobboard_archive_actions',                  'jb_template_catalog_orderby', 20);
add_action('jobboard_archive_actions',                  'jb_template_catalog_input', 50);

/**
 * Sidebar Template Actions.
 */
add_action('jobboard_sidebar',                          'jb_template_sidebar');
add_action('jobboard_sidebar_single',                   'jb_template_sidebar_single');
add_action('jobboard_sidebar_user',                     'jb_template_sidebar_profile');
add_action('jobboard_sidebar_users',                    'jb_template_sidebar_employers');
add_action('jobboard_sidebar_users',                    'jb_template_sidebar_candidates');

/**
 * Loop Template Actions.
 */
add_action('jobboard_loop_before',                      'jb_template_job_loop_start', 5 );
add_action('jobboard_loop_item_summary_before',         'jb_template_job_loop_thumbnail', 10 );
add_action('jobboard_loop_item_summary',                'jb_template_job_loop_summary_start', 5 );
add_action('jobboard_loop_item_summary',                'jb_template_job_loop_summary_title', 10 );
add_action('jobboard_loop_item_summary',                'jb_template_job_loop_summary_meta', 20 );
add_action('jobboard_loop_item_summary',                'jb_template_job_loop_summary_excerpt', 30 );
add_action('jobboard_loop_item_summary',                'jb_template_job_loop_summary_end', 100 );
add_action('jobboard_loop_item_summary_after',          'jb_template_job_loop_actions', 10 );
add_action('jobboard_loop_meta',                        'jb_template_job_loop_summary_type', 10);
add_action('jobboard_loop_meta',                        'jb_template_job_loop_summary_date', 20);
add_action('jobboard_loop_meta',                        'jb_template_job_loop_summary_author', 30);
add_action('jobboard_loop_meta',                        'jb_template_job_loop_summary_specialism', 40);
add_action('jobboard_loop_meta',                        'jb_template_job_loop_summary_location', 50);
add_action('jobboard_loop_actions',                     'jb_template_job_loop_actions_readmore', 10);
add_action('jobboard_loop_after',                       'jb_template_job_loop_end', 100 );
add_action('jobboard_loop_after',                       'jb_template_job_loop_pagination', 150);

/**
 * Single Templates Actions.
 */
add_action('jobboard_single_before_content',            'jb_template_content_wrapper', 5);
add_action('jobboard_single_before_content',            'jb_template_breadcrumb', 10);
add_action('jobboard_single_summary_before',            'jb_template_job_image');
add_action('jobboard_single_summary',                   'jb_template_job_summary_start', 5);
add_action('jobboard_single_summary',                   'jb_template_job_type', 10);
add_action('jobboard_single_summary',                   'jb_template_job_title', 20);
add_action('jobboard_single_summary',                   'jb_template_job_meta', 30);
add_action('jobboard_single_summary',                   'jb_template_job_actions', 40);
add_action('jobboard_single_summary',                   'jb_template_job_summary_end', 50);
add_action('jobboard_single_summary_after',             'jb_template_job_content');
add_action('jobboard_single_meta',                      'jb_template_job_meta_author', 10);
add_action('jobboard_single_meta',                      'jb_template_job_meta_date', 20);
add_action('jobboard_single_meta',                      'jb_template_job_meta_location', 30);
add_action('jobboard_single_actions',                   'jb_template_job_actions_apply');
add_action('jobboard_single_after_content',             'jb_template_content_wrapper_end', 100);

/**
 * Users Templates Actions.
 */
add_action('jobboard_users_before_content',             'jb_template_content_wrapper', 5 );
add_action('jobboard_users_before_content',             'jb_template_breadcrumb', 10);
add_action('jobboard_users_before_content',             'jb_template_catalog_ordering', 20);
add_action('jobboard_users_loop_before',                'jb_template_user_loop_start', 5 );
add_action('jobboard_users_loop_summary_before',        'jb_template_user_loop_thumbnail', 10);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_start', 10);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_title', 20);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_vacancies', 30);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_specialism', 40);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_location', 50);
add_action('jobboard_users_loop_employer_summary',      'jb_template_user_loop_summary_end', 100);
add_action('jobboard_users_loop_summary_after',         'jb_template_user_loop_actions', 10);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_start', 10);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_title', 20);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_salary', 30);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_specialism', 40);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_location', 50);
add_action('jobboard_users_loop_candidate_summary',     'jb_template_user_loop_summary_end', 100);
add_action('jobboard_users_loop_employer_actions',      'jb_template_user_loop_actions_view');
add_action('jobboard_users_loop_candidate_actions',     'jb_template_user_loop_actions_cv');
add_action('jobboard_users_loop_after',                 'jb_template_user_loop_end', 100 );
add_action('jobboard_users_loop_after',                 'jb_template_user_loop_pagination', 150);
add_action('jobboard_users_after_content',              'jb_template_content_wrapper_end', 100);

/**
 * User Templates Actions.
 */
add_action('jobboard_user_before_content',              'jb_template_content_wrapper', 5 );
add_action('jobboard_user_before_content',              'jb_template_breadcrumb');
add_action('jobboard_user_before_employer_content',     'jb_template_user_employer_summary');
add_action('jobboard_user_before_candidate_content',    'jb_template_user_candidate_summary');
add_action('jobboard_user_employer_summary_actions',    'jb_template_user_summary_social', 10);
add_action('jobboard_user_employer_summary_actions',    'jb_template_user_employer_summary_button', 20);
add_action('jobboard_user_employer_content',            'jb_template_user_content', 10);
add_action('jobboard_user_employer_content',            'jb_template_user_jobs', 20);
add_action('jobboard_user_employer_content_actions',    'jb_template_user_contact', 10);
add_action('jobboard_user_employer_content_actions',    'jb_template_user_employer_summary_button', 20);
add_action('jobboard_user_candidate_summary_actions',   'jb_template_user_summary_social', 10);
add_action('jobboard_user_candidate_summary_actions',   'jb_template_user_candidate_summary_button', 20);
add_action('jobboard_user_candidate_content',           'jb_template_user_content');
add_action('jobboard_user_candidate_content_actions',   'jb_template_user_contact', 10);
add_action('jobboard_user_candidate_content_actions',   'jb_template_user_candidate_summary_button', 20);
add_action('jobboard_user_after_content',               'jb_template_content_wrapper_end', 100 );

/**
 * Dashboard Templates Actions.
 */
add_action('jobboard_dashboard_candidate',              'jb_template_account' );
add_action('jobboard_dashboard_candidate_content',      'jb_template_account_content' );
add_action('jobboard_dashboard_candidate_navigation',   'jb_template_candidate_navigation' );
add_action('jobboard_dashboard_employer',               'jb_template_account' );
add_action('jobboard_dashboard_employer_content',       'jb_template_account_content' );
add_action('jobboard_dashboard_employer_navigation',    'jb_template_employer_navigation' );
add_action('jobboard_dashboard_other',                  'jb_template_account_other' );
add_action('jobboard_dashboard_not_logged',             'jb_template_login_from' );

add_action('jobboard_endpoint_candidate_page',          'jb_template_candidate_account', 10);
add_action('jobboard_endpoint_candidate_page',          'jb_template_candidate_account_applied', 20);
add_action('jobboard_endpoint_candidate_applied',       'jb_template_candidate_applied', 10);
add_action('jobboard_endpoint_candidate_applied',       'jb_template_candidate_applied_pagination', 20);
add_action('jobboard_endpoint_candidate_profile',       'jb_template_candidate_profile' );
add_action('jobboard_endpoint_employer_page',           'jb_template_employer_account', 10);
add_action('jobboard_endpoint_employer_page',           'jb_template_employer_account_applied', 20);
add_action('jobboard_endpoint_employer_jobs',           'jb_template_employer_jobs', 10);
add_action('jobboard_endpoint_employer_jobs',           'jb_template_employer_jobs_pagination', 20);
add_action('jobboard_endpoint_employer_new',            'jb_template_employer_job_new', 10);
add_action('jobboard_endpoint_employer_profile',        'jb_template_employer_profile' );

add_action('jobboard_table_applied_title',              'jb_template_job_loop_summary_title', 10);
add_action('jobboard_table_applied_title',              'jb_template_candidate_applied_locations', 20);
add_action('jobboard_table_applied_type',               'jb_template_candidate_applied_salary', 10);
add_action('jobboard_table_applied_type',               'jb_template_candidate_applied_type', 20);
add_action('jobboard_table_applied_date',               'jb_template_candidate_applied_date');
add_action('jobboard_table_applied_status',             'jb_template_candidate_applied_status');
add_action('jobboard_table_jobs_title',                 'jb_template_job_loop_summary_title', 10);
add_action('jobboard_table_jobs_title',                 'jb_template_job_loop_summary_date', 20);
add_action('jobboard_table_jobs_actions',               'jb_template_employer_jobs_actions');
add_action('jobboard_table_jobs_applications',          'jb_template_employer_jobs_application');
add_action('jobboard_table_jobs_status',                'jb_template_employer_jobs_status');

add_action('jobboard_form_profile',                     'jb_template_form_dynamic', 10 );
add_action('jobboard_form_profile',                     'jb_template_account_profile_actions', 20 );
add_action('jobboard_form_post',                        'jb_template_form_dynamic', 10 );
add_action('jobboard_form_post',                        'jb_template_employer_job_new_actions', 20 );
add_action('jobboard_form_fields',                      'jb_template_form_dynamic_field' );

add_action('jobboard_modal_applications',               'jb_template_employer_jobs_modal_start', 5);
add_action('jobboard_modal_applications',               'jb_template_employer_jobs_modal_search', 10);
add_action('jobboard_modal_applications',               'jb_template_employer_jobs_modal_table', 20);
add_action('jobboard_modal_applications',               'jb_template_employer_jobs_modal_end', 100);