<?php
/**
 * The Template for displaying profile actions.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/dashboard/global/profile-actions.php.
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

<div class="form-actions profile-actions">

    <?php do_action('jobboard_form_profile_actions'); ?>

    <?php wp_nonce_field( 'edit_profile' ); ?>

    <input type="submit" class="button" name="edit-profile" value="<?php esc_html_e('Save changes', 'jobboard'); ?>">
    <input type="hidden" name="action" value="edit_profile">
    <input type="hidden" name="form" value="jobboard-form">
</div>
