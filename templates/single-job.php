<?php
/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/jobboard/single-job.php.
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

<?php get_header('jobboard'); ?>

    <?php do_action( 'jobboard_single_before_content' ); ?>

    <?php while ( have_posts() ) : the_post(); ?>

        <?php jb_get_template_part( 'content', 'single' ); ?>

    <?php endwhile; ?>

    <?php do_action( 'jobboard_single_after_content' ); ?>

    <?php do_action( 'jobboard_sidebar_single' ); ?>

<?php get_footer('jobboard'); ?>