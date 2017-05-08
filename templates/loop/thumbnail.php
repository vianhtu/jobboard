<?php
/**
 * The Template for displaying loop thumbnail.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/loop/thumbnail.php.
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

<div class="loop-thumbnail">

    <?php echo apply_filters('jobboard_loop_thumbnail_html', sprintf('<a href="%1$s" title="%2$s"><img src="%3$s" class="%4$s" alt="%2$s"></a>', get_permalink(), get_the_title(), jb_job_image_url(), 'attachment wp-post-image'));?>

    <?php do_action('jobboard_loop_thumbnail');?>

</div>
