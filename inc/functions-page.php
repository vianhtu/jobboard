<?php
/**
 * JobBoard Page Functions
 *
 * Functions related to pages and menus.
 *
 * @author   FOX
 * @category Core
 * @package  JobBoard/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function jb_page_title(){
    if(is_page()){
        if(is_jb_endpoint_url()){
            $endpoint = JB()->query->get_current_endpoint();

            if ( $endpoint_title = JB()->query->get_endpoint_title( $endpoint ) ) {
                echo $endpoint_title;
            } else {
                the_title();
            }
        } else {
            the_title();
        }
    } else {
        the_title();
    }
}

function jb_page_id( $page ) {
    return absint(apply_filters('jb/page/id', jb_get_option('page-' . $page, -1)));
}

function jb_page_permalink( $page = '' ) {

    if(!$page){
        $permalink      = get_permalink();
    } elseif ($page_id  = jb_page_id($page)) {
        $permalink      = get_permalink($page_id);
    }

    return apply_filters( 'jb/page/permalink', $permalink, $page );
}

function jb_page_endpoint_url( $endpoint, $permalink = '' ) {
    if ( ! $permalink ) {
        $permalink = get_permalink();
    }

    $endpoint = jb_get_option('endpoint-' . $endpoint, $endpoint);

    if ( 'dashboard' === $endpoint) {
        return $permalink;
    } elseif ('logout' === $endpoint){
        return wp_logout_url($permalink);
    }

    if ( get_option( 'permalink_structure' ) ) {
        if ( strstr( $permalink, '?' ) ) {
            $query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
            $permalink    = current( explode( '?', $permalink ) );
        } else {
            $query_string = '';
        }
        $url = trailingslashit( $permalink ) . $endpoint . '/' . $query_string;
    } else {
        $url = add_query_arg( $endpoint, '', $permalink );
    }

    return apply_filters( 'jb/page/endpoint/url', $url, $endpoint, $permalink );
}

function jb_page_endpoint_base_pagination($endpoint, $permalink = ''){

    if ( ! $permalink ) {
        $permalink = get_permalink();
    }

    $permalink = jb_page_endpoint_url($endpoint, $permalink);

    return get_option( 'permalink_structure' ) ? $permalink . '%#%' : add_query_arg('paged', '%#%', $permalink);
}