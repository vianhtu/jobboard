<?php
/**
 * The Template for displaying popup logout.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/apply/other.php.
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

<div class="apply-other">
    <p><?php echo sprintf( esc_attr__( 'Hi %s%s%s', 'jobboard' ), '<strong>', esc_html($user->display_name), '</strong>'); ?></p>
    <p><?php echo sprintf( esc_html__( 'Before apply a job, you need to be a candidate. %sSign out%s', 'jobboard'), '<a href="' . esc_url(wp_logout_url(get_permalink())) . '">', '</a>') ?></p>
</div>
