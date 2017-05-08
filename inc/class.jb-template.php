<?php
/**
 * JobBoard Template.
 *
 * @class 		JobBoard_Template
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if (! class_exists('JobBoard_Template')) :
    class JobBoard_Template{
        function __construct()
        {
            add_filter( 'template_include', array( $this, 'template_loader' ) );
        }

        function template_loader($template){
            global $author;

            $find = array();
            $file = '';

            if ( is_embed() ) {
                return $template;
            }

            if ( is_single() && is_jb_job() ) {
                $file 	= 'single-job.php';
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
            } elseif (is_page() && is_jb_account_listing()){
                $file 	= 'users.php';
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
                JB()->account->setup_users();
            } elseif (is_tax() && is_jb_taxonomy()){
                $term   = get_queried_object();

                if ( is_tax( 'jobboard-tax-types' ) || is_tax( 'jobboard-tax-specialisms' ) || is_tax( 'jobboard-tax-locations' ) || is_tax( 'jobboard-tax-tags' ) ) {
                    $file = 'taxonomy-' . str_replace('jobboard-tax-', '', $term->taxonomy) . '.php';
                } else {
                    $file = 'archive-job.php';
                }
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
            } elseif (is_archive() && is_jb_jobs()){
                $file 	= 'archive-job.php';
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
            } elseif (is_jb_profile()){
                $file 	= 'user.php';
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
            } elseif (is_author() && !empty($author) && is_jb_employer($author)){
                $file 	= 'author-employer.php';
                $find[] = $file;
                $find[] = JB()->template_path() . $file;
            }

            if ( $file ) {
                $template = locate_template( array_unique( $find ) );
                if ( ! $template ) {
                    $template = JB()->plugin_directory . 'templates/' . $file;
                }
            }

            return apply_filters('jobboard_template_include', $template);
        }
    }
endif;