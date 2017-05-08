<?php
/**
 * The template for displaying job content in the single-job.php template.
 *
 * This template can be overridden by copying it to yourtheme/jobboard/content-single.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
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

do_action( 'jobboard_single_before' );

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>

<article itemscope itemtype="<?php echo jb_job_schema(); ?>" <?php post_class(); ?>>

    <?php do_action( 'jobboard_single_summary_before' ); ?>

    <?php do_action( 'jobboard_single_summary' ); ?>

    <?php do_action( 'jobboard_single_summary_after' ); ?>

    <meta itemprop="url" content="<?php the_permalink(); ?>" />

</article>

<?php do_action( 'jobboard_single_after' ); ?>