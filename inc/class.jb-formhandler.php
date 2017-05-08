<?php
/**
 * JobBoard FormHandler.
 *
 * @class 		JobBoard_FormHandler
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}


class JobBoard_FormHandler{

    private $post = array();

    function __construct()
    {
        add_action( 'template_redirect', array( $this, 'template_redirect' ));
        add_action( 'jobboard_form_action_apply_job', array($this, 'user_apply_job'));
        add_action( 'jobboard_form_action_edit_profile', array($this, 'user_edit_profile'));
        add_action( 'jobboard_form_action_add_job', array($this, 'validate_job'), 10 );
        add_action( 'jobboard_form_action_add_job', array($this, 'user_add_job'), 20 );
        add_action( 'jobboard_form_action_edit_job', array($this, 'validate_job'), 10 );
        add_action( 'jobboard_form_action_edit_job', array($this, 'user_edit_job'), 20 );
        add_action( 'jobboard_form_action_send_message', array($this, 'user_send_message'));
    }

    function template_redirect(){

        /* if method unlike POST. */
        if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
            return false;
        }

        /* if action or form does not exists. */
        if ( empty( $_POST[ 'action' ]) || empty($_POST[ 'form' ])) {
            return false;
        }

        /* if form unlike jobboard-form. */
        if( $_POST[ 'form' ] !== 'jobboard-form'){
            return false;
        }

        /* verify form false. */
        if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], $_POST[ 'action' ] ) ){
            return false;
        }

        /* 3rd validate form. */
        if(apply_filters('jobboard_form_handler_validate_' . $_POST[ 'action' ], false)){
            return false;
        };

        do_action('jobboard_form_action_' . $_POST[ 'action' ]);
    }

    function user_apply_job(){

        if(empty($_POST['id']) || false === $user_id = get_current_user_id()){
            return false;
        }

        if(!is_jb_candidate($user_id)){
            return false;
        }

        $validate   = array();
        $fields     = jb_job_apply_fields();
        $cv         = get_user_meta($user_id, 'cv', true);

        foreach ($fields as $field){
            if(empty($field['id'])){
                continue;
            }

            $value = isset($_POST[$field['id']]) ? $_POST[$field['id']] : '';

            if($field['id'] == 'cv' && isset($_FILES['cv']) && $_FILES['cv']['error'] == 0){
                if(!empty($cv['id'])){
                    wp_delete_attachment($cv['id']);
                }
                if($cv_id = $this->upload_files($_FILES['cv'])){
                    $thumbnail = wp_get_attachment_image_url($cv_id, 'thumbnail', true);
                    $cv = array(
                        'url'       => wp_get_attachment_url($cv_id),
                        'id'        => $cv_id,
                        'height'    => '',
                        'width'     => '',
                        'thumbnail' => $thumbnail
                    );
                    update_user_meta($user_id, 'cv', $cv);
                }
                $value = $cv;
            } elseif ($field['id'] == 'cv'){
                $value = $cv;
            }

            /* is required. */
            if($field['require'] && !$value){
                $validate[$field['id']] = $field['id'];
                continue;
            }
        }

        if(!empty($validate)){
            JB()->session->set( 'validate', $validate );
            jb_notice_add( esc_html__( 'Error : You need to enter all required fields.', 'jobboard' ), 'error');
            return false;
        }

        $post_id        = sanitize_text_field($_POST['id']);
        $post           = get_post($post_id, OBJECT);
        $user_email     = sanitize_email($_POST['email']);
        $display_name   = sanitize_text_field($_POST['name']);
        $covering       = sanitize_text_field($_POST['covering']);

        update_user_meta($user_id, 'covering', $covering);

        if(!$post){
            jb_notice_add( esc_html__( 'Error : Job does not exists.', 'jobboard' ), 'error');
            return false;
        }

        if($post->post_type != 'jobboard-post-jobs' || $post->post_status != 'publish'){
            jb_notice_add( esc_html__( 'Error : Job unpublished.', 'jobboard' ), 'error');
            return false;
        }

        do_action('jobboard_before_apply_job', $user_id, $post);

        if(JB()->job->apply($user_id, $post->ID)) {
            $send_email              = new JobBoard_Emails();
            $employer                = get_userdata($post->post_author);
            $employer->manager       = jb_page_endpoint_url('jobs', jb_page_permalink('dashboard'));
            $candidate               = get_userdata($user_id);
            $candidate->manager      = jb_page_endpoint_url('applied', jb_page_permalink('dashboard'));
            $candidate->covering     = $covering;
            $candidate->cv           = $cv;
            $candidate->user_email   = $user_email;
            $candidate->display_name = $display_name;

            $send_email->candidate_applied($post, $employer, $candidate);
            $send_email->employer_applied($post, $employer, $candidate);

            jb_notice_add(sprintf(esc_html__("Successfully Applied for '%s'", 'jobboard'), $post->post_title), 'success');

            do_action('jobboard_applied_job', $user_id, $post);

        } else {
            jb_notice_add( esc_html__( 'Error Apply Contact Admin.', 'jobboard' ), 'error');
        }

        do_action('jobboard_after_apply_job', $user_id, $post);
    }

    function user_edit_profile(){

        /**
         * User validate.
         *
         * @validate login, group.
         */
        if(false === $user_id = get_current_user_id()){
            return false;
        }

        $validate  = $user_meta = array();
        $user      = array('ID' => $user_id);
        $user_keys = jb_user_keys();

        /* get fields. */
        if ( is_jb_employer($user_id)) {
            $fields = jb_get_option('employer-custom-fields');
        } elseif (is_jb_candidate($user_id)) {
            $fields = jb_get_option('candidate-custom-fields');
        } else {
            return false;
        }

        $fields = apply_filters('jobboard_profile_custom_fields', $fields);

        unset($fields['change-pass-heading']);
        unset($fields['new-password']);
        unset($fields['confirm-password']);

        /**
         * Field validate.
         *
         * @validate empty.
         */
        if(empty($fields)){
            return false;
        }

        /**
         * Process data.
         *
         * @form POST, FILES
         */
        foreach ($fields as $field){
            $field = jb_parse_custom_fields($field);
            $value = isset($_POST[$field['id']]) ? $_POST[$field['id']] : '';

            if($field['type'] == 'media' && isset($_FILES[$field['id']]) && $_FILES[$field['id']]['error'] == 0){

                $old_attachment = get_user_meta($user_id, $field['id'], true);
                if(!empty($old_attachment['id'])){
                    wp_delete_attachment($old_attachment['id']);
                }

                if($new_attachment = $this->upload_files($_FILES[$field['id']])) {
                    $thumbnail = wp_get_attachment_image_url($new_attachment, 'thumbnail', true);
                    $user_meta[$field['id']] = array(
                        'url'       => wp_get_attachment_url($new_attachment),
                        'id'        => $new_attachment,
                        'height'    => '',
                        'width'     => '',
                        'thumbnail' => $thumbnail
                    );
                }

                continue;
            } elseif ($field['type'] == 'media' && $file_id = get_user_meta($user_id, $field['id'], true)) {
                $value = $file_id;
            }

            /* is required. */
            if($field['require'] && !$value){
                $validate[$field['id']] = $field['id'];
                continue;
            }

            /* in user keys. */
            if(in_array($field['id'], $user_keys)){
                $user[$field['id']] = $value;
                continue;
            }

            /* is custom meta. */
            if(isset($_POST[$field['id']])) {
                $user_meta[$field['id']] = $value;
            }
        }

        /* update user. */
        if(!empty($user) && is_wp_error($error = wp_update_user($user))) {

            $validate['user_email'] = 'user_email';

            jb_notice_add($error->get_error_message(), 'error');

            JB()->session->set( 'validate', $validate );

            return false;
        }

        JB()->session->set( 'validate', $validate );

        /* update user custom meta. */
        if(!empty($user_meta)) {
            foreach ($user_meta as $k => $v) {
                update_user_meta($user_id, $k, $v);
            }
        }

        if(!empty($validate)){
            jb_notice_add(esc_html__('Error : You need to enter all required fields.', 'jobboard'), 'error');
            return false;
        }

        jb_notice_add(esc_html__('Successfully Your Profile Updated.', 'jobboard'));

        do_action('jobboard_profile_updated', $user_id, $user_meta);
    }

    function user_add_job(){
        if(empty($this->post)){
            return;
        }

        $this->post = apply_filters('jobboard_insert_job_args', $this->post);

        do_action('jobboard_before_insert_job', $this->post);

        $featured   = '';
        $tax_input  = array();

        if(isset($this->post['tax_input'])){
            $tax_input = $this->post['tax_input'];
        }

        if(isset($this->post['attachments']['featured-image'])){
            $featured = $this->post['attachments']['featured-image'];
            unset($this->post['attachments']['featured-image']);
        }

        if(is_wp_error($post_id = wp_insert_post($this->post))){
            jb_notice_add(sprintf(esc_html__('Error : %s', 'jobboard'), $post_id->get_error_message()), 'error');
            return;
        }

        if(!empty($tax_input)){
            foreach ($tax_input as $taxonomy => $terms){
                wp_set_post_terms($post_id, $terms, $taxonomy);
            }
        }

        if($featured && $featured_id = $this->upload_files($featured)){
            set_post_thumbnail($post_id, $featured_id);
        }

        do_action('jobboard_after_insert_job', $post_id, $this->post);

        jb_notice_add(sprintf(esc_html__("Successfully Added for '%s'", 'jobboard'), get_the_title($post_id)));
    }

    function user_edit_job(){
        if(empty($this->post) || empty($_GET['post_id'])){
            return;
        }

        $post_id                    = $_GET['post_id'];
        $featured                   = '';
        $tax_input                  = array();
        $this->post['ID']           = $post_id;
        $this->post['post_status']  = get_post_status($post_id);

        if(isset($this->post['tax_input'])){
            $tax_input = $this->post['tax_input'];
        }

        if(isset($this->post['attachments']['featured-image'])){
            $featured = $this->post['attachments']['featured-image'];
            unset($this->post['attachments']['featured-image']);
        }

        if(is_wp_error($post_id = wp_update_post( $this->post, true ))){
            jb_notice_add(sprintf(esc_html__('Error : %s', 'jobboard'), $post_id->get_error_message()), 'error');
            return;
        }

        if(!empty($tax_input)){
            foreach ($tax_input as $taxonomy => $terms){
                wp_set_post_terms($post_id, $terms, $taxonomy);
            }
        }

        if($featured && $featured_id = $this->upload_files($featured)){
            if($thumbnail_id = get_post_thumbnail_id($post_id)){
                wp_delete_attachment($thumbnail_id);
            }
            set_post_thumbnail($post_id, $featured_id);
        }

        do_action('jobboard_after_update_job', $post_id, $this->post);

        jb_notice_add(sprintf(esc_html__("Successfully Updated for '%s'", 'jobboard'), get_the_title($post_id)));
    }

    function user_send_message(){
        if(empty($_POST['id']) || empty($_POST['contact-name']) || empty($_POST['contact-email']) || empty($_POST['contact-message'])){
            jb_notice_add(esc_html__('You need to enter all required fields.', 'jobboard'), 'error');
            return;
        }

        $user       = get_user_by('ID', sanitize_key($_POST['id']));

        if(!$user){
            jb_notice_add(esc_html__('User does not exist.', 'jobboard'), 'error');
            return;
        }

        $subject    = sanitize_text_field($_POST['contact-name']);
        $reply      = sanitize_email($_POST['contact-email']);
        $message    = sanitize_textarea_field($_POST['contact-message']);
        $send_email = new JobBoard_Emails();

        $send_email->send_message($subject, $reply, $user->user_email, $message);

        jb_notice_add(esc_html__("Successfully Message sent.", 'jobboard'));
    }

    function validate_job(){
        if(!$user_id = get_current_user_id()){
            return true;
        }

        if(!is_jb_employer($user_id)){
            return true;
        }

        $fields     = jb_employer_job_custom_field();

        if(empty($fields)){
            return true;
        }

        $validates  = array();
        $post_keys  = jb_job_keys();
        $tax_keys   = jb_job_tax_keys();

        foreach ($fields as $field){

            if(empty($field['id'])){
                continue;
            }

            $field = jb_parse_custom_fields($field);

            if($field['type'] == 'media'){
                if(isset($_FILES[$field['id']]) && $_FILES[$field['id']]['error'] == 0){
                    $this->post['attachments'][$field['id']] = $_FILES[$field['id']];
                } elseif ($field['require']) {
                    $validates[$field['id']] = $field['id'];
                    continue;
                }
            }

            $value = isset($_POST[$field['id']]) ? $_POST[$field['id']] : '';

            if($field['require'] && !$value){
                $validates[$field['id']] = $field['id'];
                continue;
            }

            if(in_array($field['id'], $post_keys)){
                $this->post[$field['id']] = $value;
                continue;
            }

            if(isset($tax_keys[$field['id']])){
                $this->post['tax_input']['jobboard-tax-' . $field['id']] = $value;
                continue;
            }

            if(isset($_POST[$field['id']])) {
                $this->post['meta_input'][$field['id']] = $value;
            }
        }

        if(!empty($validates)){
            $this->post = array();
            JB()->session->set( 'validate', $validates );
            jb_notice_add(esc_html__('You need to enter all required fields.', 'jobboard'), 'error');
            return true;
        }

        $this->post['post_author']  = $user_id;
        $this->post['post_status']  = 'pending';
        $this->post['post_type']    = 'jobboard-post-jobs';

        return false;
    }

    function upload_files($file, $post = array()){

        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $filetype = wp_check_filetype($file['name']);

        /* validate file type. */
        if(!empty($post['extensions']) && !in_array( $filetype['ext'], $post['extensions'])){
            return false;
        }

        $upload                     = wp_handle_upload($file, array( 'test_form' => false ));

        /* upload error. */
        if(isset($upload['error'])){
            return false;
        }

        $file_name                  = sanitize_file_name(basename($upload['file']));

        $post['post_title']         = $file_name;
        $post['post_mime_type']     = $upload['type'];
        $id                         = wp_insert_attachment($post, $upload['file']);

        if(!$id){
            unlink($upload['file']);
            return false;
        }

        if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
        }

        /* update file meta. */
        wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $upload['file']));

        return $id;
    }
}