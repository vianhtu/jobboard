<?php
/**
 * The Template for displaying user recent Jobs.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/user/recent.php.
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

<div class="user-recent">
    <h3><?php echo sprintf(esc_html__( 'Recent Jobs by %s%s%s', 'jobboard' ), '<span>', jb_account_get_display_name(), '</span>'); ?></h3>
    <div class="jobboard-archive-loop">
        <?php if($jobs->have_posts()): ?>
        <?php while ( $jobs->have_posts() ) : $jobs->the_post(); ?>
            <?php jb_get_template_part( 'content', 'jobs' );  ?>
        <?php endwhile; ?>
        <?php else: ?>
            <div class="recent-404">
            <?php esc_html_e('Recent Jobs Not Found!', 'jobboard'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
