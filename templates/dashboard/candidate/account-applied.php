<?php
/**
 * The Template for displaying application history.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/candidate/account-applied.php.
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

<div class="content-applied">
    <div class="applied-heading heading">
        <h3 class="title"><?php esc_html_e('Application History', 'jobboard'); ?></h3>
        <span class="info"><?php echo sprintf(esc_html__('You have applied for %s jobs in the past 30 days.', 'jobboard'), '<b>'.jb_candidate_count_applied().'</b>'); ?></span>
        <a class="view" href="<?php echo esc_url(jb_page_endpoint_url('applied')); ?>"><?php esc_html_e('View application', 'jobboard'); ?></a>
    </div>
</div>
