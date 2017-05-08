<?php
/**
 * JobBoard Emails.
 *
 * @class 		JobBoard_Emails
 * @version		1.0.0
 * @package		JobBoard/Classes
 * @category	Class
 * @author 		FOX
 */

if (! defined('ABSPATH')) {
    exit();
}

if(!class_exists('JobBoard_Emails')) {

    class JobBoard_Emails
    {

        public $to          = '';
        public $from        = '';
        public $reply       = '';
        public $subject     = '';
        public $message     = '';
        public $headers     = array();
        public $attachments = array();

        function __construct($to = '', $from = '', $reply = '', $subject = '', $message = '', $headers = array(), $attachments = array())
        {
            $this->to           = $to;
            $this->from         = $from;
            $this->reply        = $reply;
            $this->subject      = $subject;
            $this->message      = $message;
            $this->headers      = $headers;
            $this->attachments  = $attachments;

            add_action('jobboard_email_header', array($this, 'email_header'));
            add_action('jobboard_email_header', array($this, 'email_footer'));
        }

        /**
         * Set value.
         *
         * @param $key
         * @param $value
         */
        function _set($key, $value){
            $this->$key = $value;
        }

        /**
         * Get value.
         *
         * @param $key
         * @return mixed
         */
        function _get($key){
            return $this->$key;
        }

        function email_header()
        {
            jb_get_template('emails/email-header.php');
        }

        function email_footer()
        {
            jb_get_template('emails/email-footer.php');
        }

        function send()
        {
            if (empty($this->to)) {
                return false;
            }

            $this->headers[]        = "Content-Type: text/html; charset=UTF-8";

            if (!empty($this->from) && !empty($this->reply)) {
                $this->headers[]    = "From: $this->from <$this->reply>";
            }

            $this->wrap_message();

            wp_mail($this->to, $this->subject, $this->message, $this->headers, $this->attachments);
        }

        function wrap_message()
        {
            ob_start();

            do_action('jobboard_email_header');

            echo wpautop(wptexturize($this->message));

            do_action('jobboard_email_footer');

            $this->message = ob_get_clean();
        }

        /**
         * Send email after candidate applied job.
         *
         * @param object $post
         * @param object $employer
         * @param object $candidate
         */
        function candidate_applied($post, $employer, $candidate)
        {
            $this->to       = $candidate->user_email;
            $this->from     = jb_get_option('email-applied-candidate-from', get_bloginfo('name'));
            $this->reply    = jb_get_option('email-applied-candidate-reply', get_bloginfo('admin_email'));
            $this->subject  = jb_get_option('email-applied-candidate-subject', get_bloginfo('description'));

            if(!empty($candidate->cv['id']) && $file = get_attached_file($candidate->cv['id'])){
                $this->attachments[] = $file;
            }

            ob_start();

            jb_get_template('emails/candidate-applied.php', array('post' => $post, 'employer' => $employer, 'candidate' => $candidate));

            $this->message  = ob_get_clean();

            $this->send();
        }

        /**
         * Send email to employer after candidate applied job.
         *
         * @param object $post
         * @param object $employer
         * @param object $candidate
         */
        function employer_applied($post, $employer, $candidate){

            $this->to           = $employer->user_email;
            $this->from         = jb_get_option('email-applied-employer-from', get_bloginfo('name'));
            $this->reply        = jb_get_option('email-applied-employer-reply', get_bloginfo('admin_email'));
            $this->subject      = jb_get_option('email-applied-employer-subject', get_bloginfo('description'));

            if(!empty($candidate->cv['id']) && $file = get_attached_file($candidate->cv['id'])){
                $this->attachments[] = $file;
            }

            ob_start();

            jb_get_template('emails/employer-applied.php', array('post' => $post, 'employer' => $employer, 'candidate' => $candidate));

            $this->message  = ob_get_clean();

            $this->send();
        }

        function application_update($post, $employer, $candidate){

            $this->to           = $candidate->user_email;
            $this->from         = jb_get_option('email-application-from', get_bloginfo('name'));
            $this->reply        = jb_get_option('email-application-reply', get_bloginfo('admin_email'));
            $this->subject      = jb_get_option('email-application-subject', get_bloginfo('description'));

            ob_start();

            jb_get_template('emails/application.php', array('post' => $post, 'employer' => $employer, 'candidate' => $candidate));

            $this->message  = ob_get_clean();

            $this->send();
        }

        function send_message($subject, $reply, $to, $message){
            $this->to       = $to;
            $this->from     = get_bloginfo('name');
            $this->reply    = $reply;
            $this->subject  = $subject;
            $this->message  = $message;
            $this->send();
        }
    }
}