<?php
/**
 * The Template for displaying applications filter.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/employer/jobs-applications-filter.php.
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

<div class="applications-filter">
    <h5><?php esc_html_e('FILTER', 'jobboard'); ?></h5>
    <div class="search">
        <input type="search" class="search-field" title="<?php esc_attr_e('Enter user name, user email, application status.', 'jobboard'); ?>" placeholder="<?php esc_html_e('Search...', 'jobboard');?>">
    </div>
    <div class="loading right">
        <i class="fa fa-spinner jobboard-loading" style="display: none;"></i>
    </div>
</div>
