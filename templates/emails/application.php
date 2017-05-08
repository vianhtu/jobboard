<?php
/**
 * The Template for displaying email template.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/emails/application.php
 *
 * HOWEVER, on occasion JobBoard will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		FOX
 * @package 	JobBoard/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>

<h2><?php echo esc_html(sprintf(esc_html__('Hello %s', 'jobboard'), $candidate->display_name)); ?></h2>
<h4><?php echo esc_html(sprintf(esc_html__('Your Application %s !', 'jobboard'), $candidate->application)); ?></h4>
<table style="text-align: left;font-style: italic;">
    <tbody>
    <tr>
        <th style="min-width: 100px"><?php esc_html_e('Job title', 'jobboard'); ?></th>
        <td><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a></td>
    </tr>
    <tr>
        <th><?php esc_html_e('By', 'jobboard'); ?></th>
        <td><a href="<?php echo esc_url(get_author_posts_url($employer->ID)); ?>"><?php echo esc_html($employer->display_name); ?></a></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Email', 'jobboard'); ?></th>
        <td><?php echo esc_html($employer->user_email); ?></td>
    </tr>
    </tbody>
</table>
