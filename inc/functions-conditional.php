<?php
/**
 * JobBoard Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author      FOX
 * @category    Core
 * @package     JobBoard/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function is_jb() {
    return (is_jb_job() || is_jb_jobs() || is_jb_taxonomy() || is_jb_account_listing() || is_jb_profile() || is_jb_employer_jobs() || is_jb_dashboard()) ? true : false ;
}

function is_jb_job() {
    return is_singular( array( 'jobboard-post-jobs' ) );
}

function is_jb_jobs() {
    return ( is_post_type_archive( 'jobboard-post-jobs' ) || is_jb_taxonomy() || is_jb_employer_jobs()) ? true : false;
}

function is_jb_taxonomy($term = '') {
    return is_tax( get_object_taxonomies( 'jobboard-post-jobs' ), $term);
}

function is_jb_type($term = ''){
    return is_tax( 'jobboard-tax-types', $term );
}

function is_jb_location($term = ''){
    return is_tax( 'jobboard-tax-locations', $term );
}

function is_jb_specialism($term = ''){
    return is_tax( 'jobboard-tax-specialisms', $term );
}

function is_jb_tag($term = ''){
    return is_tax( 'jobboard-tax-tags', $term );
}

function is_jb_search(){
    return is_search() && is_jb_jobs() ? true : false;
}

function is_jb_dashboard() {
    return is_page(jb_page_id('dashboard'));
}

function is_jb_profile(){
    return !is_jb_employer_profile() && !is_jb_candidate_profile() ? false : true;
}

function is_jb_account($user_id = ''){
    if(!$user_id){
        $user_id = get_current_user_id();
    }

    if(is_jb_employer($user_id) || is_jb_candidate($user_id)){
        return true;
    }

    return false;
}

function is_jb_account_listing(){
    return !is_jb_employer_listing() && !is_jb_candidate_listing() ? false : true;
}

function is_jb_employer($user_id = '') {
    if(!$user_id){
        $user_id = get_current_user_id();
    }
    return in_array('jobboard_role_employer', jb_get_current_user_role($user_id));
}

function is_jb_employer_jobs() {
    return (is_author() && is_jb_employer(get_query_var( 'author' )) && !is_jb_profile()) ? true : false;
}

function is_jb_employer_listing(){
    return is_page(jb_page_id('employers'));
}

function is_jb_employer_profile(){
    global $jobboard_account;

    if(!isset($jobboard_account->ID)){
        return false;
    }

    if(!is_jb_employer($jobboard_account->ID)){
        return false;
    }

    return true;
}

function is_jb_employer_dashboard() {
    if(!is_jb_dashboard()){
        return false;
    }
    return is_jb_employer();
}

function is_jb_candidate($user_id = '') {
    if(!$user_id){
        $user_id = get_current_user_id();
    }
    return in_array('jobboard_role_candidate', jb_get_current_user_role($user_id));
}

function is_jb_candidate_listing(){
    return is_page(jb_page_id('candidates'));
}

function is_jb_candidate_profile(){
    global $jobboard_account;

    if(!isset($jobboard_account->ID)){
        return false;
    }

    if(!is_jb_candidate($jobboard_account->ID)){
        return false;
    }

    return true;
}

function is_jb_candidate_dashboard() {
    if(!is_jb_dashboard()){
        return false;
    }
    return is_jb_candidate();
}

function is_jb_endpoint_url( $endpoint = false ) {
    global $wp;

    $wc_endpoints = JB()->query->get_query_vars();

    if ( $endpoint !== false ) {
        if ( ! isset( $wc_endpoints[ $endpoint ] ) ) {
            return false;
        } else {
            $endpoint_var = $wc_endpoints[ $endpoint ];
        }

        return isset( $wp->query_vars[ $endpoint_var ] );
    } else {
        foreach ( $wc_endpoints as $key => $value ) {
            if ( isset( $wp->query_vars[ $key ] ) ) {
                return true;
            }
        }

        return false;
    }
}

function is_jb_applied(){
    global $post;
    return (isset($post->app_status) && $post->app_status == 'applied') ? true : false;
}

function is_jb_featured($post = ''){
    if(!$post = get_post($post)){
        return false;
    }

    if(get_post_meta($post->ID, '_featured', true)){
        return true;
    }

    return false;
}

function has_jb_type($term = '')
{
    return has_term($term, 'jobboard-tax-types');
}

function has_jb_specialism($term = '')
{
    return has_term($term, 'jobboard-tax-specialisms');
}

function has_jb_location($term = '')
{
    return has_term($term, 'jobboard-tax-locations');
}
