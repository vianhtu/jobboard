<?php
/**
 * JobBoard Install.
 *
 * @class 		JobBoard_Install
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Install')) :

    class JobBoard_Install{

        function install()
        {
            global $wp_rewrite;
            self::add_role();
            self::create_roles();
            self::create_tables();
            self::install_pages();

            // Also register endpoints - this needs to be done prior to rewrite rule flush
            add_filter( 'query_vars', array(JB()->query, 'add_query_vars'));

            // Setup endpoints.
            JB()->query->add_endpoints();

            // Setup rewrites.
            add_filter('rewrite_rules_array', array(JB()->query, 'rewrites'));

            // Flush rules after install
            $wp_rewrite->flush_rules();

            // Start setup wizard.
            set_transient('jobboard_setup_wizard', true, MINUTE_IN_SECONDS);

            // Trigger action
            do_action( 'jobboard_installed' );
        }

        private function add_role(){
            add_role('jobboard_role_employer', esc_html__('Employer', 'jobboard'),
                array( 'read' => true, 'level_0' => true )
            );

            add_role('jobboard_role_candidate', esc_html__('Candidate', 'jobboard'),
                array( 'read' => true, 'level_0' => true )
            );

            add_role('jobboard_role_jobs', esc_html__('Jobs Manager', 'jobboard'),
                array( 'read' => true, 'level_0' => true )
            );
        }

        private function create_roles(){
            global $wp_roles;

            if ( ! class_exists( 'WP_Roles' ) ) {
                return;
            }

            if ( ! isset( $wp_roles ) ) {
                $wp_roles = new WP_Roles();
            }

            $capabilities = self::get_capabilities();

            foreach ( $capabilities as $cap_group ) {
                foreach ( $cap_group as $cap ) {
                    $wp_roles->add_cap( 'jobboard_role_jobs', $cap );
                    $wp_roles->add_cap( 'administrator', $cap );
                }
            }
        }

        private function get_capabilities(){
            $capabilities     = array();
            $capability_types = array( 'job_type', 'job_location', 'job_specialism', 'job_tag' );

            $capabilities['core'] = array(
                'manage_jobboard_options'
            );

            $capabilities['jobs'] = array(
                'edit_job',
                'read_job',
                'delete_job',
                'delete_jobs',
                'delete_others_jobs',
                'delete_private_jobs',
                'delete_published_jobs',
                'edit_jobs',
                'edit_others_jobs',
                'edit_private_jobs',
                'edit_published_jobs',
                'publish_jobs',
                'read_private_jobs'
            );

            foreach ($capability_types as $capability_type) {
                $capabilities[$capability_type] = array(
                    "manage_{$capability_type}_terms",
                    "edit_{$capability_type}_terms",
                    "delete_{$capability_type}_terms",
                    "assign_{$capability_type}_terms",
                );
            }

            return $capabilities;
        }

        private function install_pages(){
            $_pages = array(
                'dashboard'   => array('post_name' => 'dashboard', 'post_title' => esc_html__('Dashboard', 'jobboard')),
                'employers'   => array('post_name' => 'employers', 'post_title' => esc_html__('Employer List', 'jobboard')),
                'candidates'  => array('post_name' => 'candidates', 'post_title' => esc_html__('Candidate List', 'jobboard')),
                'jobs'        => array('post_name' => 'jobs', 'post_title' => esc_html__('Jobs', 'jobboard')),
            );

            foreach ($_pages as $key => $page){
                $_p     = get_page_by_path($page['post_name']);
                $_pid   = '';
                if(isset($_p->ID)){
                    $_pid = $_p->ID;
                } else {
                    $_new_p = wp_insert_post(array(
                        'post_title'    => $page['post_title'],
                        'post_name'     => $page['post_name'],
                        'post_type'     => 'page',
                        'post_status'   => 'publish'
                    ), true);

                    if(!is_wp_error($_new_p)) $_pid = $_new_p;
                }

                if(class_exists('Redux')) {
                    Redux::setOption('jobboard_options', 'page-' . $key, $_pid);
                }
            }
        }

        private function create_tables() {
            global $wpdb;

            $wpdb->hide_errors();

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            dbDelta( self::get_schema() );
        }

        private function get_schema()
        {
            global $wpdb;
            $collate = '';

            if ($wpdb->has_cap('collation')) {
                $collate = $wpdb->get_charset_collate();
            }

            $tables = "CREATE TABLE {$wpdb->prefix}jobboard_applied (app_id bigint(20) NOT NULL auto_increment,user_id bigint(20) NOT NULL,post_id bigint(20) NOT NULL,app_status varchar(20) NOT NULL,app_date datetime NOT NULL default CURRENT_TIMESTAMP,PRIMARY KEY (app_id)) $collate;";
            return $tables;
        }
    }
endif;