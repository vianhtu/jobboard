<?php
/**
 * The Template for displaying job archives.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/archive-job.php.
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

    <?php do_action( 'jobboard_main_before_content' ); ?>

        <?php if ( apply_filters( 'jobboard_archive_show_title', true ) ) : ?>

        <h1 class="jobboard-page-title"><?php jb_the_page_title(); ?></h1>

        <?php endif; ?>

        <?php do_action( 'jobboard_archive_description' ); ?>

        <?php if ( have_posts() ) : ?>

            <?php do_action( 'jobboard_loop_before' ); ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <?php jb_get_template_part( 'content', 'jobs' ); ?>

            <?php endwhile; ?>

            <?php do_action( 'jobboard_loop_after' ); ?>

        <?php else: ?>

            <?php jb_get_template_part( 'loop/not-found' ); ?>

        <?php endif; ?>

    <?php do_action( 'jobboard_main_after_content' ); ?>

    <?php do_action( 'jobboard_sidebar' ); ?>

<?php get_footer('jobboard'); ?>
