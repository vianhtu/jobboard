<?php
/**
 * JobBoard Page.
 *
 * @class 		JobBoard_Page
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Page')) :
    class JobBoard_Page{
        function __construct()
        {
            add_filter('the_title', array($this, 'endpoint_title' ));
            add_filter('wp_list_pages', array($this, 'list_pages'));
            add_filter('wp_nav_menu_objects', array($this, 'nav_classes'), 2);
            add_action('template_redirect', array($this, 'page_redirect' ));
        }

        function endpoint_title( $title = '' ) {
            global $wp_query;

            if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_jb_endpoint_url() ) {
                $endpoint = JB()->query->get_current_endpoint();

                if ( $endpoint_title = JB()->query->get_endpoint_title( $endpoint ) ) {
                    $title = $endpoint_title;
                }

                remove_filter( 'the_title', array($this, 'endpoint_title'));
            }

            return $title;
        }

        function list_pages( $pages ) {
            if ( is_jb() ) {
                $pages      = str_replace( 'current_page_parent', '', $pages );
                $job_page   = 'page-item-' . jb_page_id('jobs');
                if ( is_jb_jobs() ) {
                    $pages  = str_replace( $job_page, $job_page . ' current_page_item', $pages );
                } else {
                    $pages  = str_replace( $job_page, $job_page . ' current_page_parent', $pages );
                }
            }
            return $pages;
        }

        function nav_classes($menu_items){
            if ( ! is_jb_jobs() ) {
                return $menu_items;
            }

            $jobs_page 		= (int) jb_page_id('jobs');
            $page_for_posts = (int) get_option( 'page_for_posts' );

            foreach ( (array) $menu_items as $key => $menu_item ) {
                $classes = (array) $menu_item->classes;
                if ( $page_for_posts == $menu_item->object_id ) {
                    $menu_items[$key]->current = false;
                    if ( in_array( 'current_page_parent', $classes ) ) {
                        unset( $classes[ array_search('current_page_parent', $classes) ] );
                    }
                    if ( in_array( 'current-menu-item', $classes ) ) {
                        unset( $classes[ array_search('current-menu-item', $classes) ] );
                    }
                } elseif ( is_jb_jobs() && $jobs_page == $menu_item->object_id && 'page' === $menu_item->object ) {
                    $menu_items[ $key ]->current = true;
                    $classes[] = 'current-menu-item';
                    $classes[] = 'current_page_item';
                } elseif ( is_singular( 'jobboard-post-jobs' ) && $jobs_page == $menu_item->object_id ) {
                    $classes[] = 'current_page_parent';
                }
                $menu_items[ $key ]->classes = array_unique( $classes );
            }

            return $menu_items;
        }

        function page_redirect(){
            if ( ! empty( $_GET['page_id'] ) && '' === get_option( 'permalink_structure' ) && $_GET['page_id'] == jb_page_id( 'jobs' ) ) {
                wp_safe_redirect(apply_filters('jobboard_archive_link', get_post_type_archive_link('jobboard-post-jobs')));
                exit;
            }
        }
    }
endif;