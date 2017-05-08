<?php
/**
 * The Template for displaying apply job form.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/forms/apply.php.
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

<form class="jobboard-form apply-form" method="post" action="<?php the_permalink(); ?>" enctype="multipart/form-data">

    <div class="form-title">
        <i class="fa fa-briefcase"></i>
        <span><?php esc_html_e('Application', 'jobboard'); ?></span>
        <h5><?php the_title(); ?></h5>
    </div>

    <div class="form-content">
        <?php jb_template_form_dynamic($fields); ?>
    </div>

    <div class="form-footer">
        <button type="submit" class="apply-button button"><?php esc_html_e('Apply For This Job', 'jobboard'); ?></button>
    </div>

    <?php wp_nonce_field( 'apply_job' ); ?>
    <input type="hidden" name="id" value="<?php the_ID(); ?>">
    <input type="hidden" name="action" value="apply_job">
    <input type="hidden" name="form" value="jobboard-form">
</form>
