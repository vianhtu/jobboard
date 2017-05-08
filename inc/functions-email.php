<?php
/**
 * JobBoard Email Functions
 *
 * Functions for email specific things.
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
 * basic email keys.
 *
 * @param array $keys
 * @return array
 */
function jb_email_keys($keys = array()){

    $keys['site_title']     = get_bloginfo('name');
    $keys['admin_email']    = get_bloginfo('admin_email');
    $keys['site_url']       = site_url();

    return apply_filters('jb/email/keys', $keys);
}

/**
 * send email.
 *
 * @param array $keys
 * @param $to
 * @param string $from
 * @param string $subject
 * @param string $message
 * @param array $attachments
 */
function jb_email_send($keys = array(), $to, $from = '', $subject = '', $message = '', $attachments = array()){

    $headers    = array();
    $keys       = array_merge($keys, jb_email_keys());

    if(!empty($keys)) {
        foreach ($keys as $k => $v) {
            if($subject) {
                $subject = str_replace("[$k]", $v, $subject);
            }

            if($message) {
                $message = str_replace("[$k]", $v, $message);
            }

            if($from) {
                $from = str_replace("[$k]", $v, $from);
            }
        }
    }

    if($from) {
        $headers[] = 'From: ' . $from;
    }

    $headers[] = "Content-Type: text/html; charset=UTF-8";

    wp_mail($to, $subject, $message, $headers, $attachments);
}