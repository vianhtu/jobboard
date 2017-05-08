<?php
/**
 * The Template for displaying add job actions.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/employer/add-actions.php.
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

<div class="form-actions post-actions">

    <?php do_action('jobboard_form_post_actions'); ?>

    <?php wp_nonce_field( 'add_job' ); ?>

    <input type="submit" class="button" value="<?php esc_attr_e('Post Job', 'jobboard'); ?>">
    <input type="hidden" name="action" value="add_job">
    <input type="hidden" name="form" value="jobboard-form">
</div>
