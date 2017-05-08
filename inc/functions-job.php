<?php
/**
 * JobBoard Job Functions
 *
 * Functions for job specific things.
 *
 * @author   FOX
 * @category Core
 * @package  JobBoard/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * return schema JobPosting.
 *
 * http://schema.org/
 * @return string
 */
function jb_job_schema() {
    return apply_filters('jb/job/schema', 'http://schema.org/JobPosting');
}

/**
 * return page title.
 *
 * @param  bool $echo
 * @return string
 */
function jb_the_page_title( $echo = true ) {

    if ( is_search() ) {
        $page_title = sprintf( __( 'Search Results: &ldquo;%s&rdquo;', 'jobboard' ), get_search_query() );

        if ( get_query_var( 'paged' ) )
            $page_title .= sprintf( __( '&nbsp;&ndash; Page %s', 'jobboard' ), get_query_var( 'paged' ) );

    } elseif ( is_tax() ) {

        $page_title = single_term_title( "", false );

    } else {

        $page_id = jb_page_id( 'jobs' );
        $page_title   = get_the_title( $page_id );

    }

    $page_title = apply_filters( 'jb/job/page/title', $page_title );

    if ( $echo ) {
        echo $page_title;
    } else {
        return $page_title;
    }
}

function jb_job_the_types(){
    echo jb_job_get_type();
}

/**
 * return job-type.html.
 *
 * @param string $id
 * @param string $style
 * @param string $before
 * @param string $after
 * @return array|bool|false|mixed|string|void|WP_Error
 */
function jb_job_get_type($id = '', $style = '', $before = '', $after = ''){

    $post = get_post($id);

    if(!$post)
        return false;

    $terms = get_the_terms( $post->ID, 'jobboard-tax-types' );

    if ( is_wp_error( $terms ) )
        return $terms;

    if ( !isset( $terms[0] ) )
        return false;

    $link = get_term_link( $terms[0], 'jobboard-tax-types' );

    if ( is_wp_error( $link ) )
        return $link;

    $color = get_term_meta($terms[0]->term_id, '_color', true);
    $style = apply_filters('jb/job/type/style', $style);

    if($style == 'background'){
        $style = 'background-color:' . $color . ';';
    } else {
        $style = 'color:' . $color . ';';
    }

    $type = '<a href="' . esc_url( $link ) . '" style="'.esc_attr($style).'" class="job-type" rel="tag">' . $before . $terms[0]->name . $after . '</a>';

    return apply_filters( "jb/job/type/html", $type , $style);
}

function jb_job_the_locations(){
    echo jb_job_location_html();
}

/**
 * return location.html
 *
 * @param string $id
 * @param string $before
 * @param string $sep
 * @param string $after
 * @param bool $itemprop
 * @return bool|string
 */
function jb_job_location_html($id = '', $before = '', $sep = ', ', $after = ''){
    $post = get_post($id);

    if(!$post) {
        return false;
    }

    $terms = wp_get_post_terms($post->ID, 'jobboard-tax-locations');

    if( is_wp_error($terms) ) {
        return false;
    }

    $_terms = array();
    jb_sort_terms($terms, $_terms);

    $links  = array();

    foreach ( $_terms as $index => $term ) {
        $link    = get_term_link( $term->term_id, 'jobboard-tax-locations' );
        $links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . esc_html($term->name) . '</a>';
    }

    if($address = jb_job_meta('_address')) {
        $links[] = '<span>'.esc_html($address).'</span>';
    }

    $term_links = apply_filters( "jobboard_job_location", $links );

    return $before . join( $sep, $term_links ) . $after;
}

/**
 * return job image.
 *
 * @param string $size
 * @param string $no_image_size
 * @return mixed|void
 */
function jb_job_image_url($size = '', $no_image_size = '200x200') {
    global $post, $authordata;

    if ( has_post_thumbnail() ) {
        $image = get_the_post_thumbnail_url($post->ID, $size);
    } elseif (isset($authordata->ID) && ($employer_picture = get_user_meta($authordata->ID, '_photo', true))){
        $image = jb_account_get_media_url($employer_picture);
    } else {
        $image = jb_get_placeholder_image($no_image_size);
    }

    return apply_filters('jb/job/image/url', $image);
}

/**
 * job salary.
 *
 * @param $post
 * @param bool $echo
 * @return array|default|string
 */
function jb_job_salary($post = '', $echo = true){

    $salary = jb_job_meta('_salary', '', $post);

    if($echo){
        echo esc_html($salary);
    } else {
        return $salary;
    }
}

function jb_job_the_salary(){
    echo jb_job_get_salary();
}

function jb_job_get_salary($post_id = ''){
    global $post;

    if(!$post_id && !empty($post->ID)){
        $post_id = $post->ID;
    }

    if(!$post_id){
        return false;
    }

    $min_salary     = jb_job_get_min_salary($post_id);
    $max_salary     = jb_job_get_max_salary($post_id);
    $extra_salary   = jb_job_get_extra_salary($post_id);
    $currency       = jb_job_get_currency_symbol($post_id);
    $position       = jb_get_option('currency-position', 'left');
    $salary         = '';

    if($min_salary != ''){
        $salary .= jb_get_salary_currency($min_salary, $currency, $position);
    }

    if($max_salary != ''){
        $salary .= sprintf(' - %s', jb_get_salary_currency($max_salary, $currency, $position));
    }

    if($extra_salary != ''){
        $salary .= sprintf(' %s', $extra_salary);
    }

    $salary = apply_filters('jb/job/salary', $salary, $min_salary, $max_salary, $extra_salary, $currency);

    return $salary;
}

function jb_job_get_min_salary($post_id){
    return jb_job_meta('_salary_min', 0, $post_id);
}

function jb_job_get_max_salary($post_id){
    return jb_job_meta('_salary_max', 0, $post_id);
}

function jb_job_get_extra_salary($post_id){
    return jb_job_meta('_salary_extra', '', $post_id);
}

function jb_job_get_currency($post_id){
    return jb_job_meta('_salary_currency', jb_get_option('currency', 'USD'), $post_id);
}

function jb_job_get_currency_symbol($post_id){
    $currency = jb_job_get_currency($post_id);
    return jb_get_currency_symbol($currency);
}

/**
 * return excerpt
 *
 * @param string $text
 * @param int $num_words
 * @param null $more
 * @return mixed|void
 */
function jb_job_excerpt($text = '', $num_words = 30, $more = null){

    $raw_excerpt = $text;

    if(!$text) {

        $text = get_the_content('');
        $text = strip_shortcodes($text);

        /** This filter is documented in wp-includes/post-template.php */
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
    }

    $num_words = apply_filters('jb/job/excerpt/words', $num_words);

    $text = wp_trim_words($text, $num_words, $more);

    return apply_filters( 'jb/job/excerpt', $text, $raw_excerpt );
}

/**
 * job status.
 *
 * @param string $status
 * @return string
 */
function jb_job_status($status = ''){
    global $post;

    $list = jb_job_status_list();

    if(!$status && isset($post->post_status)){
        $status = $post->post_status;
    }

    if(isset($list[$status])){
        return $list[$status];
    } else {
        return $list['trash'];
    }
}

function jb_job_apply_status(){
    global $post;

    if(!isset($post->app_status)){
        return false;
    }

    return $post->app_status;
}

/**
 * job status list.
 *
 * @return array $status
 */
function jb_job_status_list(){

    $status = apply_filters('jb/job/status/list', array(
        'publish' => esc_html__('Approved', 'jobboard'),
        'pending' => esc_html__('Pending', 'jobboard'),
        'trash'   => esc_html__('Rejected', 'jobboard')
    ));

    return $status;
}

/**
 * return job meta.
 *
 * @param $key
 * @param string $default
 * @param string $post
 * @return default|string|array
 */
function jb_job_meta($key, $default = '', $post = ''){

    $post = get_post($post);

    if(!$key){
        return $default;
    }

    $value = maybe_unserialize(get_post_meta($post->ID, $key, true));

    if(!empty($value)){
        return $value;
    } else {
        return $default;
    }
}

/**
 * return count applied.
 *
 * @return default|string
 */
function jb_job_count_applied(){
    return JB()->employer->count_applied('', array('approved'));
}

function jb_job_apply_fields(){
    $user       = wp_get_current_user();
    $covering   = get_user_meta($user->ID, 'covering', true);
    $cv         = get_user_meta($user->ID, 'cv', true);

    $fields = array(
        array (
            'id'         => 'name',
            'title'      => esc_html__('Full Name *', 'jobboard' ),
            'type'       => 'text',
            'value'      => $user->display_name,
            'require'    => 1,
        ),
        array (
            'id'         => 'email',
            'title'      => esc_html__('Email Address *', 'jobboard' ),
            'type'       => 'text',
            'input'      => 'email',
            'value'      => $user->user_email,
            'require'    => 1,
        ),
        array (
            'id'         => 'covering',
            'title'      => esc_html__('Covering Letter *', 'jobboard' ),
            'type'       => 'textarea',
            'value'      => $covering,
            'require'    => 1,
            'placeholder'=> esc_html__('Explain to the employer why you fit the job role.', 'jobboard' )
        ),
        array(
            'id'         => 'cv',
            'title'      => esc_html__('CV *', 'jobboard' ),
            'button'     => esc_html__('Select CV', 'jobboard' ),
            'type'       => 'media',
            'value'      => $cv,
            'require'    => 1,
            'types'      => 'pdf,doc,docx,rtf',
            'size'       => 1024
        )
    );

    return apply_filters('jobboard_apply_job_fields', $fields);
}