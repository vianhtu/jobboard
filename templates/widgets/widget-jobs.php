<?php
/**
 * The Template for displaying widgets jobs.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/widgets/widget-jobs.php.
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

if (!$jobs->have_posts()){
    return;
}
?>

<div class="widget-content">
    <ul>

        <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>

            <li>

                <a class="loop-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

            <?php if (!$hide_salary): ?>

                <span class="loop-salary"><?php jb_job_salary(); ?></span>

            <?php endif; ?>

            <?php if (!$hide_more): ?>

                <a class="loop-readmore" href="<?php the_permalink(); ?>"><?php echo esc_html__('Read More', 'jobboard'); ?></a>

            <?php endif; ?>

            </li>

        <?php endwhile; ?>

    </ul>
</div>