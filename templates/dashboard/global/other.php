<?php
/**
 * The Template for displaying other user permissions.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/global/other.php.
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

<div class="dashboard-other col-md-12">
    <div class="display_name">
        <?php echo sprintf( esc_html__( 'Hello %s%s%s', 'jobboard' ), '<strong>', esc_html($user->display_name), '</strong>'); ?>
    </div>
    <div class="notice">
        <?php echo sprintf( esc_html__( 'You do not have sufficient permissions to access this page %sSign out%s', 'jobboard'), '<a href="' . esc_url(wp_logout_url(get_permalink())) . '">', '</a>') ?>
    </div>
</div>