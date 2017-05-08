<?php
/**
 * JobBoard Admin.
 *
 * @class 		JobBoard_Admin
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Admin')) :

    class JobBoard_Admin{

        function __construct()
        {
            add_action('init', array($this, 'admin_settings'));
            add_action("redux/options/jobboard_options/saved", array($this, 'saving_permalink'));
            add_filter('redux/jobboard_options/field/custom/candidate-custom-fields/types', array($this, 'custom_remove_fields'));
            add_filter('redux/jobboard_options/field/custom/employer-custom-fields/types', array($this, 'custom_remove_fields'));
            add_filter('redux/custom/jobboard_options/settings', array($this, 'custom_setting'));
            add_filter('redux/custom/jobboard_options/settings/text', array($this, 'custom_setting_text'));
            add_filter('redux/custom/jobboard_options/settings/media', array($this, 'custom_setting_media'));

            add_filter('jobboard_candidate_profile_fields', array($this, 'fields_candidate_social'), 10);
            add_filter('jobboard_candidate_profile_fields', array($this, 'fields_change_password'), 50);
            add_filter('jobboard_employer_profile_fields', array($this, 'fields_employer_social'), 10);
            add_filter('jobboard_employer_profile_fields', array($this, 'fields_change_password'), 50);
            add_filter('jobboard_profile_custom_fields', array($this, 'fields_employer_social'));
            add_filter('jobboard_profile_custom_fields', array($this, 'fields_change_password'));
        }

        function admin_settings(){
            if(!class_exists('Redux'))
                return;

            $redux = new Redux();

            $redux::setArgs('jobboard_options', $this->args());
            $redux::setSections('jobboard_options', $this->sections());
        }

        function saving_permalink(){

            JB()->query->init_query_vars();
            JB()->query->add_endpoints();

            flush_rewrite_rules();
        }

        function args(){

            if(!function_exists('get_plugin_data')){
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            }

            $plugin = get_plugin_data(JB()->file, array('Name' => 'Plugin Name', 'Version' => 'Version'));

            $args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'             => 'jobboard_options',
                // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'         => $plugin['Name'],
                // Name that appears at the top of your panel
                'display_version'      => $plugin['Version'],
                // Version that appears at the top of your panel
                'menu_type'            => 'submenu',
                //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'       => false,
                // Show the sections below the admin menu item or not
                'menu_title'           => __( 'Settings', 'jobboard' ),
                'page_title'           => __( 'Settings', 'jobboard' ),
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key'       => '',
                // Set it you want google fonts to update weekly. A google_api_key value is required.
                'google_update_weekly' => false,
                // Must be defined to add google fonts to the typography module
                'async_typography'     => true,
                // Use a asynchronous font on the front end or font string
                //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
                'admin_bar'            => false,
                // Show the panel pages on the admin bar
                'admin_bar_icon'       => '',
                // Choose an icon for the admin bar menu
                'admin_bar_priority'   => 50,
                // Choose an priority for the admin bar menu
                'global_variable'      => '',
                // Set a different name for your global variable other than the opt_name
                'dev_mode'             => false,
                'forced_dev_mode_off'  => false,
                // Show the time the page took to load, etc
                'update_notice'        => true,
                // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
                'customizer'           => false,
                // Enable basic customizer support
                //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
                //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field

                // OPTIONAL -> Give you extra features
                'page_priority'        => null,
                // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'          => 'edit.php?post_type=jobboard-post-jobs',
                // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'     => 'manage_jobboard_options',
                // Permissions needed to access the options panel.
                'menu_icon'            => '',
                // Specify a custom URL to an icon
                'last_tab'             => '',
                // Force your panel to always open to a specific tab (by id)
                'page_icon'            => '',
                // Icon displayed in the admin panel next to your menu_title
                'page_slug'            => '',
                // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
                'save_defaults'        => true,
                // On load save the defaults to DB before user clicks save or not
                'default_show'         => false,
                // If true, shows the default value next to each field that is not the default value.
                'default_mark'         => '',
                // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export'   => true,
                // Shows the Import/Export panel when not used as a field.

                // CAREFUL -> These options are for advanced use only
                'transient_time'       => 60 * MINUTE_IN_SECONDS,
                'output'               => true,
                // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'           => true,
                // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.

                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'             => '',
                // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'use_cdn'              => true,
                // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
            );

            return apply_filters('jobboard_admin_args',$args);
        }

        private function sections(){
            $sections = array(
                'general-setting'   => array(
                    'title'         => esc_html__( 'General', 'jobboard' ),
                    'id'            => 'general',
                    'icon'          => 'dashicons-before dashicons-admin-settings',
                    'fields'        => array(
                        array(
                            'id'       => 'posts-per-page',
                            'type'     => 'slider',
                            'title'    => esc_html__( 'Jobs Listing', 'jobboard' ),
                            'subtitle' => esc_html__( 'Number of jobs to show per page.', 'jobboard' ),
                            'default'  => 12,
                            'min'      => 0,
                            'step'     => 1,
                            'max'      => 100,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'       => 'author-per-page',
                            'type'     => 'slider',
                            'title'    => esc_html__( 'Companies & Candidates Listing', 'jobboard' ),
                            'subtitle' => esc_html__( 'Number of Companies or Candidates to show per page.', 'jobboard' ),
                            'default'  => 12,
                            'min'      => 0,
                            'step'     => 1,
                            'max'      => 100,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'       => 'dashboard-per-page',
                            'type'     => 'slider',
                            'title'    => esc_html__( 'Dashboard Listing', 'jobboard' ),
                            'subtitle' => esc_html__( 'Number of Items in Dashboard to show per page.', 'jobboard' ),
                            'default'  => 12,
                            'min'      => 0,
                            'step'     => 1,
                            'max'      => 100,
                            'display_value' => 'text'
                        ),
                        array(
                            'id'       => 'default-currency',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Default Currency', 'jobboard' ),
                            'subtitle' => esc_html__( 'This sets default currency.', 'jobboard' ),
                            'default'  => 'USD',
                            'options'  => jb_get_currencies_options(),
                        ),
                        array(
                            'id'       => 'currency-position',
                            'type'     => 'select',
                            'title'    => esc_html__( 'Currency Position', 'jobboard' ),
                            'subtitle' => esc_html__( 'This sets the number of decimal points shown in displayed prices.', 'jobboard' ),
                            'default'  => 'left',
                            'options'  => array(
                                'left'          => esc_html__('Left ($99.99)', 'jobboard'),
                                'right'         => esc_html__('Right (99.99$)', 'jobboard'),
                                'left_space'    => esc_html__('Left with space ($ 99.99)', 'jobboard'),
                                'right_space'   => esc_html__('Right with space (99.99 $)', 'jobboard'),
                            ),
                        ),
                        array(
                            'id'       => 'font-awesome',
                            'type'     => 'switch',
                            'title'    => esc_html__( 'Load Awesome Font', 'jobboard' ),
                            'subtitle' => esc_html__( 'If your theme or other plugins support Awesome Font we recommend turn off options.', 'jobboard' ),
                            'default'  => true,
                        ),
                    )
                ),
                'page-setting'         => array(
                    'title'            => esc_html__( 'Pages', 'jobboard' ),
                    'id'               => 'page-setting',
                    'icon'             => 'dashicons dashicons-admin-page',
                    'desc'             => esc_html__('This page is set in pages template drop down.', 'jobboard'),
                    'fields'           => array(
                        array(
                            'id'       => 'page-jobs',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'title'    => esc_html__( 'Jobs', 'jobboard' ),
                            'subtitle' => esc_html__( 'Page for Job listing.', 'jobboard' ),
                            'desc'     => esc_html__( '(search, archive, taxonomy, location, tags)', 'jobboard' ),
                        ),
                        array(
                            'id'       => 'page-employers',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'title'    => esc_html__( 'Employer Listing', 'jobboard' ),
                            'subtitle' => esc_html__( 'Page for Employer listing.', 'jobboard' ),
                        ),
                        array(
                            'id'       => 'page-candidates',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'title'    => esc_html__( 'Candidate Listing', 'jobboard' ),
                            'subtitle' => esc_html__( 'Page for Candidate listing.', 'jobboard' ),
                        ),
                        array(
                            'id'       => 'page-dashboard',
                            'type'     => 'select',
                            'data'     => 'pages',
                            'title'    => esc_html__( 'Account Dashboard', 'jobboard' ),
                            'subtitle' => esc_html__( 'Page for User Dashboard.', 'jobboard' ),
                        )
                    )
                ),
                'dashboard-setting'         => array(
                    'title'                 => esc_html__( 'Dashboard', 'jobboard' ),
                    'id'                    => 'dashboard-setting',
                    'icon'                  => 'dashicons dashicons-performance'
                ),
                'employer-custom-fields'    => array(
                    'title'                 => esc_html__( 'Employers', 'jobboard' ),
                    'id'                    => 'custom-fields-employer',
                    'icon'                  => 'dashicons dashicons-businessman',
                    'desc'                  => esc_html__( 'Employer profile form.', 'jobboard' ),
                    'subsection'            => true,
                    'fields'                => array(
                        array(
                            'id'            => 'employer-custom-fields',
                            'type'          => 'rc_custom_fields',
                            'title'         => esc_html__( 'Profile Fields', 'jobboard' ),
                            'default'       => $this->default_profile_employer()
                        ),
                        array(
                            'id'            => 'employer-social-fields',
                            'type'          => 'rc_custom_fields',
                            'title'         => esc_html__( 'Social Network', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Add social network for Employer profile.', 'jobboard' ),
                            'support'       => array('text', 'heading'),
                            'default'       => $this->default_social()
                        )
                    )
                ),
                'candidate-custom-fields'   => array(
                    'title'                 => esc_html__( 'Candidates', 'jobboard' ),
                    'id'                    => 'custom-fields-candidate',
                    'icon'                  => 'dashicons dashicons-groups',
                    'desc'                  => esc_html__( 'Candidates profile form.', 'jobboard' ),
                    'subsection'            => true,
                    'fields'                => array(
                        array(
                            'id'            => 'candidate-custom-fields',
                            'type'          => 'rc_custom_fields',
                            'title'         => esc_html__( 'Profile Fields', 'jobboard' ),
                            'default'       => $this->default_profile_candidate()
                        ),
                        array(
                            'id'       => 'candidate-social-fields',
                            'type'     => 'rc_custom_fields',
                            'title'    => esc_html__( 'Social Network', 'jobboard' ),
                            'subtitle' => esc_html__( 'Add social network for Candidate profile.', 'jobboard' ),
                            'support'  => array('text', 'heading'),
                            'default'  => $this->default_social()
                        )
                    )
                ),
                'account-endpoints' => array(
                    'title'         => esc_html__( 'Endpoints', 'jobboard' ),
                    'id'            => 'account-endpoints',
                    'icon'          => 'dashicons dashicons-admin-links',
                    'desc'          => esc_html__( 'Endpoints are appended to your page URLs to handle specific actions on the accounts pages. They should be unique and can be left blank to disable the endpoint.', 'jobboard' ),
                    'subsection'    => true,
                    'fields'        => $this->default_endpoints()
                ),
                'seo-optimization'  => array(
                    'title'         => esc_html__( 'SEO Optimization', 'jobboard' ),
                    'id'            => 'seo-optimization',
                    'icon'          => 'dashicons dashicons-chart-area',
                    'fields'        => array(
                        array(
                            'id'            => 'post-job-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Job Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for "Job" post type.', 'jobboard' ),
                            'placeholder'   => esc_html__('job', 'jobboard'),
                        ),
                        array(
                            'id'            => 'taxonomy-type-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Type Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for "Type" taxonomy.', 'jobboard' ),
                            'placeholder'   => esc_html__('type', 'jobboard'),
                        ),
                        array(
                            'id'            => 'taxonomy-specialism-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Specialism Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for "Specialism" taxonomy.', 'jobboard' ),
                            'placeholder'   => esc_html__('specialism', 'jobboard'),
                        ),
                        array(
                            'id'            => 'taxonomy-location-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Location Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for "Location" taxonomy.', 'jobboard' ),
                            'placeholder'   => esc_html__('location', 'jobboard'),
                        ),
                        array(
                            'id'            => 'taxonomy-tag-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Tag Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for job tag.', 'jobboard' ),
                            'placeholder'   => esc_html__('job-tag', 'jobboard'),
                        ),
                        array(
                            'id'            => 'profile-employer-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Employer Profile Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for employer profile.', 'jobboard' ),
                            'placeholder'   => esc_html__('employers', 'jobboard'),
                        ),
                        array(
                            'id'            => 'profile-candidate-slug',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Candidate Profile Slug', 'jobboard' ),
                            'subtitle'      => esc_html__( 'Custom base slug for candidate profile.', 'jobboard' ),
                            'placeholder'   => esc_html__('candidates', 'jobboard'),
                        )
                    )
                ),
                'email-setting'     => array(
                    'title'         => esc_html__( 'Email Config', 'jobboard' ),
                    'id'            => 'email-setting',
                    'icon'          => 'dashicons dashicons-email-alt'
                ),
                'email-applied'     => array(
                    'title'         => esc_html__( 'Applied', 'jobboard' ),
                    'id'            => 'email-applied',
                    'icon'          => 'dashicons dashicons-yes',
                    'desc'          => esc_html__( 'Send email after Candidate applied a job.', 'jobboard' ),
                    'subsection'    => true,
                    'fields'        => array(
                        array(
                            'id'            => 'email-applied-candidate-section-start',
                            'type'          => 'section',
                            'title'         => esc_html__( 'Candidate', 'jobboard' ),
                            'indent'        => true
                        ),
                        array(
                            'id'            => 'email-applied-candidate-from',
                            'type'          => 'text',
                            'title'         => esc_html__( 'From', 'jobboard' ),
                            'placeholder'   => get_bloginfo('name')
                        ),
                        array(
                            'id'            => 'email-applied-candidate-reply',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Reply', 'jobboard' ),
                            'placeholder'   => get_bloginfo('admin_email')
                        ),
                        array(
                            'id'            => 'email-applied-candidate-subject',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Subject', 'jobboard' ),
                            'placeholder'   => get_bloginfo('description'),
                        ),
                        array(
                            'id'            => 'email-applied-candidate-template',
                            'type'          => 'info',
                            'style'         => 'info',
                            'title'         => esc_html__( 'Email Template.', 'jobboard' ),
                            'desc'          => esc_html__( 'This template can be overridden by copying it to yourtheme/jobboard/emails/candidate-applied.php.', 'jobboard' ),
                        ),
                        array(
                            'id'            => 'email-applied-candidate-section-end',
                            'type'          => 'section',
                            'indent'        => false,
                        ),
                        array(
                            'id'            => 'email-applied-employer-section-start',
                            'type'          => 'section',
                            'title'         => esc_html__( 'Employer', 'jobboard' ),
                            'indent'        => true
                        ),
                        array(
                            'id'            => 'email-applied-employer-from',
                            'type'          => 'text',
                            'title'         => esc_html__( 'From', 'jobboard' ),
                            'placeholder'   => get_bloginfo('name')
                        ),
                        array(
                            'id'            => 'email-applied-employer-reply',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Reply', 'jobboard' ),
                            'placeholder'   => get_bloginfo('admin_email')
                        ),
                        array(
                            'id'            => 'email-applied-employer-subject',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Subject', 'jobboard' ),
                            'placeholder'   => get_bloginfo('description'),
                        ),
                        array(
                            'id'            => 'email-applied-employer-template',
                            'type'          => 'info',
                            'style'         => 'info',
                            'title'         => esc_html__( 'Email Template.', 'jobboard' ),
                            'desc'          => esc_html__( 'This template can be overridden by copying it to yourtheme/jobboard/emails/employer-applied.php.', 'jobboard' ),
                        ),
                        array(
                            'id'            => 'email-applied-employer-section-end',
                            'type'          => 'section',
                            'indent'        => false,
                        )
                    )
                ),
                'email-application' => array(
                    'title'         => esc_html__( 'Application', 'jobboard' ),
                    'id'            => 'email-application',
                    'icon'          => 'dashicons dashicons-id',
                    'desc'          => esc_html__( 'Send email after Employer approval or reject a application.', 'jobboard' ),
                    'subsection'    => true,
                    'fields'        => array(
                        array(
                            'id'            => 'email-application-from',
                            'type'          => 'text',
                            'title'         => esc_html__( 'From', 'jobboard' ),
                            'placeholder'   => get_bloginfo('name')
                        ),
                        array(
                            'id'            => 'email-application-reply',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Reply', 'jobboard' ),
                            'placeholder'   => get_bloginfo('admin_email')
                        ),
                        array(
                            'id'            => 'email-application-subject',
                            'type'          => 'text',
                            'title'         => esc_html__( 'Subject', 'jobboard' ),
                            'placeholder'   => get_bloginfo('description'),
                        ),
                        array(
                            'id'            => 'email-application-template',
                            'type'          => 'info',
                            'style'         => 'info',
                            'title'         => esc_html__( 'Email Template.', 'jobboard' ),
                            'desc'          => esc_html__( 'This template can be overridden by copying it to yourtheme/jobboard/emails/application.php.', 'jobboard' ),
                        )
                    )
                )
            );

            return apply_filters('jobboard_admin_sections', $sections);
        }

        function default_profile(){
            return apply_filters('jobboard_admin_profile', array(
                10 => array(
                    'id'         => 'profile-heading',
                    'title'      => esc_html__('Your Profile', 'jobboard' ),
                    'subtitle'   => esc_html__('Edit and update your profile.', 'jobboard' ),
                    'type'       => 'heading',
                    'heading'    => 'h3'
                ),
                20 => array (
                    'id'         => 'user_email',
                    'title'      => esc_html__('Email', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your email', 'jobboard' ),
                    'type'       => 'text',
                    'input'      => 'email',
                    'require'    => 1,
                    'placeholder'=> esc_html__('your-email@your-domain.com', 'jobboard' )
                ),
                30 => array (
                    'id'         => 'first_name',
                    'title'      => esc_html__('First Name', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your first name', 'jobboard' ),
                    'type'       => 'text',
                    'require'    => 1,
                    'col'        => 6,
                    'placeholder'=> esc_html__('First Name', 'jobboard' )
                ),
                40 => array (
                    'id'         => 'last_name',
                    'title'      => esc_html__('Last name', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your last name', 'jobboard' ),
                    'type'       => 'text',
                    'require'    => 1,
                    'col'        => 6,
                    'placeholder'=> esc_html__('Last name', 'jobboard' )
                ),
                50 => array (
                    'id'         => 'address-1',
                    'title'      => esc_html__('Address 1', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your address 1', 'jobboard' ),
                    'type'       => 'text',
                    'require'    => 1,
                    'placeholder'=> esc_html__('Address 1', 'jobboard' )
                ),
                60 => array (
                    'id'         => 'address-2',
                    'title'      => esc_html__('Address 2', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your address 2', 'jobboard' ),
                    'type'       => 'text',
                    'placeholder'=> esc_html__('Address 2', 'jobboard' )
                ),
                70 => array (
                    'id'         => 'user_city',
                    'title'      => esc_html__('City', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your city', 'jobboard' ),
                    'type'       => 'text',
                    'placeholder'=> esc_html__('City', 'jobboard' )
                ),
                80 => array (
                    'id'         => 'user_country',
                    'title'      => esc_html__('Country', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your country', 'jobboard' ),
                    'type'       => 'text',
                    'placeholder'=> esc_html__('Country', 'jobboard' )
                ),
                90 => array (
                    'id'         => 'user_phone',
                    'title'      => esc_html__('Phone', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your phone number', 'jobboard' ),
                    'type'       => 'text',
                    'input'      => 'tel',
                    'placeholder'=> esc_html__('+1 646 4706923', 'jobboard' )
                ),
                100 => array (
                    'id'         => 'url',
                    'title'      => esc_html__('Website', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your website.', 'jobboard' ),
                    'type'       => 'text',
                    'input'      => 'url',
                    'placeholder'=> esc_html__('https://www.your-website.com', 'jobboard' )
                ),
                110 => array (
                    'id'         => 'description',
                    'title'      => esc_html__('About', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your description.', 'jobboard' ),
                    'type'       => 'textarea',
                    'placeholder'=> esc_html__('Share information to fill out your profile. This may be shown publicly.', 'jobboard' )
                ),
                120 => array(
                    'id'         => 'image-heading',
                    'title'      => esc_html__('Profile Image', 'jobboard' ),
                    'subtitle'   => esc_html__('Upload your image profile.', 'jobboard' ),
                    'type'       => 'heading',
                    'heading'    => 'h3'
                ),
                130 => array(
                    'id'         => 'user_avatar',
                    'title'      => esc_html__('Avatar Image', 'jobboard' ),
                    'subtitle'   => esc_html__('Upload your avatar image.', 'jobboard' ),
                    'type'       => 'media',
                    'input'      => 'image',
                    'types'      => 'jpg,png',
                    'size'       => 200,
                    'col'        => 6,
                ),
                140 => array(
                    'id'         => 'user_cover',
                    'title'      => esc_html__('Cover Photo', 'jobboard' ),
                    'subtitle'   => esc_html__('Upload your cover photo.', 'jobboard' ),
                    'type'       => 'media',
                    'input'      => 'image',
                    'types'      => 'jpg,png',
                    'size'       => 1024,
                    'col'        => 6,
                ),
            ));
        }

        function default_profile_employer(){
            $fields = $this->default_profile();
            $fields[111] = array(
                'id'         => 'job-heading',
                'title'      => esc_html__('Recruitment Info', 'jobboard' ),
                'subtitle'   => esc_html__('To Candidates easily find you, you need to complete the recruitment information.', 'jobboard' ),
                'type'       => 'heading',
                'heading'    => 'h3'
            );
            $fields[112] = array(
                'id'         => 'job_specialisms',
                'title'      => esc_html__('Specialisms', 'jobboard' ),
                'subtitle'   => esc_html__('Select your specialisms.', 'jobboard' ),
                'placeholder'=> esc_html__('Specialisms','jobboard'),
                'type'       => 'select',
                'multi'      => true
            );
            $fields[113] = array(
                'id'         => 'job_vacancies',
                'title'      => esc_html__('Vacancies', 'jobboard' ),
                'subtitle'   => esc_html__('Enter your vacancies.', 'jobboard' ),
                'placeholder'=> esc_html__('Vacancies','jobboard'),
                'type'       => 'text',
                'input'      => 'number'
            );
            $fields = apply_filters('jobboard_admin_profile_employer', $fields);
            ksort($fields);
            return $fields;
        }

        function default_profile_candidate(){
            $fields = $this->default_profile();

            $fields[41] = array(
                'id'         => 'user_sex',
                'title'      => esc_html__('Sex', 'jobboard' ),
                'subtitle'   => esc_html__('Select your sex.', 'jobboard' ),
                'placeholder'=> esc_html__('Your sex','jobboard'),
                'type'       => 'select',
                'options'    => array(
                    'female' => esc_html__('Female', 'jobboard'),
                    'male'   => esc_html__('Male', 'jobboard'),
                    'none'   => esc_html__('Do not want to say', 'jobboard'),
                ),
                'require'    => 1,
                'col'        => 6,
            );
            $fields[42] = array(
                'id'         => 'user_birthday',
                'title'      => esc_html__('Birthday', 'jobboard' ),
                'subtitle'   => esc_html__('Select your birthday (mm/dd/yyyy).', 'jobboard' ),
                'placeholder'=> esc_html__('10/05/1990','jobboard'),
                'type'       => 'text',
                'input'      => 'date',
                'require'    => 1,
                'col'        => 6,
            );
            $fields[111] = array(
                'id'         => 'job-heading',
                'title'      => esc_html__('Suggest jobs', 'jobboard' ),
                'subtitle'   => esc_html__('We will suggest work according to your wishes.', 'jobboard' ),
                'type'       => 'heading',
                'heading'    => 'h3'
            );
            $fields[112] = array(
                'id'         => 'job_specialisms',
                'title'      => esc_html__('Jobs Interests', 'jobboard' ),
                'subtitle'   => esc_html__('Select your interests.', 'jobboard' ),
                'placeholder'=> esc_html__('Jobs Interests','jobboard'),
                'type'       => 'select',
                'multi'      => true
            );
            $fields[113] = array(
                'id'         => 'job_salary',
                'title'      => esc_html__('Minimum Salary', 'jobboard' ),
                'subtitle'   => esc_html__('Set minimum salary ($).', 'jobboard' ),
                'placeholder'=> esc_html__('Minimum Salary','jobboard'),
                'type'       => 'text',
                'input'      => 'number'
            );
            $fields[150] = array(
                'id'         => 'cv-heading',
                'title'      => esc_html__('CV', 'jobboard' ),
                'subtitle'   => esc_html__('Upload your cv file ( .pdf, .docx, .doc, .rtf ).', 'jobboard' ),
                'type'       => 'heading',
                'heading'    => 'h3'
            );
            $fields[160] = array(
                'id'         => 'cv',
                'title'      => esc_html__('CV File', 'jobboard' ),
                'subtitle'   => esc_html__('Upload your CV file.', 'jobboard' ),
                'type'       => 'media',
                'types'      => 'pdf,docx,doc,rtf',
                'require'    => 1,
                'size'       => 1000
            );
            $fields = apply_filters('jobboard_admin_profile_candidate', $fields);
            ksort($fields);
            return $fields;
        }

        function default_social(){
            return apply_filters('jobboard_admin_social', array(
                array(
                    'id'         => 'social-heading',
                    'title'      => esc_html__('Social Network', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your social network url.', 'jobboard' ),
                    'type'       => 'heading',
                    'heading'    => 'h3',
                ),
                array (
                    'id'         => 'social-facebook',
                    'title'      => esc_html__('Facebook URL', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your Facebook url', 'jobboard' ),
                    'type'       => 'text',
                    'col'        => 6,
                    'class'      => 'fa fa-facebook-square',
                    'placeholder'=> esc_html__('https://www.facebook.com/', 'jobboard' ),
                ),
                array (
                    'id'         => 'social-twitter',
                    'title'      => esc_html__('Twitter URL', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your Twitter url', 'jobboard' ),
                    'type'       => 'text',
                    'col'        => 6,
                    'class'      => 'fa fa-twitter-square',
                    'placeholder'=> esc_html__('https://twitter.com/', 'jobboard' ),
                ),
                array (
                    'id'         => 'social-plus',
                    'title'      => esc_html__('Google+ URL', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your Google+ url', 'jobboard' ),
                    'type'       => 'text',
                    'col'        => 6,
                    'class'      => 'fa fa-google-plus-square',
                    'placeholder'=> esc_html__('https://plus.google.com/', 'jobboard' ),
                ),
                array (
                    'id'         => 'social-linkedin',
                    'title'      => esc_html__('Linkedin URL', 'jobboard' ),
                    'subtitle'   => esc_html__('Enter your Linkedin url', 'jobboard' ),
                    'type'       => 'text',
                    'col'        => 6,
                    'class'      => 'fa fa-linkedin-square',
                    'placeholder'=> esc_html__('https://www.linkedin.com/', 'jobboard' ),
                )
            ));
        }

        function default_endpoints(){

            $employer = apply_filters( 'jobboard_admin_endpoints_employer', array(
                array(
                    'id'       => 'section-employer',
                    'type'     => 'section',
                    'title'    => esc_html__( 'Employer', 'jobboard' ),
                    'indent'   => true
                ),
                array(
                    'id'       => 'endpoint-jobs',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Applications History', 'jobboard' ),
                    'subtitle' => esc_html__( 'Endpoint for the employer → view manage jobs page', 'jobboard' ),
                    'placeholder' => esc_html__('jobs', 'jobboard'),
                ),
                array(
                    'id'       => 'endpoint-new-job',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Post New ', 'jobboard' ),
                    'subtitle' => esc_html__( 'Endpoint for the employer → view post a new job page', 'jobboard' ),
                    'placeholder' => esc_html__('new-job', 'jobboard'),
                )
            ));

            $candidate = apply_filters( 'jobboard_admin_endpoints_candidate', array(
                array(
                    'id'       => 'section-candidate',
                    'type'     => 'section',
                    'title'    => esc_html__( 'Candidate', 'jobboard' ),
                    'indent'   => true
                ),
                array(
                    'id'       => 'endpoint-applied',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Applications History', 'jobboard' ),
                    'subtitle' => esc_html__( 'Endpoint for the candidate → view applications history page', 'jobboard' ),
                    'placeholder' => esc_html__('applied', 'jobboard')
                ),
                array(
                    'id'       => 'endpoint-profile',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Manage Profile', 'jobboard' ),
                    'subtitle' => esc_html__( 'Endpoint for the candidate → view manager profile page', 'jobboard' ),
                    'placeholder' => esc_html__('profile', 'jobboard')
                )
            ));

            return apply_filters( 'jobboard_admin_endpoints', array_merge($candidate, $employer));
        }

        function custom_setting($fields = array()){

            $fields['require'] = array(
                'name'          => 'require',
                'type'          => 'select',
                'title'         => esc_html__('Require', 'jobboard'),
                'subtitle'      => esc_html__('Front-end require field (*).', 'jobboard'),
                'options'       => array(
                    false       => esc_html__('No', 'jobboard'),
                    true        => esc_html__('Yes', 'jobboard'),
                )
            );

            $fields['require_notice'] = array(
                'name'          => 'notice',
                'type'          => 'text',
                'title'         => esc_html__('Require Notice', 'jobboard'),
                'subtitle'      => esc_html__('Front-end field validate notice.', 'jobboard'),
            );

            return $fields;
        }

        function custom_setting_text($fields){

            $fields['input'] = array(
                'name'      => 'input',
                'type'      => 'select',
                'title'     => esc_html__('Type', 'jobboard'),
                'subtitle'  => esc_html__('Front-end text, number, email, password...', 'jobboard'),
                'options'   => array(
                    'text'      => esc_html__('Text', 'jobboard'),
                    'number'    => esc_html__('Number', 'jobboard'),
                    'email'     => esc_html__('Email', 'jobboard'),
                    'password'  => esc_html__('Password', 'jobboard'),
                    'search'    => esc_html__('Search', 'jobboard'),
                    'tel'       => esc_html__('Tel', 'jobboard'),
                    'url'       => esc_html__('Url', 'jobboard'),
                    'time'      => esc_html__('Time', 'jobboard'),
                    'date'      => esc_html__('Date', 'jobboard'),
                    'datetime'  => esc_html__('Datetime', 'jobboard'),
                )
            );

            return $fields;
        }

        function custom_setting_media($fields = array()){

            $fields['input'] = array(
                'name'      => 'input',
                'type'      => 'select',
                'title'     => esc_html__('Type', 'jobboard'),
                'subtitle'  => esc_html__('Front-end file or image.', 'jobboard'),
                'options'   => array(
                    'file'      => esc_html__('File', 'jobboard'),
                    'image'     => esc_html__('Image', 'jobboard')
                )
            );

            $fields['require-types'] = array(
                'name'          => 'types',
                'type'          => 'text',
                'title'         => esc_html__('Upload Types', 'jobboard'),
                'subtitle'      => esc_html__('Front-end allow upload file types.', 'jobboard'),
                'placeholder'   => 'jpg,png,pdf,... or image/jpeg,image/png,application/pdf,...'
            );

            $fields['require-dimension'] = array(
                'name'          => 'size',
                'type'          => 'text',
                'title'         => esc_html__('Upload Size (Kb)', 'jobboard'),
                'subtitle'      => esc_html__('Front-end maximum upload size.', 'jobboard'),
                'default'       => 1000,
                'placeholder'   => 1000
            );

            return $fields;
        }

        function custom_remove_fields($fields){

            unset($fields['switch']);
            unset($fields['color']);
            unset($fields['color-rgba']);
            unset($fields['gallery']);
            unset($fields['ace-editor']);

            return $fields;
        }

        function fields_candidate_social($fields){
            $social = jb_get_option('candidate-social-fields');

            if(!$social){
                return $fields;
            }

            return array_merge($fields, $social);
        }

        function fields_employer_social($fields){
            $social = jb_get_option('employer-social-fields');

            if(!$social){
                return $fields;
            }

            return array_merge($fields, $social);
        }

        function fields_change_password($fields){

            $pass = array(
                'change-pass-heading'   => array(
                    'id'                => 'change-pass-heading',
                    'title'             => esc_html__('Change Password', 'jobboard'),
                    'subtitle'          => esc_html__("Leave blank if you'd like your password to remain the same.", 'jobboard'),
                    'type'              => 'heading',
                    'heading'           => 'h3'
                ),
                'new-password'          => array (
                    'id'                => 'new-password',
                    'title'             => esc_attr__('New Password', 'jobboard'),
                    'type'              => 'text',
                    'input'             => 'password',
                    'placeholder'       => esc_attr__('New Password', 'jobboard')
                ),
                'confirm-password'      => array (
                    'id'                => 'confirm-password',
                    'title'             => esc_html__('Confirm Password', 'jobboard'),
                    'type'              => 'text',
                    'input'             => 'password',
                    'placeholder'       => esc_html__('Confirm Password', 'jobboard')
                ),
            );

            return array_merge($fields, $pass);
        }
    }

endif;