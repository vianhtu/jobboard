<?php
/**
 * JobBoard Breadcrumb.
 *
 * @class 		JobBoard_Breadcrumb
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JobBoard_Breadcrumb {

	/**
	 * Breadcrumb trail.
	 *
	 * @var array
	 */
	private $crumbs = array();

	/**
	 * Add a crumb so we don't get lost.
	 *
	 * @param string $name
	 * @param string $link
	 */
	public function add_crumb( $name, $link = '' ) {
		$this->crumbs[] = array(
			strip_tags( $name ),
			$link
		);
	}

	/**
	 * Reset crumbs.
	 */
	public function reset() {
		$this->crumbs = array();
	}

	/**
	 * Get the breadcrumb.
	 *
	 * @return array
	 */
	public function get_breadcrumb() {
		return apply_filters( 'jobboard_breadcrumb', $this->crumbs, $this );
	}

	/**
	 * Generate breadcrumb trail.
	 *
	 * @return array of breadcrumbs
	 */
	public function generate() {
		$conditionals = apply_filters('jobboard_breadcrumb_conditionals', array(
            'is_jb_employer_profile',
            'is_jb_candidate_profile',
			'is_home',
			'is_404',
			'is_attachment',
			'is_single',
			'is_jb_type',
			'is_jb_location',
            'is_jb_specialism',
			'is_jb_tag',
            'is_jb_employer_jobs',
			'is_jb_jobs',
			'is_page',
			'is_post_type_archive',
			'is_category',
			'is_tag',
			'is_author',
			'is_date',
			'is_tax'
		));

        foreach ( $conditionals as $conditional ) {
            if ( call_user_func( $conditional ) ) {
                call_user_func( array( $this, 'add_crumbs_' . substr( $conditional, 3 ) ) );
                break;
            }
        }

        $this->search_trail();
        $this->paged_trail();

        return $this->get_breadcrumb();
	}

	/**
	 * Prepend the job page to job breadcrumbs.
	 */
	private function prepend_job_page() {
		$shop_page_id = jb_page_id( 'jobs' );
		$shop_page    = get_post( $shop_page_id );

		if ( $shop_page_id && $shop_page) {
			$this->add_crumb( get_the_title( $shop_page ), get_permalink( $shop_page ) );
		}
	}

	/**
	 * is home trail.
	 */
	private function add_crumbs_home() {
		$this->add_crumb( single_post_title( '', false ) );
	}

	/**
	 * 404 trail.
	 */
	private function add_crumbs_404() {
		$this->add_crumb( __( 'Error 404', 'jobboard' ) );
	}

	/**
	 * attachment trail.
	 */
	private function add_crumbs_attachment() {
		global $post;

		$this->add_crumbs_single( $post->post_parent, get_permalink( $post->post_parent ) );
		$this->add_crumb( get_the_title(), get_permalink() );
	}

	/**
	 * Single post trail.
	 *
	 * @param int    $post_id
	 * @param string $permalink
	 */
	private function add_crumbs_single( $post_id = 0, $permalink = '' ) {
		if ( ! $post_id ) {
			global $post;
		} else {
			$post = get_post( $post_id );
		}

		if ( 'jobboard-post-jobs' === get_post_type( $post ) ) {
			$this->prepend_job_page();
			if ( $terms = get_the_terms( $post->ID, 'jobboard-tax-types' ) ) {
			    foreach ($terms as $term) {
                    $main_term = apply_filters('jobboard_breadcrumb_main_term', $term);
                    $this->term_ancestors($main_term->term_id, 'jobboard-tax-types');
                    $this->add_crumb($main_term->name, get_term_link($main_term));
                }
			}
		} elseif ( 'post' != get_post_type( $post ) ) {
            $post_type = get_post_type_object( get_post_type( $post ) );
            $this->add_crumb( $post_type->labels->singular_name, get_post_type_archive_link( get_post_type( $post ) ) );
        } else {
            $cat = current( get_the_category( $post ) );
            if ( $cat ) {
                $this->term_ancestors( $cat->term_id, 'post_category' );
                $this->add_crumb( $cat->name, get_term_link( $cat ) );
            }
        }

		$this->add_crumb( get_the_title( $post ), $permalink );
	}

	/**
	 * Page trail.
	 */
	private function add_crumbs_page() {
		global $post;

		if ( $post->post_parent ) {
			$parent_crumbs = array();
			$parent_id     = $post->post_parent;

			while ( $parent_id ) {
				$page          = get_post( $parent_id );
				$parent_id     = $page->post_parent;
				$parent_crumbs[] = array( get_the_title( $page->ID ), get_permalink( $page->ID ) );
			}

			$parent_crumbs = array_reverse( $parent_crumbs );

			foreach ( $parent_crumbs as $crumb ) {
				$this->add_crumb( $crumb[0], $crumb[1] );
			}
		}

		$this->add_crumb( get_the_title(), get_permalink() );
		$this->endpoint_trail();
	}

	/**
	 * job category trail.
	 */
	private function add_crumbs_jb_type() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_job_page();
		$this->term_ancestors( $current_term->term_id, 'jobboard-tax-types' );
		$this->add_crumb( $current_term->name );
	}

    /**
     * job location trail.
     */
    private function add_crumbs_jb_location() {
        $current_term = $GLOBALS['wp_query']->get_queried_object();

        $this->prepend_job_page();
        $this->term_ancestors( $current_term->term_id, 'jobboard-tax-types' );
        $this->add_crumb( sprintf( __( 'Location: %s', 'jobboard' ), $current_term->name ));
    }

    /**
     * job specialism trail.
     */
    private function add_crumbs_jb_specialism() {
        $current_term = $GLOBALS['wp_query']->get_queried_object();

        $this->prepend_job_page();
        $this->term_ancestors( $current_term->term_id, 'jobboard-tax-types' );
        $this->add_crumb( $current_term->name );
    }

	/**
	 * job tag trail.
	 */
	private function add_crumbs_jb_tag() {
		$current_term = $GLOBALS['wp_query']->get_queried_object();

		$this->prepend_job_page();
		$this->add_crumb( sprintf( __( 'Jobs tagged &ldquo;%s&rdquo;', 'jobboard' ), $current_term->name ) );
	}

	/**
	 * job breadcrumb.
	 */
	private function add_crumbs_jb_jobs() {
		if ( get_option( 'page_on_front' ) == jb_page_id( 'jobs' ) ) {
			return;
		}

		$_name = jb_page_id( 'jobs' ) ? get_the_title( jb_page_id( 'jobs' ) ) : esc_html__('Jobs', 'jobboard');

		$this->add_crumb( $_name, get_post_type_archive_link( 'jobboard-post-jobs' ) );
	}

	/**
	 * Post type archive trail.
	 */
	private function add_crumbs_post_type_archive() {
		$post_type = get_post_type_object( get_post_type() );

		if ( $post_type ) {
			$this->add_crumb( $post_type->labels->singular_name, get_post_type_archive_link( get_post_type() ) );
		}
	}

	/**
	 * Category trail.
	 */
	private function add_crumbs_category() {
        $this_category = get_category( $GLOBALS['wp_query']->get_queried_object() );

        if ( 0 != $this_category->parent ) {
            $this->term_ancestors( $this_category->parent, 'post_category' );
            $this->add_crumb( $this_category->name, get_category_link( $this_category->term_id ) );
        }

        $this->add_crumb( single_cat_title( '', false ), get_category_link( $this_category->term_id ) );
	}

	/**
	 * Tag trail.
	 */
	private function add_crumbs_tag() {
		$queried_object = $GLOBALS['wp_query']->get_queried_object();
		$this->add_crumb( sprintf( __( 'Posts tagged &ldquo;%s&rdquo;', 'jobboard' ), single_tag_title( '', false ) ), get_tag_link( $queried_object->term_id ) );
	}

	/**
	 * Add crumbs for date based archives.
	 */
	private function add_crumbs_date() {
		if ( is_year() || is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'Y' ), get_year_link( get_the_time( 'Y' ) ) );
		}
		if ( is_month() || is_day() ) {
			$this->add_crumb( get_the_time( 'F' ), get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) );
		}
		if ( is_day() ) {
			$this->add_crumb( get_the_time( 'd' ) );
		}
	}

	/**
	 * Add crumbs for taxonomies
	 */
	private function add_crumbs_tax() {
		$this_term = $GLOBALS['wp_query']->get_queried_object();
		$taxonomy  = get_taxonomy( $this_term->taxonomy );

		$this->add_crumb( $taxonomy->labels->name );

		if ( 0 != $this_term->parent ) {
			$this->term_ancestors( $this_term->term_id, $this_term->taxonomy );
		}

		$this->add_crumb( single_term_title( '', false ), get_term_link( $this_term->term_id, $this_term->taxonomy ) );
	}

	/**
	 * Add a breadcrumb for employer archives.
	 */
	private function add_crumbs_jb_employer_jobs() {
		global $author;
		$userdata   = get_userdata( $author );
        $page_id    = jb_get_option('page-employers');
        $name       = jb_page_id( 'jobs' ) ? get_the_title( jb_page_id( 'jobs' ) ) : esc_html__('Jobs', 'jobboard');
        $this->add_crumb( get_the_title($page_id), get_page_link($page_id) );
        $this->add_crumb( jb_account_get_display_name($userdata->ID), jb_account_get_permalink($userdata->ID) );
		$this->add_crumb( $name);
	}

    /**
     * Add a breadcrumb for employer archives.
     */
    private function add_crumbs_jb_employer_profile() {
        $page_id = jb_get_option('page-employers');
        $this->add_crumb( get_the_title($page_id), get_page_link($page_id) );
        $this->add_crumb( jb_account_get_display_name());
    }

    /**
     * Add a breadcrumb for employer archives.
     */
    private function add_crumbs_jb_candidate_profile() {
        $page_id = jb_get_option('page-candidates');
        $this->add_crumb( get_the_title($page_id), get_page_link($page_id) );
        $this->add_crumb( jb_account_get_display_name());
    }

	/**
	 * Add a breadcrumb for author archives.
	 */
	private function add_crumbs_author() {
		global $author;

		$userdata = get_userdata( $author );
		$this->add_crumb( sprintf( __( 'Author: %s', 'jobboard' ), $userdata->display_name ) );
	}

	/**
	 * Add crumbs for a term.
	 * @param string $taxonomy
	 */
	private function term_ancestors( $term_id, $taxonomy ) {
		$ancestors = get_ancestors( $term_id, $taxonomy );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor ) {
			$ancestor = get_term( $ancestor, $taxonomy );

			if ( ! is_wp_error( $ancestor ) && $ancestor ) {
				$this->add_crumb( $ancestor->name, get_term_link( $ancestor ) );
			}
		}
	}

	/**
	 * Endpoints.
	 */
	private function endpoint_trail() {
		// Is an endpoint showing?
		if ( is_jb_endpoint_url() && ( $endpoint = JB()->query->get_current_endpoint() ) && ( $endpoint_title = JB()->query->get_endpoint_title( $endpoint ) ) ) {
			$this->add_crumb( $endpoint_title );
		}
	}

	/**
	 * Add a breadcrumb for search results.
	 */
	private function search_trail() {
		if ( is_search() ) {
			$this->add_crumb( sprintf( __( 'Search results for &ldquo;%s&rdquo;', 'jobboard' ), get_search_query() ), remove_query_arg( 'paged' ) );
		}
	}

	/**
	 * Add a breadcrumb for pagination.
	 */
	private function paged_trail() {
		if ( get_query_var( 'paged' ) ) {
			$this->add_crumb( sprintf( __( 'Page %d', 'jobboard' ), get_query_var( 'paged' ) ) );
		}
	}
}
