<?php
/**
 * The Template for displaying email template.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/emails/employer-applied.php
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

<h2><?php echo sprintf(esc_html__('Hello %s', 'jobboard'), $employer->display_name); ?></h2>
<h4><?php esc_html_e('A candidate have applied a job.', 'jobboard'); ?></h4>
<table style="text-align: left;font-style: italic;">
    <tbody>
    <tr>
        <th style="min-width: 100px"><?php esc_html_e('Job title', 'jobboard'); ?></th>
        <td><a href="<?php echo esc_url(get_permalink($post->ID)); ?>"><?php echo esc_html($post->post_title); ?></a></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Applied by', 'jobboard'); ?></th>
        <td><a href="mailto:<?php echo esc_attr($candidate->user_email); ?>"><?php echo esc_html($candidate->display_name); ?></a></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Email', 'jobboard'); ?></th>
        <td><?php echo esc_html($candidate->user_email); ?></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Covering', 'jobboard'); ?></th>
        <td><?php echo esc_html($candidate->covering); ?></td>
    </tr>
    <tr>
        <th><?php esc_html_e('Status', 'jobboard'); ?></th>
        <td><a href="<?php echo esc_url($employer->manager); ?>"><?php esc_html_e('Edit', 'jobboard'); ?></a></td>
    </tr>
    </tbody>
</table>